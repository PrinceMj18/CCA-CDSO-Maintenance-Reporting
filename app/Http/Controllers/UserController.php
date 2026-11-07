<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\User;
use App\Models\RequestLog;
use App\Models\MaintenanceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function userDashboard()
    {
        $user = auth()->user();

        if ($user->role->name === 'admin') {
            return redirect('/dashboard/admin');
        }

        // Get only this user's requests
        $requests = MaintenanceRequest::where('user_id', $user->id)->get();

        // Count statuses (example)
        $rejected = $requests->where('status', 'rejected')->count();
        $pending = $requests->where('status', 'pending')->count();
        $inProgress = $requests->where('status', 'in_progress')->count();
        $completed = $requests->where('status', 'completed')->count();
        $approved = $requests->where('status', 'approved')->count();
        $total = $requests->count();

        return view('pages.dashboard.user', compact('rejected', 'requests', 'pending', 'inProgress', 'completed', 'approved', 'total'));
    }

    function showForm()
    {
        return view('forms.form');
    }

    public function requestReport(Request $req)
    {
        $validated = $req->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
'other_location' => 'required_if:location,Other|nullable|string|max:255',
            'category' => 'required|string|max:100',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'title.required' => 'Please enter a title for your maintenance request.',
            'title.max' => 'The title must not exceed 255 characters.',

            'location.required' => 'Please select the location of the issue.',

            'category.required' => 'Please select a maintenance category.',

            'images.max' => 'You can upload a maximum of 5 photos.',
            'images.*.image' => 'Each uploaded file must be an image.',
            'images.*.mimes' => 'Photos must be JPG, JPEG, PNG, or WebP.',
            'images.*.max' => 'Each photo must not exceed 5 MB.',
            
        ]);
// Use the user-entered location when Other is selected.
if ($validated['location'] === 'Other') {
    $validated['location'] = $validated['other_location'];
}
        // Create the maintenance request first
        // so Laravel/MySQL can generate its ID.
        $maintenanceRequest = MaintenanceRequest::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'category' => $validated['category'],
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        // Generate the official CDSO ticket number
        // based on the request ID.
        $ticketNumber = 'CDSO-' .
            now()->format('Y') . '-' .
            str_pad($maintenanceRequest->id, 5, '0', STR_PAD_LEFT);

        // Save the ticket number
        $maintenanceRequest->update([
            'ticket_number' => $ticketNumber,
        ]);

        // Request photos are optional. Store them on Laravel's private local disk.
        foreach ($req->file('images', []) as $image) {
            $path = $image->store('maintenance-requests', 'local');
            MaintenanceImage::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'image_path' => $path,
            ]);
        }

        RequestLog::create([
            'request_id' => $maintenanceRequest->id,
            'status' => 'pending',
            'action' => 'submitted',
            'remarks' => 'Maintenance request submitted by the user.',
            'updated_by' => Auth::id(),
        ]);

        // Notify every admin immediately when a new request is submitted.
        User::whereHas('role', fn($query) => $query->where('name', 'admin'))
            ->get()
            ->each(function ($admin) use ($maintenanceRequest) {
                $admin->notify(new \App\Notifications\SystemNotification(
                    'New maintenance request',
                    "{$maintenanceRequest->ticket_number} was submitted by " .
                    ($maintenanceRequest->user?->fname ?? 'a user') . '.',
                    'request',
                    route('viewRequest.admin', $maintenanceRequest->id)
                ));
            });

        return redirect('dashboard/user')->with(
            'success',
            'Request submitted successfully. Ticket Number: ' . $ticketNumber
        );
    }
    public function trackReq(Request $request)
    {
        $user = auth()->user();

        if ($user->role->name === 'admin') {
            return redirect('/dashboard/admin');
        }

        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', 'all');
        $validStatuses = ['all', 'pending', 'approved', 'in_progress', 'completed', 'rejected'];

        if (!in_array($status, $validStatuses, true)) {
            $status = 'all';
        }

        // An exact ticket-number match takes priority over the selected status filter.
        if ($search !== '') {
            $exactTicket = MaintenanceRequest::where('user_id', $user->id)
                ->where('ticket_number', $search)
                ->first();

            if ($exactTicket) {
                $status = $exactTicket->status;
            }
        }

        $query = MaintenanceRequest::with([
            'logs.updatedBy',
            'assignedPersonnel',
            'inventoryUsages.item',
        ])->where('user_id', $user->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $like = '%' . addcslashes($search, '\\%_') . '%';

            $query->where(function ($q) use ($like) {
                $q->where('ticket_number', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('location', 'like', $like)
                    ->orWhere('category', 'like', $like)
                    ->orWhere('priority', 'like', $like);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $requests = $query->latest()->paginate(5)->withQueryString();

        $allRequests = MaintenanceRequest::where('user_id', $user->id)->get();
        $pending = $allRequests->where('status', 'pending')->count();
        $approved = $allRequests->where('status', 'approved')->count();
        $inProgress = $allRequests->where('status', 'in_progress')->count();
        $completed = $allRequests->where('status', 'completed')->count();
        $total = $allRequests->count();

        $categories = MaintenanceRequest::where('user_id', $user->id)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('user.track_req', compact(
            'requests',
            'pending',
            'approved',
            'inProgress',
            'completed',
            'total',
            'status',
            'categories'
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
        ])->where('user_id', auth()->id())->findOrFail($id);

        return view('user.view_request', compact('requestData'));
    }
}