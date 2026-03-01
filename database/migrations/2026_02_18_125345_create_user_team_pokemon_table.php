<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_team_pokemon', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_team_id')
                ->constrained('user_teams')
                ->cascadeOnDelete();

            $table->foreignId('pokemon_id')
                ->constrained('pokemons')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('slot');

            $table->timestamps();

            $table->unique(['user_team_id', 'slot']);

            $table->unique(['user_team_id', 'pokemon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_team_pokemon');
    }
};
