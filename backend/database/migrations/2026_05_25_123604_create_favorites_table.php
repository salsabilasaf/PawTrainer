<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tutorial_id')->constrained('tutorials')->onDelete('cascade');
            $table->timestamps();

            // Satu user tidak bisa favorite tutorial yang sama dua kali
            $table->unique(['user_id', 'tutorial_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
