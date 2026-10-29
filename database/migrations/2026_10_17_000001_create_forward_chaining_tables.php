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
        // 1. Tambah kode_penyakit ke tabel penyakit
        Schema::table('penyakit', function (Blueprint $table) {
            if (!Schema::hasColumn('penyakit', 'kode_penyakit')) {
                $table->string('kode_penyakit', 10)->nullable()->unique()->after('id');
            }
        });

        // 2. Tambah kode_gejala ke tabel gejala
        Schema::table('gejala', function (Blueprint $table) {
            if (!Schema::hasColumn('gejala', 'kode_gejala')) {
                $table->string('kode_gejala', 10)->nullable()->unique()->after('id');
            }
        });

        // 3. Buat tabel aturan (Basis Aturan / Rule Base Forward Chaining)
        if (!Schema::hasTable('aturan')) {
            Schema::create('aturan', function (Blueprint $table) {
                $table->id();
                $table->string('kode_aturan', 20)->unique();
                $table->string('nama_aturan', 255);
                $table->foreignId('penyakit_id')->constrained('penyakit')->onDelete('cascade');
                $table->text('keterangan')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Buat tabel pivot aturan_gejala (Premis Aturan)
        if (!Schema::hasTable('aturan_gejala')) {
            Schema::create('aturan_gejala', function (Blueprint $table) {
                $table->id();
                $table->foreignId('aturan_id')->constrained('aturan')->onDelete('cascade');
                $table->foreignId('gejala_id')->constrained('gejala')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 5. Tambah kolom penunjang Forward Chaining pada tabel hasil
        Schema::table('hasil', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil', 'status_kesimpulan')) {
                $table->string('status_kesimpulan', 50)->nullable()->after('solusi');
            }
            if (!Schema::hasColumn('hasil', 'detail_inferensi')) {
                $table->json('detail_inferensi')->nullable()->after('status_kesimpulan');
            }
            if (!Schema::hasColumn('hasil', 'aturan_terpilih_id')) {
                $table->foreignId('aturan_terpilih_id')->nullable()->after('penyakit_id')->constrained('aturan')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil', function (Blueprint $table) {
            if (Schema::hasColumn('hasil', 'aturan_terpilih_id')) {
                $table->dropForeign(['aturan_terpilih_id']);
                $table->dropColumn('aturan_terpilih_id');
            }
            if (Schema::hasColumn('hasil', 'detail_inferensi')) {
                $table->dropColumn('detail_inferensi');
            }
            if (Schema::hasColumn('hasil', 'status_kesimpulan')) {
                $table->dropColumn('status_kesimpulan');
            }
        });

        Schema::dropIfExists('aturan_gejala');
        Schema::dropIfExists('aturan');

        Schema::table('gejala', function (Blueprint $table) {
            if (Schema::hasColumn('gejala', 'kode_gejala')) {
                $table->dropColumn('kode_gejala');
            }
        });

        Schema::table('penyakit', function (Blueprint $table) {
            if (Schema::hasColumn('penyakit', 'kode_penyakit')) {
                $table->dropColumn('kode_penyakit');
            }
        });
    }
};
