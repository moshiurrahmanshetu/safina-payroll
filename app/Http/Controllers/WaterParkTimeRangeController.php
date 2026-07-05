<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WaterParkTimeRange;
use App\Models\Permission;
use App\Models\RolePermission;

class WaterParkTimeRangeController extends Controller
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
     * Display list of time ranges
     */
    public function index()
    {
        $timeRanges = WaterParkTimeRange::orderBy('duration_minutes')->get();
        return view('admin.water_park_time_ranges.index', compact('timeRanges'));
    }
}
