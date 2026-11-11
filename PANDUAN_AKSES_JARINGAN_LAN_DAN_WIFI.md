# 🌐 BUKU PANDUAN LENGKAP: AKSES JARINGAN LAN & WI-FI KE SERVER PERPUSTAKAAN
**Sistem Informasi Perpustakaan Digital (Nafisah Nasyirah)**  
**SMK Negeri 5 Telkom Banda Aceh**

### 👨‍💻 PENGEMBANG & PENYUSUN DOKUMEN
- **Nama Pengembang**: **Heri Susanto**
- **Email**: **itemsusanto@gmail.com**
- **No. HP / WhatsApp**: **081362733320**
- **Institusi**: SMKN 5 Telkom Banda Aceh
- **Versi Dokumen**: 2.4 (Oktober 2026)
- **Format Cetak PDF**: `Panduan_Akses_Jaringan_LAN_dan_WiFi_Server_Perpustakaan.pdf` (Tersedia di root aplikasi)

---

## 📋 DAFTAR ISI
1. [Arsitektur & Topologi Jaringan Intranet](#1-arsitektur--topologi-jaringan-intranet)
2. [BAB 1: Pengaturan Lengkap di Sisi Komputer Server (Laragon 6)](#bab-1-pengaturan-lengkap-di-sisi-komputer-server-laragon-6)
3. [BAB 2: Pengaturan Keamanan Server (Proteksi phpMyAdmin & MySQL)](#bab-2-pengaturan-keamanan-server-proteksi-phpmyadmin--mysql)
4. [BAB 3: Cara Akses dari Komputer & Laptop Klien](#bab-3-cara-akses-dari-komputer--laptop-klien)
5. [BAB 4: Cara Akses dari Smartphone & Tablet Siswa (Android & iOS)](#bab-4-cara-akses-dari-smartphone--tablet-siswa-android--ios)
6. [BAB 5: Checklist Uji Coba Konektivitas & Simulasi](#bab-5-checklist-uji-coba-konektivitas--simulasi)
7. [BAB 6: Troubleshooting & Solusi Kendala Jaringan](#bab-6-troubleshooting--solusi-kendala-jaringan)

---

## 1. Arsitektur & Topologi Jaringan Intranet

Sistem perpustakaan ini mengadopsi model **Client-Server Mandiri**. Seluruh file PHP, database MySQL, dan dokumen berada di Komputer Server. Pengguna (guru, staf, dan siswa) hanya membutuhkan peramban web (*web browser*) untuk mengakses sistem tanpa perlu menginstal aplikasi apapun.

```text
+---------------------------------------------------------------------------------------+
|                    TOPOLOGI INTRANET PERPUSTAKAAN SMKN 5 TELKOM                       |
+---------------------------------------------------------------------------------------+
|                                                                                       |
|                 [ ROUTER / ACCESS POINT WI-FI SEKOLAH ]                               |
|                  (Gateway: 192.168.18.1 / Subnet: 255.255.255.0)                      |
|                                     |                                                 |
|          +--------------------------+--------------------------+                      |
|          | (Kabel LAN / Wi-Fi)                                 | (Kabel / Wi-Fi)      |
|          v                                                     v                      |
|   +-----------------------------+               +-----------------------------+       |
|   |   KOMPUTER SERVER UTAMA     |               |    PERANGKAT KLIEN PENGGUNA |       |
|   |   (Laragon 6 / Apache / PHP)|               |                             |       |
|   |   IP Statis: 192.168.18.207 |               | 1. Komputer Guru / Tata Usaha|      |
|   |   Port Web: 80 & 443 (Buka) | <============ | 2. PC Sirkulasi / Barcode   |       |
|   |   Port MySQL: 3306 (Terkunci|  HTTP Port 80 | 3. Smartphone Siswa (Katalog|      |
|   |   ke Localhost 127.0.0.1)   |               |    & Presensi Mandiri)      |       |
|   +-----------------------------+               +-----------------------------+       |
+---------------------------------------------------------------------------------------+
```

### Jalur Koneksi yang Didukung:
1. **Kabel UTP / LAN (Ethernet)**: Sangat direkomendasikan untuk **Komputer Server** dan **PC Meja Sirkulasi Pustakawan** karena memiliki stabilitas 100%, bebas gangguan interferensi radio, dan latensi sangat rendah (< 1 ms).
2. **Nirkabel / Wi-Fi**: Sangat praktis untuk **Smartphone Siswa** saat mencari buku di rak, presensi mandiri, serta laptop guru di ruang baca.

---

## BAB 1: Pengaturan Lengkap di Sisi Komputer Server (Laragon 6)

Lakukan seluruh tahapan berikut pada komputer tempat Laragon di-install:

### 1.1 Mengetahui Nomor IP Komputer Server
1. Hubungkan komputer server ke router Wi-Fi atau switch hub sekolah.
2. Tekan **Windows + R** &rarr; ketik `cmd` &rarr; tekan Enter.
3. Ketik perintah:
   ```cmd
   ipconfig
   ```
4. Catat baris **IPv4 Address** (contoh: `192.168.18.207`) dan **Default Gateway** (contoh: `192.168.18.1`).

### 1.2 Mengatur IP Address Statis (Static IP) — SANGAT PENTING
Agar alamat IP server tidak berubah saat router atau server me-restart:
1. Buka **Control Panel** &rarr; **Network and Internet** &rarr; **Network and Sharing Center**.
2. Klik **Change adapter settings** di panel kiri.
3. Klik kanan adaptor yang aktif (Ethernet atau Wi-Fi) &rarr; **Properties**.
4. Klik ganda **Internet Protocol Version 4 (TCP/IPv4)**.
5. Pilih **"Use the following IP address"**:
   - **IP address**: `192.168.18.207` (atau IP tetap pilihan Anda)
   - **Subnet mask**: `255.255.255.0`
   - **Default gateway**: `192.168.18.1` (sesuai gateway router sekolah)
   - **Preferred DNS server**: `192.168.18.1` (atau `8.8.8.8`)
6. Klik **OK** &rarr; **OK**.

### 1.3 Mengubah Profil Jaringan Windows ke "Private Network"
1. Buka **Windows Settings** (`Windows + I`) &rarr; **Network & Internet**.
2. Klik status koneksi Anda (Wi-Fi atau Ethernet).
3. Pada bagian *Network profile type*, pilih **Private network**.
*(Atau jalankan PowerShell Administrator: `Set-NetConnectionProfile -NetworkCategory Private`)*.

### 1.4 Membuka Port Apache di Windows Defender Firewall (Inbound Rule)
Windows Firewall secara bawaan memblokir koneksi port HTTP dari komputer luar. Buka aksesnya dengan 1 baris perintah ini:
1. Klik Start &rarr; ketik `cmd` &rarr; Klik Kanan **Command Prompt** &rarr; **Run as administrator**.
2. Jalankan perintah:
   ```cmd
   netsh advfirewall firewall add rule name="Laragon Apache Server" dir=in action=allow protocol=TCP localport=80,443
   ```
3. Muncul balasan **Ok.** Pertahanan firewall kini telah mengizinkan request HTTP web perpustakaan dari komputer lain.

### 1.5 Memastikan Web Server Apache Aktif di Laragon
1. Buka aplikasi **Laragon** &rarr; klik tombol **"Start All"** (modul Apache dan MySQL menyala berlatar hijau).
2. Apache secara otomatis mendengarkan pada seluruh interface jaringan (`Listen 80`).

---

## BAB 2: Pengaturan Keamanan Server (Proteksi phpMyAdmin & MySQL)

Meskipun port 80 dibuka ke seluruh jaringan LAN agar aplikasi perpustakaan dapat dibuka oleh siswa, **bagian sensitif server tetap terlindungi secara ketat**:

1. **phpMyAdmin (Terkunci ke Localhost)**:
   - Terkonfigurasi pada `C:\laragon\etc\apache2\alias\phpmyadmin.conf` dengan direktif `Require local`.
   - Komputer klien di jaringan LAN otomatis ditolak dengan pesan **403 Forbidden** jika mencoba membuka URL `http://ip-server/phpmyadmin/`.
2. **Database MySQL (Port 3306 Terkunci ke Loopback)**:
   - Terkonfigurasi pada `C:\laragon\bin\mysql\mysql-8.0.30-winx64\my.ini` dengan direktif `bind-address = 127.0.0.1`.
   - Port 3306 tidak mendengarkan antarmuka LAN/Wi-Fi sehingga kebal dari pemindaian port (port scan) dan percobaan koneksi liar.

---

## BAB 3: Cara Akses dari Komputer & Laptop Klien

### 3.1 Membuka via Browser di Komputer Guru / Lab
1. Pastikan komputer klien terhubung ke Wi-Fi atau kabel LAN sekolah yang sama.
2. Buka browser (Google Chrome, Microsoft Edge, atau Mozilla Firefox).
3. Ketikkan alamat IP komputer server pada URL bar:
   ```text
   http://192.168.18.207/perpustakaan/
   ```
   *(Ganti 192.168.18.207 dengan alamat IP statis komputer server Anda)*.
4. Tekan **Enter**. Halaman Beranda Perpustakaan Digital langsung tampil utuh.

### 3.2 Membuat Shortcut Desktop di Komputer Klien (1-Klik)
1. Di layar Desktop komputer klien, Klik Kanan area kosong &rarr; **New** &rarr; **Shortcut**.
2. Pada kolom lokasi, masukkan: `http://192.168.18.207/perpustakaan/`.
3. Klik **Next** &rarr; beri nama: `Perpustakaan Digital SMKN 5` &rarr; klik **Finish**.
4. Guru atau staf cukup klik ganda shortcut tersebut untuk langsung bekerja.

---

## BAB 4: Cara Akses dari Smartphone & Tablet Siswa (Android & iOS)

### 4.1 Langkah Akses dari HP
1. Hubungkan Wi-Fi di HP siswa ke Access Point Wi-Fi sekolah yang sama dengan server.
2. Buka browser di HP (**Google Chrome** di Android atau **Safari** di iPhone).
3. Ketikkan alamat:
   ```text
   http://192.168.18.207/perpustakaan/
   ```
4. Antarmuka web otomatis menyesuaikan dengan layar smartphone.

### 4.2 Menjadikan Icon di Layar Utama HP (Add to Home screen)
- **Android (Chrome)**: Buka web &rarr; ketuk menu titik tiga (⋮) &rarr; pilih **"Tambahkan ke Layar Utama" (Add to Home screen)** &rarr; Tambah.
- **iPhone (Safari)**: Buka web &rarr; ketuk tombol Bagikan (Share) di bawah layar &rarr; pilih **"Add to Home Screen"** &rarr; Tambah.

---

## BAB 5: Checklist Uji Coba Konektivitas & Simulasi

Lakukan uji coba berikut untuk memastikan seluruh fungsi berjalan sempurna:

- [x] **Uji Ping**: Di komputer klien, buka CMD &rarr; ketik `ping 192.168.18.207`. Muncul balasan `Reply from ... time<1ms`.
- [x] **Pencarian Buku (OPAC)**: Buka menu Data Buku dari HP siswa, coba cari judul buku.
- [x] **Presensi Pengunjung**: Coba masukkan NISN siswa dari HP untuk presensi mandiri, periksa apakah riwayat kunjungan langsung tercatat di server.
- [x] **Transaksi Sirkulasi**: Staf membuka dashboard admin dari PC sirkulasi untuk memproses pinjam/kembali buku.

---

## BAB 6: Troubleshooting & Solusi Kendala Jaringan

| Gejala Masalah di Klien | Penyebab Umum | Langkah Solusi Praktis |
| :--- | :--- | :--- |
| **ERR_CONNECTION_REFUSED** *(Situs tidak dapat dijangkau)* | Apache di komputer server belum dinyalakan atau mati mendadak. | Buka Laragon di komputer server &rarr; klik tombol **"Start All"** hingga status Apache hijau. |
| **ERR_CONNECTION_TIMED_OUT** *(Waktu koneksi habis)* | Windows Defender Firewall di server memblokir Port 80, atau kedua perangkat berada di Wi-Fi yang berbeda. | Jalankan perintah `netsh advfirewall firewall add rule name="Laragon Apache Server" dir=in action=allow protocol=TCP localport=80,443` di CMD Administrator server (Bab 1.4). Pastikan server & HP berada di SSID Wi-Fi yang sama. |
| **PC Klien Bisa, tapi HP Siswa Gagal Buka Web** | Fitur *AP Isolation* / *Client Isolation* pada router Wi-Fi sekolah aktif. | Buka halaman pengaturan router Wi-Fi sekolah (biasanya `192.168.18.1`) &rarr; menu Wireless &rarr; **Nonaktifkan (Disable) AP Isolation**. |
| **Alamat IP Server Tiba-tiba Berubah** | Adapter server masih diset ke DHCP otomatis. | Atur IP Address menjadi Statis sesuai panduan Bab 1.2. |
| **Bantuan Teknis Langsung** | Kendala jaringan atau setting router sekolah. | **Heri Susanto** · WA: **081362733320** · Email: **itemsusanto@gmail.com** |

---

### 📄 Berkas PDF Resmi Siap Cetak
- **Di Folder Proyek**: [`Panduan_Akses_Jaringan_LAN_dan_WiFi_Server_Perpustakaan.pdf`](Panduan_Akses_Jaringan_LAN_dan_WiFi_Server_Perpustakaan.pdf)
- **Di Folder Backup Eksternal**: `C:\Backup_Perpustakaan\Panduan_Akses_Jaringan_LAN_dan_WiFi_Server_Perpustakaan.pdf`

*SMK Negeri 5 Telkom Banda Aceh · Hak Cipta © 2026 Heri Susanto. Seluruh hak cipta dilindungi undang-undang.*
