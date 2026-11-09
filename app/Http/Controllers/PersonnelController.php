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
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryUsage;
class PersonnelController extends Controller
{
    public function personnelDashboard()
    {
        $userId = Auth::id();

        $assigned = MaintenanceRequest::where('assigned_to', $userId);
        $approved = (clone $assigned)->where('status', 'approved')->count();
        $inProgress = (clone $assigned)->where('status', 'in_progress')->count();
        $completedToday = (clone $assigned)
            ->where('status', 'completed')
            ->whereDate('date_completed', today())
            ->count();
        $activeCount = $approved + $inProgress;
        $highPriorityCount = (clone $assigned)
            ->whereIn('status', ['approved', 'in_progress'])
            ->where('priority', 'high')
            ->count();

        // Keep every active assignment visible. Personnel may have multiple
        // approved requests queued, but only one request may be In Progress.
        $workQueue = MaintenanceRequest::with('user')
            ->where('assigned_to', $userId)
            ->whereIn('status', ['approved', 'in_progress'])
            ->orderByRaw("CASE WHEN status = 'in_progress' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN priority = 'high' THEN 0 WHEN priority = 'medium' THEN 1 ELSE 2 END")
            ->orderBy('created_at')
            ->get();

        $currentWork = $workQueue->firstWhere('status', 'in_progress');

        // Recent Updates follows the current task, not every queued or completed request.
        $recentActivity = $currentWork
            ? RequestLog::with(['request', 'updatedBy'])
                ->where('request_id', $currentWork->id)
                ->latest()
                ->limit(10)
                ->get()
            : collect();

        return view('pages.dashboard.index', compact(
            'approved',
            'inProgress',
            'completedToday',
            'activeCount',
            'highPriorityCount',
            'workQueue',
            'currentWork',
            'recentActivity'
        ));
    }


public function maintenanceRequest(Request $request)
{
    $userId = auth()->id();
    $search = trim((string) $request->input('search', ''));

    $query = MaintenanceRequest::with([
        'user',
        'assignedPersonnel',
        'logs.updatedBy',
    ])->where('assigned_to', $userId)
      ->whereIn('status', ['approved', 'in_progress', 'completed']);

    if ($search !== '') {
        $like = '%' . addcslashes($search, '\\%_') . '%';

        $query->where(function ($q) use ($like) {
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
                });
        });
    }

    if ($request->filled('category')) {
        $query->where('category', $request->input('category'));
    }

    $activeTab = $request->input('tab', 'all');

    $validTabs = ['all', 'approved', 'in_progress', 'completed'];

    if (!in_array($activeTab, $validTabs, true)) {
        $activeTab = 'all';
    }

    // Apply status filtering before pagination.
    if ($activeTab === 'all') {
    $query->whereIn('status', ['approved', 'in_progress']);
} else {
    $query->where('status', $activeTab);
}

    // Always display 5 records per page.
    $perPage = 5;

    if ($activeTab === 'all') {
        // Show the current task first, followed by approved tasks waiting in queue.
        $query->orderByRaw("CASE WHEN status = 'in_progress' THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN priority = 'high' THEN 0 WHEN priority = 'medium' THEN 1 ELSE 2 END")
            ->orderBy('created_at');
    } elseif ($activeTab === 'approved') {
        // Prioritize higher-priority tasks in the waiting queue.
        $query->orderByRaw("CASE WHEN priority = 'high' THEN 0 WHEN priority = 'medium' THEN 1 ELSE 2 END")
            ->orderBy('created_at');
    } else {
        // Completed history is shown newest first.
        $query->latest();
    }

    $requests = $query
        ->paginate($perPage)
        ->withQueryString();

    // Get accurate counts independently of pagination.
    $statusCounts = MaintenanceRequest::where('assigned_to', $userId)
        ->whereIn('status', ['approved', 'in_progress', 'completed'])
        ->when($search !== '', function ($q) use ($search) {
            $like = '%' . addcslashes($search, '\\%_') . '%';

            $q->where(function ($subQuery) use ($like) {
                $subQuery->where('ticket_number', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('location', 'like', $like)
                    ->orWhere('category', 'like', $like)
                    ->orWhere('priority', 'like', $like)
                    ->orWhereHas('user', function ($userQuery) use ($like) {
                        $userQuery->where('fname', 'like', $like)
                            ->orWhere('lname', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    });
            });
        })
        ->when($request->filled('category'), function ($q) use ($request) {
            $q->where('category', $request->input('category'));
        })
        ->selectRaw("
            COUNT(*) as all_count,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count
        ")
        ->first();

    $statusCounts = [
    'all' => (int) $statusCounts->approved_count
        + (int) $statusCounts->in_progress_count,
    'approved' => (int) $statusCounts->approved_count,
    'in_progress' => (int) $statusCounts->in_progress_count,
    'completed' => (int) $statusCounts->completed_count,
];

    return view('personnel.maintenance_request', compact(
        'requests',
        'activeTab',
        'perPage',
        'statusCounts'
    ));
}


public function viewRequest($id)
{
    $requestData = MaintenanceRequest::with([
        'images',
        'user',
        'assignedPersonnel',
        'logs.updatedBy',
        'inventoryUsages.item',
        'inventoryUsages.user',
    ])
        ->where('assigned_to', auth()->id())
        ->findOrFail($id);

    $inventoryItems = InventoryItem::orderBy('name')->get();

    $currentInProgress = MaintenanceRequest::where('assigned_to', auth()->id())
        ->where('status', 'in_progress')
        ->where('id', '!=', $requestData->id)
        ->first();

    $hasOtherInProgress = $currentInProgress !== null;

    return view('personnel.view_request_personnel', compact(
        'requestData',
        'inventoryItems',
        'hasOtherInProgress',
        'currentInProgress'
    ));
}

public function updateRequest(Request $request, $id)
{
    $maintenance = MaintenanceRequest::with('user')
        ->where('assigned_to', auth()->id())
        ->findOrFail($id);

    $validated = $request->validate([
        'status' => 'nullable|in:approved,in_progress,completed',
        'remarks' => 'nullable|string|max:5000',
        'item_id' => 'nullable|array',
        'item_id.*' => 'required|integer|distinct|exists:inventory_items,id',
        'quantity_used' => 'nullable|array',
        'quantity_used.*' => 'required|integer|min:1',
        'no_materials_used' => 'nullable|boolean',
    ]);

    $oldStatus = $maintenance->status;
    $newStatus = $validated['status'] ?: $oldStatus;
    $remarks = trim($validated['remarks'] ?? '');
    $isCompleting = $newStatus === 'completed' && $oldStatus !== 'completed';
    $itemIds = array_values($validated['item_id'] ?? []);
    $noMaterialsUsed = (bool) ($validated['no_materials_used'] ?? false);

    if ($newStatus === 'approved' && $oldStatus !== 'approved') {
        return back()->withInput()->with('error', 'A request cannot be moved back to Approved.');
    }

    if ($newStatus === 'in_progress' && $oldStatus !== 'approved' && $oldStatus !== 'in_progress') {
        return back()->withInput()->with('error', 'Only approved requests can be started.');
    }

    if ($newStatus === 'completed' && $oldStatus !== 'in_progress' && $oldStatus !== 'completed') {
        return back()->withInput()->with('error', 'Start the work before marking the request as completed.');
    }

    if ($oldStatus === 'completed' && $newStatus !== 'completed') {
        return back()->withInput()->with('error', 'A completed request cannot be reopened from this screen.');
    }

    // Materials are captured only when an in-progress job is being completed.
    if (!$isCompleting && ($itemIds || $noMaterialsUsed)) {
        return back()->withInput()->with('error', 'Materials can only be recorded when completing an in-progress request.');
    }

    // Require an explicit declaration: list the materials used, or confirm none were used.
    if ($isCompleting && !$itemIds && !$noMaterialsUsed) {
        return back()
            ->withInput()
            ->withErrors(['materials' => 'Please add the materials used, or select “No materials used” if the job required none.']);
    }

    if ($isCompleting && $itemIds && $noMaterialsUsed) {
        return back()
            ->withInput()
            ->withErrors(['materials' => 'Choose either the materials used or “No materials used”, not both.']);
    }

    if ($isCompleting && $itemIds && count($itemIds) !== count($validated['quantity_used'] ?? [])) {
        return back()
            ->withInput()
            ->withErrors(['materials' => 'Enter a quantity for every selected material.']);
    }

    DB::transaction(function () use (
        $maintenance,
        $oldStatus,
        $newStatus,
        $remarks,
        $validated,
        $isCompleting,
        $itemIds,
        $noMaterialsUsed
    ) {
        // Serialize start attempts for this personnel account. This prevents
        // two browser tabs/requests from starting two jobs at the same time.
        User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();

        $lockedMaintenance = MaintenanceRequest::whereKey($maintenance->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ((int) $lockedMaintenance->assigned_to !== (int) Auth::id()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => 'This request is no longer assigned to your account. Refresh the page.',
            ]);
        }

        if ($lockedMaintenance->status !== $oldStatus) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status' => 'This request has changed since you opened it. Refresh the page before updating it.',
            ]);
        }

        if ($newStatus === 'in_progress' && $oldStatus === 'approved') {
            $alreadyWorking = MaintenanceRequest::where('assigned_to', Auth::id())
                ->where('status', 'in_progress')
                ->where('id', '!=', $lockedMaintenance->id)
                ->exists();

            if ($alreadyWorking) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'You already have a task in progress. Complete your current task before starting another assigned request.',
                ]);
            }
        }

        // Use the locked record for all changes made in this transaction.
        $maintenance = $lockedMaintenance;

        $statusChanged = $oldStatus !== $newStatus;
        $logRemarks = $remarks;

        if ($isCompleting && $noMaterialsUsed) {
            $logRemarks = trim($logRemarks . "\nMaterials used: None.");
        } elseif ($isCompleting && $itemIds) {
            $itemsById = InventoryItem::whereIn('id', $itemIds)->get()->keyBy('id');
            $summary = [];

            foreach ($itemIds as $index => $itemId) {
                $item = $itemsById->get($itemId);
                if ($item) {
                    $quantity = (int) ($validated['quantity_used'][$index] ?? 0);
                    $summary[] = "{$item->name} — {$quantity} {$item->unit}";
                }
            }

            if ($summary) {
                $logRemarks = trim($logRemarks . "\nMaterials used: " . implode(', ', $summary) . '.');
            }
        }

        $maintenance->status = $newStatus;

        if ($isCompleting) {
            $maintenance->date_completed = now();
        }

        $maintenance->save();

        $logAction = $statusChanged ? 'status_update' : 'comment';

        if ($statusChanged || $remarks || ($isCompleting && ($itemIds || $noMaterialsUsed))) {
            RequestLog::create([
                'request_id' => $maintenance->id,
                'status' => $newStatus,
                'action' => $logAction,
                'remarks' => $logRemarks ?: (
                    $newStatus === 'in_progress'
                        ? 'Work started by assigned personnel.'
                        : ($newStatus === 'completed' ? 'Maintenance work completed.' : 'Request updated.')
                ),
                'updated_by' => Auth::id(),
            ]);
        }

        // Deduct inventory and record usage only during the first transition to Completed.
        if ($isCompleting && $itemIds) {
            foreach ($itemIds as $index => $itemId) {
                $quantityUsed = (int) ($validated['quantity_used'][$index] ?? 0);

                $item = InventoryItem::whereKey($itemId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($item->quantity < $quantityUsed) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'materials' => "Not enough {$item->name} available. Only {$item->quantity} {$item->unit} remaining.",
                    ]);
                }

                $previousQuantity = $item->quantity;
                $item->decrement('quantity', $quantityUsed);

                InventoryUsage::create([
                    'request_id' => $maintenance->id,
                    'item_id' => $item->id,
                    'quantity_used' => $quantityUsed,
                    'used_by' => Auth::id(),
                    'used_at' => now(),
                ]);

                $item->refresh();

                $crossedLow = $previousQuantity > $item->minimum_stock
                    && $item->quantity <= $item->minimum_stock;
                $crossedOut = $previousQuantity > 0 && $item->quantity <= 0;

                if ($crossedLow || $crossedOut) {
                    $severity = $item->quantity <= 0 ? 'out of stock' : 'low stock';

                    User::whereHas('role', fn ($q) => $q->where('name', 'admin'))
                        ->get()
                        ->each(function ($admin) use ($item, $severity) {
                            $admin->notify(new \App\Notifications\SystemNotification(
                                'Inventory alert',
                                "{$item->name} is {$severity} ({$item->quantity} {$item->unit} remaining).",
                                'inventory',
                                route('inventory')
                            ));
                        });
                }
            }
        }

        // Every personnel update is visible to admins.
        User::whereHas('role', fn ($q) => $q->where('name', 'admin'))
            ->get()
            ->each(function ($admin) use ($maintenance) {
                $admin->notify(new \App\Notifications\SystemNotification(
                    'Maintenance request updated',
                    "{$maintenance->ticket_number} was updated by maintenance personnel.",
                    'request',
                    route('viewRequest.admin', $maintenance->id)
                ));
            });

        // Notify requesters only when status advances through the workflow.
        if (
            $maintenance->user &&
            (
                ($oldStatus === 'approved' && $newStatus === 'in_progress') ||
                ($oldStatus === 'in_progress' && $newStatus === 'completed')
            )
        ) {
            $statusLabel = $newStatus === 'in_progress' ? 'In Progress' : 'Completed';

            $maintenance->user->notify(new \App\Notifications\SystemNotification(
                "Request {$statusLabel}",
                "Your request {$maintenance->ticket_number} is now {$statusLabel}.",
                'request',
                route('viewRequest.user', $maintenance->id)
            ));
        }
    });

    return redirect()
        ->route('viewRequest.personnel', $maintenance->id)
        ->with('success', 'Request update saved successfully.');
}
}
