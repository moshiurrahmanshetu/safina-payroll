<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSalaryHistoryPermissions extends Migration
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
            // Create SalaryController parent permission
            $salaryControllerId = DB::table('permissions')->insertGetId([
                'name' => 'SalaryController',
                'parent_id' => $parentPermission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create child permissions for SalaryController
            $permissions = [
                ['name' => 'index', 'parent_id' => $salaryControllerId],
                ['name' => 'create', 'parent_id' => $salaryControllerId],
                ['name' => 'store', 'parent_id' => $salaryControllerId],
                ['name' => 'show', 'parent_id' => $salaryControllerId],
                ['name' => 'edit', 'parent_id' => $salaryControllerId],
                ['name' => 'update', 'parent_id' => $salaryControllerId],
                ['name' => 'destroy', 'parent_id' => $salaryControllerId],
                ['name' => 'timeline', 'parent_id' => $salaryControllerId],
                ['name' => 'lock', 'parent_id' => $salaryControllerId],
                ['name' => 'unlock', 'parent_id' => $salaryControllerId],
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
                ->where('parent_id', $salaryControllerId)
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
        // Get SalaryController permission
        $salaryController = DB::table('permissions')->where('name', 'SalaryController')->first();
        
        if ($salaryController) {
            // Delete role_permissions for all child permissions
            $childPermissionIds = DB::table('permissions')
                ->where('parent_id', $salaryController->id)
                ->pluck('id')
                ->toArray();

            DB::table('role_permissions')->whereIn('permission_id', $childPermissionIds)->delete();

            // Delete child permissions
            DB::table('permissions')->where('parent_id', $salaryController->id)->delete();

            // Delete SalaryController permission
            DB::table('permissions')->where('id', $salaryController->id)->delete();
        }
    }
}
