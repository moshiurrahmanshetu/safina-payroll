<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryMetaFieldsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('category_meta_fields', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('service_category_id', false, true)->length(10);
      $table->string('field_name', 100);
      $table->tinyInteger('field_type', false, true)->length(1)->comment('0=text,1=number,2=select,3=date');
      $table->tinyInteger('required', false, true)->length(1)->default(0);
      $table->text('options')->nullable()->comment('JSON for select options');
      $table->string('conditional_field', 100)->nullable();
      $table->string('conditional_value', 255)->nullable();
      $table->integer('sort_order')->default(0);
      $table->tinyInteger('is_resource', false, true)->length(1)->default(0);
      $table->text('help_text')->nullable();
      $table->string('resource_key', 100)->nullable();
      $table->foreign('service_category_id')->references('id')->on('service_categories')->onDelete('cascade');
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
    Schema::dropIfExists('category_meta_fields');
  }
}
