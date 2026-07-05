<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolePermissionsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('role_permissions', function (Blueprint $table) {
     $table->bigInteger('role_id', false, true)->length(10);
     $table->bigInteger('permission_id', false, true)->length(10);
     $table->primary(['role_id','permission_id']);
     $table->foreign('role_id')->references('id')->on('roles');
     $table->foreign('permission_id')->references('id')->on('permissions');
   });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('role_permissions', function ($table) {
      $table->dropForeign(['role_id']);
      $table->dropForeign(['permission_id']);
    });
    Schema::dropIfExists('role_permissions');
  }
}
