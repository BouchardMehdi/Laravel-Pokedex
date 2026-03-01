<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function pokemons()
    {
        return $this->belongsToMany(
            \App\Models\Pokemon::class,
            'pokemon_user',
            'user_id',
            'pokemon_id'
        )->withTimestamps();
    }

    public function teams()
    {
        return $this->hasMany(\App\Models\UserTeam::class, 'user_id');
    }
}
