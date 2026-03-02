<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {

            if (!Schema::hasColumn('pokemons', 'hp')) {
                $table->unsignedSmallInteger('hp')->default(0);
            }
            if (!Schema::hasColumn('pokemons', 'attack')) {
                $table->unsignedSmallInteger('attack')->default(0);
            }
            if (!Schema::hasColumn('pokemons', 'defense')) {
                $table->unsignedSmallInteger('defense')->default(0);
            }
            if (!Schema::hasColumn('pokemons', 'special_attack')) {
                $table->unsignedSmallInteger('special_attack')->default(0);
            }
            if (!Schema::hasColumn('pokemons', 'special_defense')) {
                $table->unsignedSmallInteger('special_defense')->default(0);
            }
            if (!Schema::hasColumn('pokemons', 'speed')) {
                $table->unsignedSmallInteger('speed')->default(0);
            }

            if (!Schema::hasColumn('pokemons', 'has_evolutions')) {
                $table->boolean('has_evolutions')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pokemons', function (Blueprint $table) {
            $toDrop = [];

            foreach ([
                'hp','attack','defense','special_attack','special_defense','speed',
                'image_mega','image_gmax','image_alola','image_galar','image_hisui',
                'has_evolutions'
            ] as $col) {
                if (Schema::hasColumn('pokemons', $col)) $toDrop[] = $col;
            }

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
