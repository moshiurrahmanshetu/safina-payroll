<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageCounterPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_counter_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_counter_id');
            $table->unsignedBigInteger('package_id');
            $table->timestamps();

            $table->foreign('package_counter_id')
                  ->references('id')
                  ->on('package_counters')
                  ->onDelete('cascade');

            $table->foreign('package_id')
                  ->references('id')
                  ->on('packages')
                  ->onDelete('cascade');

            $table->unique(['package_counter_id', 'package_id']);

            $table->index('package_counter_id');
            $table->index('package_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_counter_packages');
    }
}
