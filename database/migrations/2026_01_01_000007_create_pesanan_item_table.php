<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_item', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pesanan_id');
            $table->uuid('barang_id');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('durasi_hari');

            // Snapshot harga saat pemesanan
            // agar tidak berubah jika penyedia update harga di kemudian hari
            $table->decimal('harga_per_hari', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('denda', 12, 2)->default(0);

            $table->enum('status', [
                'pending',
                'confirmed',
                'active',
                'overdue',
                'returned',
                'completed',
                'cancelled',
                'disputed'
            ])->default('pending');

            $table->timestamps();

            $table->foreign('pesanan_id')
                  ->references('id')
                  ->on('pesanan')
                  ->onDelete('cascade');

            $table->foreign('barang_id')
                  ->references('id')
                  ->on('barang')
                  ->onDelete('cascade');

            $table->index('pesanan_id');
            $table->index('barang_id');
            $table->index('status');
            $table->index('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_item');
    }
};
