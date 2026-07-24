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
        Schema::create('solusis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyakit_id')->constrained('penyakit')->onDelete('cascade');
            $table->string('judul_solusi');
            $table->text('deskripsi_solusi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solusis');
    }
};
