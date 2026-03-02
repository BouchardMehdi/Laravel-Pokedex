<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTeam extends Model
{
    use HasFactory;

    protected $table = 'user_teams';

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pokemons()
    {
        return $this->belongsToMany(
                Pokemon::class,
                'user_team_pokemon',
                'user_team_id',
                'pokemon_id'
            )
            ->withPivot(['slot', 'form'])
            ->withTimestamps();
    }

    // Map des slots 1 à 6
    public function getSlotsMapAttribute(): array
    {
        $map = [];

        foreach ($this->pokemons as $p) {
            $slot = (int) ($p->pivot->slot ?? 0);

            if ($slot >= 1 && $slot <= 6) {
                $map[$slot] = $p;
            }
        }

        return $map;
    }
}