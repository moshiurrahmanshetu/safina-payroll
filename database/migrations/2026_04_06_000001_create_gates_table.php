<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('gates', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name', 100);
      $table->tinyInteger('status', false, true)->length(1)->default(1)->comment('1=active, 0=inactive');
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
    Schema::dropIfExists('gates');
  }
}
