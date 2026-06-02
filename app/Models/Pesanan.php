<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected $table = 'pesanan';

    protected $fillable = [
        'pemesan_id',
        'pemilik_id',
        'total_biaya',
        'catatan_penyewa',
        'status',
        'payment_timestamp',
    ];

    protected $casts = [
        'payment_timestamp' => 'datetime',
        'total_biaya' => 'decimal:2',
    ];

    public function pemesan()
    {
        return $this->belongsTo(User::class, 'pemesan_id');
    }

    public function pemilik()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function items()
    {
        return $this->hasMany(PesananItem::class, 'pesanan_id');
    }
}
