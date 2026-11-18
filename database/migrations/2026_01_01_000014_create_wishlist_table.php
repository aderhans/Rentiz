<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wishlist')) {
            Schema::create('wishlist', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('user_id');
                $table->uuid('barang_id');
                $table->timestamps();

                $table->foreign('user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');

                $table->foreign('barang_id')
                      ->references('id')
                      ->on('barang')
                      ->onDelete('cascade');

                $table->unique(['user_id', 'barang_id']);
                $table->index('user_id');
                $table->index('barang_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};
