<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LockerItem;
use App\Models\Permission;
use App\Models\RolePermission;

class LockerItemController extends Controller
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
     * Display a listing of locker items
     */
    public function index()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $lockers = LockerItem::orderBy('id', 'desc')->get();
        return view('admin.locker_items.index', compact('lockers'));
    }

    /**
     * Show form to create new locker
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.locker_items.create');
    }

    /**
     * Store new locker
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:available,occupied',
        ]);

        LockerItem::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('locker_items.index')
            ->with('flash_success', 'Locker created successfully.');
    }

    /**
     * Show form to edit locker
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $locker = LockerItem::findOrFail($id);
        return view('admin.locker_items.edit', compact('locker'));
    }

    /**
     * Update locker
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $locker = LockerItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'status' => 'required|in:available,occupied',
        ]);

        $locker->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()->route('locker_items.index')
            ->with('flash_success', 'Locker updated successfully.');
    }

    /**
     * Delete locker
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'destroy')) {
            abort(403, 'Unauthorized access');
        }

        $locker = LockerItem::findOrFail($id);
        $locker->delete();

        return redirect()->route('locker_items.index')
            ->with('flash_success', 'Locker deleted successfully.');
    }
}
