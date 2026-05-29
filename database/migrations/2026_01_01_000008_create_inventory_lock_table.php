<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_lock', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('barang_id');
            $table->uuid('pesanan_item_id')->nullable();
            $table->uuid('user_id');
            $table->timestamp('expired_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('barang_id')
                  ->references('id')
                  ->on('barang')
                  ->onDelete('cascade');

            $table->foreign('pesanan_item_id')
                  ->references('id')
                  ->on('pesanan_item')
                  ->onDelete('set null');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index('barang_id');
            $table->index('is_active');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_lock');
    }
};
