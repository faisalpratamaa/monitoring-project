<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_projects', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('name');
            $table->foreignId('kategori_id')->constrained('master_kategoris');
            $table->integer('bobot')->default(0);
            $table->string('target');
            $table->integer('anggaran');
            $table->enum('waktu', ['TW 1', 'TW 2', 'TW 3', 'TW 4']);
            $table->enum('tipe', ['New', 'Carry Over']);
            $table->string('pic');
            $table->string('hp');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_projects');
    }
};
