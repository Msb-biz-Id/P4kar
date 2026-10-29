<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Penyakit;
use App\Models\Gejala;
use App\Models\Aturan;
use App\Models\Relasi;
use App\Models\Solusi;
use App\Models\Hasil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Buat User Admin
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Sistem Pakar',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Buat Data 3 Penyakit (Tabel 3.1)
        // Adopsi baris penyakit lama (dibuat sebelum kolom kode_penyakit ada, kode masih NULL)
        // berdasarkan nama_penyakit agar tidak terduplikasi saat seeding di database lama.
        foreach (['P01' => 'Anemia', 'P02' => 'Pankreatitis', 'P03' => 'Diabetes Melitus'] as $kodeAdopsi => $namaAdopsi) {
            $legacy = Penyakit::whereNull('kode_penyakit')->where('nama_penyakit', $namaAdopsi)->orderBy('id')->first();
            if ($legacy && !Penyakit::where('kode_penyakit', $kodeAdopsi)->exists()) {
                $legacy->update(['kode_penyakit' => $kodeAdopsi]);
            }
        }

        $penyakits = [
            'P01' => Penyakit::updateOrCreate(
                ['kode_penyakit' => 'P01'],
                [
                    'nama_penyakit' => 'Anemia',
                    'keterangan' => 'Kondisi defisiensi sel darah merah atau hemoglobin selama masa kehamilan yang menurunkan kapasitas penghantaran oksigen ke jaringan ibu dan janin. Memerlukan suplementasi zat besi dan nutrisi adekuat.',
                ]
            ),
            'P02' => Penyakit::updateOrCreate(
                ['kode_penyakit' => 'P02'],
                [
                    'nama_penyakit' => 'Pankreatitis',
                    'keterangan' => 'Peradangan akut pada kelenjar pankreas selama kehamilan yang merupakan kegawatdaruratan medis obstetri. Memerlukan penanganan segera di rumah sakit melalui puasa medis, hidrasi cairan intravena intensif, dan pengawasan ketat.',
                ]
            ),
            'P03' => Penyakit::updateOrCreate(
                ['kode_penyakit' => 'P03'],
                [
                    'nama_penyakit' => 'Diabetes Melitus',
                    'keterangan' => 'Gangguan toleransi glukosa atau intoleransi karbohidrat yang terdeteksi selama masa kehamilan (Diabetes Melitus Gestasional). Memerlukan pengaturan nutrisi medis ketat, monitoring gula darah rutin, dan terapi insulin bila diperlukan.',
                ]
            ),
            // P04: kategori tanda bahaya (F04) dari file "Tambahan Data Gejala dan Basis Pengetahuan
            // Forward Chaining.xlsx" — bukan diagnosis penyakit, melainkan peringatan evaluasi medis segera.
            'P04' => Penyakit::updateOrCreate(
                ['kode_penyakit' => 'P04'],
                [
                    'nama_penyakit' => 'Perlu Evaluasi Medis Segera (Tanda Bahaya)',
                    'keterangan' => 'Kategori kesimpulan F04, bukan diagnosis penyakit. Dipicu oleh tanda bahaya: tekanan darah menurun, jumlah urine berkurang nyata, kesadaran menurun, napas berbau buah, napas dalam dan cepat, serta nyeri perut berat disertai mual/muntah. Memerlukan penilaian tenaga kesehatan segera.',
                ]
            ),
        ];

        // 3. Buat Data 20 Gejala (Tabel 3.3)
        $daftarGejala = [
            'G01' => 'Letih, lemah dan lesu',
            'G02' => 'Lelah, lalai dan wajah tampak pucat',
            'G03' => 'Sering pusing, sulit konsentrasi dan mudah lupa',
            'G04' => 'Mata berkunang-kunang dan cenderung sering sakit',
            'G05' => 'Selera makan berkurang',
            'G06' => 'Napas pendek (pada anemia berat)',
            'G07' => 'Keluhan mual muntah lebih hebat pada kehamilan muda',
            'G08' => 'Nyeri terus menerus pada area perut yang menjalar ke punggung/bahu',
            'G09' => 'Nyeri perut saat disentuh',
            'G10' => 'Nyeri terasa semakin parah setelah makan',
            'G11' => 'Demam',
            'G12' => 'Denyut nadi cepat',
            'G13' => 'Mual dan muntah',
            'G14' => 'Trias Diabetes Melitus (sering haus, banyak makan, sering kencing)',
            'G15' => 'Mudah lelah dan mengantuk',
            'G16' => 'Penglihatan kabur',
            'G17' => 'Penurunan berat badan meskipun nafsu makan meningkat',
            'G18' => 'Masalah pada kulit (gatal-gatal, iritasi)',
            'G19' => 'Kesemutan',
            'G20' => 'Bila terdapat luka lebih sulit sembuh',
            // G21-G50: usulan tambahan dari file "Tambahan Data Gejala dan Basis Pengetahuan
            // Forward Chaining.xlsx" (sheet Basis Pengetahuan). Data lama G01-G20 tidak diubah.
            'G21' => 'Telinga berdenging (tinnitus)',
            'G22' => 'Rambut rontok lebih banyak dari biasanya',
            'G23' => 'Lidah terasa nyeri atau meradang',
            'G24' => 'Sudut bibir pecah-pecah',
            'G25' => 'Kuku berbentuk cekung seperti sendok',
            'G26' => 'Kaki terasa gelisah ketika beristirahat',
            'G27' => 'Keinginan mengonsumsi benda bukan makanan, seperti tanah atau kertas',
            'G28' => 'Tubuh terasa lebih mudah kedinginan',
            'G29' => 'Bagian dalam kelopak mata tampak pucat',
            'G30' => 'Kemampuan melakukan aktivitas fisik menurun dibandingkan biasanya',
            'G31' => 'Nafsu makan menurun',
            'G32' => 'Perut tampak membesar atau terasa kembung',
            'G33' => 'Kulit atau bagian putih mata menguning',
            'G34' => 'Frekuensi napas meningkat',
            'G35' => 'Tekanan darah menurun',
            'G36' => 'Jumlah urine berkurang secara nyata',
            'G37' => 'Perut terasa sangat tegang atau kaku',
            'G38' => 'Bunyi usus berkurang',
            'G39' => 'Nyeri perut terasa berkurang ketika tubuh condong ke depan',
            'G40' => 'Kesadaran menurun atau kebingungan',
            'G41' => 'Mulut terasa kering',
            'G42' => 'Mengalami infeksi saluran kemih berulang',
            'G43' => 'Mengalami infeksi jamur pada area genital secara berulang',
            'G44' => 'Mudah tersinggung atau mengalami perubahan suasana hati',
            'G45' => 'Terdapat bercak kulit gelap dan menebal di leher atau ketiak',
            'G46' => 'Mengalami infeksi kulit berulang',
            'G47' => 'Kulit terasa kering',
            'G48' => 'Napas berbau seperti buah',
            'G49' => 'Napas dalam dan cepat',
            'G50' => 'Nyeri perut yang disertai mual atau muntah berat',
        ];

        $gejalas = [];
        // Adopsi gejala lama (dibuat sebelum kolom kode_gejala ada, kode masih NULL)
        // berdasarkan nama_gejala agar tidak terduplikasi saat seeding di database lama.
        foreach ($daftarGejala as $kodeAdopsi => $namaAdopsi) {
            $legacy = Gejala::whereNull('kode_gejala')->where('nama_gejala', $namaAdopsi)->orderBy('id')->first();
            if ($legacy && !Gejala::where('kode_gejala', $kodeAdopsi)->exists()) {
                $legacy->update(['kode_gejala' => $kodeAdopsi]);
            }
        }

        foreach ($daftarGejala as $kode => $nama) {
            $gejalas[$kode] = Gejala::updateOrCreate(
                ['kode_gejala' => $kode],
                ['nama_gejala' => $nama]
            );
        }

        // 4. Basis Aturan Forward Chaining — disinkronkan dengan file "Tambahan Data Gejala
        // dan Basis Pengetahuan Forward Chaining.xlsx" (sheet Aturan Forward Chaining).
        // Aturan Excel dipakai persis dengan kode R01-R15 sesuai dokumen. Aturan lama
        // R01-R06 dipertahankan sebagai data historis dan dipindahkan ke R16-R21 agar
        // kodenya tidak bentrok (seperti gejala lama G01-G20 yang tetap memakai kodenya).
        $aturanLama = [
            [
                'kode_aturan' => 'R16',
                'kode_lama' => 'R01',
                'nama_aturan' => 'Aturan Anemia Lengkap (7 Gejala)',
                'penyakit_id' => $penyakits['P01']->id,
                'keterangan' => 'IF G01 AND G02 AND G03 AND G04 AND G05 AND G06 AND G07 THEN Anemia (Defisiensi Besi Berat) | Aturan lama, dipertahankan dari basis pengetahuan sebelum sinkronisasi file Excel.',
                'gejala_kodes' => ['G01', 'G02', 'G03', 'G04', 'G05', 'G06', 'G07'],
            ],
            [
                'kode_aturan' => 'R17',
                'kode_lama' => 'R02',
                'nama_aturan' => 'Aturan Anemia Gejala Utama (5 Gejala)',
                'penyakit_id' => $penyakits['P01']->id,
                'keterangan' => 'IF G01 AND G02 AND G03 AND G04 AND G05 THEN Anemia (Defisiensi Besi Ringan-Sedang) | Aturan lama, dipertahankan dari basis pengetahuan sebelum sinkronisasi file Excel.',
                'gejala_kodes' => ['G01', 'G02', 'G03', 'G04', 'G05'],
            ],
            [
                'kode_aturan' => 'R18',
                'kode_lama' => 'R03',
                'nama_aturan' => 'Aturan Pankreatitis Akut Lengkap (6 Gejala)',
                'penyakit_id' => $penyakits['P02']->id,
                'keterangan' => 'IF G08 AND G09 AND G10 AND G11 AND G12 AND G13 THEN Pankreatitis Akut pada Kehamilan | Aturan lama, dipertahankan dari basis pengetahuan sebelum sinkronisasi file Excel.',
                'gejala_kodes' => ['G08', 'G09', 'G10', 'G11', 'G12', 'G13'],
            ],
            [
                'kode_aturan' => 'R19',
                'kode_lama' => 'R04',
                'nama_aturan' => 'Aturan Pankreatitis Gejala Inti (4 Gejala)',
                'penyakit_id' => $penyakits['P02']->id,
                'keterangan' => 'IF G08 AND G09 AND G10 AND G13 THEN Pankreatitis Akut | Aturan lama, dipertahankan dari basis pengetahuan sebelum sinkronisasi file Excel.',
                'gejala_kodes' => ['G08', 'G09', 'G10', 'G13'],
            ],
            [
                'kode_aturan' => 'R20',
                'kode_lama' => 'R05',
                'nama_aturan' => 'Aturan Diabetes Melitus Gestasional Lengkap (7 Gejala)',
                'penyakit_id' => $penyakits['P03']->id,
                'keterangan' => 'IF G14 AND G15 AND G16 AND G17 AND G18 AND G19 AND G20 THEN Diabetes Melitus Gestasional | Aturan lama, dipertahankan dari basis pengetahuan sebelum sinkronisasi file Excel.',
                'gejala_kodes' => ['G14', 'G15', 'G16', 'G17', 'G18', 'G19', 'G20'],
            ],
            [
                'kode_aturan' => 'R21',
                'kode_lama' => 'R06',
                'nama_aturan' => 'Aturan Diabetes Melitus Trias Khas (3 Gejala)',
                'penyakit_id' => $penyakits['P03']->id,
                'keterangan' => 'IF G14 AND G15 AND G17 THEN Diabetes Melitus Gestasional | Aturan lama, dipertahankan dari basis pengetahuan sebelum sinkronisasi file Excel.',
                'gejala_kodes' => ['G14', 'G15', 'G17'],
            ],
        ];

        // 4a. Hapus baris aturan hasil pemetaan sementara sebelumnya (R16-R32) agar kode
        // R01-R15 dapat dipakai persis seperti di file Excel. Baris ditandai keterangan
        // "file Excel tambahan" sehingga aturan lama maupun aturan Excel final tidak ikut
        // terhapus (pengaman ganda: hanya kode R16-R32 yang dibersihkan).
        Aturan::whereIn('kode_aturan', array_map(fn($i) => sprintf('R%02d', $i), range(16, 32)))
            ->where('keterangan', 'like', '%file Excel tambahan%')
            ->delete();

        // 4b. Pindahkan aturan lama yang masih berkode R01-R06 ke R16-R21
        foreach ($aturanLama as $item) {
            Aturan::where('kode_aturan', $item['kode_lama'])
                ->where('nama_aturan', $item['nama_aturan'])
                ->update(['kode_aturan' => $item['kode_aturan']]);
        }

        // 4c. Aturan dari file Excel, kode R01-R15 persis seperti dokumen.
        // R13 (G35 OR G36 OR G40) memakai operator OR sehingga cukup satu gejala terpenuhi.
        $aturanExcel = [
            [
                'kode_aturan' => 'R01',
                'nama_aturan' => 'Aturan Anemia Indikasi Klasik (3 Gejala)',
                'penyakit_id' => $penyakits['P01']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G02 AND G03 AND G21 THEN F01 Indikasi Anemia. Sesuai aturan R01 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G02', 'G03', 'G21'],
            ],
            [
                'kode_aturan' => 'R02',
                'nama_aturan' => 'Aturan Anemia Indikasi Anemia Berat (3 Gejala)',
                'penyakit_id' => $penyakits['P01']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G01 AND G06 AND G12 THEN F01 Indikasi Anemia. Sesuai aturan R02 pada file Excel (sheet Aturan Forward Chaining); sesak berat perlu evaluasi segera.',
                'gejala_kodes' => ['G01', 'G06', 'G12'],
            ],
            [
                'kode_aturan' => 'R03',
                'nama_aturan' => 'Aturan Anemia Indikasi Defisiensi Besi (3 Gejala)',
                'penyakit_id' => $penyakits['P01']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G22 AND G25 AND G27 THEN F01 Indikasi Anemia. Sesuai aturan R03 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G22', 'G25', 'G27'],
            ],
            [
                'kode_aturan' => 'R04',
                'nama_aturan' => 'Aturan Anemia Indikasi Defisiensi Vitamin B (3 Gejala)',
                'penyakit_id' => $penyakits['P01']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G23 AND G24 AND G29 THEN F01 Indikasi Anemia. Sesuai aturan R04 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G23', 'G24', 'G29'],
            ],
            [
                'kode_aturan' => 'R05',
                'nama_aturan' => 'Aturan Pankreatitis Indikasi Nyeri (3 Gejala)',
                'penyakit_id' => $penyakits['P02']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G08 AND G09 AND G13 THEN F02 Indikasi Pankreatitis. Sesuai aturan R05 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G08', 'G09', 'G13'],
            ],
            [
                'kode_aturan' => 'R06',
                'nama_aturan' => 'Aturan Pankreatitis Indikasi Setelah Makan (3 Gejala)',
                'penyakit_id' => $penyakits['P02']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G10 AND G13 AND G32 THEN F02 Indikasi Pankreatitis. Sesuai aturan R06 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G10', 'G13', 'G32'],
            ],
            [
                'kode_aturan' => 'R07',
                'nama_aturan' => 'Aturan Pankreatitis Indikasi Demam (3 Gejala)',
                'penyakit_id' => $penyakits['P02']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G26 AND G31 AND G11 THEN F02 Indikasi Pankreatitis. Sesuai aturan R07 pada file Excel (sheet Aturan Forward Chaining); relasi G26 ditinjau.',
                'gejala_kodes' => ['G26', 'G31', 'G11'],
            ],
            [
                'kode_aturan' => 'R08',
                'nama_aturan' => 'Aturan Pankreatitis Indikasi Ikterus (3 Gejala)',
                'penyakit_id' => $penyakits['P02']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G08 AND G11 AND G33 THEN F02 Indikasi Pankreatitis. Sesuai aturan R08 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G08', 'G11', 'G33'],
            ],
            [
                'kode_aturan' => 'R09',
                'nama_aturan' => 'Aturan Diabetes Indikasi Trias dan Kabur (2 Gejala)',
                'penyakit_id' => $penyakits['P03']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G14 AND G16 THEN F03 Indikasi Diabetes Melitus. Sesuai aturan R09 pada file Excel (sheet Aturan Forward Chaining); bukan kriteria diagnosis klinis.',
                'gejala_kodes' => ['G14', 'G16'],
            ],
            [
                'kode_aturan' => 'R10',
                'nama_aturan' => 'Aturan Diabetes Indikasi Trias Lengkap (3 Gejala)',
                'penyakit_id' => $penyakits['P03']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G14 AND G17 AND G15 THEN F03 Indikasi Diabetes Melitus. Sesuai aturan R10 pada file Excel (sheet Aturan Forward Chaining); interpretasi khusus kehamilan.',
                'gejala_kodes' => ['G14', 'G17', 'G15'],
            ],
            [
                'kode_aturan' => 'R11',
                'nama_aturan' => 'Aturan Diabetes Indikasi Infeksi Kulit (3 Gejala)',
                'penyakit_id' => $penyakits['P03']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G18 AND G20 AND G43 THEN F03 Indikasi Diabetes Melitus. Sesuai aturan R11 pada file Excel (sheet Aturan Forward Chaining); perlu validasi pakar.',
                'gejala_kodes' => ['G18', 'G20', 'G43'],
            ],
            [
                'kode_aturan' => 'R12',
                'nama_aturan' => 'Aturan Diabetes Indikasi Infeksi Berulang (3 Gejala)',
                'penyakit_id' => $penyakits['P03']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G42 AND G43 AND G45 THEN F03 Indikasi Diabetes Melitus. Sesuai aturan R12 pada file Excel (sheet Aturan Forward Chaining); kombinasi tidak spesifik.',
                'gejala_kodes' => ['G42', 'G43', 'G45'],
            ],
            [
                'kode_aturan' => 'R13',
                'nama_aturan' => 'Tanda Bahaya: Salah Satu dari Tiga Gejala (Operator OR)',
                'penyakit_id' => $penyakits['P04']->id,
                'operator' => 'OR',
                'keterangan' => 'IF G35 OR G36 OR G40 THEN F04 Evaluasi medis segera. Sesuai aturan R13 pada file Excel (sheet Aturan Forward Chaining); tanda bahaya, bukan diagnosis mandiri.',
                'gejala_kodes' => ['G35', 'G36', 'G40'],
            ],
            [
                'kode_aturan' => 'R14',
                'nama_aturan' => 'Tanda Bahaya: Napas Bau Buah dan Napas dalam-Cepat (2 Gejala)',
                'penyakit_id' => $penyakits['P04']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G48 AND G49 THEN F04 Evaluasi medis segera. Sesuai aturan R14 pada file Excel (sheet Aturan Forward Chaining); tanda bahaya potensial, pertolongan segera.',
                'gejala_kodes' => ['G48', 'G49'],
            ],
            [
                'kode_aturan' => 'R15',
                'nama_aturan' => 'Tanda Bahaya: Nyeri Perut Berat dan Napas dalam-Cepat (2 Gejala)',
                'penyakit_id' => $penyakits['P04']->id,
                'operator' => 'AND',
                'keterangan' => 'IF G50 AND G49 THEN F04 Evaluasi medis segera. Sesuai aturan R15 pada file Excel (sheet Aturan Forward Chaining); tanda bahaya potensial, pertolongan segera.',
                'gejala_kodes' => ['G50', 'G49'],
            ],
        ];

        // 4d. Sinkronkan kedua set aturan beserta premisnya (idempoten).
        foreach (array_merge($aturanExcel, $aturanLama) as $item) {
            $aturan = Aturan::updateOrCreate(
                ['kode_aturan' => $item['kode_aturan']],
                [
                    'nama_aturan' => $item['nama_aturan'],
                    'penyakit_id' => $item['penyakit_id'],
                    'keterangan' => $item['keterangan'],
                    'operator' => $item['operator'] ?? 'AND',
                    'is_active' => true,
                ]
            );

            // Hubungkan relasi pivot premis aturan
            $gejalaIds = array_map(fn($k) => $gejalas[$k]->id, $item['gejala_kodes']);
            $aturan->gejalas()->sync($gejalaIds);
        }

        // 5. Seeder Solusi Medis yang Terkoreksi dan Sahih
        // Menggunakan firstOrCreate agar data solusi lama maupun tambahan dari admin tidak terhapus.
        $daftarSolusi = [
            // Solusi Anemia
            [
                'penyakit_id' => $penyakits['P01']->id,
                'judul_solusi' => 'Pola Makan Kaya Zat Besi dan Vitamin C',
                'deskripsi_solusi' => 'Tingkatkan asupan makanan kaya zat besi heme (seperti daging sapi tanpa lemak, hati ayam bersih) dan zat besi non-heme (bayam, brokoli, kacang merah). Konsumsi bersamaan dengan buah tinggi vitamin C (jeruk, jambu biji) untuk memaksimalkan penyerapan zat besi.',
            ],
            [
                'penyakit_id' => $penyakits['P01']->id,
                'judul_solusi' => 'Suplementasi Tablet Tambah Darah (TTD)',
                'deskripsi_solusi' => 'Konsumsi tablet tambah darah (zat besi dan asam folat) setiap hari sesuai resep dokter atau bidan. Minum dengan air putih atau jus jeruk pada malam hari untuk meminimalkan mual. Hindari meminum suplemen bersamaan dengan teh, kopi, atau susu karena dapat menghambat penyerapan.',
            ],
            [
                'penyakit_id' => $penyakits['P01']->id,
                'judul_solusi' => 'Pemeriksaan Kadar Hemoglobin (Hb) Berkala',
                'deskripsi_solusi' => 'Lakukan pemeriksaan laboratorium kadar hemoglobin secara rutin pada trimester kehamilan untuk memantau kemajuan terapi dan mencegah risiko persalinan prematur atau berat bayi lahir rendah (BBLR).',
            ],
            // Solusi Pankreatitis (Koreksi Medis: Tanpa tes proteinuria preeklamsia)
            [
                'penyakit_id' => $penyakits['P02']->id,
                'judul_solusi' => 'Penanganan Gawat Darurat & Puasa Medis (NPO)',
                'deskripsi_solusi' => 'Segera bawa ibu hamil ke Instalasi Gawat Darurat (IGD) rumah sakit. Pasien harus dipuasakan sementara (nil per os) untuk mengistirahatkan saluran cerna dan meminimalkan stimulasi pelepasan enzim pankreas yang merusak jaringan.',
            ],
            [
                'penyakit_id' => $penyakits['P02']->id,
                'judul_solusi' => 'Resusitasi Cairan Intravena Intensif',
                'deskripsi_solusi' => 'Pemberian hidrasi cairan infus intravena (seperti Ringer Laktat) dalam volume yang terukur secara adekuat untuk menjaga stabilitas sirkulasi darah ibu dan mempertahankan suplai oksigen serta nutrisi ke plasenta janin.',
            ],
            [
                'penyakit_id' => $penyakits['P02']->id,
                'judul_solusi' => 'Manajemen Nyeri & Pemantauan Enzim Serum',
                'deskripsi_solusi' => 'Pengendalian rasa nyeri abdomen dengan analgesik yang aman untuk keselamatan janin di bawah pengawasan dokter spesialis kandungan dan dokter spesialis penyakit dalam, disertai pemantauan rutin kadar enzim amilase/lipase serum.',
            ],
            [
                'penyakit_id' => $penyakits['P02']->id,
                'judul_solusi' => 'Reintroduksi Nutrisi Bertahap Rendah Lemak',
                'deskripsi_solusi' => 'Setelah masa kritis mereda dan nyeri perut terkontrol, pemberian nutrisi dimulai secara bertahap dari cairan jernih, dilanjutkan diet rendah lemak dan tinggi karbohidrat untuk mencegah kekambuhan inflamasi pankreas.',
            ],
            // Solusi Diabetes Melitus Gestasional
            [
                'penyakit_id' => $penyakits['P03']->id,
                'judul_solusi' => 'Terapi Nutrisi Medis (TNM) & Pola Makan Seimbang',
                'deskripsi_solusi' => 'Patuhi jadwal dan porsi makan teratur (3 kali makan utama dan 2-3 kali selingan sehat). Pilih karbohidrat kompleks dengan indeks glikemik rendah (beras merah, oat, gandum utuh) serta hindari gula murni, sirup, dan minuman manis kemasan.',
            ],
            [
                'penyakit_id' => $penyakits['P03']->id,
                'judul_solusi' => 'Pemantauan Glukosa Darah Mandiri (PGDM)',
                'deskripsi_solusi' => 'Lakukan pemantauan gula darah secara berkala menggunakan glukometer mandiri (gula darah puasa dan 1-2 jam setelah makan). Catat hasil pemeriksaan pada buku harian kontrol gula darah untuk dievaluasi oleh dokter.',
            ],
            [
                'penyakit_id' => $penyakits['P03']->id,
                'judul_solusi' => 'Aktivitas Fisik Aman & Konsultasi Terapi Lanjutan',
                'deskripsi_solusi' => 'Lakukan aktivitas fisik ringan hingga sedang seperti jalan kaki 20-30 menit setelah makan untuk membantu sensitivitas insulin, selama tidak ada kontraindikasi kehamilan. Konsultasikan ke dokter spesialis jika target glikemik memerlukan terapi insulin.',
            ],
            // Solusi P04: Tanda Bahaya / F04 Evaluasi Medis Segera (dari sheet Petunjuk file Excel)
            [
                'penyakit_id' => $penyakits['P04']->id,
                'judul_solusi' => 'Pemeriksaan Segera ke IGD atau Fasilitas Kesehatan',
                'deskripsi_solusi' => 'Tanda bahaya seperti tekanan darah menurun, jumlah urine berkurang nyata, kesadaran menurun, napas berbau buah, napas dalam dan cepat, atau nyeri perut berat disertai mual/muntah memerlukan penilaian tenaga kesehatan segera. Segera pergi ke IGD rumah sakit terdekat atau hubungi dokter/bidan tanpa menunggu jadwal kontrol.',
            ],
            [
                'penyakit_id' => $penyakits['P04']->id,
                'judul_solusi' => 'Dampingi Ibu Hamil dan Catat Gejala Sebelum Tiba di Fasilitas',
                'deskripsi_solusi' => 'Selama perjalanan menuju fasilitas kesehatan, istirahatkan ibu hamil dalam posisi nyaman, hindari memberikan makan atau minum sembarangan, dan catat gejala beserta waktu kemunculannya untuk disampaikan kepada petugas medis.',
            ],
        ];

        foreach ($daftarSolusi as $solusi) {
            Solusi::firstOrCreate(
                [
                    'penyakit_id' => $solusi['penyakit_id'],
                    'judul_solusi' => $solusi['judul_solusi'],
                ],
                [
                    'deskripsi_solusi' => $solusi['deskripsi_solusi'],
                ]
            );
        }

        // 6. Tabel relasi legacy: data lama dipertahankan dan dilengkapi pemetaan
        // gejala-penyakit dari kolom "Penyakit Terkait" file Excel tambahan.
        foreach ($aturanLama as $item) {
            foreach ($item['gejala_kodes'] as $kGejala) {
                Relasi::firstOrCreate([
                    'penyakit_id' => $item['penyakit_id'],
                    'gejala_id' => $gejalas[$kGejala]->id,
                ], [
                    'bobot' => 1.0,
                ]);
            }
        }

        $relasiGejalaPenyakit = [
            'G01' => ['P01', 'P03'],
            'G02' => ['P01'],
            'G03' => ['P01'],
            'G04' => ['P01', 'P03'],
            'G05' => ['P01', 'P02'],
            'G06' => ['P01', 'P02'],
            'G07' => ['P02'],
            'G08' => ['P02'],
            'G09' => ['P02'],
            'G10' => ['P02'],
            'G11' => ['P02'],
            'G12' => ['P01', 'P02'],
            'G13' => ['P02', 'P03'],
            'G14' => ['P03'],
            'G15' => ['P01', 'P03'],
            'G16' => ['P03'],
            'G17' => ['P03'],
            'G18' => ['P03'],
            'G19' => ['P03'],
            'G20' => ['P03'],
            'G21' => ['P01'],
            'G22' => ['P01'],
            'G23' => ['P01'],
            'G24' => ['P01'],
            'G25' => ['P01'],
            'G26' => ['P01', 'P02'],
            'G27' => ['P01'],
            'G28' => ['P01'],
            'G29' => ['P01'],
            'G30' => ['P01'],
            'G31' => ['P02'],
            'G32' => ['P02'],
            'G33' => ['P02'],
            'G34' => ['P02'],
            'G35' => ['P02'],
            'G36' => ['P02'],
            'G37' => ['P02'],
            'G38' => ['P02'],
            'G39' => ['P02'],
            'G40' => ['P02'],
            'G41' => ['P03'],
            'G42' => ['P03'],
            'G43' => ['P03'],
            'G44' => ['P03'],
            'G45' => ['P03'],
            'G46' => ['P03'],
            'G47' => ['P03'],
            'G48' => ['P03'],
            'G49' => ['P03'],
            'G50' => ['P03'],
        ];

        foreach ($relasiGejalaPenyakit as $kodeGejala => $kodePenyakits) {
            foreach ($kodePenyakits as $kodePenyakit) {
                Relasi::firstOrCreate([
                    'penyakit_id' => $penyakits[$kodePenyakit]->id,
                    'gejala_id' => $gejalas[$kodeGejala]->id,
                ], [
                    'bobot' => 1.0,
                ]);
            }
        }

        // 7. Bersihkan baris duplikat lama (sisa data sebelum kolom kode ada) yang namanya
        // sudah terwakili baris baru berkode. Referensi aturan/hasil diarahkan dulu ke baris
        // baru, baru baris lama dihapus (relasi & solusi ikut terhapus via cascade FK).
        $penyakitBaru = Penyakit::whereNotNull('kode_penyakit')->get()->keyBy('nama_penyakit');
        foreach (Penyakit::whereNull('kode_penyakit')->get() as $legacy) {
            $target = $penyakitBaru[$legacy->nama_penyakit] ?? null;
            if (!$target) {
                continue;
            }
            Aturan::where('penyakit_id', $legacy->id)->update(['penyakit_id' => $target->id]);
            Hasil::where('penyakit_id', $legacy->id)->update(['penyakit_id' => $target->id]);
            $legacy->delete();
        }

        $gejalaBaru = Gejala::whereNotNull('kode_gejala')->get()->keyBy('nama_gejala');
        foreach (Gejala::whereNull('kode_gejala')->get() as $legacy) {
            if (!$gejalaBaru->has($legacy->nama_gejala)) {
                continue;
            }
            $legacy->delete();
        }
    }
}
