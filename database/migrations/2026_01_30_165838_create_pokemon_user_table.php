<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokemon_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // ✅ IMPORTANT : on référence la table "pokemons" (pluriel)
            $table->foreignId('pokemon_id')->constrained('pokemons')->cascadeOnDelete();

            $table->timestamps();
            $table->unique(['user_id', 'pokemon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pokemon_user');
    }
};
