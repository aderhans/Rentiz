<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
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
    
    protected $table = 'transaksi';
    protected $guarded = [];

    public function pesanan() {
        return $this->belongsTo(Pesanan::class);
    }

    public function pembayaran() {
        return $this->belongsTo(Pembayaran::class);
    }
}
