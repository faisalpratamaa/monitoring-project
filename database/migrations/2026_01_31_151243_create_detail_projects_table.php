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
        Schema::create('detail_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahapan_id')->constrained('tahapans');
            $table->foreignId('project_id')->constrained('master_projects');
            $table->integer('bobot');
            $table->integer('progres')->default(0);
            $table->integer('nilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_projects');
    }
};
