<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bookings', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('service_id', false, true)->length(10);
      $table->bigInteger('user_id', false, true)->length(10)->nullable();
      $table->bigInteger('counter_id', false, true)->length(10)->nullable();
      $table->string('name', 100)->nullable();
      $table->string('phone', 20)->nullable();
      $table->string('email', 100)->nullable();
      $table->text('address')->nullable();
      $table->string('emergency_contact', 20)->nullable();
      $table->date('check_in_date')->nullable();
      $table->time('check_in_time')->nullable();
      $table->date('check_out_date')->nullable();
      $table->time('check_out_time')->nullable();
      $table->date('date');
      $table->time('start_time')->nullable();
      $table->time('end_time')->nullable();
      $table->string('time_slot', 20)->nullable();
      $table->string('promo_code', 50)->nullable();
      $table->decimal('base_price', 10, 2)->nullable();
      $table->decimal('discount_amount', 10, 2)->default(0);
      $table->decimal('manual_discount', 10, 2)->default(0);
      $table->decimal('total_price', 10, 2);
      $table->decimal('final_price', 10, 2)->default(0);
      $table->tinyInteger('status', false, true)->length(1)->default(0)->comment('0=pending,1=confirmed,2=cancelled');
      $table->json('meta_values')->nullable();
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreignId('time_slot_id')->nullable()->constrained()->onDelete('set null');
      $table->foreign('service_id')->references('id')->on('services');
      $table->foreign('user_id')->references('id')->on('users');
      $table->foreign('counter_id')->references('id')->on('counters');
      $table->foreign('created_by')->references('id')->on('users');
      $table->foreign('updated_by')->references('id')->on('users');
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
    Schema::dropIfExists('bookings');
  }
}
