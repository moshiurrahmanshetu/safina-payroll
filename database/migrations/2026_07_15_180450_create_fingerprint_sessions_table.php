<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFingerprintSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fingerprint_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('attendance_date');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('first_in')->nullable();
            $table->dateTime('last_out')->nullable();
            $table->integer('total_punch')->default(0);
            $table->string('status')->default('Active');
            $table->string('source')->default('Fingerprint');
            $table->boolean('processed')->default(false);
            $table->dateTime('processed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('attendance_date');
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fingerprint_sessions');
    }
}
