<?php
require_once __DIR__ . '/config.php';

// Cek akses: hanya administrator yang berhak melihat panduan migrasi dan pemulihan
auth('admin');

$schoolTitle = get_school_name();
$schoolSub = get_setting('navbar_subtitle', SCHOOL_SUB);
$schoolLogo = get_setting('navbar_logo', 'assets/img/logo.jpg');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Migrasi &amp; Pemulihan Data Bencana - <?=e($schoolTitle)?></title>
    <link rel="stylesheet" href="<?=u('style.css')?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.65;
            -webkit-font-smoothing: antialiased;
        }

        .guide-topbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }

        .guide-topbar-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14.5px;
            font-weight: 700;
        }

        .guide-topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .guide-container {
            max-width: 960px;
            margin: 30px auto;
            background: #ffffff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .install-cover {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #0284c7 100%);
            color: #ffffff;
            padding: 70px 45px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 6px solid #6366f1;
        }

        .install-cover img.logo {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: #ffffff;
            padding: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }

        .install-cover h1 {
            font-size: 27px;
            font-weight: 900;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.25;
        }

        .install-cover h2 {
            font-size: 17px;
            font-weight: 600;
            color: #c7d2fe;
            margin: 0 0 20px;
        }

        .install-badge {
            display: inline-block;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.35);
            padding: 5px 16px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .author-card {
            margin-top: 28px;
            background: rgba(15,23,42,0.65);
            border: 1.5px solid rgba(129,140,248,0.45);
            border-radius: 12px;
            padding: 16px 28px;
            display: inline-block;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.25);
            backdrop-filter: blur(8px);
        }

        .install-content {
            padding: 45px 50px 60px;
        }

        .section-header-banner {
            background: #f8fafc;
            border-left: 5px solid #4f46e5;
            padding: 14px 20px;
            margin: 35px 0 20px;
            border-radius: 0 8px 8px 0;
        }

        .section-header-banner h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #1e1b4b;
        }

        .step-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 28px;
            position: relative;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            transition: all 0.2s;
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .step-badge {
            background: #4f46e5;
            color: #ffffff;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(79,70,229,0.3);
        }

        .step-badge.alert {
            background: #dc2626;
            box-shadow: 0 3px 10px rgba(220,38,38,0.3);
        }

        .step-badge.warning {
            background: #d97706;
            box-shadow: 0 3px 10px rgba(217,119,6,0.3);
        }

        .step-badge.success {
            background: #059669;
            box-shadow: 0 3px 10px rgba(5,150,105,0.3);
        }

        .step-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }

        .code-box {
            background: #0f172a;
            color: #38bdf8;
            padding: 12px 16px;
            border-radius: 8px;
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: 13px;
            margin: 10px 0;
            overflow-x: auto;
            border-left: 4px solid #6366f1;
        }

        .callout-box {
            border-radius: 10px;
            padding: 14px 18px;
            margin: 14px 0;
            font-size: 13.5px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .callout-box.info {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .callout-box.danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .callout-box.warn {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .guide-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 13px;
        }

        .guide-table th {
            background: #4f46e5;
            color: #ffffff;
            padding: 10px 14px;
            text-align: left;
            font-weight: 700;
        }

        .guide-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        .guide-table tr:nth-child(even) td {
            background: #f8fafc;
        }

        .footer-note {
            background: #0f172a;
            color: #cbd5e1;
            padding: 24px;
            text-align: center;
            font-size: 12.5px;
            border-top: 4px solid #6366f1;
        }

        @media print {
            .guide-topbar { display: none !important; }
            body { background: #ffffff !important; }
            .guide-container { box-shadow: none !important; border: none !important; margin: 0 !important; max-width: 100% !important; }
            .install-cover { padding: 40px 20px !important; }
            .install-content { padding: 20px !important; }
            .step-card { page-break-inside: avoid; border-color: #cbd5e1 !important; }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar -->
    <div class="guide-topbar">
        <div class="guide-topbar-title">
            <span>🛡️</span>
            <span>Panduan Migrasi Sistem &amp; Pemulihan Data Bencana - SMKN 5 Telkom</span>
        </div>
        <div class="guide-topbar-actions">
            <button onclick="window.print()" class="btn btn-sm btn-primary" style="background:#6366f1;border-color:#4f46e5;font-weight:700;">
                🖨️ Cetak / Simpan PDF
            </button>
            <a href="<?=u('panduan_instalasi.php')?>" class="btn btn-sm btn-outline-light" style="font-weight:600;">
                📘 Panduan Instalasi
            </a>
            <a href="<?=u('index.php?page=dashboard')?>" class="btn btn-sm btn-secondary" style="font-weight:600;">
                Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Container Utama -->
    <div class="guide-container">

        <!-- Cover Header -->
        <div class="install-cover">
            <img src="<?=u($schoolLogo)?>" alt="Logo Sekolah" class="logo" onerror="this.src='https://ui-avatars.com/api/?name=SMKN5&size=120&background=4f46e5&color=fff'">
            <div class="install-badge">SOP Pemeliharaan &amp; Disaster Recovery</div>
            <h1>PANDUAN MIGRASI SISTEM &amp; PEMULIHAN DATA</h1>
            <h2>Sistem Informasi Manajemen Perpustakaan Digital SMKN 5 Telkom Banda Aceh</h2>
            <div style="font-size:13.5px;opacity:0.9;max-width:700px;margin:0 auto;">
                Panduan komprehensif penanganan migrasi ke server baru dan prosedur pemulihan data ketika Apache / MySQL rusak, crash ("shutdown unexpectedly"), hilang, atau komputer diinstal ulang.
            </div>

            <!-- Author Card -->
            <div class="author-card">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:#a5b4fc;margin-bottom:4px;font-weight:800;">
                    Disusun Oleh Pengembang Aplikasi
                </div>
                <div style="font-size:17px;font-weight:900;color:#ffffff;letter-spacing:0.2px;">
                    HERI SUSANTO
                </div>
                <div style="font-size:12.5px;color:#c7d2fe;margin-top:2px;">
                    WhatsApp: <b>081362733320</b> · Email: <b>itemsusanto@gmail.com</b>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="install-content">

            <!-- INTRO CALLOUT -->
            <div class="callout-box info">
                <span style="font-size:22px;">💡</span>
                <div>
                    <b>Kepada Pengelola Perpustakaan &amp; Teknisi:</b>
                    <br>Jangan panik apabila server perpustakaan tiba-tiba tidak dapat dibuka atau MySQL mengalami crash akibat mati lampu mendadak. Seluruh data transaksi, peminjaman buku, dan anggota perpustakaan tersimpan dalam tabel InnoDB dan file dump cadangan otomatis yang <b>dapat dipulihkan 100%</b> menggunakan petunjuk di bawah ini.
                </div>
            </div>

            <!-- BAGIAN 1: MIGRASI -->
            <div class="section-header-banner">
                <h2>BAGIAN 1: PROSEDUR MIGRASI KE KOMPUTER / SERVER BARU</h2>
            </div>

            <!-- KARTU 1: CHECKLIST MIGRASI -->
            <div class="step-card">
                <div class="step-header">
                    <div class="step-badge">1</div>
                    <div>
                        <h3 class="step-title">Langkah 1: Siapkan Salinan Berkas dari Komputer Lama</h3>
                        <small style="color:#64748b;">Mencadangkan folder web dan basis data</small>
                    </div>
                </div>
                <p>Siapkan Flashdisk atau Harddisk Eksternal, lalu salin berkas-berkas berikut:</p>
                <ol style="padding-left:20px;">
                    <li><b>Folder Aplikasi Web Perpustakaan:</b>
                        <div class="code-box">C:\laragon\www\perpustakaan  (Jika menggunakan Laragon)<br>C:\xampp\htdocs\perpustakaan   (Jika menggunakan XAMPP)</div>
                        <i>Pastikan folder <code>uploads/</code> (berisi foto cover dan foto profil) dan folder <code>database/</code> ikut tersalin lengkap.</i>
                    </li>
                    <li><b>Database SQL Cadangan:</b>
                        <br>Jalankan file pencadangan otomatis 1-klik di komputer lama:
                        <div class="code-box">backups\KLIK_UNTUK_BACKUP.bat</div>
                        File cadangan otomatis akan terbuat di <code>C:\Backup_Perpustakaan\</code> dan folder <code>backups/</code>.
                    </li>
                </ol>
            </div>

            <!-- KARTU 2: INSTALASI DI KOMPUTER BARU -->
            <div class="step-card">
                <div class="step-header">
                    <div class="step-badge">2</div>
                    <div>
                        <h3 class="step-title">Langkah 2: Pemasangan Web Server di Komputer Baru (Laragon / XAMPP)</h3>
                        <small style="color:#64748b;">Menyiapkan lingkungan server baru</small>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:10px;">
                    <div style="background:#f0fdf4;border:1.5px solid #22c55e;border-radius:8px;padding:12px 14px;">
                        <b style="color:#15803d;font-size:13px;display:block;margin-bottom:6px;">Jika Menggunakan Laragon (Direkomendasikan):</b>
                        <ol style="margin:0;padding-left:18px;font-size:12px;color:#166534;line-height:1.6;">
                            <li>Install Laragon di <code>C:\laragon</code>.</li>
                            <li>Salin folder aplikasi ke: <code>C:\laragon\www\perpustakaan</code>.</li>
                            <li>Buka Laragon dan klik <b>Start All</b>.</li>
                        </ol>
                    </div>
                    <div style="background:#fff7ed;border:1.5px solid #f97316;border-radius:8px;padding:12px 14px;">
                        <b style="color:#c2410c;font-size:13px;display:block;margin-bottom:6px;">Jika Menggunakan XAMPP:</b>
                        <ol style="margin:0;padding-left:18px;font-size:12px;color:#9a3412;line-height:1.6;">
                            <li>Install XAMPP di <code>C:\xampp</code>.</li>
                            <li>Salin folder aplikasi ke: <code>C:\xampp\htdocs\perpustakaan</code>.</li>
                            <li>Buka XAMPP Control Panel, klik <b>Start</b> pada Apache &amp; MySQL.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- KARTU 3: PULIHKAN DATA 1-KLIK -->
            <div class="step-card">
                <div class="step-header">
                    <div class="step-badge success">3</div>
                    <div>
                        <h3 class="step-title">Langkah 3: Pemulihan Database Otomatis 1-Klik</h3>
                        <small style="color:#64748b;">Mengimpor seluruh tabel dan data tanpa perlu buka phpMyAdmin</small>
                    </div>
                </div>
                <p>Di komputer baru, Anda tidak perlu repot membuat tabel satu per satu:</p>
                <ol style="padding-left:20px;">
                    <li>Buka folder <code>backups\</code> di dalam folder project (atau <code>C:\Backup_Perpustakaan\</code>).</li>
                    <li>Klik ganda file:
                        <b><code>KLIK_UNTUK_PULIHKAN.bat</code></b>
                    </li>
                    <li>Ketik huruf <b>Y</b> saat diminta konfirmasi, lalu tekan <b>Enter</b>.</li>
                    <li>Skrip secara otomatis mendeteksi MySQL di <b>Laragon</b> maupun <b>XAMPP</b>, membuat database <code>nafisah_nasyirah_perpustakaan</code> dan memulihkan seluruh data dalam waktu &lt; 3 detik!</li>
                    <li>Buka browser: <code>http://localhost/perpustakaan/</code> (atau <code>http://perpustakaan.test</code> jika pakai Laragon). Selesai!</li>
                </ol>
            </div>

            <!-- BAGIAN 2: PENYELAMATAN BENCANA -->
            <div class="section-header-banner" style="border-left-color:#dc2626;background:#fff5f5;">
                <h2 style="color:#991b1b;">BAGIAN 2: PENYELAMATAN DATA KETIKA MYSQL / APACHE RUSAK ATAU HILANG</h2>
            </div>

            <!-- KASUS A: MYSQL SHUTDOWN UNEXPECTEDLY -->
            <div class="step-card" style="border-color:#fca5a5;">
                <div class="step-header" style="border-bottom-color:#fecaca;">
                    <div class="step-badge alert">!</div>
                    <div>
                        <h3 class="step-title" style="color:#b91c1c;">Kasus 1: Error "MySQL shutdown unexpectedly" di XAMPP</h3>
                        <small style="color:#dc2626;">Masalah paling sering terjadi jika komputer mati mendadak saat MySQL aktif</small>
                    </div>
                </div>

                <div class="callout-box danger">
                    <span>🛑</span>
                    <div>
                        <b>JANGAN PANIK &amp; JANGAN HAPUS FILE <code>ibdata1</code>!</b>
                        <br>Seluruh tabel InnoDB Anda tersimpan di file <code>ibdata1</code>. Kerusakan hanya terjadi pada file log transaksi (<code>ib_logfile</code>).
                    </div>
                </div>

                <h4 style="margin:16px 0 8px;font-size:15px;color:#1e293b;">Cara Perbaikan Cepat (Metode Otomatis):</h4>
                <ol style="padding-left:20px;">
                    <li>Buka folder: <code>C:\xampp\htdocs\perpustakaan\backups\</code></li>
                    <li>Klik kanan file: <b><code>PERBAIKI_MYSQL_CRASH.bat</code></b> &rarr; pilih <i>Run as administrator</i>.</li>
                    <li>Ketik <b>Y</b> dan tekan Enter. Skrip akan meregenerasi sistem log yang rusak tanpa menyentuh data database perpustakaan Anda.</li>
                    <li>Start kembali MySQL di XAMPP. Status akan langsung berubah HIJAU!</li>
                </ol>

                <h4 style="margin:16px 0 8px;font-size:15px;color:#1e293b;">Cara Perbaikan Manual (Langkah demi Langkah):</h4>
                <ol style="padding-left:20px;line-height:1.7;">
                    <li>Klik <b>Stop</b> pada semua layanan di XAMPP Control Panel.</li>
                    <li>Buka File Explorer &rarr; masuk ke <code>C:\xampp\mysql\</code>.</li>
                    <li>Ganti nama folder <code>data</code> menjadi <code>data_lama_rusak</code>.</li>
                    <li>Buat folder baru bernama <code>data</code> di dalam <code>C:\xampp\mysql\</code>.</li>
                    <li>Salin seluruh file dari <code>C:\xampp\mysql\backup\</code> ke dalam folder <code>data</code> yang baru.</li>
                    <li>Buka folder <code>data_lama_rusak</code>, salin folder <b><code>nafisah_nasyirah_perpustakaan</code></b> ke folder <code>data</code> baru.</li>
                    <li>Salin file <b><code>ibdata1</code></b> dari <code>data_lama_rusak</code> ke folder <code>data</code> baru (pilih Replace / Timpa).</li>
                    <li><b style="color:#dc2626;">DILARANG menyalin</b> file <code>ib_logfile0</code> atau <code>ib_logfile1</code> dari folder lama.</li>
                    <li>Nyalakan kembali tombol <b>Start</b> pada MySQL di XAMPP Control Panel. Database kembali normal 100%!</li>
                </ol>
            </div>

            <!-- KASUS B: APACHE PORT CRASH -->
            <div class="step-card">
                <div class="step-header">
                    <div class="step-badge warning">2</div>
                    <div>
                        <h3 class="step-title">Kasus 2: Apache Gagal Start (Port 80 / 443 Bentrok)</h3>
                        <small style="color:#64748b;">Port web server digunakan oleh aplikasi lain (IIS, Skype, VMware)</small>
                    </div>
                </div>
                <p>Ubah port Apache ke port alternatif <code>8080</code> dengan cara:</p>
                <ol style="padding-left:20px;">
                    <li>Buka <b>XAMPP Control Panel</b>.</li>
                    <li>Pada baris Apache, klik tombol <b>Config</b> &rarr; pilih <b>Apache (httpd.conf)</b>.</li>
                    <li>Tekan <b>Ctrl + F</b>, cari kata <code>Listen 80</code> &rarr; ganti menjadi <code>Listen 8080</code>.</li>
                    <li>Cari kata <code>ServerName localhost:80</code> &rarr; ganti menjadi <code>ServerName localhost:8080</code>.</li>
                    <li>Simpan file (Ctrl + S) dan klik <b>Start</b> pada Apache.</li>
                    <li>Akses aplikasi melalui alamat baru:
                        <div class="code-box">http://localhost:8080/perpustakaan/</div>
                    </li>
                </ol>
            </div>

            <!-- KASUS C: KOMPUTER DIINSTAL ULANG -->
            <div class="step-card">
                <div class="step-header">
                    <div class="step-badge">3</div>
                    <div>
                        <h3 class="step-title">Kasus 3: Komputer Diinstal Ulang (Folder Windows.old)</h3>
                        <small style="color:#64748b;">Menyelamatkan data dari instalasi Windows sebelumnya</small>
                    </div>
                </div>
                <p>Jika Windows diinstal ulang tanpa pemformatan drive total, Windows menyimpan file lama di <code>C:\Windows.old</code>:</p>
                <ol style="padding-left:20px;">
                    <li>Buka <code>C:\Windows.old\xampp\mysql\data\</code>.</li>
                    <li>Salin folder <code>nafisah_nasyirah_perpustakaan</code> dan file <code>ibdata1</code> ke folder instalasi XAMPP baru di <code>C:\xampp\mysql\data\</code>.</li>
                    <li>Salin folder <code>C:\Windows.old\xampp\htdocs\perpustakaan</code> ke <code>C:\xampp\htdocs\perpustakaan</code>.</li>
                    <li>Aplikasi dan seluruh data masa lampau langsung aktif kembali!</li>
                </ol>
            </div>

            <!-- BAGIAN 3: SOP PENCEGAHAN -->
            <div class="section-header-banner" style="border-left-color:#059669;background:#f0fdf4;">
                <h2 style="color:#065f46;">BAGIAN 3: STANDAR OPERASIONAL PROSEDUR (SOP) PENCEGAHAN</h2>
            </div>

            <div class="step-card">
                <div class="step-header">
                    <div class="step-badge success">✓</div>
                    <div>
                        <h3 class="step-title">Praktik Terbaik Perlindungan Data Perpustakaan</h3>
                        <small style="color:#64748b;">Mencegah risiko kehilangan data secara proaktif</small>
                    </div>
                </div>
                <table class="guide-table">
                    <thead>
                        <tr>
                            <th>Tindakan Pengamanan</th>
                            <th>Frekuensi</th>
                            <th>Cara Melakukan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>Backup Otomatis 1-Klik</b></td>
                            <td>Setiap Jumat Sore</td>
                            <td>Klik ganda file <code>C:\xampp\htdocs\perpustakaan\backups\KLIK_UNTUK_BACKUP.bat</code>.</td>
                        </tr>
                        <tr>
                            <td><b>Salin ke Flashdisk Eksternal</b></td>
                            <td>2 Minggu Sekali</td>
                            <td>Salin file <code>.sql</code> dari <code>C:\Backup_Perpustakaan\</code> ke flashdisk khusus.</td>
                        </tr>
                        <tr>
                            <td><b>Windows Task Scheduler</b></td>
                            <td>Setiap Hari Pukul 16:00</td>
                            <td>Buat jadwal tugas otomatis untuk menjalankan <code>KLIK_UNTUK_BACKUP.bat</code> setiap hari saat tutup perpustakaan.</td>
                        </tr>
                        <tr>
                            <td><b>Sinkronisasi Cloud Drive</b></td>
                            <td>Otomatis Realtime</td>
                            <td>Tautkan folder <code>C:\Backup_Perpustakaan\</code> ke Google Drive for Desktop atau OneDrive sekolah.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- KONTAK DARURAT -->
            <div class="step-card" style="background:#eff6ff;border-color:#bfdbfe;">
                <div class="step-header" style="border-bottom-color:#dbeafe;">
                    <div class="step-badge" style="background:#2563eb;">📞</div>
                    <div>
                        <h3 class="step-title" style="color:#1e40af;">Pusat Dukungan Teknis Langsung</h3>
                        <small style="color:#3b82f6;">Hubungi jika membutuhkan pendampingan darurat via jarak jauh</small>
                    </div>
                </div>
                <p style="margin-bottom:8px;">
                    Jika Anda menemukan kendala yang tidak tercakup di dalam panduan ini, tim pengembang siap membantu melakukan remote assistance (AnyDesk / TeamViewer / Video Call WhatsApp):
                </p>
                <div style="font-size:15px;line-height:1.8;">
                    <b>Pengembang:</b> Heri Susanto<br>
                    <b>WhatsApp:</b> <a href="https://wa.me/6281362733320" target="_blank" style="color:#059669;font-weight:800;text-decoration:none;">081362733320 (Klik untuk Chat)</a><br>
                    <b>Email:</b> <a href="mailto:itemsusanto@gmail.com" style="color:#2563eb;font-weight:700;">itemsusanto@gmail.com</a><br>
                    <b>Lokasi:</b> SMKN 5 Telkom Banda Aceh
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer-note">
            <div><b>Sistem Informasi Manajemen Perpustakaan Digital · Versi 2.0</b></div>
            <div style="margin-top:4px;">
                Dokumen Resmi Panduan Migrasi &amp; Penyelamatan Bencana · Hak Cipta &copy; <?=date('Y')?> · <b>Heri Susanto</b> (SMKN 5 Telkom Banda Aceh)
            </div>
            <div style="margin-top:6px;font-size:11.5px;color:#94a3b8;">
                Kontak Dukungan Teknis: <b>081362733320</b> | <b>itemsusanto@gmail.com</b>
            </div>
        </div>

    </div>

</body>
</html>
