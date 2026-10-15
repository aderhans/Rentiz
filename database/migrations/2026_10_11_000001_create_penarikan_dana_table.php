<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('penarikan_dana')) {
            Schema::create('penarikan_dana', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id');
                $table->decimal('jumlah', 12, 2);
                $table->decimal('biaya_admin', 12, 2)->default(2500);
                $table->decimal('jumlah_diterima', 12, 2);
                $table->string('bank_tujuan')->default('BCA');
                $table->string('nomor_rekening');
                $table->string('atas_nama');
                $table->string('status')->default('selesai');
                $table->timestamps();

                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');

                $table->index('user_id');
                $table->index('status');
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikan_dana');
    }
};
