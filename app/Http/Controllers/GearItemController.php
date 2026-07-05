<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GearItem;
use App\Models\Permission;
use App\Models\RolePermission;

class GearItemController extends Controller
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
     * Display a listing of gear items
     */
    public function index()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $gears = GearItem::orderBy('id', 'desc')->get();
        return view('admin.gear_items.index', compact('gears'));
    }

    /**
     * Show form to create new gear
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        return view('admin.gear_items.create');
    }

    /**
     * Store new gear
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'total_stock' => 'required|integer|min:0',
        ]);

        GearItem::create([
            'name' => $request->name,
            'total_stock' => $request->total_stock,
            'available_stock' => $request->total_stock,
        ]);

        return redirect()->route('gear_items.index')
            ->with('flash_success', 'Gear item created successfully.');
    }

    /**
     * Show form to edit gear
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $gear = GearItem::findOrFail($id);
        return view('admin.gear_items.edit', compact('gear'));
    }

    /**
     * Update gear
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $gear = GearItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'total_stock' => 'required|integer|min:0',
        ]);

        // Calculate difference for available stock
        $stockDiff = $request->total_stock - $gear->total_stock;
        $newAvailable = max(0, $gear->available_stock + $stockDiff);

        $gear->update([
            'name' => $request->name,
            'total_stock' => $request->total_stock,
            'available_stock' => $newAvailable,
        ]);

        return redirect()->route('gear_items.index')
            ->with('flash_success', 'Gear item updated successfully.');
    }

    /**
     * Delete gear
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'destroy')) {
            abort(403, 'Unauthorized access');
        }

        $gear = GearItem::findOrFail($id);
        $gear->delete();

        return redirect()->route('gear_items.index')
            ->with('flash_success', 'Gear item deleted successfully.');
    }
}
