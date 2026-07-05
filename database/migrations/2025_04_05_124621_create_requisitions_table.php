<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequisitionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('requisitions', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->bigInteger('user_id', false, true)->length(10);
      $table->date('stock_out_date')->nullable();
      $table->string('received_by',50)->nullable();
      $table->string('given_by',50)->nullable();
      $table->integer('purpose_type', false, true)->length(10)->default(1);
      $table->bigInteger('purpose_id', false, true)->length(10);
      $table->text('requisitioner_comments')->nullable();
      $table->text('admin_comments')->nullable();
      $table->text('supervisor_comments')->nullable();
      $table->integer('status', false, true)->length(10)->default(0);
      $table->string('received_status',50)->nullable();
      $table->integer('counter_sign_status', false, true)->length(10)->default(0);
      $table->date('counter_sign_date')->nullable();
      $table->bigInteger('counter_sign_by', false, true)->length(10);
      $table->bigInteger('created_by', false, true)->length(10);
      $table->bigInteger('updated_by', false, true)->length(10);
      $table->foreign('purpose_id')->references('id')->on('purposes');
      $table->foreign('counter_sign_by')->references('id')->on('users');
      $table->foreign('created_by')->references('id')->on('users');
      $table->foreign('updated_by')->references('id')->on('users');
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
    Schema::dropIfExists('requisitions');
  }
}
