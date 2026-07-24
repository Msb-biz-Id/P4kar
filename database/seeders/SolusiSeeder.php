<?php

namespace Database\Seeders;

use App\Models\Solusi;
use App\Models\Penyakit;
use Illuminate\Database\Seeder;

class SolusiSeeder extends Seeder
{
    public function run()
    {
        $penyakit1 = Penyakit::where('nama_penyakit', 'Anemia')->first();
        $penyakit2 = Penyakit::where('nama_penyakit', 'Pankreatitis')->first();
        $penyakit3 = Penyakit::where('nama_penyakit', 'Diabetes Melitus')->first();

        if (!$penyakit1 || !$penyakit2 || !$penyakit3) {
            $this->command->error('Penyakit tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');
            return;
        }

        $solusiData = [
            [
                'penyakit_id' => $penyakit1->id,
                'judul_solusi' => 'Pola Makan Sehat',
                'deskripsi_solusi' => 'Makan makanan bergizi seimbang dengan porsi yang cukup. Perbanyak konsumsi sayuran, buah-buahan, protein, dan karbohidrat kompleks. Hindari makanan mentah, setengah matang, atau olahan yang tidak higienis.'
            ],
            [
                'penyakit_id' => $penyakit1->id,
                'judul_solusi' => 'Suplementasi Zat Besi',
                'deskripsi_solusi' => 'Konsumsi suplemen zat besi sesuai dosis yang dianjurkan dokter. Disertai dengan makanan tinggi vitamin C untuk meningkatkan penyerapan zat besi.'
            ],
            [
                'penyakit_id' => $penyakit2->id,
                'judul_solusi' => 'Pengawasan Medis Ketat',
                'deskripsi_solusi' => 'Lakukan pemeriksaan kehamilan secara rutin. Monitor tekanan darah secara berkala dan lakukan tes urine untuk mendeteksi proteinuria.'
            ],
            [
                'penyakit_id' => $penyakit2->id,
                'judul_solusi' => 'Pola Makan Rendah Garam',
                'deskripsi_solusi' => 'Batas asupan garam harian maksimal 6 gram. Hindari makanan olahan, makanan kemasan, dan makanan cepat saji yang tinggi sodium.'
            ],
            [
                'penyakit_id' => $penyakit3->id,
                'judul_solusi' => 'Pengaturan Pola Makan',
                'deskripsi_solusi' => 'Kontrol asupan karbohidrat dengan indeks glikemik rendah. Hindari makanan manis dan olahan. Konsumsi makanan berserat tinggi untuk mengontrol kadar gula darah.'
            ],
            [
                'penyakit_id' => $penyakit3->id,
                'judul_solusi' => 'Olahraga Teratur',
                'deskripsi_solusi' => 'Lakukan olahraga ringan seperti jalan kaki 30 menit setiap hari. Olahraga membantu meningkatkan sensitivitas insulin dan mengontrol berat badan.'
            ],
        ];

        foreach ($solusiData as $data) {
            Solusi::create($data);
        }

        $this->command->info('Solusi berhasil di-seed!');
    }
}
