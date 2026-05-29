<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_serah_terima', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pesanan_item_id');
            $table->uuid('diisi_oleh');
            $table->uuid('diverifikasi_oleh')->nullable();

            // tipe: pengambilan = saat ambil barang, pengembalian = saat kembalikan
            $table->enum('tipe', ['pengambilan', 'pengembalian']);

            // kondisi_barang menyimpan hasil checklist dalam format JSON
            // contoh: {"kelengkapan": "lengkap", "kondisi_fisik": "baik", "fungsionalitas": "normal"}
            $table->json('kondisi_barang');

            $table->string('foto_bukti')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status_verifikasi', [
                'pending',
                'verified',
                'disputed'
            ])->default('pending');

            // dicatat_at tidak menggunakan updated_at
            // karena data checklist bersifat immutable setelah disimpan
            $table->timestamp('dicatat_at')->useCurrent();

            $table->foreign('pesanan_item_id')
                  ->references('id')
                  ->on('pesanan_item')
                  ->onDelete('cascade');

            $table->foreign('diisi_oleh')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('diverifikasi_oleh')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            // Satu pesanan_item hanya boleh punya 1 checklist pengambilan
            // dan 1 checklist pengembalian
            $table->unique(['pesanan_item_id', 'tipe']);

            $table->index('pesanan_item_id');
            $table->index('tipe');
            $table->index('status_verifikasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_serah_terima');
    }
};
