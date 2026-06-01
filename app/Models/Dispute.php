<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dispute extends Model
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

    protected $table = 'dispute';

    protected $fillable = [
        'pelapor_id',
        'terlapor_id',
        'pesanan_id',
        'pesanan_item_id',
        'kategori_laporan',
        'deskripsi',
        'bukti_foto',
        'status',
        'pertimbangan_admin',
        'ditangani_oleh',
        'ditutup_at',
    ];

    protected $casts = [
        'ditutup_at' => 'datetime',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function terlapor()
    {
        return $this->belongsTo(User::class, 'terlapor_id');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
