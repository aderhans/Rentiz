<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pesanan extends Model
{
    use HasFactory;

<<<<<<< HEAD
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
=======
    public $incrementing = false;
    protected $keyType = 'string';
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

<<<<<<< HEAD
=======
    protected $table = 'pesanan';
    protected $guarded = [];

    protected $casts = [
        'payment_timestamp' => 'datetime',
        'total_biaya' => 'decimal:2',
    ];

>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
    public function pemesan()
    {
        return $this->belongsTo(User::class, 'pemesan_id');
    }

<<<<<<< HEAD
=======
    public function pemilik()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
    public function items()
    {
        return $this->hasMany(PesananItem::class, 'pesanan_id');
    }
<<<<<<< HEAD
=======

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
}
