<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pokemons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type1');
            $table->string('type2')->nullable();
            $table->boolean('is_legendary')->default(false);
            $table->boolean('is_fabulous')->default(false);
            $table->boolean('is_ultra_beast')->default(false);
            $table->boolean('is_paradox')->default(false);
            $table->unsignedInteger('generation')->nullable();
            $table->unsignedInteger('pokedex_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            $table->dropColumn([
                'is_legendary',
                'is_fabulous',
                'is_ultra_beast',
                'is_paradox',
            ]);
        });
    }
};
