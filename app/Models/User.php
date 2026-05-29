<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Disable auto-incrementing since we use UUIDs.
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Set the key type to string.
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Boot function from Laravel.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* --------------------------------------------------------
     * Role Helper Methods
     * -------------------------------------------------------- */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPenyedia(): bool
    {
        return $this->role === 'penyedia';
    }

    public function isPenyewa(): bool
    {
        return $this->role === 'penyewa';
    }

    /**
     * Get human-readable role label.
     */
    public function getRoleLabelAttribute(): string
    {
        switch ($this->role) {
            case 'admin':
                return 'Administrator';
            case 'penyedia':
                return 'Penyedia Barang';
            case 'penyewa':
                return 'Penyewa Barang';
            default:
                return 'Unknown';
        }
    }
}
