<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceRequest;
use App\Models\RequestLog;
class SuperAdminController extends Controller
{
    public function superadminDashboard()
{
    $total = User::count();

    $supe_admin = User::whereHas('role', function ($query) {
        $query->where('name', 'superadmin');
    })->count();

    $total -= $supe_admin;

    $admin = User::whereHas('role', function ($query) {
        $query->where('name', 'admin');
    })->count();

    $personnel = User::whereHas('role', function ($query) {
        $query->where('name', 'personnel');
    })->count();

    $user = User::whereHas('role', function ($query) {
        $query->where('name', 'user');
    })->count();

    return view('pages.dashboard.index', compact(
        'total',
        'admin',
        'personnel',
        'user',
        'supe_admin'
    ));
}

    public function accManagement()
{
    $users = User::with('role')
        ->whereHas('role', function ($query) {
            $query->where('name', '!=', 'superadmin');
        })
        ->latest()
        ->get();

    return view('superadmin.accounts', compact('users'));
}

    public function createAccount()
    {
        $roles = Role::whereIn('name', ['user', 'personnel', 'admin'])->orderBy('name')->get();
        return view('admin.create-account', compact('roles'));
    }

    public function edit_account(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::whereIn('name', [
    'user',
    'personnel',
    'admin'
])->orderBy('name')->get();

        return view('superadmin.edit-accounts', compact('user', 'roles'));
    }

   

public function update_account(Request $request, $id)
{
    // Only authorized super administrators can update accounts.
    abort_unless(
        auth()->check() &&
        auth()->user()->role?->name === 'superadmin',
        403
    );

    $request->merge([
        'email' => mb_strtolower(trim((string) $request->input('email'))),
    ]);

    $validated = $request->validate([
        'fname' => ['required', 'string', 'max:50'],
        'lname' => ['required', 'string', 'max:50'],

        'email' => [
            'required',
            'email:rfc',
            'ends_with:@cca.edu.ph',
            'max:255',
            \Illuminate\Validation\Rule::unique('users', 'email')->ignore($id),
        ],

        'role_id' => [
            'required',
            \Illuminate\Validation\Rule::exists('roles', 'id')
                ->whereIn('name', ['user', 'personnel', 'admin']),
        ],

        'designation' => [
            'nullable',
            'in:CDSO Head,Utility Supervisor,Supply Officer,Office Secretary,Maintenance,Utility,Gardener,Gardener/Utility,Team Leader',
        ],

        'password' => [
            'nullable',
            'confirmed',
            \Illuminate\Validation\Rules\Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols(),
        ],
    ]);

    $selectedRole = Role::findOrFail($validated['role_id']);

    $lastName = trim($validated['lname']);

    // Append designation for personnel accounts.
    if ($selectedRole->name === 'personnel') {

        if (empty($validated['designation'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'designation' => 'Please select a personnel designation.',
                ]);
        }

        $lastName .= ' (' . $validated['designation'] . ')';
    }

    // Ensure combined last name fits the database limit.
    if (mb_strlen($lastName) > 50) {
        return back()
            ->withInput()
            ->withErrors([
                'lname' => 'Last name and designation must not exceed 50 characters combined.',
            ]);
    }

    DB::transaction(function () use ($validated, $id, $selectedRole, $lastName) {

        $user = User::with('role')
            ->lockForUpdate()
            ->findOrFail($id);

        // Protect superadmin accounts.
        abort_if(
            $user->role?->name === 'superadmin',
            403,
            'Super Admin accounts cannot be modified here.'
        );

        $oldRole = $user->role?->name;

        // Update account information.
        $user->fname = $validated['fname'];
        $user->lname = $lastName;
        $user->email = $validated['email'];
        $user->role_id = $selectedRole->id;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // If personnel becomes a non-personnel account,
        // remove active assignments and return requests to Pending.
        if ($oldRole === 'personnel' && $selectedRole->name !== 'personnel') {

            $activeRequests = MaintenanceRequest::where('assigned_to', $user->id)
                ->whereIn('status', ['approved', 'in_progress'])
                ->lockForUpdate()
                ->get();

            foreach ($activeRequests as $maintenance) {

                $oldStatus = $maintenance->status;

                $maintenance->assigned_to = null;
                $maintenance->assigned_by = null;
                $maintenance->assigned_at = null;
                $maintenance->status = 'pending';

                $maintenance->save();

                // Record status change.
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => 'pending',
                    'action' => 'status_update',
                    'remarks' => 'Request returned to Pending because the assigned personnel account was changed to a non-personnel role.',
                    'updated_by' => auth()->id(),
                ]);

                // Record assignment removal.
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => 'pending',
                    'action' => 'assignment',
                    'remarks' => "Personnel assignment removed. Previous personnel: {$user->fname} {$user->lname}. Previous status: " . str_replace('_', ' ', $oldStatus) . ".",
                    'updated_by' => auth()->id(),
                ]);

                // Preserve RequestAssignment history.
            }
        }
    });

    return redirect()
        ->route('acc_superadmin')
        ->with('success', 'Account updated successfully. Any active assignments affected by the role change have been returned to Pending.');
}


    

public function delete_account($id)
{
    // Only authorized super administrators can archive accounts.
    abort_unless(
        auth()->check() &&
        auth()->user()->role?->name === 'superadmin',
        403
    );

    DB::transaction(function () use ($id) {

        $user = User::with('role')
            ->lockForUpdate()
            ->findOrFail($id);

        // Prevent archiving your own account.
        abort_if(
            auth()->id() == $user->id,
            403,
            'You cannot archive your own account.'
        );

        // Protect all superadmin accounts.
        abort_if(
            $user->role?->name === 'superadmin',
            403,
            'Super Admin accounts cannot be archived.'
        );

        // Handle active assignments if the account belongs to personnel.
        if ($user->role?->name === 'personnel') {

            $activeRequests = MaintenanceRequest::where('assigned_to', $user->id)
                ->whereIn('status', ['approved', 'in_progress'])
                ->lockForUpdate()
                ->get();

            foreach ($activeRequests as $maintenance) {

                $oldStatus = $maintenance->status;

                // Remove assignment and return request to Pending.
                $maintenance->assigned_to = null;
                $maintenance->assigned_by = null;
                $maintenance->assigned_at = null;
                $maintenance->status = 'pending';

                $maintenance->save();

                // Record status change.
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => 'pending',
                    'action' => 'status_update',
                    'remarks' => 'Request returned to Pending because the assigned personnel account was archived.',
                    'updated_by' => auth()->id(),
                ]);

                // Record assignment removal.
                RequestLog::create([
                    'request_id' => $maintenance->id,
                    'status' => 'pending',
                    'action' => 'assignment',
                    'remarks' => "Personnel assignment removed due to account archiving. Previous personnel: {$user->fname} {$user->lname}. Previous status: " . str_replace('_', ' ', $oldStatus) . ".",
                    'updated_by' => auth()->id(),
                ]);
            }
        }

        // Preserve completed requests and their original personnel attribution.
        // Laravel soft delete keeps the user record in the database.
        $user->delete();
    });

    return redirect()
        ->route('acc_superadmin')
        ->with('success', 'Account archived successfully. Active maintenance assignments have been returned to Pending.');
}


}
