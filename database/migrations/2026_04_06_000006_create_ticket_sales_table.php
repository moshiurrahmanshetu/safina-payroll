<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSalesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('ticket_sales', function (Blueprint $table) {
      $table->bigInteger('ticket_id', false, true)->length(10);
      $table->decimal('price', 10, 2);
      $table->decimal('total_price', 10, 2);
      $table->decimal('discount_amount', 10, 2)->default(0);
      $table->bigInteger('gate_id', false, true)->length(10)->nullable();
      $table->date('date')->nullable();
      $table->string('qr_code', 64)->unique()->nullable();
      $table->boolean('is_used')->default(false);
      $table->timestamp('used_at')->nullable();
      $table->bigInteger('created_by', false, true)->length(10)->nullable();
      $table->string('sale_group_token', 64)->nullable()->index();
      $table->timestamps();
      $table->foreign('ticket_id')->references('id')->on('tickets');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('ticket_sales');
  }
}
