<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'pemesan_id',
        'status',
        'total_biaya',
        'midtrans_snap_token',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function pemesan()
    {
        return $this->belongsTo(User::class, 'pemesan_id');
    }

    public function items()
    {
        return $this->hasMany(PesananItem::class, 'pesanan_id');
    }
}
