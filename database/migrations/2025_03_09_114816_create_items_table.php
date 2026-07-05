<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;     

class CreateItemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('items', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('brand_name',200)->nullable();
      $table->string('model',200)->nullable();
      $table->string('name',200);
      $table->bigInteger('category_id', false, true)->length(10);
      $table->string('measuring_unit',30);
      $table->integer('low_stock', false, true)->length(2)->default(0); 
      $table->text('remarks')->nullable();
      $table->text('attributes')->nullable();
      $table->text('combination')->nullable();
      $table->text('additional')->nullable();
      $table->integer('status', false, true)->length(2); 
      $table->string('item_img', 250)->nullable();
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreign('category_id')->references('id')->on('categories');
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
    Schema::dropIfExists('items');
  }
}
