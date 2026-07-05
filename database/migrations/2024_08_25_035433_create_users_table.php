<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table){
      $table->bigIncrements('id');
      $table->bigInteger('role_id', false, true)->length(10);
      $table->bigInteger('designation_id', false, true)->length(10);
      $table->bigInteger('department_id', false, true)->length(10);
      $table->bigInteger('supervisor_id', false, true)->length(10);
      $table->string('name',100);
      $table->string('mobile_no',15)->nullable();
      $table->string('email',150)->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password',200);
      $table->rememberToken();
      $table->string('address',200)->nullable();
      $table->string('photo',120)->nullable();
      $table->string('signature',120)->nullable();
      $table->integer('status', false, true)->length(2);
      $table->foreign('role_id')->references('id')->on('roles');
      $table->foreign('designation_id')->references('id')->on('designations');
      $table->foreign('department_id')->references('id')->on('departments');
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
    Schema::dropIfExists('users');
  }
}
