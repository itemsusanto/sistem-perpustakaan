# 📘 BUKU PANDUAN RESMI: MIGRASI & PEMINDAHAN SISTEM KE KOMPUTER LAIN
**Sistem Informasi Manajemen Perpustakaan Digital (Nafisah Nasyirah)**  
**SMK Negeri 5 Telkom Banda Aceh**

### 👨‍💻 PENGEMBANG & PEMBUAT APLIKASI
- **Nama Pengembang**: **Heri Susanto**
- **Email**: **itemsusanto@gmail.com**
- **No. HP / WhatsApp**: **081362733320**
- **Institusi**: SMKN 5 Telkom Banda Aceh
- **Versi Dokumen**: 2.4 (Oktober 2026)
- **Format Cetak PDF**: `Buku_Panduan_Migrasi_dan_Instalasi_Komputer_Laragon.pdf` (Tersedia di root aplikasi)

---

## 📋 DAFTAR ISI
1. [Persyaratan Hardware & Sistem](#1-persyaratan-hardware--sistem)
2. [Perbandingan: Laragon 6 vs XAMPP](#2-perbandingan-laragon-6-vs-xampp)
3. [BAB 1: Persiapan di Komputer Lama (Export & Backup Data)](#bab-1-persiapan-di-komputer-lama-export--backup-data)
4. [BAB 2: Instalasi Laragon di Komputer Baru](#bab-2-instalasi-laragon-di-komputer-baru)
5. [BAB 3: Konfigurasi Lingkungan Laragon (PHP, MySQL, Apache)](#bab-3-konfigurasi-lingkungan-laragon-php-mysql-apache)
6. [BAB 4: Penempatan Folder Proyek & Hak Akses](#bab-4-penempatan-folder-proyek--hak-akses)
7. [BAB 5: Pembuatan & Pemulihan Database](#bab-5-pembuatan--pemulihan-database)
8. [BAB 6: Pengujian Aplikasi & Kredensial Login](#bab-6-pengujian-aplikasi--kredensial-login)
9. [BAB 7: Akses Jaringan Lokal Intranet Sekolah (Wi-Fi & LAN)](#bab-7-akses-jaringan-lokal-intranet-sekolah-wi-fi--lan)
10. [BAB 8: Troubleshooting & Solusi Kendala](#bab-8-troubleshooting--solusi-kendala)

---

## 1. Persyaratan Hardware & Sistem
Komputer atau laptop server baru yang akan digunakan harus memenuhi spesifikasi minimum:
- **Sistem Operasi**: Windows 10 (64-bit), Windows 11 (64-bit), atau Windows Server (64-bit disarankan).
- **Processor**: Minimal Dual Core 2.0 GHz (disarankan Intel Core i3 / AMD Ryzen 3 atau lebih tinggi).
- **Memori RAM**: Minimal 4 GB (disarankan 8 GB jika diakses banyak klien serentak).
- **Ruang Disk**: Minimal 2 GB ruang kosong pada Drive C:\ (disarankan SSD).
- **Perangkat Pendukung**: Flashdisk minimal 4 GB untuk memindahkan berkas.

---

## 2. Perbandingan: Laragon 6 vs XAMPP

| Parameter Teknis | Opsi 1: LARAGON 6 (Pilihan Utama & Direkomendasikan) | Opsi 2: XAMPP (Tradisional) |
| :--- | :--- | :--- |
| **Direktori Web Utama** | `C:\laragon\www\perpustakaan\` | `C:\xampp\htdocs\perpustakaan\` |
| **Aktivasi Server** | Cukup **1 klik tombol "Start All"** | Klik tombol Start Apache & MySQL terpisah |
| **Pengelolaan Database** | Klik tombol **Database** (HeidiSQL otomatis tanpa password) atau phpMyAdmin | Buka browser: `http://localhost/phpmyadmin/` |
| **Alamat URL di Browser** | `http://localhost/perpustakaan/` atau `http://perpustakaan.test` | `http://localhost/perpustakaan/` |
| **Skrip Pemulihan 1-Klik** | Kompatibel otomatis (`KLIK_UNTUK_PULIHKAN.bat`) | Kompatibel otomatis (`KLIK_UNTUK_PULIHKAN.bat`) |
| **Kelebihan Utama** | Sangat cepat, modern, tidak sering bentrok port 80/3306 | Standar lama yang umum di lab komputer |

---

## BAB 1: Persiapan di Komputer Lama (Export & Backup Data)

### 1.1 Membuat Backup Database Terkini
1. **Cara Cepat (Batch Script)**: Buka folder `C:\laragon\www\perpustakaan\backups\` lalu klik ganda file **`KLIK_UNTUK_BACKUP.bat`**.
2. **Cara Dashboard Web**: Login Administrator &rarr; menu **Backup Database** &rarr; klik tombol hijau **"Buat Cadangan (Backup) Sekarang"**.
3. File SQL dump akan otomatis tersimpan dengan format nama `perpustakaan_backup_YYYY-MM-DD_HH-mm-ss.sql`.

### 1.2 Menyiapkan dan Mengemas Folder Proyek
1. Buka File Explorer ke folder: `C:\laragon\www\perpustakaan\`.
2. Pastikan subfolder dan berkas penting berikut lengkap:
   - `index.php`, `config.php`, `config_backup.php`, `style.css`
   - Folder `uploads/` (Memuat seluruh foto siswa dan sampul buku)
   - Folder `database/` (Memuat skema master SQL)
   - Folder `backups/` (Memuat file cadangan SQL dan script pemulihan)
   - Folder `assets/` (Memuat icon, barcode library, dan fonts)
3. Salin folder `perpustakaan` ke Flashdisk (atau kompresi ke `perpustakaan.zip`).
4. *(Opsional)* Salin juga folder cadangan eksternal `C:\Backup_Perpustakaan\` ke Flashdisk.

---

## BAB 2: Instalasi Laragon di Komputer Baru

1. Di komputer baru, unduh installer resmi **Laragon - Full (64-bit)**:
   👉 [https://laragon.org/download/](https://laragon.org/download/)
2. Jalankan file `laragon-wamp.exe` dengan hak akses Administrator (*Run as administrator*).
3. Pertahankan lokasi direktori default instalasi:
   `C:\laragon`
4. Pada jendela opsi tambahan:
   - Centang **Run Laragon when Windows starts** (jika komputer dijadikan server perpustakaan permanen).
   - Centang **Auto virtual hosts**.
5. Klik **Install** dan tunggu hingga proses selesai, lalu klik **Finish**.

---

## BAB 3: Konfigurasi Lingkungan Laragon (PHP, MySQL, Apache)

### 3.1 Konfigurasi PHP (`php.ini`)
1. Buka aplikasi Laragon &rarr; Klik Kanan di jendela Laragon &rarr; pilih **PHP** &rarr; **php.ini**.
2. Pastikan ekstensi berikut tidak bertanda titik koma (aktif):
   ```ini
   extension=pdo_mysql
   extension=mysqli
   extension=mbstring
   extension=gd
   extension=intl
   extension=fileinfo
   extension=curl
   extension=zip
   ```
3. Sesuaikan batasan memori dan upload:
   ```ini
   upload_max_filesize = 64M
   post_max_size = 64M
   memory_limit = 256M
   max_execution_time = 300
   date.timezone = Asia/Jakarta
   ```
4. Simpan file (`Ctrl + S`).

### 3.2 Konfigurasi Database MySQL (`my.ini`)
1. Klik Kanan di jendela Laragon &rarr; pilih **MySQL** &rarr; **my.ini**.
2. Di bawah blok `[mysqld]`, pastikan/tambahkan:
   ```ini
   [mysqld]
   port=3306
   bind-address = 127.0.0.1
   mysqlx = 0
   key_buffer_size=256M
   max_allowed_packet=512M
   table_open_cache=256
   sql_mode = NO_AUTO_VALUE_ON_ZERO,STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION
   ```
   *Catatan Keamanan: `bind-address = 127.0.0.1` mengamankan port 3306 agar tidak dapat dipindai atau diserang langsung oleh perangkat asing di jaringan Wi-Fi/LAN.*
3. Simpan file (`Ctrl + S`).

### 3.3 Konfigurasi Keamanan phpMyAdmin
1. Buka file: `C:\laragon\etc\apache2\alias\phpmyadmin.conf`.
2. Pastikan direktif `Require local` aktif dan `AllowOverride None`:
   ```apache
   Alias /phpmyadmin "C:/laragon/etc/apps/phpMyAdmin/"

   <Directory "C:/laragon/etc/apps/phpMyAdmin/">
       Options FollowSymLinks
       AllowOverride None  
       Require local
   </Directory>
   ```
   *Dengan aturan ini, komputer klien di jaringan LAN otomatis ditolak (403 Forbidden) saat mencoba membuka phpMyAdmin.*

---

## BAB 4: Penempatan Folder Proyek & Hak Akses

1. Tancapkan flashdisk ke komputer baru.
2. Salin folder `perpustakaan` ke dalam:
   `C:\laragon\www\`
   sehingga jalurnya menjadi:
   `C:\laragon\www\perpustakaan\`
3. Jika Anda menyalin folder cadangan eksternal, letakkan di:
   `C:\Backup_Perpustakaan\`
4. **Pemeriksaan Hak Tulis**:
   - Klik kanan folder `C:\laragon\www\perpustakaan\uploads\` &rarr; Properties &rarr; pastikan atribut **Read-only** tidak tercentang &rarr; OK.
   - Lakukan hal yang sama untuk folder `C:\laragon\www\perpustakaan\backups\`.

---

## BAB 5: Pembuatan & Pemulihan Database

### 5.1 Metode 1 (Sangat Cepat): Skrip Pemulihan Otomatis
1. Di jendela Laragon, klik tombol **"Start All"** (Apache dan MySQL menyala hijau).
2. Buka folder: `C:\laragon\www\perpustakaan\backups\` (atau `C:\Backup_Perpustakaan\`).
3. Klik dua kali file **`KLIK_UNTUK_PULIHKAN.bat`**.
4. Saat diminta nama database target, ketik:
   `nafisah_nasyirah_perpustakaan`
5. Saat diminta konfirmasi teks, ketik:
   `PULIHKAN-PRODUKSI`
6. Tekan Enter. Database langsung dibuat dan terisi 100% utuh dalam 2-3 detik!

### 5.2 Metode 2: Menggunakan HeidiSQL Bawaan Laragon
1. Klik tombol **Database** di Laragon (HeidiSQL terbuka otomatis tanpa password).
2. Klik tombol **Open**.
3. Klik kanan pada panel kiri &rarr; *Create new* &rarr; *Database* &rarr; nama: `nafisah_nasyirah_perpustakaan` (Collation: `utf8mb4_unicode_ci`) &rarr; OK.
4. Klik database tersebut &rarr; tekan shortcut **Ctrl + O** &rarr; pilih file SQL dump Anda.
5. Tekan tombol **F9** (Run) untuk mengeksekusi hingga selesai.

---

## BAB 6: Pengujian Aplikasi & Kredensial Login

1. Buka browser (Chrome atau Edge) di komputer server, lalu buka:
   `http://localhost/perpustakaan/`
   *(atau Pretty URL: `http://perpustakaan.test`)*
2. Login menggunakan akun Administrator:
   - **Username**: `admin`
   - **Password**: `admin123`
3. **Checklist Pengujian**:
   - [x] Dashboard menampilkan ringkasan data buku dan siswa.
   - [x] Menu Data Buku menampilkan cover dan stok eksemplar akurat.
   - [x] Menu Anggota menampilkan foto profil dan NISN.
   - [x] Menu Sirkulasi dapat melakukan transaksi pinjam dan kembali.
   - [x] Menu Bebas Pustaka dan Kartu Anggota dapat mencetak barcode/PDF.
   - [x] Menu Backup Database menampilkan status 3 lokasi aman aktif.

---

## BAB 7: Akses Jaringan Lokal Intranet Sekolah (Wi-Fi & LAN)

Agar guru dan siswa dapat mengakses katalog, presensi kunjungan, dan pengajuan bebas pustaka dari smartphone atau laptop masing-masing:

1. **Koneksikan Server ke Wi-Fi / Router Sekolah**.
2. **Cek Alamat IP Komputer Server**:
   - Tekan **Windows + R** &rarr; ketik `cmd` &rarr; Enter.
   - Ketik `ipconfig` &rarr; catat nomor **IPv4 Address** (contoh: `192.168.18.207`).
   - *(Disarankan):* Jadikan IP ini Static/Tetap di pengaturan adaptor jaringan Windows.
3. **Buka Port Apache di Windows Defender Firewall**:
   - Buka *Windows Defender Firewall with Advanced Security*.
   - Pilih *Inbound Rules* &rarr; *New Rule...* &rarr; *Port* &rarr; *TCP* &rarr; port: `80, 443`.
   - Pilih *Allow the connection* &rarr; centang semua profil &rarr; beri nama: `Laragon Apache Server` &rarr; Finish.
4. **Akses dari HP / Komputer Klien**:
   - Pastikan perangkat klien terhubung ke Wi-Fi yang sama.
   - Buka browser di HP/laptop klien, ketik alamat:
     `http://192.168.18.207/perpustakaan/`
     *(Ganti dengan nomor IPv4 komputer server Anda)*.
   - Aplikasi siap digunakan bersama tanpa perlu instalasi apapun di perangkat klien.

---

## BAB 8: Troubleshooting & Solusi Kendala

| Gejala Masalah | Penyebab Utama | Solusi Penanganan |
| :--- | :--- | :--- |
| **Apache Gagal Start (Port 80 Bentrok)** | Layanan IIS Windows atau Skype menggunakan port 80. | Buka `services.msc` &rarr; Stop *World Wide Web Publishing Service*. Atau di Laragon: Klik Kanan &rarr; Apache &rarr; Change Port ke `8080` (akses via `http://localhost:8080/perpustakaan/`). |
| **MySQL Gagal Start** | Proses `mysqld.exe` lama masih menggantung di background. | Buka Task Manager &rarr; End Task `mysqld.exe`. Jika tabel korup, klik ganda `backups\PERBAIKI_MYSQL_CRASH.bat`. |
| **Foto Siswa & Cover Buku Tidak Tampil** | Folder `uploads/` belum disalin lengkap atau terkunci Read-only. | Pastikan isi folder `uploads/` dari komputer lama telah di-copy utuh. Hilangkan centang Read-only pada properties folder. |
| **HP Siswa Tidak Bisa Membuka Alamat IP** | Windows Firewall memblokir koneksi masuk atau SSID Wi-Fi berbeda. | Pastikan server dan HP berada di Wi-Fi yang sama. Tambahkan Inbound Rule port 80 pada Windows Firewall (Bab 7). |
| **Bantuan Teknis Langsung** | Butuh pendampingan migrasi atau kendala server. | **Heri Susanto** · WA: **081362733320** · Email: **itemsusanto@gmail.com** |

---

### 📄 Berkas Panduan Resmi PDF
File PDF berkualitas tinggi siap cetak tersedia pada:
- **Di Folder Proyek**: `C:\laragon\www\perpustakaan\Buku_Panduan_Migrasi_dan_Instalasi_Komputer_Laragon.pdf`
- **Di Folder Backup**: `C:\Backup_Perpustakaan\Buku_Panduan_Migrasi_dan_Instalasi_Komputer_Laragon.pdf`

*SMK Negeri 5 Telkom Banda Aceh · Hak Cipta © 2026 Heri Susanto. Seluruh hak cipta dilindungi undang-undang.*
