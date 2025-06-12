<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->integer('room_id');
            $table->integer('booking_id');
            $table->decimal('price_per_day', 10, 2);
            $table->datetime('checkin_at')->nullable();
            $table->datetime('checkout_at')->nullable();
            $table->integer('status')->default(1);
            $table->datetime('checkin')->nullable();
            $table->datetime('checkout')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
