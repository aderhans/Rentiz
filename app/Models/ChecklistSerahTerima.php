<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class ChecklistSerahTerima extends Model
{
    protected $table = 'checklist_serah_terima';
    protected $guarded = [];
    public $timestamps = false; // Karena migration hanya pakai dicatat_at

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

    protected $casts = [
        'kondisi_barang' => 'array',
        'dicatat_at' => 'datetime'
    ];

    public function pesananItem() {
        return $this->belongsTo(PesananItem::class);
    }

    public function diisiOleh() {
        return $this->belongsTo(User::class, 'diisi_oleh');
    }
}
