# 📘 BUKU PANDUAN RESMI: INSTALASI, DEPLOYMENT & KONFIGURASI SERVER
## Sistem Informasi Manajemen Perpustakaan Digital (Nafisah Nasyirah)
### Panduan Lengkap Pemasangan di Sekolah Lain, Server Lokal, dan Cloud

---

### 👨‍💻 INFORMASI PENGEMBANG & HAK CIPTA
- **Pengembang Asli**: **Heri Susanto**
- **Kontak Resmi / WhatsApp**: **081362733320** (https://wa.me/6281362733320)
- **Email**: itemsusanto@gmail.com
- **Tautan Lisensi Resmi**: https://docs.google.com/document/d/1URZkS4E3CjGKhQFFtx0HA0ehoV3V1FOdujTIv7m-eIg/edit?tab=t.0
- **Status Lisensi**: Aplikasi ini **GRATIS** dan **DILARANG UNTUK DIPERJUALBELIKAN**.

---

## 📑 DAFTAR ISI
1. [BAB 1: Persyaratan Hardware & Sistem](#bab-1-persyaratan-hardware--sistem)
2. [BAB 2: Memilih Metode Instalasi yang Tepat](#bab-2-memilih-metode-instalasi-yang-tepat)
3. [BAB 3: Metode A — Standalone Windows Installer (.EXE) 1-Klik](#bab-3-metode-a--standalone-windows-installer-exe-1-klik)
4. [BAB 4: Metode B — Server Web Laragon / XAMPP di Sekolah Lain](#bab-4-metode-b--server-web-laragon--xampp-di-sekolah-lain)
5. [BAB 5: Penjelasan Khusus Password Database MySQL Root](#bab-5-penjelasan-khusus-password-database-mysql-root)
6. [BAB 6: Personalisasi & Ganti Identitas Sekolah Baru](#bab-6-personalisasi--ganti-identitas-sekolah-baru)
7. [BAB 7: Pengaturan Akses Jaringan Lokal Wi-Fi & LAN Sekolah](#bab-7-pengaturan-akses-jaringan-lokal-wi-fi--lan-sekolah)
8. [BAB 8: Troubleshooting & Solusi Kendala Teknis](#bab-8-troubleshooting--solusi-kendala-teknis)

---

## BAB 1: Persyaratan Hardware & Sistem

Aplikasi ini dirancang sangat ringan, stabil, dan responsif. Komputer atau server sekolah yang akan dipasang harus memenuhi spesifikasi berikut:

### 1. Spesifikasi Server / Komputer Perpustakaan (Rekomendasi)
- **Sistem Operasi**: Windows 10 (64-bit), Windows 11 (64-bit), atau Windows Server 2016/2019/2022.
- **Processor**: Minimal Dual Core 2.0 GHz (Disarankan Intel Core i3 / AMD Ryzen 3 atau lebih tinggi).
- **RAM**: Minimal 4 GB (Disarankan 8 GB jika diakses puluhan siswa serentak via Wi-Fi).
- **Ruang Disk Bebas**: Minimal 3 GB pada Drive C:\ (Disarankan menggunakan SSD agar pembacaan katalog instan).
- **Jaringan**: Port LAN RJ45 atau Adaptor Wi-Fi untuk koneksi ke router sekolah.

### 2. Kebutuhan Perangkat Pengguna (Klien Siswa & Guru)
- Dapat diakses dari Laptop, Komputer Lab, Tablet, maupun Smartphone (Android / iOS) melalui peramban web modern (**Google Chrome**, **Microsoft Edge**, atau **Mozilla Firefox**) tanpa perlu menginstal aplikasi tambahan di HP siswa.

---

## BAB 2: Memilih Metode Instalasi yang Tepat

Tersedia **dua metode instalasi** yang dapat dipilih sesuai kebutuhan teknis sekolah tujuan:

| Fitur | Metode A: Installer `.exe` (Mandiri) | Metode B: Laragon / XAMPP (Web Server) |
| :--- | :--- | :--- |
| **Tingkat Kesulitan** | ⭐ Sangat Mudah (Tinggal Dobel Klik) | ⭐⭐⭐ Menengah (Perlu Import SQL & Konfigurasi) |
| **Ketergantungan** | Mandiri (Tanpa perlu install Laragon/XAMPP) | Membutuhkan Laragon 6.x atau XAMPP 8.1+ |
| **Database & Runtime** | Sudah sepaket otomatis (PHP 8.1 & MySQL 8.0) | Menggunakan database MySQL bawaan Laragon |
| **Port Default** | Port 8088 (Web) & Port 3308 (MySQL) | Port 80 (Web) & Port 3306 (MySQL) |
| **Pengguna Sasaran** | Komputer perpustakaan standalone / offline | Server utama sekolah / teknisi jaringan |

---

## BAB 3: Metode A — Standalone Windows Installer (.EXE) 1-Klik
*(Rekomendasi Terbaik untuk Sekolah yang Tidak Memiliki Teknisi Khusus)*

Metode ini adalah cara paling instan. Anda tidak perlu menginstal Laragon, XAMPP, maupun melakukan konfigurasi database manual.

### Langkah-Langkah Instalasi:
1. **Salin File Installer:**
   Ambil berkas **`Installer_Perpustakaan_Sekolah_Setup.exe`** (ukuran ~95 MB) dan salin ke komputer tujuan (bisa menggunakan flashdisk).
2. **Jalankan Installer:**
   Klik kanan berkas `Installer_Perpustakaan_Sekolah_Setup.exe` lalu pilih **Run as administrator** (atau dobel-klik biasa).
3. **Proses Berjalan Otomatis:**
   - Installer akan mengekstrak seluruh sistem ke folder `C:\PustakaSekolah\`.
   - Menyiapkan runtime PHP 8.1 dan MySQL 8.0 portabel.
   - Menginisialisasi database bersih **`nafisah_nasyirah_perpustakaan`** secara otomatis.
   - Membuat shortcut resmi **Perpustakaan Nafisah Nasyirah** di Desktop dan Start Menu Windows.
   - Menambahkan izin Windows Firewall untuk akses jaringan.
4. **Membuka Aplikasi:**
   - Dobel-klik ikon shortcut di Desktop.
   - Aplikasi otomatis terbuka dalam mode jendela desktop profesional (Edge App Window) di alamat `http://127.0.0.1:8088/`.
5. **Kredensial Login Awal:**
   - **Username**: `admin`
   - **Password**: `admin123`

---

## BAB 4: Metode B — Server Web Laragon / XAMPP di Sekolah Lain
*(Untuk Server Utama Sekolah atau Integrasi Web Intranet)*

Jika sekolah tujuan sudah memiliki server Laragon atau XAMPP, ikuti langkah berikut:

### Langkah 1: Persiapan Folder Proyek
1. Pastikan di komputer server sudah terinstal **Laragon** (disarankan) atau **XAMPP** dengan PHP versi **8.1** atau **8.2**.
2. Salin seluruh folder aplikasi perpustakaan ke direktori root web server:
   - Jika menggunakan **Laragon**: letakkan di `C:\laragon\www\perpustakaan`
   - Jika menggunakan **XAMPP**: letakkan di `C:\xampp\htdocs\perpustakaan`

### Langkah 2: Pembuatan Database di MySQL
1. Buka browser dan buka phpMyAdmin (`http://localhost/phpmyadmin`) atau buka **HeidiSQL** bawaan Laragon.
2. Buat database baru dengan nama:
   - **Nama Database**: `nafisah_nasyirah_perpustakaan`
   - **Collation**: `utf8mb4_unicode_ci`

### Langkah 3: Import Data Database Bersih
1. Di phpMyAdmin / HeidiSQL, pilih database `nafisah_nasyirah_perpustakaan` yang baru saja dibuat.
2. Klik tab **Import** (Impor).
3. Pilih berkas: `c:\laragon\www\perpustakaan\database\pustaka_sekolah_clean.sql`.
4. Klik **Go** / **Kirim** dan tunggu hingga seluruh tabel berhasil dimuat.
   *(File database bersih ini sudah berisi struktur lengkap, master kategori DDC, buku contoh, pengaturan sistem, dan akun administrator bersih tanpa riwayat siswa sekolah lama).*

---

## BAB 5: Penjelasan Khusus Password Database MySQL Root

Setiap komputer server memiliki password MySQL yang berbeda. Pahami konfigurasi ini dengan baik:

### Situasi Password:
- **Di Komputer Pengembang (Heri Susanto)**: Password root adalah **`jj220386`**.
- **Di Sekolah Baru / Server Lain**:
  - Laragon bawaan default: password root adalah **kosong** (`""`).
  - XAMPP bawaan default: password root adalah **kosong** (`""`).
  - Server khusus sekolah: password root ditentukan oleh teknisi sekolah bersangkutan.

### Cara Mengatur Password di Sekolah Baru (Pilih Salah Satu):

#### Opsi 1: Menggunakan File `config.json` (Paling Praktis & Fleksibel)
Buat atau edit file bernama **`config.json`** di dalam folder aplikasi (`c:\laragon\www\perpustakaan\config.json`).
Isikan kode JSON berikut:

```json
{
  "db_host": "127.0.0.1",
  "db_port": 3306,
  "db_name": "nafisah_nasyirah_perpustakaan",
  "db_user": "root",
  "db_pass": ""
}
```
> **Catatan Penting:**
> - Jika password root di sekolah baru **kosong** (bawaan Laragon/XAMPP), biarkan `"db_pass": ""`.
> - Jika sekolah tersebut memiliki password root sendiri (misal: `admin12345`), ganti menjadi `"db_pass": "admin12345"`.
> - Jika menggunakan port MySQL selain 3306, Anda cukup mengubah angka `"db_port": 3306`.
> - File `config.json` ini otomatis dibaca oleh sistem tanpa perlu mengotak-atik baris kode program!

#### Opsi 2: Mengubah Langsung di File `config.php`
Buka file `config.php` menggunakan Text Editor (Notepad / VS Code). Cari baris konfigurasi berikut (sekitar baris 85–90):
```php
if (!defined('DB_PASS')) define('DB_PASS', 'jj220386');
```
Ubah `'jj220386'` menjadi password MySQL server sekolah baru:
```php
// Jika password MySQL sekolah kosong (default Laragon):
if (!defined('DB_PASS')) define('DB_PASS', '');

// Atau jika sekolah punya password sendiri:
if (!defined('DB_PASS')) define('DB_PASS', 'password_sekolah_anda');
```

#### Opsi 3: Menyamakan Password MySQL Sekolah Menjadi `jj220386`
Jika teknisi sekolah ingin menyamakan password server agar identik:
1. Buka Terminal Laragon / Command Prompt.
2. Jalankan perintah:
   ```bash
   mysqladmin -u root password "jj220386"
   ```
3. Password MySQL server sekolah sekarang resmi menjadi `jj220386`.

---

## BAB 6: Personalisasi & Ganti Identitas Sekolah Baru

Setelah aplikasi berhasil terpasang dan dibuka di sekolah baru:

1. **Login Administrator:**
   - Akses: `http://localhost/perpustakaan/` (atau `http://localhost:8088/` jika versi .exe)
   - Klik menu **Masuk / Login Petugas** di sudut kanan atas.
   - Masukkan:
     - **Username**: `admin`
     - **Password**: `admin123`
2. **Masuk ke Menu Pengaturan:**
   - Pada sidebar dashboard admin, klik menu **Pengaturan Sistem** (atau ikon gerigi).
3. **Ubah Informasi Sekolah:**
   - **Nama Sekolah**: Ganti dengan nama resmi sekolah baru (contoh: *SMA Negeri 1 Medan*, *SMP Negeri 2 Banda Aceh*).
   - **Sub-Judul / Motto**: Ganti motto perpustakaan sekolah baru.
   - **Alamat Lengkap, NPSN, Telepon, & Email**: Isi sesuai profil sekolah tujuan.
   - **Nama Kepala Sekolah & NIP**: Diperlukan untuk tanda tangan otomatis pada laporan dan surat bebas pustaka.
   - **Nama Kepala Perpustakaan & NIP**: Diperlukan untuk validasi sirkulasi dan kartu anggota.
4. **Ganti Logo & Kop Surat:**
   - Upload Logo Sekolah baru (format PNG transparan atau JPG).
   - Logo otomatis teraplikasi pada Navbar, Kartu Anggota Digital, Barcode/QR Buku, dan Kop Surat Cetak Laporan.
5. **Ketentuan Hak Cipta & Integritas:**
   - Pada bagian paling bawah footer akan tetap tercantum:  
     *"Di Ciptakan dan dikembangkan Oleh Heri Susanto, Aplikasi ini Gratis dilarang untuk di perjual belikan"*
   - Bagian ini terkunci secara kriptografis demi menjaga integritas lisensi sistem dan menghargai hak cipta pengembang asli saat aplikasi dibagikan gratis ke sekolah lain.

---

## BAB 7: Pengaturan Akses Jaringan Lokal Wi-Fi & LAN Sekolah
*(Agar Siswa & Guru Bisa Membuka dari HP / Laptop Masing-Masing)*

Aplikasi ini dapat diakses secara bersamaan oleh ratusan siswa di perpustakaan tanpa internet (cukup terhubung ke router Wi-Fi sekolah).

### Langkah 1: Cek IP Address Komputer Server
1. Pada komputer server perpustakaan, tekan tombol keyboard **Windows + R**, ketik `cmd`, lalu tekan **Enter**.
2. Ketik perintah: `ipconfig` lalu tekan **Enter**.
3. Cari baris **IPv4 Address** pada adapter Wi-Fi atau Ethernet yang aktif.
   - *Contoh: `192.168.1.100`*

### Langkah 2: Format Alamat URL untuk HP Siswa / Laptop Klien
Pastikan HP siswa atau laptop guru terhubung ke nama Wi-Fi yang sama dengan komputer server. Buka Google Chrome di HP dan ketik:

- **Jika Server Menggunakan Laragon:**
  $$\text{http://192.168.1.100/perpustakaan/}$$
- **Jika Server Menggunakan Installer `.EXE`:**
  Jalankan file `PustakaSekolah_LAN.bat` pada folder `C:\PustakaSekolah\`, lalu buka:
  $$\text{http://192.168.1.100:8088/}$$

### Langkah 3: Izinkan Windows Firewall (Jika HP Tidak Bisa Konek)
Jika dari HP siswa muncul tulisan *"Situs ini tidak dapat dijangkau"*:
1. Buka **Control Panel** -> **Windows Defender Firewall** -> **Advanced settings**.
2. Klik **Inbound Rules** -> **New Rule...**
3. Pilih **Port** -> Next -> Pilih **TCP** -> Masukkan Specific local ports: `80, 8088, 3306, 3308`.
4. Pilih **Allow the connection** -> Beri nama `Akses Perpustakaan Sekolah` -> Selesai.

---

## BAB 8: Troubleshooting & Solusi Kendala Teknis

### 1. Error: `SQLSTATE[HY000] [2002] Connection refused`
- **Penyebab**: Layanan database MySQL belum berjalan atau port database tidak sesuai.
- **Solusi**:
  - Jika pakai Laragon: Buka aplikasi Laragon, klik tombol **Start All**.
  - Jika pakai Installer `.EXE`: Buka folder `C:\PustakaSekolah\`, klik ganda `PustakaSekolah.bat`.
  - Periksa file `config.json` apakah port dan password sudah sesuai.

### 2. Error: `Access denied for user 'root'@'localhost'`
- **Penyebab**: Password MySQL yang dimasukkan salah.
- **Solusi**: Periksa `config.json` pada baris `"db_pass"`. Jika server sekolah tidak memakai password, pastikan nilainya `""` (kosong).

### 3. Port 80 Sudah Digunakan Aplikasi Lain (Port Conflict)
- **Penyebab**: Komputer server sudah terpasang aplikasi lain seperti IIS, Skype, atau VMware.
- **Solusi**:
  - Gunakan **Metode A (Installer .EXE)** karena secara otomatis menggunakan **Port 8088** sehingga bebas bentrok dengan aplikasi apapun.

### 4. Jika Teknisi Sekolah Lupa Password Akun Admin
- Buka phpMyAdmin / HeidiSQL pada database `nafisah_nasyirah_perpustakaan`.
- Buka tabel `users`.
- Pada baris username `admin`, ubah kolom `password` menjadi hash berikut:
  ```text
  $2y$10$wEODdY0hC2f9lA54c6vKzeM356a.p6uE/4W/C1bL2G6M7eYpYjH7.
  ```
  *(Password otomatis kembali menjadi: `admin123`)*.

---

### 📞 BANTUAN & KONSULTASI TEKNIS RESMI
Jika teknisi sekolah atau pengelola perpustakaan mengalami kendala teknis saat proses instalasi di sekolah baru, silakan hubungi langsung pengembang aplikasi:

- **Heri Susanto**
- **WhatsApp**: [0813-6273-3320](https://wa.me/6281362733320)
- **Dokumentasi & Portofolio**: [Google Docs Portofolio Heri Susanto](https://docs.google.com/document/d/1URZkS4E3CjGKhQFFtx0HA0ehoV3V1FOdujTIv7m-eIg/edit?tab=t.0)
- **Lokasi**: Banda Aceh, Indonesia

