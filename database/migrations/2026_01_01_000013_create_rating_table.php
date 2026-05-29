<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rating', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pesanan_item_id')->unique();
            $table->uuid('user_id');
            $table->uuid('barang_id');

            // nilai antara 1-5
            $table->tinyInteger('nilai')->unsigned();
            $table->text('ulasan')->nullable();
            $table->timestamps();

            $table->foreign('pesanan_item_id')
                  ->references('id')
                  ->on('pesanan_item')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('barang_id')
                  ->references('id')
                  ->on('barang')
                  ->onDelete('cascade');

            $table->index('barang_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating');
    }
};
