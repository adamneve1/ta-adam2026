<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nip',
        'password',
        'role',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function normalizedRole(): ?string
    {
        return match ($this->role) {
            'atasan', 'Kepala Stasiun', 'kepsta' => 'kepala_stasiun',
            default => $this->role,
        };
    }

    public function roleLabel(): string
    {
        return match ($this->normalizedRole()) {
            'admin' => 'Admin Sistem',
            'kepala_stasiun' => 'Kepala Stasiun',
            'lpu' => 'LPU',
            'penyetor' => 'Penyetor',
            default => $this->role ?: '-',
        };
    }

    public function isAdmin(): bool
    {
        return $this->normalizedRole() === 'admin';
    }

    public function isAtasan(): bool
    {
        return $this->normalizedRole() === 'kepala_stasiun';
    }

    public function isKepsta(): bool
    {
        return $this->isAtasan();
    }

    public function isLpu(): bool
    {
        return $this->normalizedRole() === 'lpu';
    }

    public function isPenyetor(): bool
    {
        return $this->normalizedRole() === 'penyetor';
    }
}
