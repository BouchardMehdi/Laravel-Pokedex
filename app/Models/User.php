<?php

namespace App\Models;

use App\Notifications\CustomResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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

    // Pokemons unlocked
    public function pokemons()
    {
        return $this->belongsToMany(Pokemon::class)->withTimestamps();
    }

    // Teams
    public function teams()
    {
        return $this->hasMany(UserTeam::class, 'user_id');
    }

    // Reset password mail
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}