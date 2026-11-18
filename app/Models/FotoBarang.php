<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FotoBarang extends Model
{
    use HasFactory;

    protected $table = 'foto_barang';

    protected $fillable = [
        'barang_id',
        'path_foto',
        'is_primary',
    ];

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });

        static::saved(function ($model) {
            if (!empty($model->path_foto)) {
                PersistentFile::storeFromPublicDisk($model->path_foto);
            }
        });

        static::deleted(function ($model) {
            if (!empty($model->path_foto)) {
                PersistentFile::deleteByPath($model->path_foto);
            }
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
