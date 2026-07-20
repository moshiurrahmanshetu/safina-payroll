<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeShiftPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create parent permission for EmployeeShiftController
        $parentPermission = \App\Models\Permission::create([
            'name' => 'EmployeeShiftController',
            'display_name' => 'Employee Shift Management',
            'parent_id' => null,
            'is_menu' => 1,
        ]);

        // Create child permissions for EmployeeShiftController
        $permissions = [
            ['name' => 'EmployeeShiftController@index', 'display_name' => 'List Employee Shifts'],
            ['name' => 'EmployeeShiftController@create', 'display_name' => 'Create Employee Shift'],
            ['name' => 'EmployeeShiftController@store', 'display_name' => 'Store Employee Shift'],
            ['name' => 'EmployeeShiftController@show', 'display_name' => 'View Employee Shift'],
            ['name' => 'EmployeeShiftController@edit', 'display_name' => 'Edit Employee Shift'],
            ['name' => 'EmployeeShiftController@update', 'display_name' => 'Update Employee Shift'],
            ['name' => 'EmployeeShiftController@destroy', 'display_name' => 'Delete Employee Shift'],
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'parent_id' => $parentPermission->id,
                'is_menu' => 0,
            ]);
        }

        // Assign all permissions to Admin role
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->attach($parentPermission->id);
            foreach ($permissions as $permission) {
                $perm = \App\Models\Permission::where('name', $permission['name'])->first();
                if ($perm) {
                    $adminRole->permissions()->attach($perm->id);
                }
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
        // Delete permissions
        $permissionNames = [
            'EmployeeShiftController',
            'EmployeeShiftController@index',
            'EmployeeShiftController@create',
            'EmployeeShiftController@store',
            'EmployeeShiftController@show',
            'EmployeeShiftController@edit',
            'EmployeeShiftController@update',
            'EmployeeShiftController@destroy',
        ];

        foreach ($permissionNames as $name) {
            $permission = \App\Models\Permission::where('name', $name)->first();
            if ($permission) {
                // Detach from all roles
                $permission->roles()->detach();
                // Delete permission
                $permission->delete();
            }
        }
    }
}
