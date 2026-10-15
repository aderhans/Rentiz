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
        'foto_profil',
        'status',
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
                return 'Unknown';
        }
    }

    public function pesananSebagaiPemesan()
    {
        return $this->hasMany(Pesanan::class, 'pemesan_id');
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class, 'user_id');
    }

    public function pesananSebagaiPemilik()
    {
        // A user has many Barang. A Barang has many PesananItem. A PesananItem belongs to Pesanan.
        // In eloquent, hasManyThrough is for (Final, Intermediate). Final = PesananItem, Intermediate = Barang.
        // It's easiest to count PesananItem.
        return $this->hasManyThrough(PesananItem::class, Barang::class, 'user_id', 'barang_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'user_id');
    }
}
