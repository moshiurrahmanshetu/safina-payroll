<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountRulesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('discount_rules', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name', 100);
      $table->string('code', 50)->nullable()->unique();
      $table->bigInteger('category_id', false, true)->length(10)->nullable();
      $table->tinyInteger('discount_type', false, true)->length(1)->default(0)->comment('0=fixed,1=percentage');
      $table->decimal('amount', 10, 2)->default(0);
      $table->bigInteger('service_id', false, true)->length(10)->nullable();
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->tinyInteger('status', false, true)->length(1)->default(1)->comment('0=inactive,1=active');
      $table->foreign('category_id')->references('id')->on('service_categories')->onDelete('set null');
      $table->foreign('service_id')->references('id')->on('services')->onDelete('set null');
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
    Schema::dropIfExists('discount_rules');
  }
}
