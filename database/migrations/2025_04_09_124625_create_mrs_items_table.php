<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMrsItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('mrs_items', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('requisition_id', false, true)->length(10);
      $table->bigInteger('requisition_item_id', false, true)->length(10);
      $table->bigInteger('item_id', false, true)->length(10);
      $table->bigInteger('user_id', false, true)->length(10);
      $table->string('name',200);
      $table->text('combinations')->nullable();
      $table->text('admin_comments')->nullable();
      $table->bigInteger('received_by', false, true)->length(10);
      $table->bigInteger('warehouse_id', false, true)->length(10);
      $table->decimal('quantity',12, 2);
      $table->string('measuring_unit',50);
      $table->tinyInteger('item_condition')->default(0);
      $table->date('received_date')->nullable();
      $table->foreign('requisition_id')->references('id')->on('requisitions')->onDelete('cascade');
      $table->foreign('requisition_item_id')->references('id')->on('requisition_items')->onDelete('cascade');
      $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->foreign('received_by')->references('id')->on('users')->onDelete('cascade');
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
    Schema::dropIfExists('mrs_items');
  }
}
