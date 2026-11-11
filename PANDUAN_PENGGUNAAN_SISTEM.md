# 📖 BUKU PANDUAN PENGGUNAAN SISTEM
**Sistem Informasi Manajemen Perpustakaan Digital SMKN 5 Telkom Banda Aceh**

Buku panduan ini disusun sebagai rujukan lengkap pengoperasian seluruh modul dan fitur aplikasi perpustakaan bagi administrator perpustakaan, staf/petugas sirkulasi, maupun siswa.

---

## 📑 DAFTAR ISI
1. [Akses Masuk & Login Sistem](#1-akses-masuk--login-sistem)
2. [Dashboard Utama & Grafik Kunjungan Interaktif](#2-dashboard-utama--grafik-kunjungan-interaktif)
3. [Manajemen Koleksi Buku](#3-manajemen-koleksi-buku)
4. [Manajemen Data Anggota Perpustakaan](#4-manajemen-data-anggota-perpustakaan)
5. [Cetak Kartu Anggota (Barcode Besar & QR Code)](#5-cetak-kartu-anggota-barcode-besar--qr-code)
6. [Sirkulasi: Peminjaman Buku & Tiket Peminjaman](#6-sirkulasi-peminjaman-buku--tiket-peminjaman)
7. [Sirkulasi: Pengembalian Buku & Denda Otomatis](#7-sirkulasi-pengembalian-buku--denda-otomatis)
8. [Presensi Kunjungan & Scan QR Kamera](#8-presensi-kunjungan--scan-qr-kamera)
9. [Surat Keterangan Bebas Pustaka & Verifikasi QR](#9-surat-keterangan-bebas-pustaka--verifikasi-qr)
10. [Laporan Sirkulasi & Statistik](#10-laporan-sirkulasi--statistik)
11. [Pengaturan Kop Surat, Kepala Perpustakaan & Tanda Tangan](#11-pengaturan-kop-surat-kepala-perpustakaan--tanda-tangan)

---

## 1. Akses Masuk & Login Sistem
Aplikasi menyediakan dua jenis hak akses (peran):
- **Administrator / Petugas**: Memiliki kendali penuh terhadap seluruh data buku, anggota, sirkulasi, laporan, dan pengaturan kop surat/tanda tangan.
  - Masuk melalui tab **"Administrator"** di form login.
  - Username default: `admin` | Password default: `admin123`.
- **Siswa / Anggota**: Digunakan oleh siswa untuk melihat status peminjaman, riwayat pengembalian, mencetak kartu anggota mandiri, dan mengecek status surat bebas pustaka.
  - Masuk melalui tab **"Siswa / Anggota"**.
  - Masukkan **Nomor Induk Siswa (NISN / Kode Anggota)** sebagai username dan password.

---

## 2. Dashboard Utama & Grafik Kunjungan Interaktif
Pada menu Dashboard Admin, ditampilkan ringkasan statistik terkini:
- Total judul koleksi buku dan jumlah eksemplar tersedia.
- Total anggota terdaftar dan siswa yang sedang aktif meminjam buku.
- **Grafik Tren Kunjungan Interaktif**:
  - Terdapat tombol tab **Harian**, **Bulanan**, dan **Tahunan**.
  - Grafik digambar secara dinamis berbasis data riil presensi perpustakaan sekolah.
  - Arahkan kursor (*hover*) pada grafik batang untuk melihat jumlah pasti pengunjung pada tanggal atau bulan tersebut.
- Tombol aksi cepat: *Tambah Buku Baru*, *Catat Peminjaman*, *Scan Pengunjung*, dan *Cetak Bebas Pustaka*.

---

## 3. Manajemen Koleksi Buku
Menu **Buku** (`index.php?page=admin_books`) digunakan untuk mengelola perbendaharaan pustaka:
1. **Tambah Buku**:
   - Klik tombol **"Tambah Buku Baru"**.
   - Masukkan Kode Buku / ISBN, Judul Buku, Pengarang, Penerbit, Tahun Terbit, Kategori, Jumlah Stok, Nomor Rak, dan Deskripsi.
   - Unggah gambar cover buku jika tersedia.
2. **Cari & Filter Buku**:
   - Kolom pencarian responsif mendukung pencarian berdasarkan judul, pengarang, penerbit, atau kategori.
3. **Cetak Barcode Buku**:
   - Setiap buku memiliki barcode yang dapat dicetak langsung pada stiker untuk ditempelkan pada punggung buku.

---

## 4. Manajemen Data Anggota Perpustakaan
Menu **Anggota** (`index.php?page=admin_members`) digunakan untuk mendata seluruh pemustaka:
1. **Pilihan Jurusan SMKN 5 Telkom Banda Aceh**:
   - Form pendaftaran anggota telah disesuaikan dengan jurusan resmi SMKN 5 Telkom:
     - *Teknik Komputer dan Jaringan (TKJ)*
     - *Rekayasa Perangkat Lunak (RPL)*
     - *Desain Komunikasi Visual (DKV)*
     - *Teknik Jaringan Akses Telekomunikasi (TJAT)*
     - *Teknik Transmisi Telekomunikasi (TT)*
2. **Keamanan & Proteksi Penghapusan Anggota**:
   - Sistem **melindungi data**: Siswa yang masih memiliki pinjaman buku yang belum dikembalikan **TIDAK DAPAT DIHAPUS** untuk mencegah kehilangan aset buku perpustakaan.
   - Siswa yang bersih dari pinjaman aktif dapat dihapus dengan aman tanpa merusak arsip nomor surat bebas pustaka yang pernah diterbitkan.

---

## 5. Cetak Kartu Anggota (Barcode Besar & QR Code)
Fitur kartu anggota dirancang sesuai standar kartu saku (*wallet card*) modern:
1. Akses menu **Anggota** -> klik tombol **"Cetak Kartu"** pada baris siswa yang bersangkutan.
2. Tampilan Kartu Bagian Depan:
   - Identitas nama, foto, NISN, jurusan, dan **Kode Batang (Barcode) Ukuran Besar** yang sangat mudah dibaca oleh barcode scanner laser/kamera.
3. Tampilan Kartu Bagian Belakang:
   - **QR Code Resolusi Tinggi**: Memudahkan scan cepat via kamera smartphone untuk absensi instan.
   - Tata tertib perpustakaan.
   - Alamat lengkap SMKN 5 Telkom Banda Aceh.
   - Tanggal cetak resmi dan blok tanda tangan langsung Kepala Perpustakaan lengkap beserta nama dan NIP resmi dari menu Pengaturan.

---

## 6. Sirkulasi: Peminjaman Buku & Tiket Peminjaman
Menu **Peminjaman** (`index.php?page=admin_loans`):
1. **Mencatat Peminjaman**:
   - Masukkan ID Anggota atau scan barcode kartu siswa.
   - Masukkan kode buku atau scan barcode buku.
   - Tentukan tanggal jatuh tempo (default: 7 hari).
   - Klik **"Simpan Peminjaman"**.
2. **Tiket Peminjaman (Struk Peminjaman)**:
   - Setelah peminjaman dicatat, sistem dapat mencetak tiket struk bukti peminjaman yang memuat Dual QR Code & Barcode untuk mempermudah petugas saat proses pengembalian buku.

---

## 7. Sirkulasi: Pengembalian Buku & Denda Otomatis
1. Masukkan kode peminjaman atau scan struk tiket pada menu Peminjaman / Pengembalian.
2. Sistem otomatis menghitung durasi keterlambatan. Jika melewati jatuh tempo, denda dihitung secara otomatis berdasarkan tarif denda per hari yang diatur di Pengaturan.
3. Petugas memeriksa kondisi buku:
   - Jika buku dalam kondisi baik, klik **"Proses Pengembalian"**.
   - Stok buku di rak otomatis bertambah kembali secara seketika (*real-time*).

---

## 8. Presensi Kunjungan & Scan QR Kamera
Menu **Kunjungan** (`index.php?page=admin_visits`):
1. **Fitur Scan QR Kamera Otomatis**:
   - Arahkan kamera laptop/webcam ke QR Code kartu anggota siswa.
   - Sistem otomatis mendeteksi kode siswa dalam hitungan milidetik.
   - Dilengkapi efek suara beep dan konfirmasi suara ramah pemustaka.
2. **Pencatatan Manual**:
   - Jika siswa tidak membawa kartu, petugas cukup mengetikkan NISN atau nama siswa pada kolom presensi cepat.
3. Seluruh riwayat kunjungan tersimpan rapi dan menjadi sumber data untuk Laporan Kunjungan bulanan/tahunan.

---

## 9. Surat Keterangan Bebas Pustaka & Verifikasi QR
Menu **Bebas Pustaka** (`index.php?page=admin_clearance`):
Fitur unggulan untuk siswa tingkat akhir yang mengurus kelulusan atau pengambilan ijazah.

1. **Penerbitan Surat Bebas Pustaka**:
   - Cari nama atau NISN siswa.
   - Jika siswa masih meminjam buku, sistem menampilkan daftar buku yang belum dikembalikan serta tombol **"Pelunasan & Terbitkan"**.
   - Jika siswa sudah bersih dari tanggungan, klik **"Cetak Surat Bebas Pustaka"**.
2. **Format Surat Resmi (A4)**:
   - Menggunakan Kop Surat Resmi Pemerintah Aceh & SMKN 5 Telkom Banda Aceh.
   - Format didesain **PAS TEPAT 1 HALAMAN A4** sehingga tidak terpotong ke halaman berikutnya.
   - Memuat nomor registrasi surat otomatis, tabel identitas siswa, ringkasan sirkulasi nihil pinjaman, tanda tangan Kepala Perpustakaan & Petugas, serta **QR Code Verifikasi Dokumen**.
3. **Format Kartu Saku Bebas Pustaka**:
   - Selain format surat A4, tersedia opsi cetak kartu saku bebas pustaka yang praktis dibawa oleh siswa.
4. **Verifikasi Keaslian Dokumen (Anti-Pemalsuan)**:
   - Siapa pun (termasuk pihak tata usaha sekolah atau universitas) dapat memindai (*scan*) QR Code yang ada di surat menggunakan kamera smartphone.
   - Browser akan langsung membuka halaman validasi resmi yang menyatakan:
     **"DOKUMEN RESMI TERVERIFIKASI SAH & ASLI"** (dengan centang hijau resmi).

---

## 10. Laporan Sirkulasi & Statistik
Menu **Laporan** (`index.php?page=reports`):
- Laporan Peminjaman dan Pengembalian Buku (berdasarkan rentang tanggal).
- Laporan Denda Keterlambatan dan Kas Masuk.
- Laporan Rekapitulasi Kunjungan Pemustaka.
- Laporan Koleksi Buku Paling Populer / Sering Dipinjam.
- Seluruh laporan dapat diekspor langsung ke format **Cetak / PDF** dengan tanda tangan pengesahan resmi.

---

## 11. Pengaturan Kop Surat, Kepala Perpustakaan & Tanda Tangan
Menu **Pengaturan** (`index.php?page=settings`):
Pada bagian **"Pengaturan Tanda Tangan Cetak Dokumen & Kop Surat"**:
- **Nama Kepala Perpustakaan**: Masukkan nama lengkap beserta gelar (contoh: `Sahriati, S. Pd`).
- **NIP Kepala Perpustakaan**: Masukkan nomor induk pegawai resmi.
- **Nama Petugas Sirkulasi / Administrasi**: Nama staf pengelola perpustakaan.
- **Kota Dokumen**: Default: `Banda Aceh`.
- **Logo Sekolah**: Dapat diubah dengan mengunggah gambar logo sekolah terbaru.

Seluruh data yang diisi pada menu Pengaturan ini akan langsung tersinkronisasi secara otomatis ke Kartu Anggota, Surat Bebas Pustaka, dan seluruh Lembar Laporan Cetak!
