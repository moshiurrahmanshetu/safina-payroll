<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalWorkflowToPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('pending')->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->unsignedBigInteger('returned_by')->nullable()->after('approved_at');
            $table->timestamp('returned_at')->nullable()->after('returned_by');
            $table->text('approval_remark')->nullable()->after('returned_at');
            $table->timestamp('submitted_at')->nullable()->after('approval_remark');

            // Foreign keys
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['returned_by']);
            $table->dropIndex(['approval_status']);
            $table->dropColumn([
                'approval_status',
                'approved_by',
                'approved_at',
                'returned_by',
                'returned_at',
                'approval_remark',
                'submitted_at'
            ]);
        });
    }
}
