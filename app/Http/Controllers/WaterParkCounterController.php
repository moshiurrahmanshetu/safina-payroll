<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WaterParkCounter;
use App\Models\User;
use App\Models\Permission;
use App\Models\RolePermission;

class WaterParkCounterController extends Controller
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
     * Display a listing of water park counters
     */
    public function index()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $counters = WaterParkCounter::with('users')->orderBy('id', 'desc')->get();
        return view('admin.water_park_counters.index', compact('counters'));
    }

    /**
     * Show form to create new counter
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $users = User::where('status', 1)->pluck('name', 'id')->toArray();
        return view('admin.water_park_counters.create', compact('users'));
    }

    /**
     * Store new counter
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $counter = WaterParkCounter::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Assign users if provided
        if ($request->has('users')) {
            $counter->users()->sync($request->users);
        }

        return redirect()->route('water_park_counters.index')
            ->with('flash_success', 'Water Park Counter created successfully.');
    }

    /**
     * Show form to edit counter
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $counter = WaterParkCounter::with('users')->findOrFail($id);
        $users = User::where('status', 1)->pluck('name', 'id')->toArray();
        $assignedUserIds = $counter->users->pluck('id')->toArray();

        return view('admin.water_park_counters.edit', compact('counter', 'users', 'assignedUserIds'));
    }

    /**
     * Update counter
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $counter = WaterParkCounter::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:0,1',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        $counter->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        // Update user assignments
        if ($request->has('users')) {
            $counter->users()->sync($request->users);
        } else {
            $counter->users()->detach();
        }

        return redirect()->route('water_park_counters.index')
            ->with('flash_success', 'Water Park Counter updated successfully.');
    }

    /**
     * Delete counter
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'destroy')) {
            abort(403, 'Unauthorized access');
        }

        $counter = WaterParkCounter::findOrFail($id);

        // Check if counter has tickets
        if ($counter->tickets()->count() > 0) {
            return redirect()->back()
                ->with('flash_error', 'Cannot delete counter. It has associated tickets.');
        }

        $counter->users()->detach();
        $counter->delete();

        return redirect()->route('water_park_counters.index')
            ->with('flash_success', 'Water Park Counter deleted successfully.');
    }
}
