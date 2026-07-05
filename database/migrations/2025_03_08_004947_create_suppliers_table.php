<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuppliersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('suppliers', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('contact_name',150);
      $table->string('company_name',150)->nullable();
      $table->bigInteger('supplier_type', false, true)->length(10);
      $table->string('address',150)->nullable(); 
      $table->string('mobile',20); 
      $table->string('email',150)->nullable(); 
      $table->string('web_site',150)->nullable(); 
      $table->bigInteger('status', false, true)->length(10);
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
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
    Schema::dropIfExists('suppliers');
  }
}
