<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menyimpan backup biner (Base64) untuk setiap file gambar yang di-upload
     * agar tidak pernah hilang meskipun container cloud (Render/PaaS) mengalami restart/ephemeral reset.
     */
    public function up(): void
    {
        if (!Schema::hasTable('persistent_files')) {
            Schema::create('persistent_files', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('path')->unique();
                $table->string('mime_type', 100)->default('image/jpeg');
                $table->longText('content');
                $table->timestamps();

                $table->index('path');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persistent_files');
    }
};
