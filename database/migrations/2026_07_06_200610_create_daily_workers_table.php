<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_workers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('worker_id', 20)->unique();
            $table->string('photo')->nullable();
            $table->string('full_name', 150);
            $table->string('mobile', 20);
            $table->string('email', 100)->nullable();
            $table->string('nid', 50)->nullable();
            $table->text('present_address')->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->unsignedBigInteger('work_area_id');
            $table->decimal('daily_wage', 10, 2);
            $table->date('joining_date');
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            // Foreign keys
            $table->foreign('work_area_id')->references('id')->on('work_areas')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('worker_id');
            $table->index('status');
            $table->index('work_area_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_workers');
    }
}
