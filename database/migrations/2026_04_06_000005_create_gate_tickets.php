<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGateTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gate_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gate_id');
            $table->unsignedBigInteger('ticket_id');
            $table->timestamps();
            $table->foreign('gate_id')->references('id')->on('gates')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            $table->unique(['gate_id', 'ticket_id']);
            $table->index('gate_id');
            $table->index('ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gate_tickets');
    }
}
