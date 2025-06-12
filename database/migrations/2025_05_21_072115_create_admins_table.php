<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 125)->unique();
            $table->string('password', 255);
            $table->string('phone', 20)->nullable();
            $table->string('role', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('admins')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@tomosia.com',
                'password' => Hash::make('123456'),
                'role' => 'superadmin'
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@tomosia.com',
                'password' => Hash::make('123456'),
                'role' => 'staff'
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
