<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_pricings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('item_type', ['locker', 'gear']);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->integer('extra_unit_minutes')->default(30);
            $table->decimal('extra_unit_price', 10, 2)->default(0);
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
        Schema::dropIfExists('item_pricings');
    }
}
