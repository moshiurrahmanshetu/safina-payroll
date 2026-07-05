<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemPricing;
use App\Models\LockerItem;
use App\Models\GearItem;
use App\Models\Permission;
use App\Models\RolePermission;

class ItemPricingController extends Controller
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
     * Display a listing of item pricings
     */
    public function index()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'index')) {
            abort(403, 'Unauthorized access');
        }

        $pricings = ItemPricing::with('item')->orderBy('id', 'desc')->get();
        return view('admin.item_pricings.index', compact('pricings'));
    }

    /**
     * Show form to create new pricing
     */
    public function create()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        $lockers = LockerItem::where('status', 'available')->pluck('name', 'id')->toArray();
        $gears = GearItem::all()->pluck('name', 'id')->toArray();

        return view('admin.item_pricings.create', compact('lockers', 'gears'));
    }

    /**
     * Store new pricing
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'create')) {
            abort(403, 'Unauthorized access');
        }

        // Validation: item_id required only for gear, not for locker (global pricing)
        $rules = [
            'item_type' => 'required|in:locker,gear',
            'duration_minutes' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'extra_unit_minutes' => 'required|integer|min:1',
            'extra_unit_price' => 'required|numeric|min:0',
        ];

        // item_id required only for gear
        if ($request->item_type === 'gear') {
            $rules['item_id'] = 'required|integer';
        }

        $request->validate($rules);

        // Check for duplicate pricing
        if ($request->item_type === 'locker') {
            // For lockers: check if global pricing already exists (item_id = NULL)
            $exists = ItemPricing::where('item_type', 'locker')
                ->whereNull('item_id')
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('flash_error', 'Global locker pricing already exists. Please edit the existing locker pricing.')
                    ->withInput();
            }
        } else {
            // For gear: check if pricing exists for this specific item
            $exists = ItemPricing::where('item_type', 'gear')
                ->where('item_id', $request->item_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('flash_error', 'Pricing already exists for this gear item.')
                    ->withInput();
            }
        }

        ItemPricing::create([
            'item_type' => $request->item_type,
            'item_id' => $request->item_type === 'locker' ? null : $request->item_id,
            'duration_minutes' => $request->duration_minutes,
            'base_price' => $request->base_price,
            'extra_unit_minutes' => $request->extra_unit_minutes,
            'extra_unit_price' => $request->extra_unit_price,
        ]);

        return redirect()->route('item_pricings.index')
            ->with('flash_success', 'Item pricing created successfully.');
    }

    /**
     * Show form to edit pricing
     */
    public function edit($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $pricing = ItemPricing::findOrFail($id);
        $lockers = LockerItem::where('status', 'available')->pluck('name', 'id')->toArray();
        $gears = GearItem::all()->pluck('name', 'id')->toArray();

        return view('admin.item_pricings.edit', compact('pricing', 'lockers', 'gears'));
    }

    /**
     * Update pricing
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $pricing = ItemPricing::findOrFail($id);

        $request->validate([
            'duration_minutes' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'extra_unit_minutes' => 'required|integer|min:1',
            'extra_unit_price' => 'required|numeric|min:0',
        ]);

        $pricing->update([
            'duration_minutes' => $request->duration_minutes,
            'base_price' => $request->base_price,
            'extra_unit_minutes' => $request->extra_unit_minutes,
            'extra_unit_price' => $request->extra_unit_price,
        ]);

        return redirect()->route('item_pricings.index')
            ->with('flash_success', 'Item pricing updated successfully.');
    }

    /**
     * Delete pricing
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'destroy')) {
            abort(403, 'Unauthorized access');
        }

        $pricing = ItemPricing::findOrFail($id);
        $pricing->delete();

        return redirect()->route('item_pricings.index')
            ->with('flash_success', 'Item pricing deleted successfully.');
    }
}
