<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Aturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimulasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Penguji',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        $penyakit = Penyakit::create(['kode_penyakit' => 'P01', 'nama_penyakit' => 'Anemia']);
        $g1 = Gejala::create(['kode_gejala' => 'G01', 'nama_gejala' => 'Letih']);
        $g2 = Gejala::create(['kode_gejala' => 'G02', 'nama_gejala' => 'Pucat']);

        $aturan = Aturan::create([
            'kode_aturan' => 'R01',
            'nama_aturan' => 'Aturan Anemia',
            'penyakit_id' => $penyakit->id,
            'is_active' => true,
        ]);
        $aturan->gejalas()->sync([$g1->id, $g2->id]);
    }

    public function test_guest_cannot_access_simulasi_page()
    {
        $response = $this->get(route('admin.simulasi.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_simulasi_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.simulasi.index'));
        $response->assertStatus(200);
        $response->assertSee('Uji Blackbox, Aturan & Validasi');
    }

    public function test_simulasi_proses_returns_json_evaluation()
    {
        $g1 = Gejala::where('kode_gejala', 'G01')->first();
        $g2 = Gejala::where('kode_gejala', 'G02')->first();

        $response = $this->actingAs($this->admin)->postJson(route('admin.simulasi.proses'), [
            'gejala' => [$g1->id, $g2->id],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'status_kesimpulan' => 'cocok_penuh',
                'penyakit' => [
                    'nama_penyakit' => 'Anemia',
                ],
                'aturan_terpilih' => [
                    'kode_aturan' => 'R01',
                    'match_percentage' => 100,
                ],
            ],
        ]);
    }
}
