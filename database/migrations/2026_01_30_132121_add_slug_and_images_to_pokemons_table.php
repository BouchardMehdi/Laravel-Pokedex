<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            if (!Schema::hasColumn('pokemons', 'slug')) {
                $table->string('slug')->nullable()->index();
            }
            if (!Schema::hasColumn('pokemons', 'image_default')) {
                $table->string('image_default')->nullable();
            }
            if (!Schema::hasColumn('pokemons', 'image_shiny')) {
                $table->string('image_shiny')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            if (Schema::hasColumn('pokemons', 'image_shiny')) $table->dropColumn('image_shiny');
            if (Schema::hasColumn('pokemons', 'image_default')) $table->dropColumn('image_default');
            if (Schema::hasColumn('pokemons', 'slug')) $table->dropColumn('slug');
        });
    }
};
