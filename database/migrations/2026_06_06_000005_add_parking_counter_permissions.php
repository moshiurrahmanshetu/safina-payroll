<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddParkingCounterPermissions extends Migration
{
    public function up()
    {
        // Get Admin role
        $adminRoleId = DB::table('roles')
            ->where('name', 'Admin')
            ->value('id');

        if (!$adminRoleId) {
            $adminRoleId = DB::table('roles')->value('id');
        }

        // Create/Get parent permission
        $controllerId = DB::table('permissions')
            ->where('name', 'ParkingCounterController')
            ->whereNull('parent_id')
            ->value('id');

        if (!$controllerId) {
            $controllerId = DB::table('permissions')->insertGetId([
                'name'      => 'ParkingCounterController',
                'parent_id' => null,
            ]);
        }

        $permissions = [
            'view_parking_counters',
            'create_parking_counter',
            'edit_parking_counter',
            'delete_parking_counter',
        ];

        foreach ($permissions as $permissionName) {

            $permissionId = DB::table('permissions')
                ->where('name', $permissionName)
                ->where('parent_id', $controllerId)
                ->value('id');

            if (!$permissionId) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name'      => $permissionName,
                    'parent_id' => $controllerId,
                ]);
            }

            if ($adminRoleId) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role_id'       => $adminRoleId,
                        'permission_id' => $permissionId,
                    ],
                    []
                );
            }
        }
    }

    public function down()
    {
        $controllerId = DB::table('permissions')
            ->where('name', 'ParkingCounterController')
            ->whereNull('parent_id')
            ->value('id');

        if (!$controllerId) {
            return;
        }

        $permissionIds = DB::table('permissions')
            ->where('parent_id', $controllerId)
            ->pluck('id')
            ->toArray();

        if (!empty($permissionIds)) {
            DB::table('role_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();
        }
    }
}