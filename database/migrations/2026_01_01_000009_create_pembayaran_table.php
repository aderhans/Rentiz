<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pesanan_id')->unique();
            $table->string('kode_bayar')->nullable();
            $table->enum('metode_bayar', [
                'transfer_bank',
                'dompet_digital',
                'virtual_account'
            ])->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->string('midtrans_token')->nullable();
            $table->string('midtrans_redirect_url')->nullable();
            $table->json('webhook_data')->nullable();
            $table->enum('status', [
                'pending',
                'paid',
                'expired',
                'failed',
                'refunded'
            ])->default('pending');
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('dibayar_at')->nullable();
            $table->timestamps();

            $table->foreign('pesanan_id')
                  ->references('id')
                  ->on('pesanan')
                  ->onDelete('cascade');

            $table->index('status');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
