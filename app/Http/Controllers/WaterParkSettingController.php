<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WaterParkSetting;
use App\Models\Permission;
use App\Models\RolePermission;

class WaterParkSettingController extends Controller
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
     * Get or create the single setting record
     */
    private function getSetting()
    {
        $setting = WaterParkSetting::find(1);
        
        if (!$setting) {
            $setting = WaterParkSetting::create([
                'id' => 1,
                'duration_minutes' => 120,
                'price' => 350,
                'extra_unit_minutes' => 30,
                'extra_unit_price' => 100,
            ]);
        }
        
        return $setting;
    }

    /**
     * Show edit form for water park settings
     */
    public function edit()
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        $settings = $this->getSetting();

        return view('admin.water_park_settings.edit', compact('settings'));
    }

    /**
     * Update water park settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$this->hasPermission($user->role_id, 'edit')) {
            abort(403, 'Unauthorized access');
        }

        // Validate
        $request->validate([
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'extra_unit_minutes' => 'required|integer|min:1',
            'extra_unit_price' => 'required|numeric|min:0',
        ]);

        $settings = $this->getSetting();

        $settings->update([
            'duration_minutes' => $request->duration_minutes,
            'price' => $request->price,
            'extra_unit_minutes' => $request->extra_unit_minutes,
            'extra_unit_price' => $request->extra_unit_price,
        ]);

        return redirect()->route('water_park_settings.edit')
            ->with('flash_success', 'Water Park settings updated successfully.');
    }
}
