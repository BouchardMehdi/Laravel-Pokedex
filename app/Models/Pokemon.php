<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $table = 'pokemons';

    protected $fillable = [
        'pokedex_number',
        'name',
        'slug',
        'generation',
        'type1',
        'type2',

        'hp',
        'attack',
        'defense',
        'special_attack',
        'special_defense',
        'speed',

        'is_legendary',
        'is_fabulous',
        'is_ultra_beast',
        'is_paradox',

        'image_default',
        'image_shiny',

        'forms',
    ];

    protected $casts = [
        'is_legendary' => 'boolean',
        'is_fabulous' => 'boolean',
        'is_ultra_beast' => 'boolean',
        'is_paradox' => 'boolean',

        'forms' => 'array',
    ];
}
