<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFingerprintLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fingerprint_logs', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code');
            $table->dateTime('punch_datetime');
            $table->enum('punch_type', ['IN', 'OUT']);
            $table->string('device_id')->nullable();
            $table->string('source')->default('CSV');
            $table->string('import_batch');
            $table->boolean('processed')->default(false);
            $table->dateTime('processed_at')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();

            $table->index('employee_code');
            $table->index('punch_datetime');
            $table->index('processed');
            $table->index('import_batch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fingerprint_logs');
    }
}
