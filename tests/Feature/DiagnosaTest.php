<?php

namespace Tests\Feature;

use App\Models\Gejala;
use App\Models\Penyakit;
use App\Models\Relasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosaTest extends TestCase
{
    use RefreshDatabase;

    public function test_diagnosa_proses_calculates_correct_naive_bayes_probability()
    {
        // 1. Setup Mock Diseases
        $penyakitA = Penyakit::create([
            'nama_penyakit' => 'Penyakit A',
            'keterangan' => 'Keterangan A',
        ]);
        $penyakitB = Penyakit::create([
            'nama_penyakit' => 'Penyakit B',
            'keterangan' => 'Keterangan B',
        ]);

        // 2. Setup Mock Symptoms
        $gejala1 = Gejala::create(['nama_gejala' => 'Gejala 1']);
        $gejala2 = Gejala::create(['nama_gejala' => 'Gejala 2']);
        $gejala3 = Gejala::create(['nama_gejala' => 'Gejala 3']);

        // 3. Setup Relations with specific conditional probabilities (weights)
        // Penyakit A: G1=0.8, G2=0.2, G3=0.01 (fallback)
        Relasi::create(['penyakit_id' => $penyakitA->id, 'gejala_id' => $gejala1->id, 'bobot' => 0.8]);
        Relasi::create(['penyakit_id' => $penyakitA->id, 'gejala_id' => $gejala2->id, 'bobot' => 0.2]);

        // Penyakit B: G1=0.1, G2=0.9, G3=0.01 (fallback)
        Relasi::create(['penyakit_id' => $penyakitB->id, 'gejala_id' => $gejala1->id, 'bobot' => 0.1]);
        Relasi::create(['penyakit_id' => $penyakitB->id, 'gejala_id' => $gejala2->id, 'bobot' => 0.9]);

        // Prior P(A) = P(B) = 0.5 (uniform)
        // Let's say user selects G1 and G2:
        // P(G1, G2 | A) = 0.8 * 0.2 = 0.16
        // P(G1, G2 | B) = 0.1 * 0.9 = 0.09
        //
        // Posterior:
        // P(A | G1, G2) = (0.16 * 0.5) / ((0.16 * 0.5) + (0.09 * 0.5))
        //               = 0.08 / 0.125 = 0.64 (64%)
        // P(B | G1, G2) = (0.09 * 0.5) / ((0.16 * 0.5) + (0.09 * 0.5))
        //               = 0.045 / 0.125 = 0.36 (36%)

        $response = $this->post(route('diagnosa.proses'), [
            'nama_pasien' => 'Dewi',
            'alamat_pasien' => 'Jakarta',
            'gejala' => [$gejala1->id, $gejala2->id],
        ]);

        $response->assertStatus(200);

        // Verify Hasil record was created
        $this->assertDatabaseHas('hasil', [
            'nama_pasien' => 'Dewi',
            'alamat_pasien' => 'Jakarta',
            'penyakit_id' => $penyakitA->id, // A has higher probability (64% vs 36%)
        ]);

        // Check if correct probability is stored in database
        $hasil = \App\Models\Hasil::first();
        $this->assertNotNull($hasil);
        
        $probabilitasArray = $hasil->probabilitas;
        $this->assertIsArray($probabilitasArray);

        // Find Penyakit A and Penyakit B probabilities
        $probA = collect($probabilitasArray)->firstWhere('nama_penyakit', 'Penyakit A')['probabilitas'];
        $probB = collect($probabilitasArray)->firstWhere('nama_penyakit', 'Penyakit B')['probabilitas'];

        $this->assertEqualsWithDelta(0.64, $probA, 0.0001);
        $this->assertEqualsWithDelta(0.36, $probB, 0.0001);
    }

    public function test_diagnosa_proses_handles_under_threshold_rule()
    {
        // Setup 3 diseases to make maximum uniform probability 33.3% (< 50%)
        $penyakitA = Penyakit::create(['nama_penyakit' => 'Penyakit A']);
        $penyakitB = Penyakit::create(['nama_penyakit' => 'Penyakit B']);
        $penyakitC = Penyakit::create(['nama_penyakit' => 'Penyakit C']);

        $gejala1 = Gejala::create(['nama_gejala' => 'Gejala 1']);

        // Set equal weights
        Relasi::create(['penyakit_id' => $penyakitA->id, 'gejala_id' => $gejala1->id, 'bobot' => 0.5]);
        Relasi::create(['penyakit_id' => $penyakitB->id, 'gejala_id' => $gejala1->id, 'bobot' => 0.5]);
        Relasi::create(['penyakit_id' => $penyakitC->id, 'gejala_id' => $gejala1->id, 'bobot' => 0.5]);

        // Prior P(A)=P(B)=P(C)=0.33
        // If user selects G1:
        // P(G1|A)=P(G1|B)=P(G1|C)=0.5
        // All posterior probabilities will be 33.3% (0.333333) which is < 50%

        $response = $this->post(route('diagnosa.proses'), [
            'nama_pasien' => 'Siti',
            'alamat_pasien' => 'Bandung',
            'gejala' => [$gejala1->id],
        ]);

        $response->assertStatus(200);

        // Verify Hasil record was created with null penyakit_id
        $this->assertDatabaseHas('hasil', [
            'nama_pasien' => 'Siti',
            'alamat_pasien' => 'Bandung',
            'penyakit_id' => null,
        ]);

        $hasil = \App\Models\Hasil::first();
        $this->assertNotNull($hasil);
        $this->assertStringContainsString('tidak cukup spesifik', $hasil->solusi);
    }
}
