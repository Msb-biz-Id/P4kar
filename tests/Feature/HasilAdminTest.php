<?php

namespace Tests\Feature;

use App\Models\Hasil;
use App\Models\Penyakit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasilAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Penyakit $penyakit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Pakar',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        $this->penyakit = Penyakit::create([
            'kode_penyakit' => 'P01',
            'nama_penyakit' => 'Anemia',
        ]);
    }

    public function test_admin_can_view_riwayat_diagnosa_with_delete_buttons()
    {
        $hasil = Hasil::create([
            'nama_pasien' => 'Ibu Fatimah',
            'alamat_pasien' => 'Jakarta Timur',
            'penyakit_id' => $this->penyakit->id,
            'status_kesimpulan' => 'cocok_penuh',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.hasil.index'));

        $response->assertStatus(200);
        $response->assertSee('Ibu Fatimah');
        $response->assertSee('Hapus Riwayat');
        $response->assertSee('Kosongkan Riwayat');
    }

    public function test_admin_can_delete_single_hasil()
    {
        $hasil = Hasil::create([
            'nama_pasien' => 'Ibu Fatimah',
            'alamat_pasien' => 'Jakarta Timur',
            'penyakit_id' => $this->penyakit->id,
            'status_kesimpulan' => 'cocok_penuh',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.hasil.destroy', $hasil));

        $response->assertRedirect(route('admin.hasil.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('hasil', ['id' => $hasil->id]);
    }

    public function test_admin_can_delete_all_hasil()
    {
        Hasil::create([
            'nama_pasien' => 'Pasien 1',
            'penyakit_id' => $this->penyakit->id,
        ]);
        Hasil::create([
            'nama_pasien' => 'Pasien 2',
            'penyakit_id' => $this->penyakit->id,
        ]);

        $this->assertEquals(2, Hasil::count());

        $response = $this->actingAs($this->admin)->delete(route('admin.hasil.destroy-all'));

        $response->assertRedirect(route('admin.hasil.index'));
        $response->assertSessionHas('success');
        $this->assertEquals(0, Hasil::count());
    }

    public function test_guest_cannot_delete_hasil()
    {
        $hasil = Hasil::create([
            'nama_pasien' => 'Pasien Anonim',
            'penyakit_id' => $this->penyakit->id,
        ]);

        $response = $this->delete(route('admin.hasil.destroy', $hasil));
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('hasil', ['id' => $hasil->id]);
    }
}
