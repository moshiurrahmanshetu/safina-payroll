<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payroll_month', 7); // YYYY-MM format
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('salary_id')->nullable();
            $table->decimal('generated_salary', 12, 2)->default(0);
            $table->decimal('attendance_adjustment', 12, 2)->default(0);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->tinyInteger('status')->default(0); // 0=Draft, 1=Sent To Manager, 2=Approved, 3=Rejected
            $table->string('approval_status', 20)->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('approval_remark')->nullable();
            $table->timestamp('submitted_at')->nullable();

            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('returned_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('user_id');
            $table->index('payroll_month');
            $table->index('status');
            $table->index('approval_status');

            // Unique constraint: one payroll per user per month
            $table->unique(['user_id', 'payroll_month']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
