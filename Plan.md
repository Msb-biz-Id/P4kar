# RENCANA KERJA REVISI SISTEM PAKAR DIAGNOSA PENYAKIT IBU HAMIL
## Transisi Total dari Metode Naive Bayes Classifier ke Metode Forward Chaining (Basis Aturan IF-THEN)

---

## 1. Ringkasan Eksekutif & Hasil Audit Sheet B

Berdasarkan audit menyeluruh terhadap file excel `Catatan revisi utk website job TA.xlsx` (khususnya **Sheet B - Website**) dan peninjauan langsung pada kode program repositori, ditemukan bahwa sistem saat ini masih dibangun sepenuhnya dengan paradigma probabilistik **Naive Bayes Classifier** (menghitung *prior*, *likelihood*, dan *softmax posterior probability*).

Target revisi adalah melakukan transformasi total ke **Metode Forward Chaining** berbasis penalaran maju (*data-driven reasoning*) dengan aturan produksi **IF-THEN**. Sistem pakar yang dihasilkan harus memenuhi standar akurasi penalaran, transparansi keputusan (*explainability / trace inference*), keabsahan rujukan medis, serta bebas dari kecacatan fungsional (*clean code & anti-slop standard*).

### Tabel Matriks Audit Eksisting vs Target Ideal (12 Komponen Sheet B)

| No | Komponen (Sheet B) | Estimasi Perubahan | Prioritas | Kondisi Eksisting di Repositori | Kebutuhan Target Ideal 100% |
|:--:|:---|:---:|:---:|:---|:---|
| **1** | **Halaman Utama** | 80% (0.80) | Tinggi | Berisi narasi dan label Naive Bayes Classifier pada hero banner dan footer copyright. | Seluruh teks diubah ke Forward Chaining dengan penjelasan penalaran aturan klinis gejala ke kesimpulan penyakit. |
| **2** | **Login & Autentikasi** | 0% (0.00) | Tetap | Otentikasi Laravel Breeze berfungsi normal, namun header form login masih memuat teks metode lama. | Fungsi otentikasi dipertahankan 100%; header teks metode lama diselaraskan secara bersih. |
| **3** | **Dashboard Admin** | 15% (0.15) | Rendah | Card dan sidebar masih berlabel "Data Relasi" dengan asumsi bobot probabilitas. | Label card & sidebar diselaraskan menjadi "Basis Aturan (Rules IF-THEN)"; ringkasan statistik metode diperbarui. |
| **4** | **Data Penyakit** | 5% (0.05) | Tetap | Tersedia 3 entitas penyakit: Anemia, Pankreatitis, Diabetes Melitus. Belum memiliki kode baku. | Pertahankan 3 penyakit; tambahkan kode penyakit baku (P01, P02, P03) untuk memudahkan pelacakan aturan. |
| **5** | **Data Gejala** | 20% (0.20) | Sedang | Tersedia 20 gejala tanpa kolom kode gejala resmi di tabel database (hanya ID numerik). | Tambahkan kolom `kode_gejala` (G01 s/d G20) dan verifikasi terminologi gejala dengan literatur medis kebidanan. |
| **6** | **Relasi Penyakit-Gejala** | 90% (0.90) | Wajib | Tabel `relasi` menyimpan nilai float `bobot` (0.01 - 1.0) untuk probabilitas Naive Bayes. | Rombak menjadi Basis Aturan (*Knowledge Base*): Aturan R01, R02, dst. dengan relasi premis multi-gejala (IF G... AND G... THEN P...). |
| **7** | **Mesin Inferensi Diagnosis** | 100% (1.00) | Wajib | Menghitung log-prior, log-likelihood, normalisasi softmax, dan ambang batas probabilitas 50%. | Ganti total dengan Algoritma Forward Chaining: *Working Memory*, *Rule Matching*, evaluasi premis, deteksi *exact match*, *partial match*, dan *conflict resolution*. |
| **8** | **Form Konsultasi** | 15% (0.15) | Rendah | Form checklist 20 gejala berfungsi tetapi belum menampilkan kode gejala resmi (G01 - G20). | UI dipertahankan; tambahkan kode gejala, validasi input minimal 1 gejala, penataan grup gejala yang ergonomis. |
| **9** | **Halaman Hasil Diagnosis** | 75% (0.75) | Tinggi | Menampilkan bar persentase probabilitas Naive Bayes per penyakit. | Tampilkan hasil evaluasi aturan, status kepastian (Aturan Terpenuhi / Kasus Parsial / Multi-Diagnosis / Tidak Cocok), serta riwayat pelacakan inferensi (*inference trace*). |
| **10** | **Data Solusi & Penanganan** | 20% (0.20) | Sedang | Terdapat kekeliruan fatal pada seeder: Solusi Pankreatitis berisi instruksi preeklamsia (cek urine proteinuria & batasi garam). | Koreksi total solusi Pankreatitis sesuai protokol medis gastroenterologi bumil (puasa NPO, hidrasi IV, analgesik, pemantauan amilase/lipase). Verifikasi solusi Anemia & Diabetes Gestasional. |
| **11** | **Database & Migrasi** | 65% (0.65) | Tinggi | Skema masih menggunakan kolom `bobot` di relasi dan kolom `probabilitas` (JSON) di tabel hasil. | Migrasi skema: Tambah kode penyakit/gejala, buat tabel `aturan` & `aturan_gejala`, update tabel `hasil` dengan `detail_inferensi` dan `status_kesimpulan`. |
| **12** | **Pengujian Aplikasi** | 100% (1.00) | Wajib | Unit/Feature test di `tests/Feature/DiagnosaTest.php` menguji perhitungan probabilitas Naive Bayes. | Tulis ulang seluruh skenario pengujian PHPUnit untuk Forward Chaining (uji kecocokan penuh, uji parsial, uji multi-match, uji no-match, uji validasi aturan). |

---

## 2. Arsitektur Transisi: Naive Bayes ke Forward Chaining

### 2.1 Konsep Penalaran Forward Chaining
Forward Chaining adalah metode penalaran maju yang dimulai dari himpunan fakta yang diketahui (gejala klinis yang dirasakan ibu hamil), kemudian menguji aturan-aturan produksi yang premisnya terpenuhi hingga mencapai kesimpulan diagnosa (penyakit) beserta solusi penanganannya.

```
+-----------------------------------------------------------+
|              Fakta Masukan (Working Memory)               |
|            Gejala yang Dipilih Pasien (Misal: G01, G02, G04) |
+-----------------------------+-----------------------------+
                              |
                              v
+-----------------------------------------------------------+
|                   Mesin Inferensi                        |
|                  (Pattern Matching)                       |
|   Mencocokkan fakta dengan Basis Aturan (Knowledge Base)  |
+-----------------------------+-----------------------------+
                              |
       +----------------------+----------------------+
       |                      |                      |
       v                      v                      v
[ Kasus 1: Cocok Penuh ]  [ Kasus 2: Multi-Match ] [ Kasus 3: Parsial/No Match ]
Premis Aturan Terpenuhi   > 1 Aturan Terpenuhi    Sebagian premis terpenuhi
100% (Diagnosa Pasti)     (Resolusi Konflik)      atau Tidak ada kecocokan
       |                      |                      |
       +----------------------+----------------------+
                              |
                              v
+-----------------------------------------------------------+
|            Hasil Diagnosa & Rekomendasi Solusi            |
|       + Pohon/Rincian Pelacakan Aturan (Trace Log)        |
+-----------------------------------------------------------+
```

### 2.2 Desain Basis Pengetahuan (Rule Base IF-THEN)
Berdasarkan pembagian 20 gejala awal dan 3 penyakit pada dokumen skripsi:
- **Penyakit P01 (Anemia)**: Terdiri dari premis gejala G01 sampai G07.
- **Penyakit P02 (Pankreatitis)**: Terdiri dari premis gejala G08 sampai G13.
- **Penyakit P03 (Diabetes Melitus Gestasional)**: Terdiri dari premis gejala G14 sampai G20.

#### Tabel Aturan Produksi Inti:
1. **Rule R01 (Anemia Lengkap / Berat)**:
   - `IF` G01 (Letih, lemah, lesu) `AND` G02 (Wajah pucat) `AND` G03 (Pusing/mudah lupa) `AND` G04 (Mata berkunang-kunang) `AND` G05 (Selera makan berkurang) `AND` G06 (Napas pendek) `AND` G07 (Mual muntah berlebih kehamilan muda)
   - `THEN` Diagnosa: **Anemia Defisiensi Besi Berat** (Tingkat Keyakinan: 100%).
2. **Rule R02 (Anemia Ringan / Sedang)**:
   - `IF` G01 `AND` G02 `AND` (G03 `OR` G04 `OR` G05)
   - `THEN` Diagnosa: **Anemia Defisiensi Besi Ringan-Sedang**.
3. **Rule R03 (Pankreatitis Akut Utama)**:
   - `IF` G08 (Nyeri perut menjalar ke punggung/bahu) `AND` G09 (Nyeri perut saat disentuh) `AND` G10 (Nyeri memburuk setelah makan) `AND` G13 (Mual dan muntah)
   - `THEN` Diagnosa: **Pankreatitis Akut pada Kehamilan** (Tingkat Keyakinan: 100%).
4. **Rule R04 (Pankreatitis dengan Gejala Sistemik Tambahan)**:
   - `IF` Memenuhi R03 `AND` (G11 (Demam) `OR` G12 (Denyut nadi cepat))
   - `THEN` Diagnosa: **Pankreatitis Akut dengan Respons Inflamasi Sistemik**.
5. **Rule R05 (Diabetes Melitus Gestasional Khas - Trias DM)**:
   - `IF` G14 (Trias DM: polidipsi, polifagi, poliuri) `AND` G15 (Mudah lelah dan mengantuk) `AND` G17 (Penurunan BB meski nafsu makan naik)
   - `THEN` Diagnosa: **Diabetes Melitus Gestasional Khas**.
6. **Rule R06 (Diabetes Melitus Gestasional Komplikasi Lanjut)**:
   - `IF` Memenuhi R05 `AND` (G16 (Penglihatan kabur) `OR` G18 (Gatal/iritasi kulit) `OR` G19 (Kesemutan) `OR` G20 (Luka sulit sembuh))
   - `THEN` Diagnosa: **Diabetes Melitus Gestasional dengan Gejala Lanjut**.

*Catatan: Sistem juga dilengkapi penghitungan kecocokan parsial (Best Match Ratio) jika pasien tidak mencentang seluruh premis aturan secara eksak, sehingga sistem tidak langsung buntu.*

---

## 3. Rencana Kerja Implementasi Detil (Per Komponen 1 - 12)

### Komponen 1: Halaman Utama (Homepage)
- [x] Ubah teks pada file `resources/views/diagnosa/index.blade.php`:
  - Hapus frasa `Naive Bayes Classifier`.
  - Ganti dengan `Metode Forward Chaining (Basis Aturan Penalaran Maju)`.
  - Perbarui penjelasan alur: *Sistem pakar penalaran berbasis aturan gejala klinis kebidanan secara langsung dan terstruktur*.
  - Perbarui teks hak cipta pada footer.
- [x] Terapkan prinsip antislop: Hindari klaim berlebihan ("Revolusioner", "Akurasi Mutlak 100%"), gunakan bahasa klinis yang lugas dan informatif.

### Komponen 2: Login & Autentikasi
- [x] Periksa file `resources/views/auth/login.blade.php`:
  - Ganti sub-judul `MENGGUNAKAN METODE NAIVE BAYES CLASSIFIER` menjadi `METODE FORWARD CHAINING`.
- [x] Pertahankan alur autentikasi Breeze (login, validasi sesi, proteksi CSRF, redirect dashboard) tanpa mengubah logic keamanan yang telah stabil.

### Komponen 3: Dashboard Admin
- [x] Ubah file `resources/views/dashboard.blade.php`:
  - Ubah card `Data Relasi` menjadi `Basis Aturan (Rules)`.
  - Tampilkan jumlah aturan aktif (`\App\Models\Aturan::count()`).
  - Perbarui teks sambutan admin agar merefleksikan pengelolaan aturan inferensi IF-THEN.
- [x] Ubah file `resources/views/layouts/admin.blade.php`:
  - Ganti menu navigasi `Data Relasi` (`fas fa-project-diagram`) menjadi `Basis Aturan` (`fas fa-code-branch` atau `fas fa-scroll`).

### Komponen 4: Data Penyakit
- [x] Pastikan model `App\Models\Penyakit` memiliki atribut `kode_penyakit` (P01: Anemia, P02: Pankreatitis, P03: Diabetes Melitus).
- [x] Sesuaikan tampilan index, modal tambah, dan modal edit di `resources/views/admin/penyakit/index.blade.php` untuk menampilkan dan mengelola `kode_penyakit`.
- [x] Pastikan relasi ke `Aturan` (`hasMany`) dan `Solusi` (`hasMany`) terhubung rapi.

### Komponen 5: Data Gejala
- [x] Tambahkan atribut `kode_gejala` (G01 s/d G20) pada model `App\Models\Gejala` dan tabel database.
- [x] Sesuaikan tampilan index, modal tambah, dan modal edit di `resources/views/admin/gejala/index.blade.php` agar menampilkan kode gejala terurut rapi.
- [x] Lakukan verifikasi teks gejala agar sesuai istilah baku kedokteran/kebidanan Indonesia.

### Komponen 6: Relasi Penyakit-Gejala ke Basis Aturan (Rules IF-THEN)
- [x] Ganti fungsionalitas `RelasiController` dan view `admin.relasi` menjadi `AturanController` dan view `admin.aturan`:
  - Form input tidak lagi meminta nilai numerik `bobot` (0.01 - 1.0).
  - Admin dapat membuat Aturan baru: Kode Aturan (R01, R02, dst.), Penyakit Hasil (Consequent), dan memilih satu atau banyak gejala premis (Antecedent) melalui checklist / multi-select.
  - Tabel menampilkan ekspresi aturan: `IF [Gejala A AND Gejala B] THEN [Penyakit X]`.
  - Sediakan fitur aktif/nonaktif aturan jika diperlukan untuk pengujian.

### Komponen 7: Mesin Inferensi Diagnosis (Forward Chaining Engine)
- [x] Bangun kelas service tersendiri `app/Services/ForwardChainingService.php` agar arsitektur bersih (*Separation of Concerns*).
- [x] Logika inferensi:
  1. **Inisialisasi Fakta**: Terima array `gejala_ids` dari input pasien.
  2. **Evaluasi Aturan**: Ambil semua aturan aktif beserta premis gejalanya.
  3. **Penghitungan Kecocokan**:
     - Hitung jumlah premis yang cocok: `count(intersection(fakta, premis_aturan))`.
     - Hitung persentase kecocokan: `(premis_cocok / total_premis_aturan) * 100%`.
  4. **Klasifikasi Hasil**:
     - *Exact Match (100%)*: Aturan terpicu penuh. Jika ada lebih dari satu, sajikan sebagai diagnosis banding (*differential diagnosis*).
     - *Partial Match (>= 50% & < 100%)*: Jika tidak ada aturan yang cocok 100%, ambil aturan dengan rasio tertinggi sebagai potensi diagnosis dengan catatan premis yang belum terpenuhi.
     - *No Match (< 50%)*: Gejala yang dimasukkan tidak mencukupi untuk mengarah ke salah satu dari 3 penyakit.
  5. **Trace Log Generator**: Simpan langkah penalaran: daftar fakta yang cocok, aturan yang diuji, dan kesimpulan akhir.
- [x] Perbarui `app/Http/Controllers/DiagnosaController.php` untuk memanggil `ForwardChainingService`.

### Komponen 8: Form Konsultasi
- [x] Perbarui `resources/views/diagnosa/konsultasi.blade.php`:
  - Tampilkan lencana kode gejala (misal: `[G01]`, `[G02]`) di samping deskripsi gejala.
  - Berikan pengelompokan visual atau filter cepat agar pasien mudah membaca daftar 20 gejala.
  - Sediakan tombol "Pilih Semua", "Hapus Semua Pilihan", dan counter real-time jumlah gejala yang dipilih.
  - Pastikan aksesibilitas keyboard (fokus checkbox jelas, dapat dicentang dengan tombol `Space`).

### Komponen 9: Halaman Hasil Diagnosis
- [x] Rombak tampilan `resources/views/diagnosa/hasil.blade.php` dan `resources/views/admin/hasil/show.blade.php`:
  - Hapus card "Analisis Probabilitas Naive Bayes".
  - Ganti dengan "Hasil Evaluasi Basis Aturan (Forward Chaining)":
    - Status Diagnosa: **Pasti (Aturan Terpenuhi 100%)**, **Kemungkinan Tinggi (Kecocokan Parsial)**, atau **Tidak Teridentifikasi**.
    - Rincian Evaluasi Aturan: Tabel interaktif menampilkan kode aturan, nama penyakit sasaran, jumlah premis terpenuhi vs dipersyaratkan, dan persentase keselarasan.
    - Pohon Pelacakan Aturan (*Inference Trace*): Menampilkan gejala yang cocok (warna hijau) dan gejala yang tidak dirasakan pasien (warna abu-abu/merah) untuk transparansi sistem pakar.
    - Penanganan Kasus Multi-Diagnosis: Menampilkan peringatan klinis bahwa pasien memiliki indikasi lebih dari satu penyakit secara bersamaan dan memerlukan konfirmasi laboratorium segera.

### Komponen 10: Data Solusi & Penanganan (Koreksi Medis)
- [x] Perbaiki seeder dan database solusi:
  - **Koreksi Fatal Pankreatitis**:
    - *Hapus*: Instruksi diet rendah garam dan pemeriksaan protein urine (protokol preeklamsia yang salah tempat).
    - *Ganti dengan solusi medis valid*:
      1. Penanganan Gawat Darurat & Puasa Medis (NPO): Pengistirahatan saluran cerna sementara untuk menghentikan stimulasi enzim pankreas.
      2. Rehidrasi Cairan Intravena Intensif: Mengganti cairan tubuh yang hilang dan mempertahankan perfusi plasenta janin.
      3. Manajemen Nyeri & Pengawasan Ketat: Pemberian analgesik yang aman untuk kehamilan serta pemantauan kadar amilase/lipase serum.
      4. Nutrisi Bertahap Pasca-Akut: Pemberian diet rendah lemak secara bertahap setelah fase nyeri akut mereda.
  - **Verifikasi Solusi Anemia**: Suplementasi zat besi + asam folat + vitamin C, anjuran konsumsi makanan hewani tinggi zat besi heme dan sayuran hijau.
  - **Verifikasi Solusi Diabetes Melitus Gestasional**: Terapi Nutrisi Medis (TNM), pembatasan karbohidrat sederhana, monitoring glukosa darah berkala (GDP & 2 jam PP), dan konsultasi terapi insulin bila diet tidak mencukupi target.

### Komponen 11: Database & Migrasi
- [x] Buat file migrasi baru:
  - `add_kode_to_penyakit_and_gejala_table`: Menambahkan `kode_penyakit` (string 10) dan `kode_gejala` (string 10).
  - `create_aturans_table`: Menyimpan definisi aturan (`id`, `kode_aturan`, `penyakit_id`, `nama_aturan`, `keterangan`, `is_active`, `timestamps`).
  - `create_aturan_gejala_table`: Tabel pivot premis aturan (`id`, `aturan_id`, `gejala_id`, `timestamps`).
  - `update_hasils_table_for_forward_chaining`: Mengganti atau menyesuaikan kolom `probabilitas` menjadi `detail_inferensi` (JSON) dan menambahkan `status_kesimpulan` (enum/string: `cocok_penuh`, `parsial`, `multi_match`, `tidak_cocok`).
- [x] Buat seeder komprehensif: `ForwardChainingSeeder.php` yang mengisi data master lengkap (3 penyakit, 20 gejala berkode, 6 aturan inti tervalidasi, dan solusi medis terverifikasi).

### Komponen 12: Pengujian Aplikasi (Testing Suite)
- [x] Tulis ulang file pengujian `tests/Feature/DiagnosaTest.php`:
  1. `test_forward_chaining_exact_match_anemia()`: Memilih G01-G07 menghasilkan diagnosa Anemia dengan status cocok 100%.
  2. `test_forward_chaining_exact_match_pankreatitis()`: Memilih G08-G13 menghasilkan diagnosa Pankreatitis dengan status cocok 100%.
  3. `test_forward_chaining_exact_match_diabetes()`: Memilih G14-G20 menghasilkan diagnosa Diabetes Melitus dengan status cocok 100%.
  4. `test_forward_chaining_partial_match()`: Memilih sebagian gejala menghasilkan status kecocokan parsial dengan persentase tepat.
  5. `test_forward_chaining_multi_diagnosis()`: Memilih kombinasi gejala dua penyakit menghasilkan daftar diagnosis banding.
  6. `test_forward_chaining_no_match()`: Memilih gejala acak atau minim yang tidak mencapai ambang batas menghasilkan pesan tidak teridentifikasi.
- [x] Lakukan pengujian Blackbox untuk form konsultasi, navigasi, dan antarmuka admin.
- [x] Jalankan seluruh suite test (`php artisan test`) dan pastikan 100% test lulus.

---

## 4. Jadwal & Urutan Tahapan Eksekusi

```
+-------------------------------------------------------------------------------+
| Fase 1: Fondasi Data & Skema Database                                        |
| 1. Migrasi kode_penyakit & kode_gejala                                        |
| 2. Migrasi tabel aturan & aturan_gejala                                       |
| 3. Migrasi penyesuaian tabel hasil                                            |
| 4. Seeder ForwardChainingSeeder (Data master, Aturan IF-THEN, Solusi Medis)   |
+-------------------------------------------------------------------------------+
                                        |
                                        v
+-------------------------------------------------------------------------------+
| Fase 2: Mesin Inferensi (Core Logic)                                         |
| 1. Pembuatan ForwardChainingService.php                                       |
| 2. Implementasi Working Memory & Pattern Matching                             |
| 3. Implementasi Conflict Resolution & Inference Trace Logging                 |
| 4. Unit Test Core Inference Engine                                            |
+-------------------------------------------------------------------------------+
                                        |
                                        v
+-------------------------------------------------------------------------------+
| Fase 3: Integrasi Controller & Public Views                                  |
| 1. Pembaruan DiagnosaController (proses & hasil)                              |
| 2. Perubahan Teks Halaman Utama (Homepage)                                    |
| 3. Pembaruan Form Konsultasi (Tampilan Kode Gejala & UX)                      |
| 4. Rombak Halaman Hasil Diagnosa (Rule Table & Trace Log)                     |
+-------------------------------------------------------------------------------+
                                        |
                                        v
+-------------------------------------------------------------------------------+
| Fase 4: Panel Admin (Manajemen Basis Pengetahuan)                             |
| 1. Dashboard Admin & Sidebar (Basis Aturan)                                   |
| 2. CRUD Penyakit & Gejala dengan Kode Resmi                                   |
| 3. CRUD Basis Aturan (AturanController & Form IF-THEN)                        |
| 4. Koreksi Data Solusi Medis Pankreatitis, Anemia, & Diabetes                 |
| 5. Detail Hasil Diagnosa di Admin                                             |
+-------------------------------------------------------------------------------+
                                        |
                                        v
+-------------------------------------------------------------------------------+
| Fase 5: Pengujian Menyeluruh & Verifikasi Akhir                              |
| 1. Pembaruan DiagnosaTest.php (Feature & Unit Tests)                          |
| 2. Eksekusi php artisan test (Target: 100% Passed)                            |
| 3. Validasi Anti-Slop (No em dash, responsive mobile, kontras AA, no dead link)|
| 4. Dokumentasi teknis untuk bab 4 skripsi                                     |
+-------------------------------------------------------------------------------+
```

---

## 5. Kriteria Keberhasilan (Acceptance Criteria 100%)

1. **Integritas Konseptual**: Tidak ada lagi sisa formula probabilistik Naive Bayes (log-space, likelihood, prior, softmax) di dalam alur diagnosa.
2. **Kesesuaian Basis Aturan**: Sistem bekerja murni dengan aturan IF-THEN yang dapat dikonfigurasi dan diverifikasi.
3. **Koreksi Medis**: Solusi untuk Pankreatitis telah dikoreksi sepenuhnya dari kesalahan preeklamsia ke penanganan pankreatitis yang sahih.
4. **Explainability**: Pengguna dapat melihat alasan mengapa suatu penyakit didiagnosa (aturan mana yang terpicu dan gejala mana yang cocok).
5. **Kualitas Pengujian**: Seluruh test PHPUnit lulus 100% tanpa error maupun regresi.
6. **Kualitas UI/UX (Anti-Slop)**: Tampilan responsif pada ponsel dan desktop, teks bersih tanpa em dash, kontras warna memenuhi WCAG AA, dan seluruh tombol interaktif memiliki fungsi nyata.
