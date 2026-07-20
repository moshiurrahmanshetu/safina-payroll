<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPayrollApprovalPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get PayrollController parent permission ID
        $payrollControllerId = DB::table('permissions')->where('name', 'PayrollController')->value('id');

        if ($payrollControllerId) {
            // Add submit permission
            DB::table('permissions')->updateOrInsert([
                'name' => 'submit',
                'parent_id' => $payrollControllerId
            ]);

            // Add approve permission
            DB::table('permissions')->updateOrInsert([
                'name' => 'approve',
                'parent_id' => $payrollControllerId
            ]);

            // Add returnPayroll permission
            DB::table('permissions')->updateOrInsert([
                'name' => 'returnPayroll',
                'parent_id' => $payrollControllerId
            ]);

            // Add show permission (for detail view)
            DB::table('permissions')->updateOrInsert([
                'name' => 'show',
                'parent_id' => $payrollControllerId
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('permissions')->where('name', 'submit')->delete();
        DB::table('permissions')->where('name', 'approve')->delete();
        DB::table('permissions')->where('name', 'returnPayroll')->delete();
        DB::table('permissions')->where('name', 'show')->where('parent_id', function($query) {
            $query->select('id')->from('permissions')->where('name', 'PayrollController');
        })->delete();
    }
}
