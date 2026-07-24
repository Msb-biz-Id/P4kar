# PANDUAN DEPLOYMENT: LAUNCH & RUN DI CPANEL VIA GITHUB

Dokumen ini menjelaskan langkah-langkah praktis dan aman (*best practices*) untuk mengunggah (*upload*), menjalankan (*run*), dan memperbarui (*update*) aplikasi Laravel Sistem Pakar ini di **cPanel Hosting** menggunakan repositori **GitHub**.

---

## 1. Prasyarat (Prerequisites)
Sebelum memulai, pastikan Anda memiliki:
1.  Akun **cPanel** aktif dengan fitur **Terminal** dan/atau **Git™ Version Control** diaktifkan oleh provider hosting Anda.
2.  Akun **GitHub** dan repositori proyek (baik Publik maupun Privat).
3.  Versi PHP di cPanel disesuaikan ke versi **PHP 8.2** (atau versi PHP minimal yang kompatibel dengan proyek Laravel Anda).

---

## 2. Langkah 1: Push Proyek ke GitHub
Jika proyek lokal Anda belum diunggah ke GitHub, jalankan perintah berikut di terminal komputer lokal Anda:

```bash
git init
git add .
git commit -m "Initial commit - Sistem Pakar Hamil"
git branch -M main
git remote add origin git@github.com:USERNAME/NAMA_REPOSITORI.git
git push -u origin main
```
*Catatan: Pastikan berkas `.env` dan folder `vendor/` tidak ikut terunggah (sudah diabaikan otomatis oleh berkas `.gitignore`).*

---

## 3. Langkah 2: Setup SSH Key cPanel untuk Akses GitHub (Untuk Repositori Privat)
Jika repositori GitHub Anda bersifat **Privat**, cPanel membutuhkan kunci SSH (*SSH Key*) untuk dapat meng-clone kode Anda secara aman.

1.  **Generate SSH Key di cPanel**:
    *   Masuk ke cPanel, cari menu **SSH Access**.
    *   Klik **Manage SSH Keys** -> **Generate a New Key**.
    *   Biarkan isian nama default (`id_rsa`), masukkan password kunci, lalu klik **Generate Key**.
2.  **Otorisasi Kunci**:
    *   Kembali ke halaman sebelumnya, cari kunci yang baru dibuat pada daftar *Private Keys*.
    *   Klik **Manage** -> **Authorize** agar kunci tersebut aktif.
3.  **Dapatkan Public Key**:
    *   Pada daftar *Public Keys*, klik **View/Download** pada kunci Anda.
    *   Salin (copy) seluruh teks kode kunci SSH yang ditampilkan.
4.  **Daftarkan di GitHub (Deploy Keys)**:
    *   Buka repositori proyek Anda di GitHub.
    *   Masuk ke menu **Settings** -> **Deploy keys** -> **Add deploy key**.
    *   Masukkan judul (misal: *Hosting cPanel*), tempel (*paste*) kode kunci SSH tadi di kolom *Key*.
    *   *Opsional*: Centang "Allow write access" jika Anda membutuhkan cPanel untuk mengirim push kembali ke GitHub (biasanya cukup read-only, jadi tidak perlu dicentang).
    *   Klik **Add key**.

---

## 4. Langkah 3: Menggunakan Fitur "Git Version Control" cPanel
1.  Masuk ke cPanel, cari dan buka menu **Git™ Version Control**.
2.  Klik tombol **Create** (atau **Clone a Repository**).
3.  Konfigurasikan isian berikut:
    *   **Clone URL**: Masukkan alamat repositori Git Anda.
        *   Jika *Privat* (menggunakan SSH): `git@github.com:USERNAME/NAMA_REPOSITORI.git`
        *   Jika *Publik* (bisa menggunakan HTTPS): `https://github.com/USERNAME/NAMA_REPOSITORI.git`
    *   **Directory Path**: Lokasi folder penyimpanan di hosting.
        > [!IMPORTANT]
        > Demi keamanan, jangan meng-clone proyek langsung ke dalam folder `public_html`. Letakkan folder proyek di luar folder publik (misalnya: `/home/username/expert-sistem`). Ini mencegah berkas sensitif seperti `.env` diakses langsung melalui web browser.
    *   **Repository Name**: Berikan nama repositori bebas (misal: `expert-sistem`).
4.  Klik tombol **Create**. Tunggu beberapa saat hingga cPanel selesai meng-clone proyek dari GitHub.

---

## 5. Langkah 4: Menghubungkan Folder Public Laravel ke public_html
Untuk membuat aplikasi dapat diakses publik, folder `public` di dalam proyek Laravel Anda harus terhubung dengan direktori utama domain di cPanel (`public_html`).

### Cara Terkini & Paling Direkomendasikan: Menggunakan Symlink (Terminal cPanel)
1.  Buka menu **Terminal** di cPanel Anda.
2.  Hapus atau ganti nama folder `public_html` bawaan cPanel yang masih kosong:
    ```bash
    rm -rf public_html
    ```
3.  Buat tautan simbolis (*Symbolic Link / Symlink*) yang mengarahkan `public_html` ke folder `public` proyek Laravel Anda:
    ```bash
    ln -s /home/USERNAME/expert-sistem/public /home/USERNAME/public_html
    ```
    *(Ganti `USERNAME` dengan username cPanel Anda, dan `expert-sistem` dengan direktori folder proyek Anda).*

---

## 6. Langkah 5: Konfigurasi Aplikasi di Terminal cPanel
Melalui menu **Terminal** cPanel, lakukan konfigurasi instalasi dependency dan database:

1.  **Masuk ke Direktori Proyek**:
    ```bash
    cd expert-sistem
    ```
2.  **Buat Berkas Environment (.env)**:
    ```bash
    cp .env.example .env
    ```
3.  **Ubah Isi Konfigurasi `.env`**:
    Gunakan **File Manager** cPanel untuk membuka berkas `/home/username/expert-sistem/.env` dan edit parameter berikut:
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://nama-domain-anda.com
    APP_TIMEZONE=Asia/Jakarta

    # Jika menggunakan SQLite:
    DB_CONNECTION=sqlite
    # Jika menggunakan MySQL di cPanel:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_db_cpanel
    DB_USERNAME=user_db_cpanel
    DB_PASSWORD=password_user_db_cpanel
    ```
4.  **Install Dependency via Composer**:
    Di Terminal cPanel, jalankan perintah install production dependency:
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
5.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```
6.  **Setup Database**:
    *   *Jika menggunakan SQLite*: Buat berkas database SQLite kosong terlebih dahulu:
        ```bash
        touch database/database.sqlite
        ```
    *   Jalankan migrasi tabel beserta seeder data keputusan:
        ```bash
        php artisan migrate --force --seed
        ```
7.  **Optimasi Cache Produksi**:
    Bersihkan dan simpan konfigurasi cache agar performa sistem lebih cepat:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

---

## 7. Langkah 6: Cara Deploy Pembaruan (Update Code) di Masa Depan
Ketika Anda melakukan perubahan kode di komputer lokal, lalu mem-push perubahan tersebut ke GitHub, Anda dapat melakukan pembaruan di server hosting dengan sangat mudah:

### Opsi A: Melalui Menu "Git Version Control" cPanel (Tanpa Coding/Terminal)
1.  Buka menu **Git™ Version Control** di cPanel.
2.  Klik tombol **Manage** di sebelah nama repositori Anda.
3.  Pilih tab **Pull or Deploy**.
4.  Klik tombol **Update from Source** untuk mengunduh perubahan kode terbaru dari GitHub.

### Opsi B: Melalui Terminal cPanel
1.  Buka **Terminal** cPanel.
2.  Masuk ke direktori proyek dan lakukan git pull:
    ```bash
    cd expert-sistem
    git pull origin main
    ```
3.  Segarkan cache konfigurasi agar perubahan konfigurasi baru terbaca:
    ```bash
    php artisan config:cache
    php artisan migrate --force
    ```
