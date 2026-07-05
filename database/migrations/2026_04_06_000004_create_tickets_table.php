<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
 public function up()
{
    Schema::create('tickets', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('name', 150);
        $table->decimal('price', 10, 2);
        $table->tinyInteger('status')->default(1);
        $table->string('ticket_number', 20)->unique()->nullable();
        $table->boolean('is_used')->default(false);
        $table->bigInteger('gate_id', false, true)->length(10)->nullable();
        $table->unsignedBigInteger('created_by');
        $table->unsignedBigInteger('updated_by');
        $table->timestamps();
        $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
    });
}

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('tickets');
  }
}
