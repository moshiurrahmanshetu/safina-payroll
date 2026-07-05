<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLockerGearTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('locker_gear_tickets', function (Blueprint $table) {
            // Primary Key
            $table->string('ticket_number', 50)->primary();
            $table->string('qr_code', 100)->unique();
            $table->enum('status', [
                'checked_in',
                'checked_out'
            ])->default('checked_in');

            $table->timestamp('entry_time')->nullable();
            $table->timestamp('exit_time')->nullable();
            $table->decimal('total_amount',10,2)->default(0);
            $table->decimal('extra_amount',10,2)->default(0);
            $table->unsignedBigInteger('extra_collected_by')->nullable();
            $table->unsignedBigInteger('extra_collected_counter_id')->nullable();
            $table->timestamp('extra_collected_at')->nullable();
            $table->unsignedBigInteger('locker_gear_counter_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->index('extra_collected_by');
            $table->index('extra_collected_counter_id');
            $table->index('extra_collected_at');

            $table->foreign('extra_collected_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('extra_collected_counter_id')
                ->references('id')
                ->on('locker_gear_counters')
                ->onDelete('set null');

            $table->foreign('locker_gear_counter_id')
                ->references('id')
                ->on('locker_gear_counters')
                ->onDelete('set null');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });


        Schema::create('locker_gear_ticket_items', function (Blueprint $table) {

            $table->bigIncrements('id');

            // FK to ticket_number
            $table->string('ticket_number', 50);

            $table->enum('item_type', [
                'locker',
                'gear'
            ]);

            $table->unsignedBigInteger('item_id');

            $table->integer('quantity')->default(1);

            $table->timestamps();

            $table->index('ticket_number');

            $table->foreign('ticket_number')
                ->references('ticket_number')
                ->on('locker_gear_tickets')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('locker_gear_ticket_items');
        Schema::dropIfExists('locker_gear_tickets');
    }
}