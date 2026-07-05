<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParkingTicketPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parking_ticket_payments', function (Blueprint $table) {
            $table->id();
            $table->string('parking_ticket_number');
            $table->enum('payment_type', ['entry', 'extra'])->default('entry');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('parking_counter_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('parking_ticket_number')->references('ticket_number')->on('parking_tickets')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parking_counter_id')->references('id')->on('parking_counters')->onDelete('cascade');

            // Indexes
            $table->index('parking_ticket_number');
            $table->index('payment_type');
            $table->index('payment_date');
            $table->index('created_by');
            $table->index('parking_counter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parking_ticket_payments');
    }
}
