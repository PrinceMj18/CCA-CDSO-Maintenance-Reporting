<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;
use Throwable;

class BackupController extends Controller
{
    public function index()
{
    return view('admin.backup', [
        'databaseDriver' => config('database.default') === 'pgsql'
            ? 'pgsql'
            : 'mysql',
    ]);
}

    /**
     * Create a PostgreSQL SQL file, custom-format dump, or complete ZIP package.
     * The active Laravel database connection is used, so localhost and Render
     * do not need separate hardcoded database paths.
     */
    public function backup(Request $request)
    {
        $validated = $request->validate([
            'format' => ['required', 'in:sql,dump,zip'],
        ]);

        $connection = $this->databaseConnection();
        if ($connection instanceof \Illuminate\Http\RedirectResponse) {
            return $connection;
        }

        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $format = $validated['format'];
        $extension = $format === 'dump' ? 'dump' : ($format === 'sql' ? 'sql' : 'zip');
        $fileName = "CCA_Backup_{$timestamp}.{$extension}";
        $outputPath = $directory . DIRECTORY_SEPARATOR . $fileName;
        $temporarySql = $directory . DIRECTORY_SEPARATOR . "database_{$timestamp}.sql";
        $temporaryDump = $directory . DIRECTORY_SEPARATOR . "database_{$timestamp}.dump";

        try {
            if ($format === 'sql' || $format === 'zip') {
    $this->createSqlBackup($connection, $temporarySql);
}

            if ($format === 'dump') {
    if ($connection['driver'] !== 'pgsql') {
        return back()->with(
            'error',
            'PostgreSQL custom dump format is only available when using PostgreSQL.'
        );
    }

    $this->runPostgresUtility(
        'pg_dump',
        [
            '--host=' . $connection['host'],
            '--port=' . $connection['port'],
            '--username=' . $connection['username'],
            '--dbname=' . $connection['database'],
            '--schema=public',
            '--no-owner',
            '--no-privileges',
            '--format=custom',
            '--file=' . $temporaryDump,
        ],
        $connection
    );

    $this->assertNonEmptyFile($temporaryDump);

    return response()->download($temporaryDump, $fileName)
        ->deleteFileAfterSend(true);
}

            if ($format === 'sql') {
                $this->assertNonEmptyFile($temporarySql);
                return response()->download($temporarySql, $fileName, [
                    'Content-Type' => 'application/sql',
                ])->deleteFileAfterSend(true);
            }

            // ZIP package: portable SQL + uploaded public storage files + instructions.
            $this->assertNonEmptyFile($temporarySql);
            $zip = new ZipArchive();
            if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Unable to create the backup ZIP file.');
            }

            $zip->addFile($temporarySql, 'database.sql');
            $publicStorage = storage_path('app/public');
            if (File::isDirectory($publicStorage)) {
                foreach (File::allFiles($publicStorage) as $file) {
                    $relative = str_replace('\\', '/', substr(
                        $file->getPathname(),
                        strlen($publicStorage) + 1
                    ));
                    $zip->addFile($file->getPathname(), 'storage/' . $relative);
                }
            }

            $readme = "CCA Maintenance Reporting System Backup\n"
                . "Created: " . now()->toDateTimeString() . "\n"
                . "Database: " . strtoupper($connection['driver']) . "\n"
                . "Database file: database.sql\n"
                . "Uploaded public files: storage/\n\n"
                . "Restore database.sql using PostgreSQL psql with ON_ERROR_STOP enabled. "
                . "The database backup contains the application public schema only.\n"
                . "Restore only into the intended Supabase database.\n"
                . "Copy the contents of storage/ into Laravel storage/app/public.\n\n"
                . "Note: This package backs up the application's connected database and Laravel public storage files. "
                . "It does not export Supabase platform-managed Auth users, Storage metadata, project settings, "
                . "or database roles outside the connected database privileges.\n";
            $zip->addFromString('RESTORE_README.txt', $readme);
            $zip->close();

            $this->assertNonEmptyFile($outputPath);
            return response()->download($outputPath, $fileName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend(true);
        } catch (Throwable $exception) {
            report($exception);
            foreach ([$outputPath, $temporarySql, $temporaryDump] as $path) {
                if (is_file($path)) {
                    @unlink($path);
                }
            }

            return back()->with('error', 'Backup failed. Check the Laravel log for details and confirm that PostgreSQL 17 client utilities are installed.');
        }
    }

    /** Restore a ZIP package, plain SQL file, or PostgreSQL custom dump. */
    public function restore(Request $request)
    {
        $request->validate([
            'backup' => ['required', 'file', 'max:512000'],
        ]);

        $connection = $this->databaseConnection();
        if ($connection instanceof \Illuminate\Http\RedirectResponse) {
            return $connection;
        }

        $uploaded = $request->file('backup');
        $extension = strtolower($uploaded->getClientOriginalExtension());
        if (!in_array($extension, ['zip', 'sql', 'dump'], true)) {
            return back()->with('error', 'Unsupported backup file. Please select a .zip, .sql, or .dump backup.');
        }

        $directory = storage_path('app/backups');
        File::ensureDirectoryExists($directory);
        $restoreDirectory = $directory . DIRECTORY_SEPARATOR . 'restore_' . now()->format('Ymd_His_u');
        File::ensureDirectoryExists($restoreDirectory);

        try {
            $databaseFile = null;
            $isCustomDump = $extension === 'dump';
            $extractedStorage = null;

            if ($extension === 'sql' || $extension === 'dump') {
                $databaseFile = $uploaded->getRealPath();
            } else {
                $zip = new ZipArchive();
                if ($zip->open($uploaded->getRealPath()) !== true) {
                    throw new \RuntimeException('The ZIP backup could not be opened.');
                }

                $databaseIndex = $zip->locateName('database.sql', ZipArchive::FL_NOCASE);
                if ($databaseIndex === false) {
                    $zip->close();
                    throw new \RuntimeException('The ZIP does not contain database.sql.');
                }

                $databaseContents = $zip->getFromIndex($databaseIndex);
                if ($databaseContents === false || trim($databaseContents) === '') {
                    $zip->close();
                    throw new \RuntimeException('The SQL backup inside the ZIP is empty or unreadable.');
                }
                $databaseFile = $restoreDirectory . DIRECTORY_SEPARATOR . 'database.sql';
                File::put($databaseFile, $databaseContents);

                // Stage uploaded files separately; do not replace live files unless the database restore succeeds.
                $extractedStorage = $restoreDirectory . DIRECTORY_SEPARATOR . 'storage';
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    if (!is_string($entry) || !str_starts_with($entry, 'storage/')) {
                        continue;
                    }
                    $relative = substr($entry, strlen('storage/'));
                    $normalized = str_replace('\\', '/', $relative);
                    $segments = explode('/', $normalized);
                    if ($normalized === '' || str_starts_with($normalized, '/') || in_array('..', $segments, true)) {
                        continue;
                    }
                    if (str_ends_with($entry, '/')) {
                        continue;
                    }

                    $contents = $zip->getFromIndex($i);
                    if ($contents === false) {
                        continue;
                    }
                    $destination = $extractedStorage . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $normalized);
                    File::ensureDirectoryExists(dirname($destination));
                    File::put($destination, $contents);
                }
                $zip->close();
            }

            $this->assertNonEmptyFile($databaseFile);

            if ($isCustomDump) {
                if ($connection['driver'] !== 'pgsql') {
                    throw new \RuntimeException(
                        'PostgreSQL custom dumps cannot be restored into MySQL.'
                    );
                }

                // Restore only application objects in public. Exclude the public
                // schema container itself so we never drop/recreate Supabase's
                // schema or its grants. Also exclude any managed PostgREST triggers.
                $restoreList = $restoreDirectory . DIRECTORY_SEPARATOR . 'restore.list';

                try {
                    $this->createFilteredPostgresRestoreList(
                        $databaseFile,
                        $restoreList,
                        $connection
                    );

                    $this->runPostgresUtility('pg_restore', [
                        '--host=' . $connection['host'],
                        '--port=' . $connection['port'],
                        '--username=' . $connection['username'],
                        '--dbname=' . $connection['database'],
                        '--schema=public',
                        '--clean',
                        '--if-exists',
                        '--no-owner',
                        '--no-privileges',
                        '--exit-on-error',
                        '--single-transaction',
                        '--use-list=' . $restoreList,
                        $databaseFile,
                    ], $connection);
                } finally {
                    if (is_file($restoreList)) {
                        @unlink($restoreList);
                    }
                }
            } else {
                $this->restoreSqlBackup($connection, $databaseFile);
            }

            if ($extractedStorage && File::isDirectory($extractedStorage)) {
                $livePublicStorage = storage_path('app/public');
                File::ensureDirectoryExists($livePublicStorage);
                // Merge backed-up files without deleting unrelated files already on the server.
                File::copyDirectory($extractedStorage, $livePublicStorage);
            }

            return back()->with(
    'success',
    'The ' . strtoupper($connection['driver']) .
    ' database backup and uploaded files were restored successfully.'
);
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', 'Restore failed. The backup may be incompatible or the database user may lack required permissions. Check the Laravel log before trying again.');
        } finally {
            File::deleteDirectory($restoreDirectory);
        }
    }

    /** Return active PostgreSQL connection settings or a user-facing error. */
    private function postgresConnection(): array|\Illuminate\Http\RedirectResponse
    {
        $connectionName = config('database.default');
        $config = config("database.connections.{$connectionName}");

        if (!$config || ($config['driver'] ?? null) !== 'pgsql') {
            return back()->with('error', 'Backup and restore currently require Laravel to use its PostgreSQL database connection.');
        }

        $host = $config['host'] ?? '127.0.0.1';
        if (is_array($host)) {
            $host = $host[0] ?? '127.0.0.1';
        }

        return [
            'host' => (string) $host,
            'port' => (string) ($config['port'] ?? '5432'),
            'database' => (string) ($config['database'] ?? ''),
            'username' => (string) ($config['username'] ?? ''),
            'password' => (string) ($config['password'] ?? ''),
            'sslmode' => (string) ($config['sslmode'] ?? 'prefer'),
        ];
    }

/**
 * Get the active Laravel database connection.
 * Supports MySQL locally and PostgreSQL on Render.
 */
private function databaseConnection(): array|\Illuminate\Http\RedirectResponse
{
    $connectionName = config('database.default');
    $config = config("database.connections.{$connectionName}");

    $driver = $config['driver'] ?? null;

    if (!$config || !in_array($driver, ['mysql', 'pgsql'], true)) {
        return back()->with(
            'error',
            'Unsupported database. Only MySQL and PostgreSQL are supported.'
        );
    }

    $host = $config['host'] ?? '127.0.0.1';

    if (is_array($host)) {
        $host = $host[0] ?? '127.0.0.1';
    }

    return [
        'driver' => $driver,
        'host' => (string) $host,
        'port' => (string) ($config['port'] ?? ($driver === 'mysql' ? '3306' : '5432')),
        'database' => (string) ($config['database'] ?? ''),
        'username' => (string) ($config['username'] ?? ''),
        'password' => (string) ($config['password'] ?? ''),
        'sslmode' => (string) ($config['sslmode'] ?? 'prefer'),
    ];
}

/**
 * Create SQL backup using the active database driver.
 */
private function createSqlBackup(array $connection, string $outputPath): void
{
    if ($connection['driver'] === 'mysql') {
        $binary = $this->findMySqlUtility('mysqldump');

        if (!$binary) {
            throw new \RuntimeException('MySQL mysqldump.exe was not found.');
        }

        $command = '"' . $binary . '"'
            . ' --host=' . escapeshellarg($connection['host'])
            . ' --port=' . escapeshellarg($connection['port'])
            . ' --user=' . escapeshellarg($connection['username'])
            . ' --single-transaction'
            . ' --routines --triggers --events --add-drop-table';

        if ($connection['password'] !== '') {
            $command .= ' --password=' . escapeshellarg($connection['password']);
        }

        $command .= ' ' . escapeshellarg($connection['database'])
            . ' > ' . escapeshellarg($outputPath);

        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !is_file($outputPath) || filesize($outputPath) === 0) {
            throw new \RuntimeException('MySQL database backup failed.');
        }

        return;
    }

    // Build a PostgreSQL archive limited to the application's public schema,
    // then render it as portable SQL without dropping/recreating the schema.
    $temporaryDump = $outputPath . '.temporary.dump';
    $restoreList = $temporaryDump . '.list';

    try {
        $this->runPostgresUtility('pg_dump', [
            '--host=' . $connection['host'],
            '--port=' . $connection['port'],
            '--username=' . $connection['username'],
            '--dbname=' . $connection['database'],
            '--schema=public',
            '--no-owner',
            '--no-privileges',
            '--format=custom',
            '--file=' . $temporaryDump,
        ], $connection);

        $this->assertNonEmptyFile($temporaryDump);

        $this->createFilteredPostgresRestoreList(
            $temporaryDump,
            $restoreList,
            $connection
        );

        $this->runPostgresUtility('pg_restore', [
            '--schema=public',
            '--clean',
            '--if-exists',
            '--no-owner',
            '--no-privileges',
            '--use-list=' . $restoreList,
            '--file=' . $outputPath,
            $temporaryDump,
        ], $connection);

        $this->assertNonEmptyFile($outputPath);
    } finally {
        foreach ([$temporaryDump, $restoreList] as $temporaryFile) {
            if (is_file($temporaryFile)) {
                @unlink($temporaryFile);
            }
        }
    }
}

/**
 * Restore SQL backup using the active database driver.
 */
private function restoreSqlBackup(array $connection, string $sqlPath): void
{
    if ($connection['driver'] === 'mysql') {
        $binary = $this->findMySqlUtility('mysql');

        if (!$binary) {
            throw new \RuntimeException('MySQL mysql.exe was not found.');
        }

        $command = '"' . $binary . '"'
            . ' --host=' . escapeshellarg($connection['host'])
            . ' --port=' . escapeshellarg($connection['port'])
            . ' --user=' . escapeshellarg($connection['username']);

        if ($connection['password'] !== '') {
            $command .= ' --password=' . escapeshellarg($connection['password']);
        }

        $command .= ' ' . escapeshellarg($connection['database'])
            . ' < ' . escapeshellarg($sqlPath);

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            throw new \RuntimeException('MySQL database restore failed.');
        }

        return;
    }

    // Older SQL backups may still contain DROP/CREATE statements for the
    // Supabase-managed event trigger. Remove only statements targeting that
    // specific trigger; leave all application schema and data statements intact.
    $sanitizedSqlPath = $sqlPath . '.sanitized.sql';

    try {
        $this->removeManagedEventTriggerStatements(
            $sqlPath,
            $sanitizedSqlPath
        );

        $this->runPostgresUtility('psql', [
            '--host=' . $connection['host'],
            '--port=' . $connection['port'],
            '--username=' . $connection['username'],
            '--dbname=' . $connection['database'],
            '--set=ON_ERROR_STOP=1',
            '--single-transaction',
            '--file=' . $sanitizedSqlPath,
        ], $connection);
    } finally {
        if (is_file($sanitizedSqlPath)) {
            @unlink($sanitizedSqlPath);
        }
    }
}

/**
 * Find MySQL command-line utilities.
 */
private function findMySqlUtility(string $utility): ?string
{
    if (PHP_OS_FAMILY === 'Windows') {
        $candidates = [
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\' . $utility . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.4\\bin\\' . $utility . '.exe',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }
    }

    return $this->findUtility(
        $utility . (PHP_OS_FAMILY === 'Windows' ? '.exe' : '')
    );
}

    /**
     * Create a pg_restore TOC list containing only application objects.
     * The public schema container itself is excluded so it is never dropped,
     * and Supabase-managed PostgREST event triggers are excluded by name.
     */
    private function createFilteredPostgresRestoreList(
        string $archivePath,
        string $listPath,
        array $connection
    ): void {
        $binary = env('PG_RESTORE_BINARY') ?: $this->findUtility('pg_restore');

        if (!$binary) {
            throw new \RuntimeException('pg_restore was not found on the server.');
        }

        $process = new Process(
            [$binary, '--list', $archivePath],
            base_path(),
            [
                'PGPASSWORD' => $connection['password'],
                'PGSSLMODE' => $connection['sslmode'],
            ]
        );
        $process->setTimeout(3600);
        $process->mustRun();

        $lines = preg_split('/\r\n|\r|\n/', $process->getOutput());
        $filtered = [];

        foreach ($lines as $line) {
            // TOC entries use forms such as "SCHEMA - public" and
            // "EVENT TRIGGER - pgrst_ddl_watch". Match the trigger names
            // independently of entry formatting to avoid missing them.
            if (preg_match('/\bSCHEMA\s+-\s+public\b|\bCOMMENT\s+-\s+SCHEMA\s+public\b/i', $line)) {
                continue;
            }

            if (preg_match('/\bpgrst_(?:drop_watch|ddl_watch)\b/i', $line)) {
                continue;
            }

            $filtered[] = $line;
        }

        $filteredContents = implode(PHP_EOL, $filtered);
        if (trim($filteredContents) === '') {
            throw new \RuntimeException('Unable to create a valid PostgreSQL restore list.');
        }

        File::put($listPath, $filteredContents . PHP_EOL);
    }

    /**
     * Sanitize older plain-SQL backups. New backups already contain only the
     * public schema and exclude the schema container and managed event triggers.
     * This compatibility pass removes only those specific legacy statements.
     */
    private function removeManagedEventTriggerStatements(
        string $inputPath,
        string $outputPath
    ): void {
        $input = fopen($inputPath, 'rb');
        if ($input === false) {
            throw new \RuntimeException('Unable to read the SQL backup file.');
        }

        $output = fopen($outputPath, 'wb');
        if ($output === false) {
            fclose($input);
            throw new \RuntimeException('Unable to create the temporary SQL restore file.');
        }

        $insideTargetStatement = false;
        $skipCurrentStatement = false;
        $statementBuffer = '';

        try {
            while (($line = fgets($input)) !== false) {
                if (!$insideTargetStatement) {
                    $isEventTriggerDdl = preg_match(
                        '/^\s*(?:DROP|CREATE|ALTER)\s+EVENT\s+TRIGGER\b|^\s*COMMENT\s+ON\s+EVENT\s+TRIGGER\b/i',
                        $line
                    );
                    $isPublicSchemaDdl = preg_match(
                        '/^\s*(?:DROP\s+SCHEMA(?:\s+IF\s+EXISTS)?|CREATE\s+SCHEMA(?:\s+IF\s+NOT\s+EXISTS)?|ALTER\s+SCHEMA)\s+"?public"?(?=\s|;|$)|^\s*COMMENT\s+ON\s+SCHEMA\s+"?public"?(?=\s|;|$)/i',
                        $line
                    );

                    if ($isEventTriggerDdl || $isPublicSchemaDdl) {
                        $insideTargetStatement = true;
                        $skipCurrentStatement = (bool) $isPublicSchemaDdl
                            || (bool) preg_match('/\bpgrst_(?:drop_watch|ddl_watch)\b/i', $line);
                        $statementBuffer = '';
                    } else {
                        if (fwrite($output, $line) === false) {
                            throw new \RuntimeException('Unable to write the temporary SQL restore file.');
                        }
                        continue;
                    }
                }

                $statementBuffer .= $line;
                if (preg_match('/\bpgrst_(?:drop_watch|ddl_watch)\b/i', $line)) {
                    $skipCurrentStatement = true;
                }

                // These DDL statements are terminated by a semicolon and do not
                // contain function bodies, so buffering them line-by-line is safe.
                if (str_contains($line, ';')) {
                    if (!$skipCurrentStatement && fwrite($output, $statementBuffer) === false) {
                        throw new \RuntimeException('Unable to write the temporary SQL restore file.');
                    }

                    $insideTargetStatement = false;
                    $skipCurrentStatement = false;
                    $statementBuffer = '';
                }
            }

            if (!feof($input)) {
                throw new \RuntimeException('An error occurred while reading the SQL backup file.');
            }

            if ($insideTargetStatement && !$skipCurrentStatement && $statementBuffer !== '') {
                if (fwrite($output, $statementBuffer) === false) {
                    throw new \RuntimeException('Unable to write the temporary SQL restore file.');
                }
            }
        } finally {
            fclose($input);
            fclose($output);
        }

        $this->assertNonEmptyFile($outputPath);
    }

    /** Run a PostgreSQL utility without shell interpolation. */
    private function runPostgresUtility(string $utility, array $arguments, array $connection): void
    {
        $binaryEnvironmentKey = strtoupper(str_replace('-', '_', $utility)) . '_BINARY';
        $binary = env($binaryEnvironmentKey) ?: $this->findUtility($utility);
        if (!$binary) {
            throw new \RuntimeException("{$utility} was not found on the server.");
        }

        $process = new Process(
            array_merge([$binary], $arguments),
            base_path(),
            [
                'PGPASSWORD' => $connection['password'],
                'PGSSLMODE' => $connection['sslmode'],
            ]
        );
        $process->setTimeout(3600);
        $process->mustRun();
    }

    /** Find an executable through PATH on Windows or Linux. */
    private function findUtility(string $utility): ?string
    {
        $finder = PHP_OS_FAMILY === 'Windows' ? 'where.exe' : 'which';
        try {
            $process = new Process([$finder, $utility]);
            $process->setTimeout(10);
            $process->run();
            if (!$process->isSuccessful()) {
                return null;
            }
            $lines = preg_split('/\r\n|\r|\n/', trim($process->getOutput()));
            $path = trim($lines[0] ?? '');
            return $path !== '' ? $path : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function assertNonEmptyFile(?string $path): void
    {
        if (!$path || !is_file($path) || filesize($path) === 0) {
            throw new \RuntimeException('The generated backup file is missing or empty.');
        }
    }
}
