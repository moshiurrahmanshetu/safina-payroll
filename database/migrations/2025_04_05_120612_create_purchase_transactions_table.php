<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseTransactionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */    

  public function up()
  {
    Schema::create('purchase_transactions', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('supplier_id', false, true)->length(10);
      $table->bigInteger('purchase_id', false, true)->length(10);
      $table->date('payment_date');
      $table->decimal('amount',12, 2);
      $table->integer('payment_type', false, true)->length(2);
      $table->string('invoice_no',50)->nullable();
      $table->string('money_rceipt_no',50)->nullable();
      $table->string('received_by',50)->nullable();
      $table->bigInteger('given_by', false, true)->length(10);
      $table->string('attachment_copy',250)->nullable();
      $table->text('remarks')->nullable();
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreign('given_by')->references('id')->on('users');
      $table->foreign('created_by')->references('id')->on('users');
      $table->foreign('updated_by')->references('id')->on('users');
      $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
      $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
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
    Schema::dropIfExists('purchase_transactions');
  }
}
