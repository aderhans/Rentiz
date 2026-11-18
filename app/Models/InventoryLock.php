<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class InventoryLock extends Model
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
    
    protected $table = 'inventory_lock';
    protected $guarded = [];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }

    public function pesananItem() {
        return $this->belongsTo(PesananItem::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
