<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class Wishlist extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;
    protected $keyType = 'string';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Otomatis adaptasi jika nama tabel adalah 'wishlist' atau 'wishlists'
        try {
            if (Schema::hasTable('wishlist')) {
                $this->setTable('wishlist');
            } elseif (Schema::hasTable('wishlists')) {
                $this->setTable('wishlists');
            } else {
                $this->setTable('wishlist');
            }
        } catch (\Throwable $e) {
            $this->setTable('wishlist');
        }
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
