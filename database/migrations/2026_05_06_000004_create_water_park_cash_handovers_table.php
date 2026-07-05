<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWaterParkCashHandoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_park_cash_handovers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('water_park_counter_id');
            $table->decimal('amount', 10, 2)->unsigned();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('receiver_user_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('remark')->nullable();
            $table->date('business_date');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('water_park_counter_id')->references('id')->on('water_park_counters')->onDelete('restrict');
            $table->foreign('receiver_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['user_id', 'water_park_counter_id', 'status'], 'idx_user_counter_status');
            $table->index('status', 'idx_status');
            $table->index('requested_at', 'idx_requested_at');
            $table->index(['user_id', 'status', 'requested_at'], 'idx_user_status_requested');
            $table->index(['water_park_counter_id', 'status', 'requested_at'], 'idx_counter_status_requested');
            $table->index('receiver_user_id', 'idx_receiver_user_id');

            // Unique index to prevent duplicate pending handovers
            $table->unique(['user_id', 'water_park_counter_id', 'status'], 'unique_pending_handover')
                  ->where('status', 'pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('water_park_cash_handovers');
    }
}
