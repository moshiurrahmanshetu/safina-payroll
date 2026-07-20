

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    
    {
        Schema::create('salaries', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        // Earnings
        $table->decimal('basic_salary', 15, 2)->default(0);
        $table->decimal('house_rent', 15, 2)->default(0);
        $table->decimal('medical', 15, 2)->default(0);
        $table->decimal('transport', 15, 2)->default(0);
        $table->decimal('food', 15, 2)->default(0);
        $table->decimal('mobile', 15, 2)->default(0);
        $table->decimal('other_allowance', 15, 2)->default(0);
        $table->decimal('festival_bonus', 15, 2)->default(0);

        // Deductions
        $table->decimal('late_fine', 15, 2)->default(0);
        $table->decimal('absent_deduction', 15, 2)->default(0);
        $table->decimal('advance_salary', 15, 2)->default(0);
        $table->decimal('tax', 15, 2)->default(0);
        $table->decimal('pf', 15, 2)->default(0);
        $table->decimal('other_deduction', 15, 2)->default(0);

        // Calculated Fields
        $table->decimal('gross_salary', 15, 2)->default(0);
        $table->decimal('net_salary', 15, 2)->default(0);

        // Salary Revision
        $table->date('effective_from');

        $table->string('salary_increment_reason',255);

        $table->text('remarks')->nullable();

        // Flags
        $table->boolean('is_current')->default(true);

        // Lock further revision
        $table->boolean('revision_locked')->default(false);

        // Lock payroll processing
        $table->boolean('salary_locked')->default(false);

        $table->tinyInteger('status')->default(1);

        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();

        $table->timestamps();

        // Foreign Keys
        $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');

        $table->foreign('created_by')
            ->references('id')
            ->on('users')
            ->nullOnDelete();

        $table->foreign('updated_by')
            ->references('id')
            ->on('users')
            ->nullOnDelete();

        // Indexes
        $table->index('user_id');
        $table->index('effective_from');
        $table->index('is_current');
        $table->index('revision_locked');
        $table->index('salary_locked');
        $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
