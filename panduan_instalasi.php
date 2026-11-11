<?php
require_once __DIR__ . '/config.php';

// Cek akses: hanya administrator yang berhak melihat tutorial instalasi
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
    <title>Buku Panduan Resmi Instalasi Sekolah Baru - <?=e($schoolTitle)?></title>
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

        /* Cover Section */
        .install-cover {
            background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #0284c7 100%);
            color: #ffffff;
            padding: 70px 45px 60px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 6px solid #34d399;
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
            font-size: 26px;
            font-weight: 900;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.3;
        }

        .install-cover h2 {
            font-size: 17px;
            font-weight: 600;
            color: #a7f3d0;
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
            margin-top: 26px;
            background: rgba(15,23,42,0.75);
            border: 1.5px solid rgba(52,211,153,0.5);
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

        .step-card {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 30px;
            position: relative;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            transition: all 0.2s;
        }

        .step-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
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
            background: #047857;
            color: #ffffff;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            flex-shrink: 0;
            box-shadow: 0 3px 10px rgba(4,120,87,0.3);
        }

        .step-title {
            margin: 0;
            font-size: 17.5px;
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
            border-left: 4px solid #10b981;
            position: relative;
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

        .callout-box.warn {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .callout-box.tip {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            color: #075985;
        }

        .guide-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
            font-size: 13px;
        }

        .guide-table th {
            background: #047857;
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
            background: #f8fafc;
            border-top: 1.5px solid #e2e8f0;
            padding: 24px;
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
        }

        @media print {
            .no-print, .guide-topbar {
                display: none !important;
            }
            body {
                background: #ffffff !important;
            }
            .guide-container {
                max-width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
            .step-card {
                page-break-inside: avoid;
            }
            .install-cover {
                padding: 40px 30px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Bar (Screen Only) -->
    <div class="guide-topbar no-print">
        <div class="guide-topbar-title">
            <span style="font-size:18px;">📘</span>
            <span>BUKU PANDUAN RESMI INSTALASI SEKOLAH BARU · <?=e($schoolTitle)?></span>
        </div>
        <div class="guide-topbar-actions">
            <a href="<?=u('index.php')?>" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);padding:6px 14px;border-radius:6px;font-size:12.5px;text-decoration:none;font-weight:700;">
                🏠 Kembali ke Aplikasi
            </a>
            <?php if (file_exists(__DIR__ . '/Buku_Panduan_Instalasi_dan_Server_Sekolah_Baru.pdf')): ?>
                <a href="<?=u('Buku_Panduan_Instalasi_dan_Server_Sekolah_Baru.pdf')?>" download="Buku_Panduan_Instalasi_dan_Server_Sekolah_Baru.pdf" class="btn btn-sm" style="background:#10b981;color:#fff;padding:6px 14px;border-radius:6px;font-size:12.5px;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    <?=icon('download', '', 14)?> Unduh PDF Resmi
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-sm btn-primary" style="padding:7px 18px;border-radius:6px;font-size:12.5px;font-weight:800;display:inline-flex;align-items:center;gap:6px;background:#047857;color:#fff;border:none;cursor:pointer;">
                <?=icon('print', '', 15)?> Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Main Container -->
    <div class="guide-container">

        <!-- Cover Section -->
        <div class="install-cover">
            <img src="<?=u($schoolLogo)?>" class="logo" alt="Logo <?=e($schoolTitle)?>">
            <div class="install-badge">DOKUMEN RESMI PENGEMBANG SISTEM · EDISI 2026</div>
            <h1 style="margin-top:14px;">BUKU PANDUAN INSTALASI &amp; DEPLOYMENT SERVER</h1>
            <h2>Sistem Informasi Perpustakaan Digital (Nafisah Nasyirah)</h2>
            <p style="max-width:720px;margin:0 auto;color:#d1fae5;font-size:13.5px;line-height:1.7;">
                Panduan praktis langkah demi langkah untuk menginstal aplikasi perpustakaan di sekolah lain, server intranet, komputer lab, maupun laptop perpustakaan baru. Meliputi penjelasan detail metode standalone (.exe), server Laragon/XAMPP, pengaturan password database, personalisasi identitas sekolah baru, dan akses jaringan lokal via Wi-Fi.
            </p>

            <!-- Box Identitas Pembuat Aplikasi -->
            <div class="author-card">
                <div style="font-size:11px;font-weight:800;letter-spacing:1px;color:#34d399;text-transform:uppercase;margin-bottom:4px;">
                    👨‍💻 PENGEMBANG &amp; PEMBUAT APLIKASI
                </div>
                <div style="font-size:22px;font-weight:900;color:#ffffff;letter-spacing:-0.3px;">
                    Heri Susanto
                </div>
                <div style="font-size:13px;color:#cbd5e1;margin-top:2px;">
                    Asal Institusi: <b>SMKN 5 Telkom Banda Aceh</b>
                </div>
                <div style="margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,0.15);display:flex;gap:20px;justify-content:center;font-size:13px;color:#f8fafc;flex-wrap:wrap;">
                    <span>📱 <b>WhatsApp:</b> <b style="color:#34d399;">081362733320</b></span>
                    <span>✉️ <b>Email:</b> <b style="color:#34d399;">itemsusanto@gmail.com</b></span>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="install-content">

            <!-- Ringkasan Pilihan Metode -->
            <div style="background:#f8fafc;border:2px solid #e2e8f0;border-radius:14px;padding:22px 26px;margin-bottom:34px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <span style="font-size:24px;">💡</span>
                    <h3 style="margin:0;font-size:17px;font-weight:800;color:#0f172a;">Dua Pilihan Metode Instalasi untuk Sekolah Baru</h3>
                </div>
                <p style="margin:0 0 16px;font-size:13.5px;color:#475569;line-height:1.65;">
                    Anda dapat memilih salah satu dari 2 metode instalasi di bawah ini tergantung pada kondisi komputer di sekolah tujuan:
                </p>

                <!-- Grid 2 Pilihan -->
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:16px;">
                    <!-- Opsi A -->
                    <div style="background:#f0fdf4;border:2px solid #22c55e;border-radius:12px;padding:18px 20px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <span style="background:#16a34a;color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:6px;">METODE 1: INSTALLER .EXE</span>
                            <span style="font-size:11.5px;font-weight:800;color:#15803d;">⭐ PALING MUDAH (1-KLIK)</span>
                        </div>
                        <h4 style="margin:0 0 6px;font-size:15px;color:#14532d;font-weight:800;">Mandiri, Portabel &amp; Tanpa Laragon/XAMPP</h4>
                        <ul style="margin:0;padding-left:18px;font-size:12.5px;color:#166534;line-height:1.65;">
                            <li>File Setup: <b><code>Installer_Perpustakaan_Sekolah_Setup.exe</code></b></li>
                            <li>Tinggal klik 1 kali, otomatis jalan di Windows 10 &amp; 11.</li>
                            <li>Sudah include database bersih &amp; runtime mandiri.</li>
                            <li>Port: <b>8088</b> (Web) dan <b>3308</b> (MySQL) - bebas konflik port.</li>
                            <li>Sangat cocok untuk komputer perpustakaan sekolah umum.</li>
                        </ul>
                    </div>

                    <!-- Opsi B -->
                    <div style="background:#eff6ff;border:2px solid #3b82f6;border-radius:12px;padding:18px 20px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <span style="background:#2563eb;color:#fff;font-size:11px;font-weight:800;padding:4px 10px;border-radius:6px;">METODE 2: LARAGON / XAMPP</span>
                            <span style="font-size:11.5px;font-weight:800;color:#1d4ed8;">STANDAR SERVER</span>
                        </div>
                        <h4 style="margin:0 0 6px;font-size:15px;color:#1e3a8a;font-weight:800;">Fleksibel untuk Teknisi Jaringan Sekolah</h4>
                        <ul style="margin:0;padding-left:18px;font-size:12.5px;color:#1e40af;line-height:1.65;">
                            <li>Direktori: <b><code>C:\laragon\www\perpustakaan\</code></b></li>
                            <li>Konfigurasi password MySQL mudah via <b><code>config.json</code></b>.</li>
                            <li>Database diimport via file: <b><code>pustaka_sekolah_clean.sql</code></b>.</li>
                            <li>Alamat Akses: <code>http://localhost/perpustakaan/</code></li>
                            <li>Cocok untuk server utama sekolah dengan IP statik.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- BAB 1: METODE A (INSTALLER MANDIRI .EXE) -->
            <!-- ============================================================= -->
            <div style="margin-bottom:20px;border-left:5px solid #16a34a;padding-left:18px;">
                <span style="font-size:11.5px;font-weight:900;letter-spacing:1px;color:#16a34a;text-transform:uppercase;">BAB 1</span>
                <h2 style="margin:2px 0 6px;font-size:22px;color:#0f172a;font-weight:900;">Instalasi Cepat Metode 1: Installer Mandiri .EXE (1-Klik)</h2>
                <p style="margin:0;font-size:13.5px;color:#64748b;">
                    Metode ini sangat disarankan jika komputer sekolah tujuan tidak memiliki Laragon maupun XAMPP:
                </p>
            </div>

            <div class="step-card" style="border-left:4px solid #16a34a;">
                <div class="step-header">
                    <div class="step-badge" style="background:#16a34a;">1</div>
                    <div>
                        <h3 class="step-title">Langkah-Langkah Instalasi Mandiri (.EXE)</h3>
                        <small style="color:#64748b;">Tanpa perlu setting database atau server manual</small>
                    </div>
                </div>
                <ol style="padding-left:20px;margin-bottom:12px;line-height:1.75;font-size:13px;">
                    <li>Ambil file <b><code>Installer_Perpustakaan_Sekolah_Setup.exe</code></b> (ukuran ~95 MB di Desktop Anda) lalu salin ke flashdisk.</li>
                    <li>Pindahkan file installer tersebut ke komputer perpustakaan sekolah tujuan.</li>
                    <li>Dobel-klik file <b><code>Installer_Perpustakaan_Sekolah_Setup.exe</code></b>.</li>
                    <li>Sistem akan otomatis mengekstrak seluruh paket ke <code>C:\PustakaSekolah\</code>, menginisialisasi database bersih, mengaktifkan akselerasi PHP OPcache, dan membuat shortcut resmi di Desktop.</li>
                    <li>Setelah instalasi selesai, shortcut <b>"Perpustakaan Nafisah Nasyirah"</b> otomatis muncul di Desktop. Dobel-klik shortcut tersebut, aplikasi langsung terbuka di layar siap digunakan!</li>
                </ol>
                <div class="callout-box info" style="margin-top:12px;">
                    <span>✓</span>
                    <div>
                        <b>Akun Default Administrator:</b><br>
                        Username: <b><code>admin</code></b> | Password: <b><code>admin123</code></b>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- BAB 2: METODE B (SERVER LARAGON / XAMPP) -->
            <!-- ============================================================= -->
            <div style="margin:45px 0 20px;border-left:5px solid #2563eb;padding-left:18px;">
                <span style="font-size:11.5px;font-weight:900;letter-spacing:1px;color:#2563eb;text-transform:uppercase;">BAB 2</span>
                <h2 style="margin:2px 0 6px;font-size:22px;color:#0f172a;font-weight:900;">Instalasi Metode 2: Web Server Laragon / XAMPP di Sekolah Lain</h2>
                <p style="margin:0;font-size:13.5px;color:#64748b;">
                    Gunakan panduan ini jika aplikasi dipasang pada server sekolah yang menggunakan Laragon atau XAMPP:
                </p>
            </div>

            <!-- LANGKAH B1: PENJELASAN PASSWORD -->
            <div class="step-card" style="border-left:4px solid #2563eb;">
                <div class="step-header">
                    <div class="step-badge" style="background:#2563eb;">2.1</div>
                    <div>
                        <h3 class="step-title">Penjelasan Khusus: Password Database MySQL Root</h3>
                        <small style="color:#64748b;">Memahami perbedaan password di komputer Heri Susanto vs sekolah baru</small>
                    </div>
                </div>
                <p style="font-size:13px;line-height:1.7;">
                    Di komputer pengembang (Heri Susanto), password MySQL root adalah <b><code>jj220386</code></b>.<br>
                    Namun, di komputer sekolah lain yang baru menginstal Laragon atau XAMPP, password MySQL root bawaannya adalah <b>KOSONG</b> (tanpa password), atau teknisi sekolah telah mengatur password tersendiri.
                </p>

                <div class="callout-box tip" style="margin:14px 0;">
                    <span style="font-size:22px;">🔑</span>
                    <div>
                        <b style="color:#0369a1;font-size:14px;">Cara Paling Praktis Mengatur Password di Sekolah Baru:</b><br>
                        Cukup buat atau edit berkas bernama <b><code>config.json</code></b> tepat di dalam folder proyek <code>c:\laragon\www\perpustakaan\config.json</code>:
                        <div class="code-box">
{
  "db_host": "127.0.0.1",
  "db_port": 3306,
  "db_name": "nafisah_nasyirah_perpustakaan",
  "db_user": "root",
  "db_pass": ""
}
                        </div>
                        <small style="color:#0369a1;display:block;margin-top:6px;">
                            • Jika password MySQL di sekolah baru <b>kosong</b> (bawaan Laragon/XAMPP): cukup isi <code>"db_pass": ""</code>.<br>
                            • Jika server sekolah memiliki password sendiri (contoh: <i>sekolah123</i>): isi <code>"db_pass": "sekolah123"</code>.<br>
                            • Dengan adanya file <code>config.json</code> ini, Anda tidak perlu mengubah satu baris pun kode program PHP!
                        </small>
                    </div>
                </div>
            </div>

            <!-- LANGKAH B2: IMPORT SQL BERSIH -->
            <div class="step-card" style="border-left:4px solid #2563eb;">
                <div class="step-header">
                    <div class="step-badge" style="background:#2563eb;">2.2</div>
                    <div>
                        <h3 class="step-title">Langkah Import Database Bersih (Tanpa Data Siswa Lama)</h3>
                        <small style="color:#64748b;">Membuat database baru dan mengimport master data</small>
                    </div>
                </div>
                <ol style="padding-left:20px;margin-bottom:12px;line-height:1.75;font-size:13px;">
                    <li>Buka <b>phpMyAdmin</b> (<code>http://localhost/phpmyadmin</code>) atau <b>HeidiSQL</b> bawaan Laragon.</li>
                    <li>Buat database baru bernama persis:
                        <div class="code-box" style="color:#10b981;font-weight:700;">nafisah_nasyirah_perpustakaan</div>
                        Collation: <code>utf8mb4_unicode_ci</code>
                    </li>
                    <li>Klik database <code>nafisah_nasyirah_perpustakaan</code> tersebut &rarr; buka tab <b>Import</b>.</li>
                    <li>Pilih file SQL bersih yang sudah disediakan di folder:
                        <div class="code-box">database/pustaka_sekolah_clean.sql</div>
                    </li>
                    <li>Klik <b>Import / Kirim</b>. Seluruh tabel, master kategori DDC, buku contoh, dan akun admin langsung siap dipakai tanpa data siswa sekolah lama!</li>
                </ol>
            </div>

            <!-- ============================================================= -->
            <!-- BAB 3: PERSONALISASI IDENTITAS SEKOLAH BARU -->
            <!-- ============================================================= -->
            <div style="margin:45px 0 20px;border-left:5px solid #d97706;padding-left:18px;">
                <span style="font-size:11.5px;font-weight:900;letter-spacing:1px;color:#d97706;text-transform:uppercase;">BAB 3</span>
                <h2 style="margin:2px 0 6px;font-size:22px;color:#0f172a;font-weight:900;">Personalisasi &amp; Ganti Identitas Sekolah Baru</h2>
                <p style="margin:0;font-size:13.5px;color:#64748b;">
                    Menyesuaikan nama sekolah, kop surat, dan logo perpustakaan setelah sistem terpasang:
                </p>
            </div>

            <div class="step-card" style="border-left:4px solid #d97706;">
                <div class="step-header">
                    <div class="step-badge" style="background:#d97706;">3</div>
                    <div>
                        <h3 class="step-title">Mengubah Pengaturan Sekolah dari Dashboard Admin</h3>
                        <small style="color:#64748b;">Menu Pengaturan Sistem</small>
                    </div>
                </div>
                <ol style="padding-left:20px;margin-bottom:12px;line-height:1.75;font-size:13px;">
                    <li>Buka aplikasi di browser: <code>http://localhost/perpustakaan/</code> (atau <code>http://localhost:8088/</code> jika versi .exe).</li>
                    <li>Klik tombol <b>Masuk / Login Petugas</b> &rarr; login dengan akun <code>admin</code> / <code>admin123</code>.</li>
                    <li>Pada menu sidebar admin, pilih <b>Pengaturan Sistem</b> (ikon gerigi).</li>
                    <li>Sesuaikan identitas sekolah baru Anda:
                        <br>• <b>Nama Sekolah</b>: Masukkan nama sekolah baru Anda (contoh: <i>SMP Negeri 1 Banda Aceh</i>).
                        <br>• <b>Alamat, NPSN, Nomor Telepon &amp; Email</b>: Isi sesuai identitas sekolah bersangkutan.
                        <br>• <b>Nama Kepala Sekolah &amp; NIP</b>: Digunakan untuk tanda tangan otomatis surat bebas pustaka.
                        <br>• <b>Nama Kepala Perpustakaan &amp; NIP</b>: Digunakan untuk pengesahan kartu anggota &amp; sirkulasi.
                        <br>• <b>Upload Logo Sekolah</b>: Unggah logo sekolah baru (format PNG/JPG transparan). Logo ini otomatis muncul di navbar, kartu anggota, dan kop surat cetak.
                    </li>
                    <li>Klik <b>Simpan Pengaturan</b>. Seluruh antarmuka langsung berganti nama sesuai identitas sekolah baru Anda!</li>
                </ol>
                <div class="callout-box warn" style="margin-top:12px;">
                    <span>🔒</span>
                    <div>
                        <b>Perlindungan Hak Cipta Asli:</b><br>
                        Pada baris paling bawah footer akan selalu tercantum teks: <i>"Di Ciptakan dan dikembangkan Oleh Heri Susanto, Aplikasi ini Gratis dilarang untuk di perjual belikan"</i>. Bagian ini dilindungi secara kriptografis demi menghormati hak cipta pembuat aplikasi saat dibagikan gratis.
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- BAB 4: AKSES WI-FI & LAN SEKOLAH DARI HP -->
            <!-- ============================================================= -->
            <div style="margin:45px 0 20px;border-left:5px solid #6366f1;padding-left:18px;">
                <span style="font-size:11.5px;font-weight:900;letter-spacing:1px;color:#6366f1;text-transform:uppercase;">BAB 4</span>
                <h2 style="margin:2px 0 6px;font-size:22px;color:#0f172a;font-weight:900;">Akses Jaringan Lokal Intranet Sekolah (Wi-Fi &amp; LAN)</h2>
                <p style="margin:0;font-size:13.5px;color:#64748b;">
                    Memungkinkan siswa dan guru mengakses perpustakaan dari smartphone atau laptop tanpa kuota internet:
                </p>
            </div>

            <div class="step-card" style="border-left:4px solid #6366f1;">
                <div class="step-header">
                    <div class="step-badge" style="background:#6366f1;">4</div>
                    <div>
                        <h3 class="step-title">Cara Menghubungkan HP Siswa ke Server Perpustakaan</h3>
                        <small style="color:#64748b;">Akses presensi mandiri dan baca buku digital</small>
                    </div>
                </div>
                <ol style="padding-left:20px;margin-bottom:12px;line-height:1.75;font-size:13px;">
                    <li>Pastikan komputer server perpustakaan dan HP siswa terhubung ke Wi-Fi sekolah yang sama.</li>
                    <li>Cari tahu IP Address Server:
                        <br>• Buka CMD (tekan Windows + R &rarr; ketik <code>cmd</code> &rarr; Enter).
                        <br>• Ketik perintah: <code>ipconfig</code> lalu Enter.
                        <br>• Catat nomor <b>IPv4 Address</b> (contoh: <code>192.168.1.100</code>).
                    </li>
                    <li>Buka Google Chrome di HP siswa dan ketik alamat:
                        <div class="code-box" style="font-size:14.5px;font-weight:700;">
http://192.168.1.100/perpustakaan/
                        </div>
                        <i>(Jika menggunakan versi .EXE, ketik: <code>http://192.168.1.100:8088/</code>)</i>
                    </li>
                    <li>Siswa dan guru dapat langsung presensi barcode, membaca buku digital, dan mengajukan peminjaman buku dari genggaman HP masing-masing!</li>
                </ol>
            </div>

            <!-- ============================================================= -->
            <!-- BAB 5: TROUBLESHOOTING & BANTUAN TEKNIS -->
            <!-- ============================================================= -->
            <div class="step-card" style="background:#fffaf0;border-color:#fbd38d;border-left:4px solid #d97706;">
                <div class="step-header" style="border-bottom-color:#fed7aa;">
                    <div class="step-badge" style="background:#d97706;">5</div>
                    <div>
                        <h3 class="step-title" style="color:#92400e;">Solusi Kendala Umum &amp; Kontak Bantuan Pengembang</h3>
                        <small style="color:#b45309;">Troubleshooting teknis</small>
                    </div>
                </div>
                <ul style="padding-left:20px;line-height:1.8;margin:0;font-size:13px;color:#451a03;">
                    <li>
                        <b>Pesan <i>Connection Refused (Error 2002)</i>:</b> Layanan MySQL belum dinyalakan atau port tidak cocok. Buka Laragon dan klik <b>Start All</b>, atau periksa port di file <code>config.json</code>.
                    </li>
                    <li>
                        <b>Pesan <i>Access Denied for user 'root'</i>:</b> Password root MySQL salah. Masukkan password yang benar ke dalam file <code>config.json</code> pada baris <code>"db_pass"</code>.
                    </li>
                    <li>
                        <b>Jika Teknisi Lupa Password Admin:</b> Buka tabel <code>users</code> di database, ubah kolom password pada user <code>admin</code> menjadi:
                        <br><code>$2y$10$wEODdY0hC2f9lA54c6vKzeM356a.p6uE/4W/C1bL2G6M7eYpYjH7.</code> (password kembali ke <code>admin123</code>).
                    </li>
                    <li>
                        <b>Layanan Bantuan Resmi Pengembang:</b>
                        <div style="margin-top:6px;font-weight:700;color:#047857;font-size:13.5px;">
                            Heri Susanto · WhatsApp: 081362733320 · Email: itemsusanto@gmail.com
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Footer Note -->
        <div class="footer-note">
            <div><b>Sistem Informasi Manajemen Perpustakaan Digital · Edisi Deployment Sekolah Baru</b></div>
            <div style="margin-top:4px;">
                Dokumen Resmi Panduan Instalasi &amp; Server · Hak Cipta &copy; <?=date('Y')?> · <b>Heri Susanto</b> (SMKN 5 Telkom Banda Aceh)
            </div>
            <div style="margin-top:6px;font-size:11.5px;color:#94a3b8;">
                Kontak Bantuan &amp; Konsultasi Resmi: <b>081362733320</b> | <b>itemsusanto@gmail.com</b>
            </div>
        </div>

    </div>

</body>
</html>
