<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\WaterParkSetting;

class CreateWaterParkSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('water_park_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('duration_minutes')->default(120);
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('extra_unit_minutes')->default(30);
            $table->decimal('extra_unit_price', 10, 2)->default(0);
            $table->timestamps();
        });

        // Create default single record
        WaterParkSetting::firstOrCreate(
            ['id' => 1],
            [
                'duration_minutes' => 120,
                'price' => 350,
                'extra_unit_minutes' => 30,
                'extra_unit_price' => 100,
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('water_park_settings');
    }
}
