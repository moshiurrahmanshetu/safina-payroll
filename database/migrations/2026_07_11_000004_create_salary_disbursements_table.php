<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_disbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('payment_date');
            $table->enum('payment_method', ['Cash', 'Bank', 'Mobile Banking', 'Cheque']);
            $table->string('reference_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_status', ['Paid', 'Pending', 'Cancelled'])->default('Paid');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('payroll_id')->references('id')->on('payrolls')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('payroll_id');
            $table->index('employee_id');
            $table->index('payment_date');
            $table->index('payment_status');
            $table->index('payment_method');

            // Unique constraint to prevent duplicate payments for same payroll
            $table->unique('payroll_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_disbursements');
    }
};
