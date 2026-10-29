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
        Schema::table('aturan', function (Blueprint $table) {
            if (!Schema::hasColumn('aturan', 'operator')) {
                // Operator logika premis: AND (default) atau OR sesuai basis pengetahuan Excel
                // (contoh: aturan R13 "G35 OR G36 OR G40").
                $table->string('operator', 3)->default('AND')->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aturan', function (Blueprint $table) {
            if (Schema::hasColumn('aturan', 'operator')) {
                $table->dropColumn('operator');
            }
        });
    }
};
