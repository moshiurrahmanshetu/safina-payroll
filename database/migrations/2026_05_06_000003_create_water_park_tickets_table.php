<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaterParkTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_park_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('water_park_counter_id')->nullable();
            $table->string('ticket_number')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('duration_minutes')->default(120);
            $table->integer('extra_unit_minutes')->default(30);
            $table->decimal('extra_unit_price', 10, 2)->default(0);
            $table->enum('status', ['pending', 'checked_in', 'checked_out'])->default('pending');
            $table->dateTime('entry_time')->nullable();
            $table->dateTime('exit_time')->nullable();
            $table->integer('extra_minutes')->default(0);
            $table->decimal('extra_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('water_park_counter_id')->references('id')->on('water_park_counters')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('water_park_tickets');
    }
}
