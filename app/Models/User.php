<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
            'role' => \App\Enums\UserRole::class,
        ];
    }

    public function isOwner(): bool
    {
        return $this->role === \App\Enums\UserRole::OWNER || $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        $roleValue = $this->role instanceof \App\Enums\UserRole ? $this->role->value : (string) $this->role;
        return in_array($roleValue, ['owner', 'admin']);
    }
}
