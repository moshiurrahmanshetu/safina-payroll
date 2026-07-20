<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_months', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shift_id')->nullable();  
           
            $table->string('attendance_month', 7); // Format: YYYY-MM
            $table->json('attendance_json')->nullable();
            $table->integer('summary_present')->default(0);
            $table->integer('summary_late')->default(0);
            $table->integer('summary_halfday')->default(0);
            $table->integer('summary_absent')->default(0);
            $table->integer('summary_leave')->default(0);
            $table->integer('summary_holiday')->default(0);
            $table->integer('summary_weekly_off')->default(0);
            $table->boolean('attendance_locked')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Unique constraint: one record per user per month
            $table->unique(['user_id', 'attendance_month']);

            // Indexes for performance
            $table->index('user_id');
            $table->index('attendance_month');
            $table->index('attendance_locked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_months');
    }
}
