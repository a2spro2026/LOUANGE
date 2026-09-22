<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const STATUTS = [
        'gerant' => 'Gérant',
        'assistant' => 'Assistant(e)',
        'commercial' => 'Commercial',
        'atelier' => 'Atelier',
        'admin' => 'Admin',
    ];

    protected $fillable = [
        'name',
        'contact',
        'login',
        'statut',
        'actif',
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
            'actif' => 'boolean',
        ];
    }

    public function statutLabel(): string
    {
        return self::STATUTS[$this->statut] ?? (string) $this->statut;
    }
}
