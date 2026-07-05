<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockInsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */ 
  public function up()
  {
    Schema::create('stock_ins', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('purchase_id', false, true)->length(10);
      $table->bigInteger('supplier_id', false, true)->length(10);
      $table->bigInteger('purchase_item_id', false, true)->length(10);
      $table->bigInteger('item_id', false, true)->length(10);
      $table->bigInteger('warehouse_id', false, true)->length(10);
      $table->bigInteger('department_id', false, true)->length(10)->default(0);
      $table->text('combinations')->nullable();
      $table->date('stock_date');
      $table->decimal('quantity',12, 2);
      $table->string('received_by',50)->nullable();
      $table->string('given_by',50)->nullable(); 
      $table->text('remarks')->nullable();
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreign('created_by')->references('id')->on('users');
      $table->foreign('updated_by')->references('id')->on('users');
      $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
      $table->foreign('purchase_item_id')->references('id')->on('purchase_items')->onDelete('cascade');
      $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
      $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
      $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
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
    Schema::dropIfExists('stock_ins');
  }
}
