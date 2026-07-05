<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCounterUserTable extends Migration
{
    public function up()
    {
        Schema::create('counter_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('counter_id', false, true)->length(10);
            $table->bigInteger('user_id', false, true)->length(10);
            $table->timestamps();
            
            $table->foreign('counter_id')->references('id')->on('counters')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['counter_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('counter_user');
    }
}
