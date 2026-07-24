<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('isi');
            $table->string('kategori')->default('pengumuman');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('file_path')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['kategori', 'is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};
