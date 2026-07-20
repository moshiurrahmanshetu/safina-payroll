<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddShiftPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the parent permission for HR & Payroll
        $parentPermission = DB::table('permissions')->where('name', 'HR & Payroll')->first();
        
        if ($parentPermission) {
            // Create ShiftController parent permission
            $shiftControllerId = DB::table('permissions')->insertGetId([
                'name' => 'ShiftController',
                'parent_id' => $parentPermission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create child permissions for ShiftController
            $permissions = [
                ['name' => 'index', 'parent_id' => $shiftControllerId],
                ['name' => 'create', 'parent_id' => $shiftControllerId],
                ['name' => 'store', 'parent_id' => $shiftControllerId],
                ['name' => 'show', 'parent_id' => $shiftControllerId],
                ['name' => 'edit', 'parent_id' => $shiftControllerId],
                ['name' => 'update', 'parent_id' => $shiftControllerId],
                ['name' => 'destroy', 'parent_id' => $shiftControllerId],
            ];

            foreach ($permissions as $permission) {
                DB::table('permissions')->insert([
                    'name' => $permission['name'],
                    'parent_id' => $permission['parent_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Assign all permissions to role_id 1 (Admin)
            $allPermissionIds = DB::table('permissions')
                ->where('parent_id', $shiftControllerId)
                ->pluck('id')
                ->toArray();

            foreach ($allPermissionIds as $permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => 1,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get ShiftController permission
        $shiftController = DB::table('permissions')->where('name', 'ShiftController')->first();
        
        if ($shiftController) {
            // Delete role_permissions for all child permissions
            $childPermissionIds = DB::table('permissions')
                ->where('parent_id', $shiftController->id)
                ->pluck('id')
                ->toArray();

            DB::table('role_permissions')->whereIn('permission_id', $childPermissionIds)->delete();

            // Delete child permissions
            DB::table('permissions')->where('parent_id', $shiftController->id)->delete();

            // Delete ShiftController permission
            DB::table('permissions')->where('id', $shiftController->id)->delete();
        }
    }
}
