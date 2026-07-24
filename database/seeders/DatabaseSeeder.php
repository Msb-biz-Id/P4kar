<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Relasi;
use App\Models\Solusi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Create diseases (Tabel 3.1)
        $anemia = Penyakit::create([
            'nama_penyakit' => 'Anemia',
            'keterangan' => 'Anemia pada ibu hamil perlu ditangani dengan peningkatan asupan makanan kaya zat besi, suplementasi tablet besi, dan monitoring rutin. Pada kasus berat, transfusi darah mungkin diperlukan. Konsultasikan dengan dokter kandungan untuk penanganan lebih lanjut.'
        ]);
        $pankreatitis = Penyakit::create([
            'nama_penyakit' => 'Pankreatitis',
            'keterangan' => 'Pankreatitis pada ibu hamil memerlukan penanganan medis segera. Penanganan meliputi puasa, pemberian cairan intravena, pengendalian rasa nyeri, dan monitoring ketat. Hindari makanan berlemak dan berat. Segera ke IGD jika nyeri hebat disertai muntah.'
        ]);
        $diabetes = Penyakit::create([
            'nama_penyakit' => 'Diabetes Melitus',
            'keterangan' => 'Diabetes melitus pada ibu hamil memerlukan pengelolaan gula darah yang ketat. Penanganan meliputi diet terkontrol, olahraga teratur, monitoring gula darah rutin, dan pemberian insulin jika diperlukan. Kontrol ke dokter spesialis kandungan dan endokrin secara berkala.'
        ]);

        // Create 20 symptoms (Tabel 3.3)
        $gejala = [
            Gejala::create(['nama_gejala' => 'Letih, lemah dan lesu']),
            Gejala::create(['nama_gejala' => 'Lelah, lalai dan wajah tampak pucat']),
            Gejala::create(['nama_gejala' => 'Sering pusing, sulit konsentrasi dan mudah lupa']),
            Gejala::create(['nama_gejala' => 'Mata berkunang-kunang dan cenderung sering sakit']),
            Gejala::create(['nama_gejala' => 'Selera makan berkurang']),
            Gejala::create(['nama_gejala' => 'Napas pendek (pada anemia berat)']),
            Gejala::create(['nama_gejala' => 'Keluhan mual muntah lebih hebat pada kehamilan muda']),
            Gejala::create(['nama_gejala' => 'Nyeri terus menerus pada area perut yang menjalar ke punggung/bahu']),
            Gejala::create(['nama_gejala' => 'Nyeri perut saat disentuh']),
            Gejala::create(['nama_gejala' => 'Nyeri terasa semakin parah setelah makan']),
            Gejala::create(['nama_gejala' => 'Demam']),
            Gejala::create(['nama_gejala' => 'Denyut nadi cepat']),
            Gejala::create(['nama_gejala' => 'Mual dan muntah']),
            Gejala::create(['nama_gejala' => 'Trias Diabetes Melitus (sering haus, banyak makan, sering kencing)']),
            Gejala::create(['nama_gejala' => 'Mudah lelah dan mengantuk']),
            Gejala::create(['nama_gejala' => 'Penglihatan kabur']),
            Gejala::create(['nama_gejala' => 'Penurunan berat badan meskipun nafsu makan meningkat']),
            Gejala::create(['nama_gejala' => 'Masalah pada kulit (gatal-gatal, iritasi)']),
            Gejala::create(['nama_gejala' => 'Kesemutan']),
            Gejala::create(['nama_gejala' => 'Bila terdapat luka lebih sulit sembuh']),
        ];

        // Decision Table (Tabel Keputusan) - Naive Bayes weights
        // 1.0 = Gejala TAHU (YA) untuk penyakit tersebut
        $relasiData = [
            // Anemia: G01-G07 = 1.0, G08-G20 = 0.01 (tidak terkait)
            [$anemia->id, $gejala[0]->id, 1.0],   // G01
            [$anemia->id, $gejala[1]->id, 1.0],   // G02
            [$anemia->id, $gejala[2]->id, 1.0],   // G03
            [$anemia->id, $gejala[3]->id, 1.0],   // G04
            [$anemia->id, $gejala[4]->id, 1.0],   // G05
            [$anemia->id, $gejala[5]->id, 1.0],   // G06
            [$anemia->id, $gejala[6]->id, 1.0],   // G07
            [$anemia->id, $gejala[7]->id, 0.01],  // G08
            [$anemia->id, $gejala[8]->id, 0.01],  // G09
            [$anemia->id, $gejala[9]->id, 0.01],  // G10
            [$anemia->id, $gejala[10]->id, 0.01], // G11
            [$anemia->id, $gejala[11]->id, 0.01], // G12
            [$anemia->id, $gejala[12]->id, 0.01], // G13
            [$anemia->id, $gejala[13]->id, 0.01], // G14
            [$anemia->id, $gejala[14]->id, 0.01], // G15
            [$anemia->id, $gejala[15]->id, 0.01], // G16
            [$anemia->id, $gejala[16]->id, 0.01], // G17
            [$anemia->id, $gejala[17]->id, 0.01], // G18
            [$anemia->id, $gejala[18]->id, 0.01], // G19
            [$anemia->id, $gejala[19]->id, 0.01], // G20

            // Pankreatitis: G08-G13 = 1.0, lainnya = 0.01
            [$pankreatitis->id, $gejala[0]->id, 0.01],  // G01
            [$pankreatitis->id, $gejala[1]->id, 0.01],  // G02
            [$pankreatitis->id, $gejala[2]->id, 0.01],  // G03
            [$pankreatitis->id, $gejala[3]->id, 0.01],  // G04
            [$pankreatitis->id, $gejala[4]->id, 0.01],  // G05
            [$pankreatitis->id, $gejala[5]->id, 0.01],  // G06
            [$pankreatitis->id, $gejala[6]->id, 0.01],  // G07
            [$pankreatitis->id, $gejala[7]->id, 1.0],   // G08
            [$pankreatitis->id, $gejala[8]->id, 1.0],   // G09
            [$pankreatitis->id, $gejala[9]->id, 1.0],   // G10
            [$pankreatitis->id, $gejala[10]->id, 1.0],  // G11
            [$pankreatitis->id, $gejala[11]->id, 1.0],  // G12
            [$pankreatitis->id, $gejala[12]->id, 1.0],  // G13
            [$pankreatitis->id, $gejala[13]->id, 0.01], // G14
            [$pankreatitis->id, $gejala[14]->id, 0.01], // G15
            [$pankreatitis->id, $gejala[15]->id, 0.01], // G16
            [$pankreatitis->id, $gejala[16]->id, 0.01], // G17
            [$pankreatitis->id, $gejala[17]->id, 0.01], // G18
            [$pankreatitis->id, $gejala[18]->id, 0.01], // G19
            [$pankreatitis->id, $gejala[19]->id, 0.01], // G20

            // Diabetes Melitus: G14-G20 = 1.0, lainnya = 0.01
            [$diabetes->id, $gejala[0]->id, 0.01],  // G01
            [$diabetes->id, $gejala[1]->id, 0.01],  // G02
            [$diabetes->id, $gejala[2]->id, 0.01],  // G03
            [$diabetes->id, $gejala[3]->id, 0.01],  // G04
            [$diabetes->id, $gejala[4]->id, 0.01],  // G05
            [$diabetes->id, $gejala[5]->id, 0.01],  // G06
            [$diabetes->id, $gejala[6]->id, 0.01],  // G07
            [$diabetes->id, $gejala[7]->id, 0.01],  // G08
            [$diabetes->id, $gejala[8]->id, 0.01],  // G09
            [$diabetes->id, $gejala[9]->id, 0.01],  // G10
            [$diabetes->id, $gejala[10]->id, 0.01], // G11
            [$diabetes->id, $gejala[11]->id, 0.01], // G12
            [$diabetes->id, $gejala[12]->id, 0.01], // G13
            [$diabetes->id, $gejala[13]->id, 1.0],  // G14
            [$diabetes->id, $gejala[14]->id, 1.0],  // G15
            [$diabetes->id, $gejala[15]->id, 1.0],  // G16
            [$diabetes->id, $gejala[16]->id, 1.0],  // G17
            [$diabetes->id, $gejala[17]->id, 1.0],  // G18
            [$diabetes->id, $gejala[18]->id, 1.0],  // G19
            [$diabetes->id, $gejala[19]->id, 1.0],  // G20
        ];

        foreach ($relasiData as $data) {
            Relasi::create([
                'penyakit_id' => $data[0],
                'gejala_id' => $data[1],
                'bobot' => $data[2],
            ]);
        }

        // Create solutions
        Solusi::create([
            'penyakit_id' => $anemia->id,
            'judul_solusi' => 'Pola Makan Sehat',
            'deskripsi_solusi' => 'Makan makanan bergizi seimbang dengan porsi yang cukup. Perbanyak konsumsi sayuran, buah-buahan, protein, dan karbohidrat kompleks. Hindari makanan mentah, setengah matang, atau olahan yang tidak higienis.'
        ]);
        Solusi::create([
            'penyakit_id' => $anemia->id,
            'judul_solusi' => 'Suplementasi Zat Besi',
            'deskripsi_solusi' => 'Konsumsi suplemen zat besi sesuai dosis yang dianjurkan dokter. Disertai dengan makanan tinggi vitamin C untuk meningkatkan penyerapan zat besi.'
        ]);
        Solusi::create([
            'penyakit_id' => $pankreatitis->id,
            'judul_solusi' => 'Pengawasan Medis Ketat',
            'deskripsi_solusi' => 'Lakukan pemeriksaan kehamilan secara rutin. Monitor tekanan darah secara berkala dan lakukan tes urine untuk mendeteksi proteinuria.'
        ]);
        Solusi::create([
            'penyakit_id' => $pankreatitis->id,
            'judul_solusi' => 'Pola Makan Rendah Garam',
            'deskripsi_solusi' => 'Batas asupan garam harian maksimal 6 gram. Hindari makanan olahan, makanan kemasan, dan makanan cepat saji yang tinggi sodium.'
        ]);
        Solusi::create([
            'penyakit_id' => $diabetes->id,
            'judul_solusi' => 'Pengaturan Pola Makan',
            'deskripsi_solusi' => 'Kontrol asupan karbohidrat dengan indeks glikemik rendah. Hindari makanan manis dan olahan. Konsumsi makanan berserat tinggi untuk mengontrol kadar gula darah.'
        ]);
        Solusi::create([
            'penyakit_id' => $diabetes->id,
            'judul_solusi' => 'Olahraga Teratur',
            'deskripsi_solusi' => 'Lakukan olahraga ringan seperti jalan kaki 30 menit setiap hari. Olahraga membantu meningkatkan sensitivitas insulin dan mengontrol berat badan.'
        ]);
    }
}
