<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_tickets', function (Blueprint $table) {
            $table->string('ticket_number')->unique();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('parking_counter_id')->nullable();
            $table->string('vehicle_number');
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->timestamp('entry_time')->nullable();
            $table->timestamp('exit_time')->nullable();
            $table->integer('total_minutes')->nullable();
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->decimal('extra_amount', 10, 2)->nullable();
            $table->time('parking_slot_start_time')->nullable();
            $table->time('parking_slot_end_time')->nullable();
            $table->integer('slot_multiplier')->nullable();
            $table->enum('status', ['pending', 'checked_in', 'checked_out'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('restrict');
            $table->foreign('parking_counter_id')->references('id')->on('parking_counters')->onDelete('set null');
            
            $table->index('parking_counter_id');
            $table->index('ticket_number');
            $table->index('vehicle_number');
            $table->index('status');
            $table->index('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parking_tickets');
    }
}
