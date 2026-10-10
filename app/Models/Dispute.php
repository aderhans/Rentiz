<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dispute extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $table = 'dispute';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
=======
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
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
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

<<<<<<< HEAD
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

=======
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
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
<<<<<<< HEAD

    public function pesananItem()
    {
        return $this->belongsTo(PesananItem::class, 'pesanan_item_id');
    }
=======
>>>>>>> 9b97ebf48e4c35675c2ebcafdd0b258b0ffdae31
}
