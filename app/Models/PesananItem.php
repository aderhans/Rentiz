<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PesananItem extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $table = 'pesanan_item';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'pesanan_id',
        'barang_id',
        'kuantitas',
        'harga_satuan',
        'subtotal',
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
    protected $table = 'pesanan_item';

    protected $guarded = [];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'harga_per_hari' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
<<<<<<< HEAD
=======

    public function inventoryLock()
    {
        return $this->hasOne(InventoryLock::class);
    }

    public function checklistPengambilan()
    {
        return $this->hasOne(ChecklistSerahTerima::class)->where('tipe', 'pengambilan');
    }
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
}
