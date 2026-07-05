<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequisitionItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('requisition_items', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('requisition_id', false, true)->length(10);
      $table->bigInteger('item_id', false, true)->length(10);
      $table->bigInteger('category_id', false, true)->length(10);
      $table->string('name',200);
      $table->text('description')->nullable();
      $table->bigInteger('warehouse_id', false, true)->length(10);
      $table->text('combinations')->nullable();
      $table->decimal('req_quantity',12, 2);
      $table->decimal('given_quantity',12, 2)->default(0);
      $table->string('measuring_unit',50);
      $table->tinyInteger('returnable')->default(0);
      $table->tinyInteger('product_type')->default(0);
      $table->date('stock_out_date')->nullable();
      $table->integer('status', false, true)->length(10)->default(0);
      $table->foreign('requisition_id')->references('id')->on('requisitions')->onDelete('cascade');
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
    Schema::dropIfExists('requisition_items');
  }
}
