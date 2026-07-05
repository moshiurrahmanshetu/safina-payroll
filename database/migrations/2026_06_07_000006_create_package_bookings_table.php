<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('package_id', false, true)->length(10);
            $table->unsignedBigInteger('package_counter_id')->nullable();
            $table->date('date');
            $table->integer('quantity')->default(1);
            $table->integer('total_person')->default(1);
            $table->decimal('base_amount', 10, 2)->default(0);
            $table->integer('extra_person')->default(0);
            $table->decimal('extra_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->string('qr_code', 64)->nullable()->unique();
            $table->string('booking_token', 64)->unique()->nullable();
            $table->boolean('is_used')->default(0);
            $table->enum('ticket_status', ['draft', 'printed'])->default('draft');
            $table->json('ticket_data')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->bigInteger('created_by', false, true)->length(10);
            $table->foreign('package_counter_id')->references('id')->on('package_counters')->onDelete('set null');
            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_bookings');
    }
}
