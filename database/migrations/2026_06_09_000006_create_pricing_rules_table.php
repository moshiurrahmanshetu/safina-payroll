<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricingRulesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('pricing_rules', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('service_id', false, true)->length(10);
      $table->tinyInteger('rule_type', false, true)->length(1)->default(0)->comment('0=seasonal,1=weekend,2=holiday');
      $table->date('start_date')->nullable();
      $table->date('end_date')->nullable();
      $table->json('days')->nullable()->comment('JSON array for weekend days like ["sat","sun"]');
      $table->tinyInteger('price_type', false, true)->length(1)->default(0)->comment('0=fixed,1=percentage');
      $table->decimal('amount', 10, 2)->default(0);
      $table->tinyInteger('status', false, true)->length(1)->default(1)->comment('0=inactive,1=active');
      $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
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
    Schema::dropIfExists('pricing_rules');
  }
}
