<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermanentEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permanent_employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_id', 20)->unique();
            $table->string('photo')->nullable();
            $table->string('full_name', 150);
            $table->string('father_name', 150);
            $table->string('mother_name', 150);
            $table->string('gender', 20);
            $table->date('date_of_birth')->nullable();
            $table->string('nid', 50)->nullable();
            $table->string('mobile', 20);
            $table->string('email', 100)->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->date('joining_date');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('designation_id');
            $table->tinyInteger('employment_status')->default(1);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('restrict');

            // Indexes
            $table->index('employee_id');
            $table->index('employment_status');
            $table->index('department_id');
            $table->index('designation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permanent_employees');
    }
}
