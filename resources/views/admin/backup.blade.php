
@extends('layouts.apps')

@section('content')
<div
    class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6"
    x-data="{
        showConfirm: false,
        actionType: '',
        restoreForm: null,
        fileName: '',
        backupFormat: 'zip',

        openConfirmation(type, form = null) {
            this.actionType = type;
            this.restoreForm = form;
            this.showConfirm = true;
        },

        confirmAction() {
            if (this.actionType === 'backup') {
                this.$refs.backupForm.submit();
            } else if (this.actionType === 'restore' && this.restoreForm) {
                this.restoreForm.submit();
            } else if (this.actionType === 'clear-file') {
                this.$refs.backupFileInput.value = '';
                this.fileName = '';
            }

            this.showConfirm = false;
        }
    }"
>
    <div class="max-w-5xl mx-auto">
        <div class="mb-8">
            <p class="text-xs tracking-wider uppercase text-[#607844] dark:text-[#a8bd91] font-semibold">System Administration</p>
            <h1 class="mt-2 text-3xl font-semibold text-gray-800 dark:text-gray-100">Backup &amp; Restore</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Protect the maintenance reporting system by creating backups and restoring previously saved system data.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-[#edf3e8] border border-[#dce7d2] text-[#52693a] dark:bg-[#607844]/15 dark:border-[#607844]/30 dark:text-[#c3d5b2]">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
                <ul class="list-disc ml-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            <!-- CREATE BACKUP -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-5">
                    <div class="w-11 h-11 rounded-lg bg-[#607844]/10 text-[#607844] dark:text-[#a8bd91] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Create Backup</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Download a system backup</p>
                    </div>
                </div>

                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">
                    Choose a backup format for the database currently connected to this system.
                </p>

                <form x-ref="backupForm" action="{{ route('admin.backup.create') }}" method="POST">
                    @csrf

                    <div class="space-y-3 mb-6">
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-[#607844]">
                            <input type="radio" name="format" value="zip" x-model="backupFormat" class="mt-1 text-[#607844] focus:ring-[#607844]">
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">Complete System Backup (.zip)</span>
                                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">Includes the {{ strtoupper($databaseDriver ?? 'mysql') }} database backup, uploaded files, and restore instructions. Restore it to the same database type.</span>
                                <span class="inline-block mt-2 text-[11px] font-semibold text-[#607844] dark:text-[#a8bd91]">RECOMMENDED</span>
                            </span>
                        </label>

                        <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-[#607844]">
                            <input type="radio" name="format" value="sql" x-model="backupFormat" class="mt-1 text-[#607844] focus:ring-[#607844]">
                            <span class="min-w-0">
                                <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">{{ ($databaseDriver ?? 'mysql') === 'pgsql' ? 'PostgreSQL SQL Script (.sql)' : 'MySQL SQL Script (.sql)' }}</span>
                                <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">Database records and structure only. This file is intended for the same database type and does not include uploaded files.</span>
                            </span>
                        </label>

                        @if(($databaseDriver ?? 'mysql') === 'pgsql')
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-[#607844]">
                                <input type="radio" name="format" value="dump" x-model="backupFormat" class="mt-1 text-[#607844] focus:ring-[#607844]">
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-gray-800 dark:text-gray-100">PostgreSQL Custom Backup (.dump)</span>
                                    <span class="block mt-1 text-xs text-gray-500 dark:text-gray-400">Compact custom-format backup, restored with pg_restore. Does not include uploaded images.</span>
                                </span>
                            </label>
                        @endif
                    </div>

                    <button type="button" @click="openConfirmation('backup')" class="w-full px-5 py-3 text-sm font-semibold text-white bg-[#607844] rounded-lg hover:bg-[#52693a] transition-colors">
                        <span class="inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download Backup
                        </span>
                    </button>
                </form>
            </div>

            <!-- RESTORE BACKUP -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center mb-5">
                    <div class="w-11 h-11 rounded-lg bg-[#607844]/10 text-[#607844] dark:text-[#a8bd91] flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Restore Backup</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Restore a previously saved backup</p>
                    </div>
                </div>

                <div class="p-4 mb-5 rounded-lg bg-amber-50/70 border border-amber-200 dark:bg-amber-900/10 dark:border-amber-800/50">
                    <p class="text-sm text-amber-800 dark:text-amber-300 leading-relaxed">
                        <strong>Warning:</strong> Restoring a backup can replace existing database objects and records. Create a fresh backup before proceeding, and restore only into the intended database.
                    </p>
                </div>

                <form
                    x-ref="restoreForm"
                    action="{{ route('admin.backup.restore') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    @submit.prevent="openConfirmation('restore', $event.target)"
                >
                    @csrf

                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select Backup File</label>

                    <input
                        x-ref="backupFileInput"
                        type="file"
                        name="backup"
                        accept=".zip,.sql,.dump"
                        required
                        @change="fileName = $event.target.files[0]?.name || ''"
                        class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#607844]/10 file:text-[#52693a] hover:file:bg-[#607844]/20 dark:file:bg-[#607844]/20 dark:file:text-[#c3d5b2]"
                    >

                    <!-- SELECTED FILE WITH REMOVE BUTTON -->
                    <div
                        x-show="fileName"
                        x-cloak
                        class="mt-3 flex items-center justify-between gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-3 py-2"
                    >
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 shrink-0 text-[#607844]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 2v6h6"/>
                            </svg>
                            <span x-text="fileName" class="text-xs text-gray-600 dark:text-gray-300 truncate"></span>
                        </div>

                        <button
                            type="button"
                            @click="openConfirmation('clear-file')"
                            class="shrink-0 inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700 dark:text-red-400"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Remove
                        </button>
                    </div>

                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Accepted formats: ZIP package, SQL script{{ ($databaseDriver ?? 'mysql') === 'pgsql' ? ', or PostgreSQL custom dump' : '' }}.
                    </p>

                    <button
                        type="button"
                        @click="if ($refs.restoreForm.reportValidity()) openConfirmation('restore', $refs.restoreForm)"
                        class="w-full mt-5 px-5 py-3 text-sm font-semibold text-white bg-[#607844] rounded-lg hover:bg-[#52693a] transition-colors"
                    >
                        Restore Backup
                    </button>
                </form>
            </div>
        </div>

        <!-- INFORMATION -->
        <div class="mt-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-5">Backup Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm">
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300">Available Formats</p>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">
                        ZIP and {{ strtoupper($databaseDriver ?? 'mysql') }} SQL{{ ($databaseDriver ?? 'mysql') === 'pgsql' ? ', plus PostgreSQL custom dump' : '' }}
                    </p>
                </div>
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300">Connected Database</p>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">
                        {{ ($databaseDriver ?? 'mysql') === 'pgsql' ? 'PostgreSQL (deployed database)' : 'MySQL (local database)' }}
                    </p>
                </div>
                <div>
                    <p class="font-medium text-gray-700 dark:text-gray-300">Included Files</p>
                    <p class="mt-1 text-gray-500 dark:text-gray-400">Complete ZIP includes Laravel public storage files</p>
                </div>
            </div>

            <p class="mt-5 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                Backups are created from the database connected to this Laravel application. ZIP backups also include uploaded files stored by the application. SQL and ZIP database contents are specific to the database type shown above, so restore them to a matching MySQL or PostgreSQL database. Supabase platform-managed Auth users, Storage service metadata, project settings, and external services are not included.
            </p>
        </div>
    </div>

    <!-- CONFIRMATION MODAL -->
    <div
        x-cloak
        x-show="showConfirm"
        x-transition.opacity
        @keydown.escape.window="showConfirm = false"
        @click.self="showConfirm = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="confirm-title"
    >
        <div x-show="showConfirm" x-transition class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-gray-800">

            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-[#607844]/10 text-[#607844] dark:text-[#a8bd91]">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        :d="actionType === 'restore'
                            ? 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
                            : actionType === 'clear-file'
                                ? 'M6 18L18 6M6 6l12 12'
                                : 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z'"
                    />
                </svg>
            </div>

            <h2
                id="confirm-title"
                class="text-center text-lg font-semibold text-gray-900 dark:text-white"
                x-text="actionType === 'restore'
                    ? 'Restore System Backup?'
                    : actionType === 'clear-file'
                        ? 'Remove Selected File?'
                        : 'Create System Backup?'"
            ></h2>

            <p
                class="mt-3 text-center text-sm leading-6 text-gray-500 dark:text-gray-300"
                x-text="actionType === 'restore'
                    ? 'Restoring may replace existing database objects and records. Make sure you have a recent backup before continuing.'
                    : actionType === 'clear-file'
                        ? 'Are you sure you want to remove the selected backup file? You will need to select it again before restoring.'
                        : 'Create and download the selected backup format? This may take a moment.'"
            ></p>

            <div class="mt-6 flex gap-3">
                <button
                    type="button"
                    @click="showConfirm = false"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    @click="confirmAction()"
                    class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold text-white transition-colors"
                    :class="actionType === 'clear-file'
                        ? 'bg-red-600 hover:bg-red-700'
                        : 'bg-[#607844] hover:bg-[#52693a]'"
                    x-text="actionType === 'restore'
                        ? 'Yes, Restore'
                        : actionType === 'clear-file'
                            ? 'Yes, Remove'
                            : 'Yes, Create Backup'"
                ></button>
            </div>
        </div>
    </div>
</div>
@endsection
