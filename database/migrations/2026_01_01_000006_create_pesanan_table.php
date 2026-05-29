<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pemesan_id');
            $table->uuid('pemilik_id');
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->text('catatan_penyewa')->nullable();
            $table->enum('status', [
                'pending_payment',
                'paid',
                'confirmed',
                'active',
                'returned',
                'completed',
                'cancelled',
                'refunded',
                'disputed'
            ])->default('pending_payment');
            $table->timestamp('payment_timestamp')->nullable();
            $table->timestamps();

            $table->foreign('pemesan_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('pemilik_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->index('pemesan_id');
            $table->index('pemilik_id');
            $table->index('status');
            $table->index('payment_timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
