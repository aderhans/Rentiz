<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('pesanan_id')->nullable();
            $table->uuid('dispute_id')->nullable();

            // Penerima notifikasi berdasarkan role
            $table->enum('untuk_role', ['penyewa', 'penyedia', 'admin']);

            $table->enum('tipe', [
                // === PENYEWA ===
                'pembayaran_berhasil',      // Pembayaran sukses, escrow aktif
                'pesanan_dikonfirmasi',     // Penyedia konfirmasi pesanan
                'pesanan_dibatalkan',       // Penyedia batalkan pesanan
                'pengingat_h24',            // H-24 jam sebelum batas pengembalian
                'pengingat_h3',             // H-3 jam sebelum batas pengembalian
                'denda_aktif',              // Melewati batas waktu, denda berjalan
                'laporan_diterima',         // Laporan berhasil dikirim ke admin
                'pertimbangan_diberikan',   // Admin sudah tulis pertimbangan (penyewa & penyedia)
                'laporan_ditutup',          // Laporan ditutup admin

                // === PENYEDIA ===
                'pesanan_masuk',            // Ada pesanan baru masuk
                'escrow_cair',              // Dana escrow sudah dicairkan
                'barang_diverifikasi',      // Barang lolos verifikasi admin
                'barang_ditolak',           // Barang ditolak admin

                // === ADMIN ===
                'barang_pending',           // Ada barang baru menunggu verifikasi
                'laporan_masuk'             // Ada laporan baru dari penyewa
            ]);

            $table->string('judul');
            $table->text('pesan');
            $table->enum('channel', [
                'in_app',
                'whatsapp',
                'email'
            ])->default('in_app');
            $table->json('data_payload')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('dikirim_at')->useCurrent();
            $table->timestamp('dibaca_at')->nullable();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('pesanan_id')
                  ->references('id')
                  ->on('pesanan')
                  ->onDelete('set null');



            $table->index('user_id');
            $table->index('untuk_role');
            $table->index('is_read');
            $table->index('tipe');
            $table->index('dikirim_at');

            // Index kombinasi untuk query notifikasi per user yang belum dibaca
            $table->index(['user_id', 'is_read', 'untuk_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
