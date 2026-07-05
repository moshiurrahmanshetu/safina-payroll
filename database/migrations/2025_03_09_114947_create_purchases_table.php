<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('purchases', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('contact_name',200);
      $table->string('company_name',200)->nullable();
      $table->integer('supplier_type', false, true)->length(2);
      $table->string('address',250)->nullable();
      $table->string('mobile',20);
      $table->string('email',150)->nullable();
      $table->string('web_site',250)->nullable();
      $table->bigInteger('supplier_id', false, true)->length(10);
      $table->date('purchase_date'); 
      $table->string('po_number',150)->nullable();
      $table->bigInteger('purchase_person', false, true)->length(10);
      $table->string('invoice_no',50)->nullable();
      $table->string('fob_point',150)->nullable();
      $table->decimal('discount',12, 2)->nullable();
      $table->decimal('sub_total',12, 2);
      $table->decimal('vat_percent',12, 2);
      $table->decimal('vat',12, 2);
      $table->decimal('grand_total',12, 2);
      $table->string('inword',150)->nullable();
      $table->text('special_instruction')->nullable();
      $table->integer('status', false, true)->length(10);
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreign('created_by')->references('id')->on('users');
      $table->foreign('updated_by')->references('id')->on('users');
      $table->foreign('purchase_person')->references('id')->on('users');
      $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
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
    Schema::dropIfExists('purchases');
  }
}
