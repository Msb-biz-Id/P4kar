<?php

namespace Tests\Feature;

use App\Models\Aturan;
use App\Models\Gejala;
use App\Models\Hasil;
use App\Models\Penyakit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnosaTest extends TestCase
{
    use RefreshDatabase;

    public function test_diagnosa_proses_handles_exact_match_forward_chaining()
    {
        // 1. Setup Data Penyakit & Gejala
        $penyakit = Penyakit::create([
            'kode_penyakit' => 'P01',
            'nama_penyakit' => 'Anemia',
            'keterangan' => 'Kekurangan sel darah merah.',
        ]);

        $g1 = Gejala::create(['kode_gejala' => 'G01', 'nama_gejala' => 'Letih, lemah dan lesu']);
        $g2 = Gejala::create(['kode_gejala' => 'G02', 'nama_gejala' => 'Wajah tampak pucat']);
        $g3 = Gejala::create(['kode_gejala' => 'G03', 'nama_gejala' => 'Sering pusing']);

        // 2. Setup Aturan R01 (IF G01 AND G02 AND G03 THEN Anemia)
        $aturan = Aturan::create([
            'kode_aturan' => 'R01',
            'nama_aturan' => 'Aturan Anemia Klinis',
            'penyakit_id' => $penyakit->id,
            'keterangan' => 'IF G01 AND G02 AND G03 THEN Anemia',
            'is_active' => true,
        ]);
        $aturan->gejalas()->sync([$g1->id, $g2->id, $g3->id]);

        // 3. Pasien memilih semua premis G01, G02, G03
        $response = $this->post(route('diagnosa.proses'), [
            'nama_pasien' => 'Siti Aminah',
            'alamat_pasien' => 'Jakarta Selatan',
            'gejala' => [$g1->id, $g2->id, $g3->id],
        ]);

        $hasil = Hasil::first();
        $this->assertNotNull($hasil);

        $response->assertRedirect(route('diagnosa.hasil', $hasil->id));

        // 4. Verifikasi Hasil di Database
        $this->assertDatabaseHas('hasil', [
            'nama_pasien' => 'Siti Aminah',
            'penyakit_id' => $penyakit->id,
            'aturan_terpilih_id' => $aturan->id,
            'status_kesimpulan' => 'cocok_penuh',
        ]);

        $detail = $hasil->detail_inferensi;
        $this->assertIsArray($detail);
        $this->assertEquals('cocok_penuh', $detail['status_kesimpulan']);
        $this->assertEquals(100.0, $detail['evaluasi_aturan'][0]['match_percentage']);

        // 5. Cek halaman hasil render dengan baik
        $resultPage = $this->get(route('diagnosa.hasil', $hasil->id));
        $resultPage->assertStatus(200);
        $resultPage->assertSee('Anemia');
        $resultPage->assertSee('Aturan Terpenuhi 100%');
    }

    public function test_diagnosa_proses_handles_partial_match_forward_chaining()
    {
        $penyakit = Penyakit::create([
            'kode_penyakit' => 'P02',
            'nama_penyakit' => 'Pankreatitis',
            'keterangan' => 'Peradangan pankreas.',
        ]);

        $g1 = Gejala::create(['kode_gejala' => 'G08', 'nama_gejala' => 'Nyeri perut menjalar']);
        $g2 = Gejala::create(['kode_gejala' => 'G09', 'nama_gejala' => 'Nyeri perut saat disentuh']);
        $g3 = Gejala::create(['kode_gejala' => 'G10', 'nama_gejala' => 'Nyeri parah setelah makan']);
        $g4 = Gejala::create(['kode_gejala' => 'G13', 'nama_gejala' => 'Mual dan muntah']);

        // Aturan butuh 4 gejala
        $aturan = Aturan::create([
            'kode_aturan' => 'R04',
            'nama_aturan' => 'Pankreatitis Akut',
            'penyakit_id' => $penyakit->id,
            'is_active' => true,
        ]);
        $aturan->gejalas()->sync([$g1->id, $g2->id, $g3->id, $g4->id]);

        // Pasien hanya memilih 3 dari 4 gejala (75% match, >= 50%)
        $response = $this->post(route('diagnosa.proses'), [
            'nama_pasien' => 'Dewi Sartika',
            'alamat_pasien' => 'Bandung',
            'gejala' => [$g1->id, $g2->id, $g3->id],
        ]);

        $hasil = Hasil::first();
        $this->assertNotNull($hasil);
        $response->assertRedirect(route('diagnosa.hasil', $hasil->id));

        $this->assertDatabaseHas('hasil', [
            'nama_pasien' => 'Dewi Sartika',
            'penyakit_id' => $penyakit->id,
            'status_kesimpulan' => 'parsial',
        ]);

        $detail = $hasil->detail_inferensi;
        $this->assertEquals('parsial', $detail['status_kesimpulan']);
        $this->assertEquals(75.0, $detail['evaluasi_aturan'][0]['match_percentage']);

        $resultPage = $this->get(route('diagnosa.hasil', $hasil->id));
        $resultPage->assertStatus(200);
        $resultPage->assertSee('Kecocokan Parsial');
    }

    public function test_diagnosa_proses_handles_under_threshold_forward_chaining()
    {
        $penyakit = Penyakit::create([
            'kode_penyakit' => 'P03',
            'nama_penyakit' => 'Diabetes Melitus',
        ]);

        $g1 = Gejala::create(['kode_gejala' => 'G14', 'nama_gejala' => 'Trias DM']);
        $g2 = Gejala::create(['kode_gejala' => 'G15', 'nama_gejala' => 'Mudah lelah']);
        $g3 = Gejala::create(['kode_gejala' => 'G16', 'nama_gejala' => 'Penglihatan kabur']);
        $g4 = Gejala::create(['kode_gejala' => 'G17', 'nama_gejala' => 'Penurunan BB']);

        $aturan = Aturan::create([
            'kode_aturan' => 'R05',
            'nama_aturan' => 'Diabetes Melitus Gestasional',
            'penyakit_id' => $penyakit->id,
            'is_active' => true,
        ]);
        $aturan->gejalas()->sync([$g1->id, $g2->id, $g3->id, $g4->id]);

        // Pasien hanya memilih 1 gejala (25% match, < 50%)
        $response = $this->post(route('diagnosa.proses'), [
            'nama_pasien' => 'Nurul',
            'gejala' => [$g1->id],
        ]);

        $hasil = Hasil::first();
        $this->assertNotNull($hasil);

        $this->assertDatabaseHas('hasil', [
            'nama_pasien' => 'Nurul',
            'penyakit_id' => null,
            'status_kesimpulan' => 'tidak_cocok',
        ]);

        $resultPage = $this->get(route('diagnosa.hasil', $hasil->id));
        $resultPage->assertStatus(200);
        $resultPage->assertSee('Gejala Tidak Mengarah Spesifik');
    }

    public function test_diagnosa_proses_handles_multi_match_forward_chaining()
    {
        $penyakitA = Penyakit::create(['kode_penyakit' => 'P01', 'nama_penyakit' => 'Penyakit A']);
        $penyakitB = Penyakit::create(['kode_penyakit' => 'P02', 'nama_penyakit' => 'Penyakit B']);

        $g1 = Gejala::create(['kode_gejala' => 'G01', 'nama_gejala' => 'Gejala 1']);
        $g2 = Gejala::create(['kode_gejala' => 'G02', 'nama_gejala' => 'Gejala 2']);
        $g3 = Gejala::create(['kode_gejala' => 'G03', 'nama_gejala' => 'Gejala 3']);
        $g4 = Gejala::create(['kode_gejala' => 'G04', 'nama_gejala' => 'Gejala 4']);

        $aturanA = Aturan::create([
            'kode_aturan' => 'R01',
            'nama_aturan' => 'Aturan A',
            'penyakit_id' => $penyakitA->id,
            'is_active' => true,
        ]);
        $aturanA->gejalas()->sync([$g1->id, $g2->id]);

        $aturanB = Aturan::create([
            'kode_aturan' => 'R02',
            'nama_aturan' => 'Aturan B',
            'penyakit_id' => $penyakitB->id,
            'is_active' => true,
        ]);
        $aturanB->gejalas()->sync([$g3->id, $g4->id]);

        // Input pasien memenuhi kedua aturan
        $response = $this->post(route('diagnosa.proses'), [
            'nama_pasien' => 'Rina',
            'gejala' => [$g1->id, $g2->id, $g3->id, $g4->id],
        ]);

        $hasil = Hasil::first();
        $this->assertEquals('multi_match', $hasil->status_kesimpulan);
    }

    public function test_diagnosa_proses_validates_required_fields()
    {
        $response = $this->post(route('diagnosa.proses'), []);
        $response->assertSessionHasErrors(['nama_pasien', 'gejala']);
    }
}
