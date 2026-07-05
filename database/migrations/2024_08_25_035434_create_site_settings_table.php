<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('site_settings', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name',100);
      $table->string('email',150)->unique();
      $table->string('logo',100)->nullable();
      $table->string('logo_alt',150)->nullable();
      $table->string('pdf_header_img',150)->nullable();
      $table->string('pdf_footer_img',150)->nullable();
      $table->boolean('pdf_no_header_footer',1);
      
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('site_settings');
  }
}
