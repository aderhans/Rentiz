<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('kategori_id')->nullable();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('kondisi');
            $table->decimal('harga_per_hari', 12, 2);
            $table->decimal('denda_per_hari', 12, 2)->default(0);
            $table->string('kota');
            $table->text('alamat_pengambilan')->nullable();
            $table->text('ketentuan_jaminan')->nullable();
            $table->integer('min_durasi_sewa')->default(1);
            $table->integer('max_durasi_sewa')->nullable();
            $table->enum('status', [
                'pending',
                'active',
                'rented',
                'unavailable',
                'rejected',
                'suspended'
            ])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('kategori_id')
                  ->references('id')
                  ->on('kategori')
                  ->onDelete('set null');

            $table->index('kota');
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
