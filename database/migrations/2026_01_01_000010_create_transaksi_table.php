<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pesanan_id')->unique();
            $table->uuid('pembayaran_id')->unique();
            $table->enum('status_escrow', [
                'ditahan',
                'diteruskan',
                'direfund',
                'sebagian_direfund'
            ])->default('ditahan');
            $table->timestamp('selesai_at')->nullable();
            $table->timestamp('refund_at')->nullable();
            $table->timestamps();

            $table->foreign('pesanan_id')
                  ->references('id')
                  ->on('pesanan')
                  ->onDelete('cascade');

            $table->foreign('pembayaran_id')
                  ->references('id')
                  ->on('pembayaran')
                  ->onDelete('cascade');

            $table->index('status_escrow');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
