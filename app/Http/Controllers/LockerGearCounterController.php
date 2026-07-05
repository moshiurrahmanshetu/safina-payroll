<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LockerGearCounter;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;

class LockerGearCounterController extends Controller
{
    /**
     * Check if role has specific permission
     */
    private function hasPermission($role_id, $permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        if (!$permission) {
            return false;
        }

        return RolePermission::where('role_id', $role_id)
            ->where('permission_id', $permission->id)
            ->exists();
    }

    /**
     * Display a listing of counters
     */
    public function index()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $counters = LockerGearCounter::withCount(['users','lockerGearTickets'])->orderBy('id', 'desc')->paginate(20);

        return view('admin.locker_gear_counters.index', compact('counters'));
    }

    /**
     * Show the form for creating a new counter
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $users = User::where('status', 1)->get();
        return view('admin.locker_gear_counters.create', compact('users'));
    }

    /**
     * Store a newly created counter
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'users' => 'nullable|array',
        ]);

        $counter = LockerGearCounter::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Assign users to counter
        $counter->users()->sync($request->users ?? []);

        return redirect()->route('locker_gear_counters.index')
            ->with('flash_success', 'Counter created successfully');
    }

    /**
     * Show the form for editing a counter
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $counter = LockerGearCounter::findOrFail($id);
        $users = User::where('status', 1)->get();
        $selectedUsers = $counter->users->pluck('id')->toArray();
        $selectedTickets = $counter->LockerGearTickets->pluck('id')->toArray();
        return view('admin.locker_gear_counters.edit', compact('counter', 'users', 'selectedUsers', 'selectedTickets'));
    }

    /**
     * Update the specified counter
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $counter = LockerGearCounter::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'users' => 'nullable|array',
        ]);

        // Update user assignments
        $counter->users()->sync($request->users ?? []);

        $counter->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('locker_gear_counters.index')
            ->with('flash_success', 'Counter updated successfully');
    }

    /**
     * Show form to assign users to counter
     */
    public function assignUsers($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'assignUsers')) {
            abort(403, 'Unauthorized access');
        }

        $counter = LockerGearCounter::findOrFail($id);
        $assignedUserIds = $counter->users()->pluck('users.id')->toArray();
        
        // Get users who can access locker gear module
        $users = User::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.locker_gear_counters.assign_users', compact('counter', 'users', 'assignedUserIds'));
    }

    /**
     * Update user assignments
     */
    public function updateUsers(Request $request, $id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'assignUsers')) {
            abort(403, 'Unauthorized access');
        }

        $counter = LockerGearCounter::findOrFail($id);

        $request->validate([
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Sync users
        $userIds = $request->user_ids ?? [];
        $counter->users()->sync($userIds);

        return redirect()->route('locker_gear_counters.index')
            ->with('flash_success', 'User assignments updated successfully');
    }
}
