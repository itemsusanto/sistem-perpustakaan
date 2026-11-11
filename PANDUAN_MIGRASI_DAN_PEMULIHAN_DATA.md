# 🛡️ PANDUAN LENGKAP MIGRASI SISTEM & PEMULIHAN DATA PERPUSTAKAAN
**Sistem Informasi Manajemen Perpustakaan Digital SMKN 5 Telkom Banda Aceh**

### 👨‍💻 INFORMASI PENGEMBANG & DUKUNGAN TEKNIS
- **Nama Pengembang**: **Heri Susanto**
- **Email**: **itemsusanto@gmail.com**
- **No. HP / WhatsApp**: **081362733320**
- **Institusi**: SMKN 5 Telkom Banda Aceh

---

## 📌 PENGANTAR
Dokumen ini disusun khusus sebagai pedoman operasional bagi Staf Perpustakaan, Guru Pembina, maupun Teknisi Lab Komputer dalam menangani 2 (dua) kondisi penting:
1. **Migrasi Sistem**: Memindahkan seluruh aplikasi perpustakaan dan data transaksi ke komputer/laptop/server baru secara aman tanpa kehilangan data.
2. **Pemulihan Data (Disaster Recovery)**: Mengembalikan data buku, anggota, dan riwayat sirkulasi yang hilang atau tidak bisa diakses akibat Apache/MySQL error, database crash (*MySQL shutdown unexpectedly*), layanan web server hilang, maupun komputer rusak/diinstal ulang.

---

## 📑 DAFTAR ISI
1. [BAGIAN 1: PANDUAN MIGRASI KE KOMPUTER / SERVER BARU](#bagian-1-panduan-migrasi-ke-komputer--server-baru)
   - [A. Checklist Berkas yang Wajib Disalin](#a-checklist-berkas-yang-wajib-disalin)
   - [B. Metode 1: Migrasi Otomatis 1-Klik (Paling Cepat & Praktis)](#b-metode-1-migrasi-otomatis-1-klik-paling-cepat--praktis)
   - [C. Metode 2: Migrasi Manual via phpMyAdmin](#c-metode-2-migrasi-manual-via-phpmyadmin)
   - [D. Pengujian & Penyesuaian Jaringan (LAN/Wi-Fi)](#d-pengujian--penyesuaian-jaringan-lanwi-fi)
2. [BAGIAN 2: CARA MEMULIHKAN DATA KETIKA MYSQL ATAU APACHE HILANG / CRASH](#bagian-2-cara-memulihkan-data-ketika-mysql-atau-apache-hilang--crash)
   - [Kasus 1: Error "MySQL shutdown unexpectedly" di XAMPP (Crash Database)](#kasus-1-error-mysql-shutdown-unexpectedly-di-xampp-crash-database)
   - [Kasus 2: Apache Hilang / Tidak Mau Start (Port 80/443 Bentrok)](#kasus-2-apache-hilang--tidak-mau-start-port-80443-bentrok)
   - [Kasus 3: Service Apache/MySQL Hilang dari XAMPP Control Panel](#kasus-3-service-apachemysql-hilang-dari-xampp-control-panel)
   - [Kasus 4: Komputer Diinstal Ulang / Folder XAMPP Rusak / Terhapus](#kasus-4-komputer-diinstal-ulang--folder-xampp-rusak--terhapus)
   - [Kasus 5: Database Tidak Sengaja Terhapus di phpMyAdmin](#kasus-5-database-tidak-sengaja-terhapus-di-phpmyadmin)
3. [BAGIAN 3: STANDAR OPERASIONAL PROSEDUR (SOP) PENCEGAHAN KEHILANGAN DATA](#bagian-3-standar-operasional-prosedur-sop-pencegahan-kehilangan-data)
   - [A. Jadwal Rutin Backup Mingguan](#a-jadwal-rutin-backup-mingguan)
   - [B. Otomatisasi Backup Harian via Windows Task Scheduler](#b-otomatisasi-backup-harian-via-windows-task-scheduler)
   - [C. Sinkronisasi Cadangan ke Cloud (Google Drive / OneDrive)](#c-sinkronisasi-cadangan-ke-cloud-google-drive--onedrive)
4. [HUBUNGI BANTUAN TEKNIS](#hubungi-bantuan-teknis)

---

# BAGIAN 1: PANDUAN MIGRASI KE KOMPUTER / SERVER BARU

Jika perpustakaan mendapatkan komputer server baru atau laptop operasional lama akan diganti, ikuti langkah-langkah migrasi berikut agar seluruh data buku, foto sampul, data anggota, presensi, serta transaksi peminjaman berpindah 100% utuh.

### A. Checklist Berkas yang Wajib Disalin
Dari Komputer Lama, siapkan media penyimpanan eksternal (Flashdisk / Harddisk Eksternal / Google Drive) dan salin folder berikut:
1. **Folder Project Lengkap**:
   - Lokasi: `C:\xampp\htdocs\perpustakaan`
   - *Pastikan folder `uploads/` (berisi foto cover buku & foto profil anggota) dan folder `database/` ikut tersalin.*
2. **File Dump Database SQL Terbaru**:
   - Lokasi: `C:\xampp\htdocs\perpustakaan\backups\` ATAU `C:\Backup_Perpustakaan\`
   - Nama file: `perpustakaan_backup_YYYY-MM-DD_HH-MM-SS.sql` (atau `nafisah_nasyirah_perpustakaan.sql`).

---

### B. Metode 1: Migrasi Otomatis 1-Klik (Paling Cepat & Praktis)

Metode ini menggunakan skrip pemulihan instan yang sudah tersedia di dalam aplikasi.

#### Langkah 1: Pasang XAMPP di Komputer Baru
1. Unduh dan pasang XAMPP (disarankan versi PHP 8.0, 8.1, atau 8.2) di `C:\xampp`.
2. Buka **XAMPP Control Panel**, lalu klik **Start** pada modul **Apache** dan **MySQL**. Pastikan keduanya berwarna hijau.

#### Langkah 2: Salin Folder Aplikasi
1. Buka folder `C:\xampp\htdocs\` di komputer baru.
2. Tempel (*paste*) folder `perpustakaan` sehingga jalurnya menjadi:
   `C:\xampp\htdocs\perpustakaan`

#### Langkah 3: Jalankan Skrip Pemulihan 1-Klik
1. Buka folder:
   `C:\xampp\htdocs\perpustakaan\backups\`
2. Klik ganda file:
   `KLIK_UNTUK_PULIHKAN.bat`
3. Jendela hitam (Command Prompt) akan terbuka dan otomatis mencari file cadangan database SQL terbaru.
4. Tekan huruf **Y** pada keyboard saat diminta konfirmasi, lalu tekan **Enter**.
5. Skrip akan secara otomatis:
   - Membuat basis data `nafisah_nasyirah_perpustakaan` dengan enkoding UTF-8.
   - Mengimpor seluruh tabel, data koleksi buku, anggota, peminjaman, denda, dan presensi.
6. Tunggu hingga muncul tulisan:
   `[SUKSES] DATABASE PERPUSTAKAAN BERHASIL DIPULIHKAN 100%!`
7. Selesai! Buka browser dan akses `http://localhost/perpustakaan/`.

---

### C. Metode 2: Migrasi Manual via phpMyAdmin

Gunakan metode ini jika Anda terbiasa menggunakan antarmuka grafis phpMyAdmin:

1. **Ekspor Database dari Komputer Lama**:
   - Di komputer lama, buka `http://localhost/phpmyadmin/`.
   - Klik database **`nafisah_nasyirah_perpustakaan`** di bilah sisi kiri.
   - Klik tab menu **Export (Ekspor)** di bagian atas.
   - Pilih metode **Quick (Cepat)** & format **SQL**, lalu klik tombol **Export / Kirim**.
   - Simpan file `.sql` tersebut ke Flashdisk.

2. **Siapkan Komputer Baru**:
   - Pastikan XAMPP telah aktif (Apache & MySQL berstatus *Running / Hijau*).
   - Salin folder `perpustakaan` ke `C:\xampp\htdocs\perpustakaan`.

3. **Buat Database Baru di Komputer Baru**:
   - Buka peramban di komputer baru: `http://localhost/phpmyadmin/`.
   - Klik **New (Baru)** di panel kiri.
   - Ketikkan nama database: `nafisah_nasyirah_perpustakaan`.
   - Pilih Collation: `utf8mb4_unicode_ci` lalu klik **Create (Buat)**.

4. **Impor File SQL**:
   - Klik nama database `nafisah_nasyirah_perpustakaan` yang baru dibuat.
   - Klik tab **Import (Impor)** di menu atas.
   - Klik tombol **Choose File (Pilih Berkas)**, lalu pilih file `.sql` yang Anda bawa dari komputer lama.
   - Gulir ke bawah dan klik tombol **Import / Kirim**.
   - Tunggu hingga muncul pesan sukses berwarna hijau: *"Import has been successfully finished"*.

---

### D. Pengujian & Penyesuaian Jaringan (LAN/Wi-Fi)

Setelah pemindahan selesai, lakukan pengetesan berikut:
1. Buka browser di komputer baru dan akses: `http://localhost/perpustakaan/`.
2. Login menggunakan akun Administrator (`admin` / `admin123`) atau akun anggota.
3. Pastikan cover buku, data transaksi sirkulasi, dan nama anggota tampil lengkap.
4. **Agar Siswa & Guru Bisa Mengakses via Wi-Fi Sekolah**:
   - Buka `cmd` di komputer server baru, ketik `ipconfig` & temukan **IPv4 Address** (contoh: `192.168.1.50`).
   - Perangkat klien (HP/laptop siswa) yang terhubung ke Wi-Fi sekolah cukup membuka:
     `http://192.168.1.50/perpustakaan/`

---

# BAGIAN 2: CARA MEMULIHKAN DATA KETIKA MYSQL ATAU APACHE HILANG / CRASH

Situasi darurat seperti listrik padam mendadak, laptop kehabisan baterai saat menulis data, atau infeksi virus dapat menyebabkan XAMPP rusak, MySQL tidak bisa dibuka, atau Apache mogok. Ikuti panduan pemulihan berikut sesuai gejala yang dialami:

---

### KASUS 1: Error "MySQL shutdown unexpectedly" di XAMPP (Crash Database)

#### 🔍 Gejala:
Saat tombol **Start** MySQL di XAMPP Control Panel ditekan, status berubah merah dengan pesan kesalahan:
`Error: MySQL shutdown unexpectedly. This may be due to a blocked port, missing dependencies, improper privileges, a crash...`

#### 💡 Penyebab:
File transaksi InnoDB (`ib_logfile0`, `ib_logfile1`) korup akibat komputer mati mendadak saat MySQL sedang aktif.

#### 🛠️ Solusi Penyelamatan 100% Data Utuh (Trik Rescue Folder XAMPP):
> ⚠️ **PERINGATAN PENTING**: Jangan pernah menghapus file `ibdata1`! File tersebut adalah tempat seluruh isi tabel database Anda tersimpan!

Langkah perbaikan cepat tanpa kehilangan satu data pun:
1. Buka aplikasi **XAMPP Control Panel** dan klik **Stop** pada semua modul (Apache & MySQL).
2. Buka Windows File Explorer dan masuk ke folder:
   `C:\xampp\mysql\`
3. Ubah nama folder `data` menjadi `data_lama_rusak`.
4. Buat folder baru di dalam `C:\xampp\mysql\` dengan nama `data`.
5. Buka folder `C:\xampp\mysql\backup`.
6. Salin **seluruh isi** folder `backup` ke dalam folder `data` yang baru dibuat.
7. Sekarang, buka folder `data_lama_rusak`:
   - Salin folder database Anda: **`nafisah_nasyirah_perpustakaan`**
   - Tempel (*paste*) folder tersebut ke dalam folder `C:\xampp\mysql\data\`
8. Masih di folder `data_lama_rusak`:
   - Cari dan salin file bernama **`ibdata1`**
   - Tempel (*paste*) file `ibdata1` tersebut ke dalam folder `C:\xampp\mysql\data\` (Pilih *Replace / Timpa* jika diminta).
   - 🛑 **JANGAN SALIN** file `ib_logfile0` atau `ib_logfile1` dari folder lama (karena file inilah yang menyebabkan crash).
9. Buka kembali **XAMPP Control Panel**, lalu klik tombol **Start** pada MySQL.
10. **Hasil**: MySQL akan langsung berubah warna hijau (*Running*) normal, dan seluruh data perpustakaan tetap utuh sempurna!

---

### KASUS 2: Apache Hilang / Tidak Mau Start (Port 80/443 Bentrok)

#### 🔍 Gejala:
Modul Apache tidak bisa di-start atau berhenti seketika. Pada log XAMPP tertulis:
`Port 80 in use by "Unable to open process" with PID...` atau `Port 443 in use...`

#### 💡 Penyebab:
Port standar web server (80 atau 443) telah dipakai oleh aplikasi lain seperti IIS (*World Wide Web Publishing Service*), Skype, VMware, atau antivirus.

#### 🛠️ Solusi:
Ubah port Apache ke port alternatif (misal: 8080):
1. Buka **XAMPP Control Panel**.
2. Pada baris **Apache**, klik tombol **Config** &rarr; pilih **Apache (httpd.conf)**.
3. Tekan **Ctrl + F** di Notepad, cari baris `Listen 80`.
4. Ubah menjadi:
   ```apache
   Listen 8080
   ```
5. Cari juga baris `ServerName localhost:80`, ubah menjadi:
   ```apache
   ServerName localhost:8080
   ```
6. Simpan file (**Ctrl + S**), lalu tutup Notepad.
7. Klik **Start** pada Apache di XAMPP. Sekarang Apache akan langsung berwarna hijau.
8. Buka browser dengan alamat:
   `http://localhost:8080/perpustakaan/`

---

### KASUS 3: Service Apache/MySQL Hilang dari XAMPP Control Panel

#### 🔍 Gejala:
Terdapat tanda silang merah `[X]` di samping tombol modul Apache atau MySQL di XAMPP Control Panel, atau muncul pesan *Service not installed*.

#### 🛠️ Solusi:
1. Jalankan XAMPP Control Panel dengan hak administrator:
   - Klik kanan shortcut **XAMPP Control Panel** &rarr; pilih **Run as administrator**.
2. Klik tanda centang pada kotak di samping Apache dan MySQL untuk mendaftarkan layanan (*Windows Service*).
3. Atau jalankan skrip registrasi manual bawaan XAMPP:
   - Buka `C:\xampp\apache\apache_installservice.bat` (Klik kanan &rarr; *Run as administrator*).
   - Buka `C:\xampp\mysql\mysql_installservice.bat` (Klik kanan &rarr; *Run as administrator*).
4. Restart komputer dan buka kembali XAMPP.

---

### KASUS 4: Komputer Diinstal Ulang / Folder XAMPP Rusak / Terhapus

Jika komputer terkena virus parah atau Windows terpaksa diinstal ulang:

#### Skenario 4A: Jika Anda Memiliki File Backup SQL
1. Pasang XAMPP baru di `C:\xampp`.
2. Letakkan folder web perpustakaan ke `C:\xampp\htdocs\perpustakaan`.
3. Buka folder `backups/` di dalam project perpustakaan, atau ambil file backup dari Flashdisk / Dokumen:
   - Jalankan `KLIK_UNTUK_PULIHKAN.bat`, atau
   - Buka phpMyAdmin, buat database `nafisah_nasyirah_perpustakaan`, lalu impor file `.sql` cadangan Anda.

#### Skenario 4B: Komputer Diinstal Ulang Tanpa Format Partisi (Ada Folder `Windows.old`)
Jika teknisi menginstal ulang Windows tanpa memformat harddisk:
1. Buka File Explorer &rarr; buka drive `C:\`.
2. Cari folder bernama `C:\Windows.old`.
3. Telusuri folder:
   `C:\Windows.old\xampp\mysql\data\`
4. Salin folder `nafisah_nasyirah_perpustakaan` dan file `ibdata1` dari sana ke folder XAMPP yang baru (`C:\xampp\mysql\data\`).
5. Seluruh data perpustakaan dari sebelum komputer diinstal ulang akan terselamatkan!

---

### KASUS 5: Database Tidak Sengaja Terhapus di phpMyAdmin

Jika seseorang tidak sengaja menekan tombol *Drop Database* di phpMyAdmin:

1. Buka folder:
   `C:\xampp\htdocs\perpustakaan\backups\`
2. Klik ganda file:
   `KLIK_UNTUK_PULIHKAN.bat`
3. Ketik **Y** lalu tekan **Enter**.
4. Dalam 5 detik, database `nafisah_nasyirah_perpustakaan` beserta seluruh isinya akan dibuat ulang dan pulih 100%!

---

# BAGIAN 3: STANDAR OPERASIONAL PROSEDUR (SOP) PENCEGAHAN KEHILANGAN DATA

Untuk menjamin perlindungan jangka panjang terhadap aset data perpustakaan sekolah:

### A. Jadwal Rutin Backup Mingguan
Setiap hari Jumat sebelum jam operasional perpustakaan berakhir:
1. Buka folder:
   `C:\xampp\htdocs\perpustakaan\backups\`
2. Klik ganda file:
   `KLIK_UNTUK_BACKUP.bat`
3. Skrip otomatis membuat 3 salinan cadangan sekaligus:
   - Salinan 1: Di luar folder XAMPP (`C:\Backup_Perpustakaan\`) agar aman jika XAMPP diutak-atik.
   - Salinan 2: Di folder dokumen pengguna (`Dokumen\Backup_Perpustakaan\`).
   - Salinan 3: Di folder web `database\nafisah_nasyirah_perpustakaan.sql` untuk kesiapan ekspor instan.
4. Salin satu berkas cadangan terbaru tersebut ke dalam **Flashdisk khusus perpustakaan**.

---

### B. Otomatisasi Backup Harian via Windows Task Scheduler
Agar staf tidak perlu mengingat jadwal backup manual, atur Windows agar mencadangkan data secara otomatis setiap hari pukul 16.00:
1. Tekan tombol **Windows + R**, ketik `taskschd.msc`, tekan Enter.
2. Di panel sebelah kanan, klik **Create Basic Task... (Buat Tugas Dasar)**.
3. Beri nama tugas: `Backup Otomatis Perpustakaan SMKN 5`.
4. Pilih pemicu (*Trigger*): **Daily (Setiap Hari)**, atur jam ke `16:00`.
5. Pilih aksi (*Action*): **Start a program**.
6. Klik **Browse** dan arahkan ke file:
   `C:\xampp\htdocs\perpustakaan\backups\KLIK_UNTUK_BACKUP.bat`
7. Klik **Next** lalu **Finish**.
8. Sekarang sistem akan secara otomatis menyimpan cadangan data setiap sore tanpa campur tangan manusia!

---

### C. Sinkronisasi Cadangan ke Cloud (Google Drive / OneDrive)
Untuk proteksi terhadap risiko hardware rusak atau komputer hilang:
1. Pasang aplikasi **Google Drive for Desktop** atau gunakan **Microsoft OneDrive**.
2. Arahkan folder sinkronisasi ke folder `C:\Backup_Perpustakaan`.
3. Setiap kali backup dibuat, file database SQL otomatis terunggah ke penyimpanan awan (*cloud*) sekolah secara aman.

---

# 📞 HUBUNGI BANTUAN TEKNIS

Apabila Anda mengalami kendala teknis darurat, kerusakan sistem yang belum teratasi oleh panduan ini, atau membutuhkan bantuan pemulihan data khusus:

- 👤 **Pengembang Aplikasi**: **Heri Susanto**
- 📱 **WhatsApp**: **081362733320**
- ✉️ **Email**: **itemsusanto@gmail.com**
- 🏫 **Institusi**: SMKN 5 Telkom Banda Aceh

*Kami siap memberikan panduan teknis langsung (via AnyDesk / TeamViewer / WhatsApp) demi kelancaran operasional perpustakaan sekolah.*
