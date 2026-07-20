<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAttendanceReportPermissions extends Migration
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
            // Create AttendanceReportController parent permission
            $attendanceReportControllerId = DB::table('permissions')->insertGetId([
                'name' => 'AttendanceReportController',
                'parent_id' => $parentPermission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create child permissions for AttendanceReportController
            $permissions = [
                ['name' => 'index', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'employeeDaily', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'employeeMonthly', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'dailyRegister', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'monthlyRegister', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'lateReport', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'departmentReport', 'parent_id' => $attendanceReportControllerId],
                ['name' => 'shiftReport', 'parent_id' => $attendanceReportControllerId],
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
                ->where('parent_id', $attendanceReportControllerId)
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
        // Get AttendanceReportController permission
        $attendanceReportController = DB::table('permissions')->where('name', 'AttendanceReportController')->first();
        
        if ($attendanceReportController) {
            // Delete role_permissions for all child permissions
            $childPermissionIds = DB::table('permissions')
                ->where('parent_id', $attendanceReportController->id)
                ->pluck('id')
                ->toArray();

            DB::table('role_permissions')->whereIn('permission_id', $childPermissionIds)->delete();

            // Delete child permissions
            DB::table('permissions')->where('parent_id', $attendanceReportController->id)->delete();

            // Delete AttendanceReportController permission
            DB::table('permissions')->where('id', $attendanceReportController->id)->delete();
        }
    }
}
