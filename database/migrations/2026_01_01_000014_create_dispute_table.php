<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispute', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Pelapor selalu penyewa, terlapor selalu penyedia
            $table->uuid('pelapor_id');
            $table->uuid('terlapor_id');

            // Nullable karena laporan tidak selalu terkait transaksi
            // contoh: laporan akun mencurigakan sebelum/tanpa transaksi
            $table->uuid('pesanan_id')->nullable();
            $table->uuid('pesanan_item_id')->nullable();

            $table->enum('kategori_laporan', [
                'barang_tidak_sesuai_deskripsi', // Kondisi/spesifikasi barang berbeda dari listing
                'penyedia_tidak_responsif',       // Penyedia tidak konfirmasi atau susah dihubungi
                'pembatalan_sepihak',             // Penyedia batalkan pesanan setelah pembayaran
                'biaya_tidak_sesuai',             // Ada biaya tambahan di luar kesepakatan platform
                'akun_mencurigakan',              // Identitas atau akun penyedia terindikasi palsu
                'perilaku_tidak_pantas',          // Perlakuan buruk dari penyedia
                'lainnya'
            ]);

            $table->text('deskripsi');
            $table->string('bukti_foto')->nullable();

            $table->enum('status', [
                'open',                     // Laporan baru masuk, belum ditangani
                'under_review',             // Sedang ditinjau admin
                'pertimbangan_diberikan',   // Admin sudah tulis pertimbangan, notif dikirim
                'closed'                    // Laporan ditutup
            ])->default('open');

            // Pertimbangan admin — bukan keputusan final
            // Keputusan akhir tetap di tangan kedua pihak
            $table->text('pertimbangan_admin')->nullable();

            $table->uuid('ditangani_oleh')->nullable();
            $table->timestamp('ditutup_at')->nullable();
            $table->timestamps();

            $table->foreign('pelapor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('terlapor_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('pesanan_id')
                  ->references('id')
                  ->on('pesanan')
                  ->onDelete('set null');

            $table->foreign('pesanan_item_id')
                  ->references('id')
                  ->on('pesanan_item')
                  ->onDelete('set null');

            $table->foreign('ditangani_oleh')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('set null');

            $table->index('pelapor_id');
            $table->index('terlapor_id');
            $table->index('status');
            $table->index('kategori_laporan');
        });

        Schema::table('notifikasi', function (Blueprint $table) {
            $table->foreign('dispute_id')
                  ->references('id')
                  ->on('dispute')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropForeign(['dispute_id']);
        });
        Schema::dropIfExists('dispute');
    }
};
