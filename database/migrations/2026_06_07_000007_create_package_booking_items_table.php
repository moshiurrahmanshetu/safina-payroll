<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageBookingItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_booking_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('package_booking_id', false, true)->length(10);
            $table->bigInteger('service_id', false, true)->length(10);
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('source', 20)->default('package')->comment('package=included,extra=added extra');
            $table->foreign('package_booking_id')->references('id')->on('package_bookings')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('tickets')->onDelete('cascade');
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
        Schema::dropIfExists('package_booking_items');
    }
}
