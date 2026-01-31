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
        Schema::create('progres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_tahapan_id')->constrained('sub_tahapans');
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained('users');
            $table->string('file')->nullable();
            $table->integer('nilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres');
    }
};
