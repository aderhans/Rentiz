<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('aksi');
            $table->text('detail')->nullable();
            $table->string('ip_address')->nullable();

            // log bersifat immutable, hanya ada created_at
            $table->timestamp('terjadi_at')->useCurrent();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');

            $table->index('user_id');
            $table->index('aksi');
            $table->index('terjadi_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log');
    }
};
