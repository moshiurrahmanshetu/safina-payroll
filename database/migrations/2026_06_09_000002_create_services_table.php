<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('services', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('service_category_id', false, true)->length(10)->nullable();
      $table->string('name', 150);
      $table->tinyInteger('pricing_type', false, true)->length(1)->default(0)->comment('0=fixed,1=hourly,2=daily');
      $table->integer('guest_capacity')->nullable();
      $table->text('service_details')->nullable();
      $table->tinyInteger('status', false, true)->length(1)->default(1);
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreign('service_category_id')->references('id')->on('service_categories');
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
    Schema::dropIfExists('services');
  }
}
