<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\User;
use App\Models\MaintenanceRequest;
use Illuminate\Support\Facades\DB;
use App\Models\RequestAssignment;
use App\Models\RequestLog;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryItem;
use App\Models\InventoryUsage;
use Carbon\Carbon;


class AdminController extends Controller
{
    // public function adminDashboard()
    // {
    //     $low_stocks = InventoryItem::whereColumn(
    //         'quantity',
    //         '<=',
    //         'minimum_stock'
    //     )->where(
    //             'quantity',
    //             '>',
    //             0
    //         )->count();
    //     $total = MaintenanceRequest::count();
    //     $approved = MaintenanceRequest::approved()->count();
    //     $pending = MaintenanceRequest::pending()->count();
    //     $completed = MaintenanceRequest::completed()->count();
    //     $inProgress = MaintenanceRequest::inProgress()->count();
    //     $categoryData = MaintenanceRequest::select('category', DB::raw('COUNT(*) as total'))
    //         ->groupBy('category')
    //         ->pluck('total', 'category');

    //     $today = now()->toDateString();

    //     $dailyRequests = MaintenanceRequest::whereDate('DATE(created_at', $today);

    //     $dailyTotal = (clone $dailyRequests)->count();

    //     $dailyApproved = (clone $dailyRequests)
    //         ->where('status', 'approved')
    //         ->count();

    //     $dailyPending = (clone $dailyRequests)
    //         ->where('status', 'pending')
    //         ->count();

    //     $dailyCompleted = (clone $dailyRequests)
    //         ->where('status', 'completed')
    //         ->count();

    //     $dailyInProgress = (clone $dailyRequests)
    //         ->where('status', 'in_progress')
    //         ->count();

    //     $dailyRejected = (clone $dailyRequests)
    //         ->where('status', 'rejected')
    //         ->count();

    //     return view('pages.dashboard.index', compact(
    //         'total',
    //         'approved',
    //         'pending',
    //         'completed',
    //         'inProgress',
    //         'categoryData',
    //         'low_stocks',

    //         //daily
    //         'dailyTotal',
    //         'dailyApproved',
    //         'dailyPending',
    //         'dailyCompleted',
    //         'dailyInProgress',
    //         'dailyRejected'
    //     ));
    // }

    public function adminDashboard()
    {
        $low_stocks = InventoryItem::whereColumn('quantity', '<=', 'minimum_stock')
            ->where('quantity', '>', 0)->count();
        $totalInventory = InventoryItem::count();
        $outOfStock = InventoryItem::where('quantity', 0)->count();

        $analytics = $this->getDashboardAnalytics('overall');

        return view('pages.dashboard.index', array_merge([
            'totalInventory' => $totalInventory,
            'low_stocks' => $low_stocks,
            'outOfStock' => $outOfStock,
            'initialPeriod' => 'overall',
        ], $analytics));
    }

    public function dashboardAnalytics(Request $request)
    {
        $period = $request->query('period', 'overall');

        abort_unless(
            in_array($period, ['overall', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'decade'], true),
            422,
            'Invalid dashboard period.'
        );

        return response()->json($this->getDashboardAnalytics($period));
    }

    private function getDashboardAnalytics(string $period): array
    {
        $now = now();
        $start = null;
        $end = null;

        switch ($period) {
            case 'daily':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'weekly':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'monthly':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'quarterly':
                $quarterStartMonth = intdiv($now->month - 1, 3) * 3 + 1;
                $start = $now->copy()->month($quarterStartMonth)->startOfMonth();
                $end = $start->copy()->addMonths(2)->endOfMonth();
                break;
            case 'yearly':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'decade':
                $start = $now->copy()->subYears(9)->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
        }

        $baseQuery = MaintenanceRequest::query();

if ($start && $end) {
    $baseQuery->whereBetween('created_at', [$start, $end]);
}

        // Aggregate all status totals in one database query instead of issuing
        // a separate COUNT query for every status on each period change.
        $statusRow = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved")
            ->selectRaw("SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected")
            ->first();

        $total = (int) ($statusRow->total ?? 0);
        $statusCounts = [
            'pending' => (int) ($statusRow->pending ?? 0),
            'approved' => (int) ($statusRow->approved ?? 0),
            'in_progress' => (int) ($statusRow->in_progress ?? 0),
            'completed' => (int) ($statusRow->completed ?? 0),
            'rejected' => (int) ($statusRow->rejected ?? 0),
        ];

$categoryData = (clone $baseQuery)
    ->where('status', '!=', 'rejected')
    ->select('category', DB::raw('COUNT(*) as total'))
    ->groupBy('category')
    ->orderByDesc('total')
    ->pluck('total', 'category')
    ->map(fn ($value) => (int) $value);

        $topCategoryCount = (int) ($categoryData->first() ?? 0);

$topCategory = $topCategoryCount > 0
    ? $categoryData
        ->filter(fn ($count) => $count === $topCategoryCount)
        ->keys()
        ->implode(', ')
    : 'No data';

        // Most frequently reported problem locations for the same selected date range.
        // Keep each location paired with the categories reported there.
        $locationRows = (clone $baseQuery)
            ->select('location', 'category', DB::raw('COUNT(*) as total'))
            ->whereNotNull('location')
            ->whereRaw("TRIM(location) <> ''")
            ->groupBy('location', 'category')
            ->get();

        $locationData = $locationRows
            ->groupBy('location')
            ->map(function ($rows) {
                return [
                    'total' => (int) $rows->sum('total'),
                    'categories' => $rows->pluck('category')->filter()->unique()->values()->all(),
                ];
            })
            ->sortByDesc('total')
            ->take(10);

        [$labels, $trendData] = $this->buildDashboardTrend($period, $baseQuery, $now);

        $titles = [
            'overall' => ['Overall', 'Overall Maintenance Requests', 'All recorded maintenance requests, grouped by year.'],
            'daily' => ['Daily', 'Maintenance Requests Today', 'Requests received today, grouped by hour.'],
            'weekly' => ['Weekly', 'Maintenance Requests This Week', 'Requests received this week, grouped by day.'],
            'monthly' => ['Monthly', 'Maintenance Requests This Month', 'Requests received this month, grouped by day.'],
            'quarterly' => ['Quarterly', 'Maintenance Requests This Quarter', 'Requests received this quarter, grouped by month.'],
            'yearly' => ['Yearly', 'Maintenance Requests This Year', 'Requests received this year, grouped by month.'],
            'decade' => ['10-Year', 'Maintenance Requests — Last 10 Years', 'Requests received during the last 10 years, grouped by year.'],
        ];

        return [
    'period' => $period,
    'title' => $titles[$period][0],
    'chartTitle' => $titles[$period][1],
    'description' => $titles[$period][2],

    'total' => $total,
    'dateRange' => $this->formatDashboardDateRange(
        $period,
        $start,
        $end
    ),

    'pending' => $statusCounts['pending'],
    'approved' => $statusCounts['approved'],
    'inProgress' => $statusCounts['in_progress'],
    'completed' => $statusCounts['completed'],
    'rejected' => $statusCounts['rejected'],

    'categoryData' => $categoryData->toArray(),
    'topCategory' => $topCategory,
    'topCategoryCount' => $topCategoryCount,
    'locationData' => $locationData->toArray(),

    // IMPORTANT: use one naming convention
    'labels' => $labels->toArray(),
    'data' => $trendData->toArray(),
];
    }
private function formatDashboardDateRange($period, $start, $end)
{
    if ($period === 'overall') {
        return 'All recorded maintenance requests';
    }

    if (!$start || !$end) {
        return 'All recorded maintenance requests';
    }

    if ($start->isSameDay($end)) {
        return $start->format('F j, Y');
    }

    if ($start->year === $end->year) {
        return $start->format('F j') . ' – ' . $end->format('F j, Y');
    }

    return $start->format('F j, Y') . ' – ' . $end->format('F j, Y');
}
    


private function buildDashboardTrend(string $period, $baseQuery, Carbon $now): array
{
    $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();

    // Database-compatible date extraction.
    $expressions = [
        'year' => $driver === 'pgsql'
            ? 'CAST(EXTRACT(YEAR FROM created_at) AS INTEGER)'
            : 'YEAR(created_at)',

        'hour' => $driver === 'pgsql'
            ? 'CAST(EXTRACT(HOUR FROM created_at) AS INTEGER)'
            : 'HOUR(created_at)',

        'day' => $driver === 'pgsql'
            ? 'CAST(EXTRACT(DAY FROM created_at) AS INTEGER)'
            : 'DAY(created_at)',

        'month' => $driver === 'pgsql'
            ? 'CAST(EXTRACT(MONTH FROM created_at) AS INTEGER)'
            : 'MONTH(created_at)',
    ];

    // Overall and 10-year trend.
    if ($period === 'overall' || $period === 'decade') {

        $rows = (clone $baseQuery)
            ->selectRaw("{$expressions['year']} as period, COUNT(*) as total")
            ->groupByRaw($expressions['year'])
            ->orderBy('period')
            ->get()
            ->mapWithKeys(fn ($row) => [
                (int) $row->period => (int) $row->total
            ]);

        $years = $period === 'decade'
            ? range($now->year - 9, $now->year)
            : $rows->keys()->sort()->values()->all();

        return [
            collect($years)->map(fn ($year) => (string) $year)->values(),
            collect($years)->map(fn ($year) => (int) $rows->get($year, 0))->values(),
        ];
    }

    // Daily: group requests by hour.
    if ($period === 'daily') {

        $rows = (clone $baseQuery)
            ->selectRaw("{$expressions['hour']} as period, COUNT(*) as total")
            ->groupByRaw($expressions['hour'])
            ->get()
            ->mapWithKeys(fn ($row) => [
                (int) $row->period => (int) $row->total
            ]);

        return [
            collect(range(0, 23))
                ->map(fn ($hour) => Carbon::createFromTime($hour)->format('g A'))
                ->values(),

            collect(range(0, 23))
                ->map(fn ($hour) => (int) $rows->get($hour, 0))
                ->values(),
        ];
    }

    // Weekly: group requests by date.
    if ($period === 'weekly') {

        $start = $now->copy()->startOfWeek();

        $rows = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as period, COUNT(*) as total')
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->mapWithKeys(fn ($row) => [
                (string) $row->period => (int) $row->total
            ]);

        return [
            collect(range(0, 6))
                ->map(fn ($day) => $start->copy()->addDays($day)->format('D, M d'))
                ->values(),

            collect(range(0, 6))
                ->map(function ($day) use ($start, $rows) {
                    $date = $start->copy()->addDays($day)->toDateString();

                    return (int) $rows->get($date, 0);
                })
                ->values(),
        ];
    }

    // Monthly: group requests by day of the month.
    if ($period === 'monthly') {

        $rows = (clone $baseQuery)
            ->selectRaw("{$expressions['day']} as period, COUNT(*) as total")
            ->groupByRaw($expressions['day'])
            ->get()
            ->mapWithKeys(fn ($row) => [
                (int) $row->period => (int) $row->total
            ]);

        return [
            collect(range(1, $now->daysInMonth))
                ->map(fn ($day) => $now->copy()->day($day)->format('M d'))
                ->values(),

            collect(range(1, $now->daysInMonth))
                ->map(fn ($day) => (int) $rows->get($day, 0))
                ->values(),
        ];
    }

    // Quarterly: group requests by month.
    if ($period === 'quarterly') {

        $quarterStartMonth = intdiv($now->month - 1, 3) * 3 + 1;

        $quarterStart = $now->copy()
            ->month($quarterStartMonth)
            ->startOfMonth();

        $rows = (clone $baseQuery)
            ->selectRaw("{$expressions['month']} as period, COUNT(*) as total")
            ->groupByRaw($expressions['month'])
            ->get()
            ->mapWithKeys(fn ($row) => [
                (int) $row->period => (int) $row->total
            ]);

        return [
            collect(range(0, 2))
                ->map(fn ($month) => $quarterStart->copy()->addMonths($month)->format('M Y'))
                ->values(),

            collect(range(0, 2))
                ->map(fn ($month) => (int) $rows->get(
                    $quarterStart->copy()->addMonths($month)->month,
                    0
                ))
                ->values(),
        ];
    }

    // Yearly: group requests by month.
    $rows = (clone $baseQuery)
        ->selectRaw("{$expressions['month']} as period, COUNT(*) as total")
        ->groupByRaw($expressions['month'])
        ->get()
        ->mapWithKeys(fn ($row) => [
            (int) $row->period => (int) $row->total
        ]);

    return [
        collect(range(1, 12))
            ->map(fn ($month) => Carbon::create()->month($month)->format('M'))
            ->values(),

        collect(range(1, 12))
            ->map(fn ($month) => (int) $rows->get($month, 0))
            ->values(),
    ];
}



    public function createAccount()
    {
        $allowedRoles = auth()->user()->role->name === 'superadmin' ? ['user', 'personnel', 'admin'] : ['user', 'personnel'];
        $roles = Role::whereIn('name', $allowedRoles)->orderBy('name')->get();
        return view('admin.create-account', compact('roles'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:50',
            'lname' => 'required|string|max:50',
            'designation' => 'nullable|in:CDSO Head,Utility Supervisor,Supply Officer,Office Secretary,Maintenance,Utility,Gardener,Gardener/Utility,Team Leader',
            'email' => ['required', 'email', 'unique:users,email', 'regex:/^[A-Za-z0-9._%+-]+@cca\.edu\.ph$/'],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $allowedRoles = auth()->user()->role->name === 'superadmin' ? ['user', 'personnel', 'admin'] : ['user', 'personnel'];
        $role = Role::whereIn('name', $allowedRoles)->whereKey($validated['role_id'])->firstOrFail();
// Require a designation for personnel accounts.
if ($role->name === 'personnel' && empty($validated['designation'])) {
    return back()
        ->withInput()
        ->withErrors([
            'designation' => 'Please select the personnel designation.',
        ]);
}

// Append designation to the last name without changing the database structure.
$displayLastName = $validated['lname'];

if ($role->name === 'personnel') {
    $displayLastName .= ' (' . $validated['designation'] . ')';
}

// Ensure the combined value fits the existing last-name limit.
if (mb_strlen($displayLastName) > 50) {
    return back()
        ->withInput()
        ->withErrors([
            'designation' => 'The last name and designation together must not exceed 50 characters.',
        ]);
}


        User::create([
            'fname' => $validated['fname'],
            'lname' => $displayLastName,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'status' => 'approved',
        ]);

        $destination = auth()->user()->role->name === 'superadmin' ? 'acc_superadmin' : 'accountsmanagement';
        return redirect()->route($destination)->with('success', 'Account created successfully.');
    }

    public function accManagement()
    {
        $users = User::with('role')->latest()->get(); // get all users with roles
        return view('admin.accounts', compact('users'));
    }

    public function editAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('admin.edit', compact('user', 'roles'));
    }

    public function updateAccount(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // validate input
        $validated = $request->validate([
            'fname' => 'required|string|max:50',
            'lname' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|min:6',
        ]);

        // update basic fields
        $user->fname = $validated['fname'];
        $user->lname = $validated['lname'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        // update password ONLY if filled
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('accountsmanagement')
            ->with('success', 'User updated successfully!');
    }
    public function destroyAccount($id)
    {
        $user = User::findOrFail($id);

        if ((int) auth()->id() === (int) $user->id) {
            return redirect()->back()->with('error', 'You cannot archive your own account.');
        }
        if (!in_array($user->role->name ?? '', ['user', 'personnel'], true)) {
            return redirect()->back()->with('error', 'Administrators can archive User and Personnel accounts only.');
        }

        // Soft deletion archives the account while preserving historical requests and logs.
        $user->delete();

        return redirect()->back()->with(
            'success',
            'Account archived successfully.'
        );
    }

    public function maintenanceRequest(Request $request)
    {
        $statuses = ['pending', 'approved', 'in_progress', 'completed', 'rejected'];
        $search = trim((string) $request->input('search', ''));
        $category = $request->input('category');
        $requests = [];
$perPage = (int) $request->input('per_page', 5);
        // Keep the search predicate identical for every status tab so a ticket can
        // be found regardless of its current workflow stage.
        $applyFilters = function ($query) use ($search, $category) {
            if ($search !== '') {
                $like = '%' . addcslashes($search, '\\%_') . '%';

                $query->where(function ($q) use ($search, $like) {
                    $q->where('ticket_number', 'like', $like)
                        ->orWhere('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('location', 'like', $like)
                        ->orWhere('category', 'like', $like)
                        ->orWhere('priority', 'like', $like)
                        ->orWhereHas('user', function ($userQuery) use ($like) {
                            $userQuery->where('fname', 'like', $like)
                                ->orWhere('lname', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        })
                        ->orWhereHas('assignedPersonnel', function ($personnelQuery) use ($like) {
                            $personnelQuery->where('fname', 'like', $like)
                                ->orWhere('lname', 'like', $like)
                                ->orWhere('email', 'like', $like);
                        });
                });
            }

            if ($category) {
                $query->where('category', $category);
            }

            return $query;
        };

        foreach ($statuses as $status) {
            $query = MaintenanceRequest::with([
                'user',
                'assignedPersonnel',
                'logs.updatedBy',
                'inventoryUsages.item',
            ])->where('status', $status);

            $requests[$status] = $applyFilters($query)
    ->latest()
    ->paginate($perPage, ['*'], $status . '_page')
    ->appends($request->except('tab'))
    ->appends(['tab' => $status]);
        }

        // A ticket search should open the tab that owns the matching request.
        // Exact ticket number wins; otherwise use the most recent matching result.
        $activeTab = $request->input('tab', 'pending');
        if (!in_array($activeTab, $statuses, true)) {
            $activeTab = 'pending';
        }

        if ($search !== '') {
            $matchQuery = $applyFilters(MaintenanceRequest::query());

            $match = (clone $matchQuery)
                ->where('ticket_number', $search)
                ->first();

            if (!$match) {
                $match = $matchQuery->latest()->first();
            }

            if ($match) {
                $activeTab = $match->status;
            }
        } elseif ($category) {
            // If a category filter leaves the selected tab empty, open the first
            // tab that contains matching requests.
            if (($requests[$activeTab] ?? null)?->total() === 0) {
                foreach ($statuses as $status) {
                    if ($requests[$status]->total() > 0) {
                        $activeTab = $status;
                        break;
                    }
                }
            }
        }

        return view('admin.maintenance_request', compact('requests', 'activeTab', 'perPage'));
    }

    public function viewRequest($id)
    {
        $requestData = MaintenanceRequest::with([
            'images',
            'user',
            'assignedPersonnel',
            'assignedBy',
            'assignments.personnel',
            'assignments.assignedBy',
            'logs.updatedBy',
            'inventoryUsages.item',
            'inventoryUsages.user',
        ])->findOrFail($id);

        $personnels = User::whereHas('role', function ($query) {
            $query->where('name', 'personnel');
        })->orderBy('fname')->orderBy('lname')->get();

        return view('admin.view_request', compact('requestData', 'personnels'));
    }

    public function updateRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject,save',
            'priority' => 'required|in:low,medium,high',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'remarks' => 'nullable|string|max:5000',
        ]);

        $remarks = trim((string) ($validated['remarks'] ?? ''));
        $message = 'Request update saved successfully.';

        DB::transaction(function () use ($id, $validated, $remarks, &$message) {
            $maintenance = MaintenanceRequest::with('user')
                ->lockForUpdate()
                ->findOrFail($id);

            $oldStatus = $maintenance->status;
            $oldAssignedTo = $maintenance->assigned_to;
            $oldPriority = $maintenance->priority;
            $newStatus = $oldStatus;
            $newAssignedTo = $oldAssignedTo;

            if ($validated['action'] === 'approve') {
                if ($oldStatus !== 'pending') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'action' => 'Only pending requests can be approved.',
                    ]);
                }

                $newStatus = 'approved';
                $newAssignedTo = $validated['assigned_to'] ?? null;

                if (!$newAssignedTo) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'assigned_to' => 'Select maintenance personnel before approving this request.',
                    ]);
                }
            } elseif ($validated['action'] === 'reject') {
                if ($oldStatus !== 'pending') {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'action' => 'Only pending requests can be rejected.',
                    ]);
                }

                $newStatus = 'rejected';
                $newAssignedTo = null;
            } else {
                if ($oldStatus === 'pending' && !empty($validated['assigned_to'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'assigned_to' => 'Approve the request before assigning personnel.',
                    ]);
                }

                if ($oldStatus === 'completed') {
                    // Completed work is immutable; only priority/remarks can be updated.
                    $newAssignedTo = $oldAssignedTo;
                } elseif ($oldStatus === 'rejected') {
                    $newAssignedTo = null;
                } elseif (in_array($oldStatus, ['approved', 'in_progress'], true)
                    && !empty($validated['assigned_to'])) {
                    // Reassignment is allowed while work is active; preserve status.
                    $newAssignedTo = (int) $validated['assigned_to'];
                }
            }

            if ($newAssignedTo) {
                $isPersonnel = User::whereKey($newAssignedTo)
                    ->whereHas('role', fn ($q) => $q->where('name', 'personnel'))
                    ->exists();

                if (!$isPersonnel) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'assigned_to' => 'The selected account is not an active maintenance personnel account.',
                    ]);
                }
            }

            if ($oldStatus === 'completed' && $newAssignedTo !== $oldAssignedTo) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'assigned_to' => 'Completed requests cannot be reassigned.',
                ]);
            }

            $assignmentChanged = (string) $oldAssignedTo !== (string) $newAssignedTo;
            $statusChanged = $oldStatus !== $newStatus;
            $priorityChanged = $oldPriority !== $validated['priority'];

            $maintenance->status = $newStatus;
            $maintenance->priority = $validated['priority'];
            $maintenance->assigned_to = $newAssignedTo;

            if ($assignmentChanged) {
                $maintenance->assigned_by = $newAssignedTo ? Auth::id() : null;
                $maintenance->assigned_at = $newAssignedTo ? now() : null;
            }

            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                $maintenance->date_completed = now();
            }

            $maintenance->save();

            // Append assignment history. Never delete prior assignment records:
            // maintenance_requests.assigned_to remains the source of current truth.
            if ($assignmentChanged && $newAssignedTo) {
                RequestAssignment::create([
                    'request_id' => $maintenance->id,
                    'personnel_id' => $newAssignedTo,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                ]);
            }

            if ($statusChanged) {
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => $newStatus,
                    'action' => 'status_update',
                    // Keep the status event descriptive. The user's remark is
                    // logged once below as a separate comment event.
                    'remarks' => $newStatus === 'approved'
                        ? 'Request approved.'
                        : 'Request rejected.',
                    'updated_by' => Auth::id(),
                ]);
            }

            if ($assignmentChanged) {
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => $newStatus,
                    'action' => 'assignment',
                    // Assignment history records the assignment event, not the
                    // free-text remark (which is logged once as a comment below).
                    'remarks' => $newAssignedTo
                        ? ($oldAssignedTo ? 'Maintenance request reassigned to another personnel.' : 'Maintenance personnel assigned.')
                        : 'Personnel assignment removed.',
                    'updated_by' => Auth::id(),
                ]);
            }

            if ($priorityChanged) {
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => $newStatus,
                    'action' => 'priority_update',
                    'remarks' => "Priority changed from {$oldPriority} to {$validated['priority']}.",
                    'updated_by' => Auth::id(),
                ]);
            }

            if ($remarks !== '') {
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => $newStatus,
                    'action' => 'comment',
                    'remarks' => $remarks,
                    'updated_by' => Auth::id(),
                ]);
            }

            if ($assignmentChanged && $newAssignedTo) {
                $personnel = User::find($newAssignedTo);

                if ($personnel) {
                    $personnel->notify(new \App\Notifications\SystemNotification(
                        'New maintenance assignment',
                        "You have been assigned {$maintenance->ticket_number}: {$maintenance->title}.",
                        'assignment',
                        route('viewRequest.personnel', $maintenance->id)
                    ));
                }

                if ($oldAssignedTo) {
                    $oldPersonnel = User::find($oldAssignedTo);
                    if ($oldPersonnel) {
                        $oldPersonnel->notify(new \App\Notifications\SystemNotification(
                            'Maintenance request reassigned',
                            "{$maintenance->ticket_number}: {$maintenance->title} has been reassigned to another personnel.",
                            'assignment',
                            route('maintenanceRequest.personnel')
                        ));
                    }
                }
            }

            if ($remarks !== '' && $newAssignedTo && !$assignmentChanged) {
                $personnel = User::find($newAssignedTo);
                if ($personnel) {
                    $personnel->notify(new \App\Notifications\SystemNotification(
                        'Admin added remarks',
                        "The admin added remarks to this request: {$maintenance->title} ({$maintenance->ticket_number}).",
                        'request',
                        route('viewRequest.personnel', $maintenance->id)
                    ));
                }
            }

            // Requesters are notified only when their request is approved.
            if ($maintenance->user && $oldStatus === 'pending' && $newStatus === 'approved') {
                $maintenance->user->notify(new \App\Notifications\SystemNotification(
                    'Request approved',
                    "Your request {$maintenance->ticket_number} was approved.",
                    'request',
                    route('viewRequest.user', $maintenance->id)
                ));
            }

            if (!$statusChanged && !$assignmentChanged && !$priorityChanged && $remarks === '') {
                $message = 'No changes were needed; the request is already up to date.';
            } elseif ($assignmentChanged && $oldAssignedTo && $newAssignedTo) {
                $message = 'Request reassigned successfully. The previous personnel no longer has it in their active list.';
            } elseif ($statusChanged && $newStatus === 'approved') {
                $message = 'Request approved and personnel assigned successfully.';
            } elseif ($statusChanged && $newStatus === 'rejected') {
                $message = 'Request rejected successfully.';
            } else {
                $message = 'Request update saved successfully.';
            }
        });

        return redirect()
            ->route('viewRequest.admin', $id)
            ->with('success', $message);
    }

    public function viewHistoryRequest($id)
    {
        // Kept for old links/bookmarks. History is now part of the single request module.
        return redirect()->route('viewRequest.admin', $id);
    }

    public function logs(Request $request)
    {
        // Kept for old links/bookmarks. Ticket logs are now embedded in Maintenance Requests.
        return redirect()->route('maintenanceRequest.admin', $request->query());
    }

    public function inventory(Request $request)
    {
        $query = InventoryItem::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {

            if ($request->status === 'Out of Stock') {
                $query->where('quantity', 0);
            } elseif ($request->status === 'Low Stock') {
                $query->whereColumn('quantity', '<=', 'minimum_stock')
                    ->where('quantity', '>', 0);
            } elseif ($request->status === 'In Stock') {
                $query->whereColumn('quantity', '>', 'minimum_stock');
            }
        }

        // Pagination settings
$perPage = (int) $request->input('per_page', 5);

$inventoryItems = $query
    ->orderBy('name')
    ->paginate($perPage)
    ->withQueryString();

        // Dashboard statistics
        $total_items = InventoryItem::count();

        $low_stocks = InventoryItem::whereColumn(
            'quantity',
            '<=',
            'minimum_stock'
        )->where(
                'quantity',
                '>',
                0
            )->count();

        $out_of_stock = InventoryItem::where(
            'quantity',
            0
        )->count();

        $estimated_value = InventoryItem::sum(
            DB::raw('quantity * unit_cost')
        );

        return view('admin.inventory', compact(
            'inventoryItems',
            'total_items',
            'low_stocks',
            'out_of_stock',
            'estimated_value',
            'perPage'
        ));
    }

    public function save_inventory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $inventory = InventoryItem::create([
            'name' => $request->name,
            'category' => $request->category,
            'unit' => $request->unit,
            'quantity' => $request->quantity,
            'minimum_stock' => $request->minimum_stock,
            'unit_cost' => $request->unit_cost,
            'location' => $request->location,
            'description' => $request->description,
        ]);

        $this->notifyInventoryIssue($inventory);

        return redirect()->back()->with('success', 'Inventory item added successfully.');
    }

    public function edit_inventory(Request $request, $id)
    {
        $materials = InventoryItem::findOrFail($id);

        return view('admin.partials.edit-inventory', compact('materials'));
    }

    public function update_inventory(Request $request, $id)
    {
        $inventory = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $previousQuantity = $inventory->quantity;
        $inventory->update($validated);
        $this->notifyInventoryIssue($inventory, $previousQuantity);

        return redirect()->route('inventory');
    }


    public function delete_inventory(Request $request, $id)
    {
        $inventory = InventoryItem::findOrFail($id);

        $inventory->delete();
        return redirect()->route('inventory');
    }


    private function notifyInventoryIssue(InventoryItem $inventory, ?int $previousQuantity = null): void
    {
        if ($inventory->quantity > $inventory->minimum_stock) {
            return;
        }

        // Avoid repeatedly notifying admins while an item remains in the same alert state.
        if ($previousQuantity !== null) {
            $crossedLow = $previousQuantity > $inventory->minimum_stock
                && $inventory->quantity <= $inventory->minimum_stock;

            $crossedOut = $previousQuantity > 0 && $inventory->quantity <= 0;

            if (!$crossedLow && !$crossedOut) {
                return;
            }
        }

        $severity = $inventory->quantity <= 0 ? 'out of stock' : 'low stock';

        User::whereHas('role', fn ($query) => $query->where('name', 'admin'))
            ->get()
            ->each(function ($admin) use ($inventory, $severity) {
                $admin->notify(new \App\Notifications\SystemNotification(
                    'Inventory alert',
                    "{$inventory->name} is {$severity} ({$inventory->quantity} {$inventory->unit} remaining).",
                    'inventory',
                    route('inventory')
                ));
            });
    }

}
