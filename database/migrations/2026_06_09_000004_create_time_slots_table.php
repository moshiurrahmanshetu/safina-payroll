<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeSlotsTable extends Migration
{
  public function up()
  {
    Schema::create('time_slots', function (Blueprint $table) {
      $table->id();
      $table->foreignId('service_id')->nullable()->constrained()->onDelete('cascade');
      $table->string('name');
      $table->time('start_time');
      $table->time('end_time');
      $table->decimal('price', 10, 2)->default(0);
      $table->boolean('status')->default(1);
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('time_slots');
  }
}
