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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone');
            $table->datetime('check_in')->nullable();
            $table->datetime('check_out')->nullable();
            $table->decimal('deposit', 10, 2)->nullable();
            $table->decimal('total_payment', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
