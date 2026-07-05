<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('purchase_items', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('purchase_id', false, true)->length(10);
      $table->bigInteger('item_id', false, true)->length(10);
      $table->string('name',200);
      $table->text('description')->nullable();
      $table->bigInteger('category_id', false, true)->length(10);
      $table->text('combinations')->nullable();
      $table->decimal('unit_price',12, 2);
      $table->string('measuring_unit',50);
      $table->decimal('quantity',12, 2);
      $table->decimal('per_total',12, 2);
      $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
      $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
      $table->foreign('category_id')->references('id')->on('categories');
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
    Schema::dropIfExists('purchase_items');
  }
}
