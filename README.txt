SISTEM INFORMASI PERPUSTAKAAN SMKN 5 TELKOM - V2

1. Ekstrak folder perpustakaan_smkn5_v2 ke C:\laragon\www\
2. Start All di Laragon.
3. Import database/perpustakaan_smkn5.sql melalui phpMyAdmin/HeidiSQL.
4. Buka http://localhost/perpustakaan_smkn5_v2/

LOGIN ADMIN
Username: admin
Password: admin123

FITUR
- Website publik/landing page.
- Pencarian buku tanpa login.
- Login admin dan anggota.
- Registrasi anggota mandiri + verifikasi admin.
- Pengajuan peminjaman anggota.
- Persetujuan dan pengembalian oleh admin.
- Stok otomatis.
- Notifikasi dashboard buku terlambat.
- Kunjungan harian.
- Rekap kunjungan berdasarkan periode.
- Cetak/Simpan PDF via browser.
- Export CSV yang dibuka di Excel.
- Kartu anggota digital dan cetak/Simpan PDF.
- Tanpa framework/CDN agar ringan di LAN.

LAN
Cari IPv4 server dengan ipconfig, misalnya 192.168.1.10. Dari perangkat lain di Wi-Fi buka:
http://192.168.1.10/perpustakaan_smkn5_v2/
Jika tidak bisa, izinkan Apache/httpd pada Windows Firewall jaringan Private.
