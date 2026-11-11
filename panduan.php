<?php
require_once __DIR__ . '/config.php';

// Batasi akses: hanya administrator yang berhak melihat dokumen buku panduan
auth('admin');

$schoolTitle = get_setting('navbar_title', SCHOOL);
$schoolSub = get_setting('navbar_subtitle', SCHOOL_SUB);
$schoolLogo = get_setting('navbar_logo', 'assets/img/logo.jpg');
$schoolAddress = get_setting('info_address', 'Jl. Teuku Nyak Arief No. 123, Banda Aceh');
$schoolPhone = get_setting('info_phone', '(0651) 123456');
$schoolEmail = get_setting('info_email', 'perpustakaan@smkn5telkom.sch.id');
$headName = get_setting('report_head_name', 'Drs. Suparman, M.Pd');
$headNip = get_setting('report_head_nip', 'NIP. 19750815 200501 1 004');
$headTitle = get_setting('report_head_title', 'Kepala Perpustakaan');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Panduan Penggunaan - Sistem Perpustakaan <?=e($schoolTitle)?></title>
    <link rel="stylesheet" href="<?=u('style.css')?>">
    <style>
        /* ==========================================================================
           USER MANUAL / GUIDE PDF STYLESHEET
           ========================================================================== */
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

        /* Top Action Bar (Screen Only) */
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
            max-width: 980px;
            margin: 30px auto;
            background: #ffffff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Cover Page */
        .guide-cover {
            background: linear-gradient(135deg, #0b254e 0%, #17324d 50%, #1e40af 100%);
            color: #ffffff;
            padding: 80px 50px 70px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-bottom: 6px solid #38bdf8;
        }

        .guide-cover::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 280px;
            height: 280px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, rgba(2, 132, 199, 0) 70%);
            border-radius: 50%;
        }

        .guide-cover img.logo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: #ffffff;
            padding: 4px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            margin-bottom: 22px;
        }

        .guide-cover h1 {
            font-size: 32px;
            font-weight: 900;
            margin: 0 0 10px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
            line-height: 1.25;
        }

        .guide-cover h2 {
            font-size: 19px;
            font-weight: 600;
            color: #93c5fd;
            margin: 0 0 24px;
        }

        .guide-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .guide-cover-meta {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 28px;
            flex-wrap: wrap;
            font-size: 13px;
            color: #cbd5e1;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 24px;
        }

        /* Content Area */
        .guide-content {
            padding: 45px 50px 60px;
        }

        .guide-toc {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 26px 32px;
            margin-bottom: 45px;
        }

        .guide-toc h3 {
            margin: 0 0 18px;
            font-size: 17px;
            color: #0b254e;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
        }

        .guide-toc ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 28px;
        }

        .guide-toc li a {
            color: #0369a1;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dashed #cbd5e1;
            transition: color 0.15s ease;
        }

        .guide-toc li a:hover {
            color: #0284c7;
        }

        .guide-section {
            margin-bottom: 55px;
        }

        .guide-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 2.5px solid #0284c7;
            padding-bottom: 10px;
            margin-bottom: 22px;
        }

        .guide-section-header .section-num {
            background: #0284c7;
            color: #ffffff;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 17px;
            flex-shrink: 0;
        }

        .guide-section-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0b254e;
        }

        .guide-subsection {
            margin-top: 32px;
            margin-bottom: 32px;
        }

        .guide-subsection h3 {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .guide-subsection h3::before {
            content: '▶';
            font-size: 11px;
            color: #0284c7;
        }

        p {
            font-size: 14px;
            line-height: 1.7;
            color: #334155;
            margin: 0 0 14px;
        }

        .guide-steps {
            list-style: none;
            counter-reset: step-counter;
            padding: 0;
            margin: 16px 0;
        }

        .guide-steps li {
            position: relative;
            padding-left: 42px;
            margin-bottom: 16px;
            font-size: 14px;
            color: #334155;
            line-height: 1.65;
        }

        .guide-steps li::before {
            content: counter(step-counter);
            counter-increment: step-counter;
            position: absolute;
            left: 0;
            top: 2px;
            width: 26px;
            height: 26px;
            background: #e0f2fe;
            color: #0284c7;
            border: 1.5px solid #bae6fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
        }

        /* Screenshot Container with Desktop Frame Border Effect */
        .screenshot-box {
            margin: 20px 0 26px;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        }

        .screenshot-header {
            background: #e2e8f0;
            padding: 8px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            border-bottom: 1px solid #cbd5e1;
        }

        .screenshot-dots {
            display: flex;
            gap: 6px;
        }

        .screenshot-dots span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .screenshot-dots span:nth-child(1) { background: #f87171; }
        .screenshot-dots span:nth-child(2) { background: #fbbf24; }
        .screenshot-dots span:nth-child(3) { background: #4ade80; }

        .screenshot-box img {
            width: 100%;
            height: auto;
            display: block;
            border-bottom: 1px solid #e2e8f0;
        }

        .screenshot-caption {
            padding: 10px 16px;
            font-size: 12.5px;
            color: #475569;
            background: #ffffff;
            line-height: 1.55;
            border-top: 1px solid #f1f5f9;
        }

        .info-callout {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 18px 0;
            font-size: 13.5px;
            color: #166534;
            line-height: 1.6;
        }

        .warn-callout {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 18px 0;
            font-size: 13.5px;
            color: #92400e;
            line-height: 1.6;
        }

        .tip-callout {
            background: #f0f9ff;
            border-left: 4px solid #0284c7;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 18px 0;
            font-size: 13.5px;
            color: #0369a1;
            line-height: 1.6;
        }

        .guide-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 24px;
            font-size: 13px;
        }

        .guide-table th {
            background: #0f172a;
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

        .guide-table tr:nth-child(even) {
            background: #f8fafc;
        }

        /* Footer */
        .guide-footer {
            background: #f8fafc;
            border-top: 2px solid #e2e8f0;
            padding: 30px 50px;
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4;
                margin: 12mm 10mm;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .guide-topbar, .no-print {
                display: none !important;
            }
            .guide-container {
                max-width: 100% !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .guide-cover {
                padding: 70px 40px !important;
                page-break-after: always !important;
                break-after: page !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .guide-toc {
                page-break-after: always !important;
                break-after: page !important;
            }
            .guide-content {
                padding: 10px 0 !important;
            }
            .guide-section {
                page-break-before: always !important;
                break-before: page !important;
            }
            .guide-subsection {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .screenshot-box {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 14px 0 18px !important;
            }
            .screenshot-box img {
                max-height: 470px !important;
                object-fit: contain !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .info-callout, .warn-callout, .tip-callout {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating / Sticky Top Toolbar (Screen Only) -->
    <div class="guide-topbar no-print">
        <div class="guide-topbar-title">
            <span style="font-size:20px;">📖</span>
            <div>
                <div>Buku Panduan Penggunaan Sistem Perpustakaan</div>
                <div style="font-size:11.5px;color:#94a3b8;font-weight:500;">Pengembang: <b style="color:#38bdf8;">Heri Susanto</b> · Kontak: <b>081362733320</b> · Email: <b>itemsusanto@gmail.com</b></div>
            </div>
        </div>
        <div class="guide-topbar-actions">
            <a href="<?=u('index.php')?>" class="btn btn-sm" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);padding:6px 14px;border-radius:6px;font-size:12.5px;text-decoration:none;font-weight:700;">
                🏠 Kembali ke Aplikasi
            </a>
            <a href="<?=u('panduan_migrasi_pemulihan.php')?>" class="btn btn-sm" style="background:#6366f1;color:#fff;padding:6px 14px;border-radius:6px;font-size:12.5px;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                🛡️ Panduan Migrasi &amp; Pemulihan Data
            </a>
            <?php if (file_exists(__DIR__ . '/Buku_Panduan_Penggunaan_Perpustakaan_Heri_Susanto.pdf')): ?>
                <a href="<?=u('Buku_Panduan_Penggunaan_Perpustakaan_Heri_Susanto.pdf')?>" download="Buku_Panduan_Penggunaan_Perpustakaan_Heri_Susanto.pdf" class="btn btn-sm" style="background:#10b981;color:#fff;padding:6px 14px;border-radius:6px;font-size:12.5px;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    <?=icon('download', '', 14)?> Unduh PDF Panduan
                </a>
            <?php elseif (file_exists(__DIR__ . '/Buku_Panduan_Perpustakaan_SMKN5.pdf')): ?>
                <a href="<?=u('Buku_Panduan_Perpustakaan_SMKN5.pdf')?>" download="Buku_Panduan_Perpustakaan_SMKN5.pdf" class="btn btn-sm" style="background:#10b981;color:#fff;padding:6px 14px;border-radius:6px;font-size:12.5px;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    <?=icon('download', '', 14)?> Unduh File PDF
                </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-sm btn-primary" style="padding:7px 18px;border-radius:6px;font-size:12.5px;font-weight:800;display:inline-flex;align-items:center;gap:6px;background:#0284c7;color:#fff;border:none;cursor:pointer;">
                <?=icon('print', '', 15)?> Cetak / Simpan ke PDF
            </button>
        </div>
    </div>

    <!-- Main Guide Document Container -->
    <div class="guide-container">
        
        <!-- COVER PAGE -->
        <div class="guide-cover">
            <img src="<?=u($schoolLogo)?>" class="logo" alt="Logo <?=e($schoolTitle)?>">
            <div class="guide-badge">DOKUMEN RESMI OPERASIONAL &amp; PETUNJUK TEKNIS</div>
            <h1 style="margin-top:16px;">BUKU PANDUAN PENGGUNAAN SISTEM</h1>
            <h2>Sistem Informasi Perpustakaan Digital · <?=e($schoolTitle)?></h2>
            <p style="max-width:720px;margin:0 auto;color:#e2e8f0;font-size:14px;line-height:1.75;">
                Petunjuk komprehensif operasional aplikasi perpustakaan sekolah modern, mencakup hak akses Administrator, katalogisasi buku &amp; sumber dana, sirkulasi peminjaman, surat bebas pustaka, kustomisasi laporan dengan kop resmi, pemindai kamera kunjungan, serta layanan mandiri Anggota Siswa &amp; Guru.
            </p>

            <!-- Box Pembuat Aplikasi pada Cover -->
            <div style="margin-top:30px;background:rgba(15,23,42,0.65);border:1.5px solid rgba(56,189,248,0.4);border-radius:12px;padding:16px 28px;display:inline-block;text-align:center;box-shadow:0 8px 30px rgba(0,0,0,0.25);backdrop-filter:blur(8px);">
                <div style="font-size:11.5px;font-weight:800;letter-spacing:1px;color:#38bdf8;text-transform:uppercase;margin-bottom:5px;">
                    👨‍💻 PENGEMBANG &amp; PEMBUAT APLIKASI
                </div>
                <div style="font-size:20px;font-weight:900;color:#ffffff;letter-spacing:-0.3px;">
                    Heri Susanto
                </div>
                <div style="font-size:13px;color:#cbd5e1;margin-top:2px;">
                    Asal Institusi: <b>SMKN 5 Telkom Banda Aceh</b>
                </div>
                <div style="margin-top:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,0.15);display:flex;gap:20px;justify-content:center;font-size:13px;color:#f8fafc;flex-wrap:wrap;">
                    <span>📱 <b>Kontak / WA:</b> <b style="color:#38bdf8;">081362733320</b></span>
                    <span>✉️ <b>Email:</b> <b style="color:#38bdf8;">itemsusanto@gmail.com</b></span>
                </div>
            </div>

            <div class="guide-cover-meta">
                <span>🏫 <b>Institusi:</b> <?=e($schoolTitle)?></span>
                <span>📍 <b>Kota:</b> Banda Aceh</span>
                <span>📅 <b>Edisi Panduan:</b> Versi 2.0 (Lengkap &amp; Terbaru)</span>
                <span>🔒 <b>Hak Akses:</b> Pengelola &amp; Administrator</span>
            </div>
        </div>

        <!-- CONTENT AREA -->
        <div class="guide-content">

            <!-- DAFTAR ISI -->
            <div class="guide-toc">
                <h3><?=icon('book', '', 18)?> DAFTAR ISI PANDUAN PENGGUNAAN</h3>
                <ul>
                    <li><a href="#bab1"><span>BAB 1. Akses &amp; Profil Pengembang</span> <span>→</span></a></li>
                    <li><a href="#bab1_1">&nbsp;&nbsp;1.1 Profil Pembuat Aplikasi &amp; Bantuan</a></li>
                    <li><a href="#bab1_2">&nbsp;&nbsp;1.2 Alamat Akses &amp; Autentikasi Pengguna</a></li>
                    <li><a href="#bab2"><span>BAB 2. Panduan Menu Administrator</span> <span>→</span></a></li>
                    <li><a href="#bab2_1">&nbsp;&nbsp;2.1 Login &amp; Dashboard Admin</a></li>
                    <li><a href="#bab2_2">&nbsp;&nbsp;2.2 Manajemen Data Buku &amp; Siswa Peminjam</a></li>
                    <li><a href="#bab2_3">&nbsp;&nbsp;2.3 Manajemen Data Anggota &amp; Pas Foto</a></li>
                    <li><a href="#bab2_4">&nbsp;&nbsp;2.4 Cetak Kartu Anggota (KTA) Resmi</a></li>
                    <li><a href="#bab2_5">&nbsp;&nbsp;2.5 Sirkulasi Peminjaman &amp; Pengembalian</a></li>
                    <li><a href="#bab2_6">&nbsp;&nbsp;2.6 Layanan Bebas Pustaka Online</a></li>
                    <li><a href="#bab2_7">&nbsp;&nbsp;2.7 Laporan, Kop Resmi &amp; Kustom Kolom</a></li>
                    <li><a href="#bab2_8">&nbsp;&nbsp;2.8 Pengaturan Sistem, Logo &amp; Tanda Tangan</a></li>
                    <li><a href="#bab2_9">&nbsp;&nbsp;2.9 Presensi Kamera Berdampingan</a></li>
                    <li><a href="#bab3"><span>BAB 3. Panduan Menu Anggota (Siswa/Guru)</span> <span>→</span></a></li>
                    <li><a href="#bab3_1">&nbsp;&nbsp;3.1 Login &amp; Dashboard Anggota</a></li>
                    <li><a href="#bab3_2">&nbsp;&nbsp;3.2 Cari Buku &amp; Pengajuan Pinjam Mandiri</a></li>
                    <li><a href="#bab3_3">&nbsp;&nbsp;3.3 Kartu Digital Peminjaman Resmi (QR)</a></li>
                    <li><a href="#bab3_4">&nbsp;&nbsp;3.4 Cetak Kartu &amp; Pasang Foto Profil</a></li>
                    <li><a href="#bab3_5">&nbsp;&nbsp;3.5 Presensi Kunjungan &amp; Cek Bebas Pustaka</a></li>
                    <li><a href="#bab4"><span>BAB 4. SOP, Tips Cetak PDF &amp; FAQ</span> <span>→</span></a></li>
                    <li><a href="#bab5"><span>BAB 5. Panduan Instalasi &amp; Pindah Komputer</span> <span>→</span></a></li>
                    <li><a href="#bab5_1">&nbsp;&nbsp;5.1 Perangkat Keras &amp; Lunak yang Dibutuhkan</a></li>
                    <li><a href="#bab5_2">&nbsp;&nbsp;5.2 Instalasi XAMPP di Komputer Baru</a></li>
                    <li><a href="#bab5_3">&nbsp;&nbsp;5.3 Cara Pindah Folder (Copy-Paste)</a></li>
                    <li><a href="#bab5_4">&nbsp;&nbsp;5.4 Pemindahan Database (Export/Import)</a></li>
                    <li><a href="#bab5_5">&nbsp;&nbsp;5.5 Menjalankan &amp; Akses Jaringan Wi-Fi</a></li>
                </ul>
            </div>

            <!-- ================================================================= -->
            <!-- BAB 1: PENGENALAN & AKSES SISTEM -->
            <!-- ================================================================= -->
            <div class="guide-section" id="bab1">
                <div class="guide-section-header">
                    <div class="section-num">1</div>
                    <h2>Akses Sistem &amp; Profil Pengembang Aplikasi</h2>
                </div>

                <!-- 1.1 PROFIL PEMBUAT APLIKASI -->
                <div class="guide-subsection" id="bab1_1">
                    <h3>1.1 Profil Pengembang &amp; Kontak Resmi Pembuat Aplikasi</h3>
                    <p>
                        Aplikasi <b>Sistem Informasi Perpustakaan Digital SMKN 5 Telkom Banda Aceh</b> ini dikembangkan secara terstruktur, aman, dan berstandar industri oleh praktisi teknologi informasi sekolah:
                    </p>

                    <div style="background:#f8fafc;border:2px solid #0284c7;border-radius:12px;padding:24px 28px;margin:18px 0 26px;box-shadow:0 6px 20px rgba(2,132,199,0.08);">
                        <div style="display:flex;align-items:center;gap:18px;margin-bottom:16px;border-bottom:1px solid #e2e8f0;padding-bottom:14px;flex-wrap:wrap;">
                            <div style="width:58px;height:58px;border-radius:12px;background:#0284c7;color:#ffffff;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;flex-shrink:0;">
                                👨‍💻
                            </div>
                            <div>
                                <div style="font-size:12px;font-weight:800;letter-spacing:1px;color:#0284c7;text-transform:uppercase;">Identitas Pengembang Resmi</div>
                                <div style="font-size:22px;font-weight:900;color:#0f172a;line-height:1.2;">Heri Susanto</div>
                                <div style="font-size:13.5px;color:#64748b;font-weight:600;">Pengembang Aplikasi Sistem Informasi Perpustakaan · SMKN 5 Telkom Banda Aceh</div>
                            </div>
                        </div>

                        <table style="width:100%;border-collapse:collapse;font-size:13.5px;">
                            <tr>
                                <td style="padding:7px 0;width:170px;color:#64748b;font-weight:700;">Nama Lengkap:</td>
                                <td style="padding:7px 0;color:#0f172a;font-weight:800;font-size:14.5px;">Heri Susanto</td>
                            </tr>
                            <tr>
                                <td style="padding:7px 0;color:#64748b;font-weight:700;">Kontak / WhatsApp:</td>
                                <td style="padding:7px 0;">
                                    <a href="https://wa.me/6281362733320" target="_blank" style="color:#0284c7;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                                        📞 081362733320
                                    </a>
                                    <span style="color:#64748b;font-size:12.5px;">(Konsultasi, Bimbingan Teknis &amp; Layanan Dukungan)</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:7px 0;color:#64748b;font-weight:700;">Email Resmi:</td>
                                <td style="padding:7px 0;">
                                    <a href="mailto:itemsusanto@gmail.com" style="color:#0284c7;font-weight:800;text-decoration:none;">
                                        ✉️ itemsusanto@gmail.com
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:7px 0;color:#64748b;font-weight:700;">Asal Institusi / Sekolah:</td>
                                <td style="padding:7px 0;color:#0f172a;font-weight:700;">SMKN 5 Telkom Banda Aceh</td>
                            </tr>
                            <tr>
                                <td style="padding:7px 0;color:#64748b;font-weight:700;vertical-align:top;">Lingkup Dukungan:</td>
                                <td style="padding:7px 0;color:#334155;line-height:1.6;">
                                    Pemeliharaan basis data, instalasi server intranet sekolah, pelatihan operator perpustakaan, kustomisasi cetak kartu &amp; laporan, serta pengembangan fitur lanjutan sesuai kurikulum dan regulasi perpustakaan sekolah.
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- 1.2 ALAMAT AKSES -->
                <div class="guide-subsection" id="bab1_2">
                    <h3>1.2 Alamat Akses &amp; Persyaratan Lingkungan Sistem</h3>
                    <p>
                        Aplikasi dirancang khusus berjalan pada arsitektur web modern yang fleksibel: dapat dipasang secara lokal pada jaringan intranet sekolah (XAMPP / Apache / Nginx) maupun hosting berbasis cloud/VPS. Sistem bersifat <i>Zero External CDN Dependency</i> sehingga seluruh font, pustaka script, dan icon dapat beroperasi tanpa perlu koneksi internet luar.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Tampilan Halaman Utama / Landing Page Publik</div>
                            <div>Resolusi: Desktop Web</div>
                        </div>
                        <img src="<?=u('assets/img/guide/01_halaman_utama.png')?>" alt="Halaman Utama">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 1.1:</b> Halaman Utama Perpustakaan dengan fitur pencarian buku kilat, info koleksi, buku terpopuler, dan akses login mandiri.
                        </div>
                    </div>

                    <div class="tip-callout">
                        <b>🌐 Alamat Akses Aplikasi pada Jaringan Sekolah:</b><br>
                        - <b>Komputer Server / Meja Petugas Admin:</b> <code>http://localhost/perpustakaan_2/</code><br>
                        - <b>Komputer Klien / Smartphone Siswa &amp; Guru (Wi-Fi Sekolah):</b> Buka peramban (Chrome, Edge, Safari) dan ketikkan alamat IP server lokal (Contoh: <code>http://192.168.1.10/perpustakaan_2/</code>).
                    </div>

                    <div class="guide-subsection">
                        <h3>Autentikasi &amp; Tingkat Hak Akses Pengguna</h3>
                        <p>
                            Aplikasi membedakan hak akses secara ketat menjadi 2 kelompok pengguna untuk menjaga keamanan data:
                        </p>
                    <table class="guide-table">
                        <thead>
                            <tr>
                                <th>Peran (Role)</th>
                                <th>Username Bawaan</th>
                                <th>Password Bawaan</th>
                                <th>Wewenang Utama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Administrator</b></td>
                                <td><code>admin</code></td>
                                <td><code>admin123</code></td>
                                <td>Akses penuh ke seluruh manajemen buku, data anggota, persetujuan sirkulasi, laporan berkop resmi, dan pengaturan sistem.</td>
                            </tr>
                            <tr>
                                <td><b>Anggota Siswa</b></td>
                                <td>NISN / Username Siswa (cth: <code>andipratama</code>)</td>
                                <td><code>123456</code> (dapat diubah)</td>
                                <td>Pencarian buku, pengajuan pinjam online mandiri, tiket kartu digital QR, presensi kunjungan, dan cek bebas pustaka.</td>
                            </tr>
                            <tr>
                                <td><b>Guru / Tenaga Pendidik</b></td>
                                <td>NIP / ID Anggota</td>
                                <td><code>123456</code></td>
                                <td>Peminjaman referensi ajar, presensi buku tamu harian, dan cetak kartu keanggotaan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="screenshot-box">
                    <div class="screenshot-header">
                        <div class="screenshot-dots"><span></span><span></span><span></span></div>
                        <div>Tampilan Halaman Login Dua Mode (Anggota &amp; Admin)</div>
                        <div>index.php?page=login</div>
                    </div>
                    <img src="<?=u('assets/img/guide/02_halaman_login.png')?>" alt="Halaman Login">
                    <div class="screenshot-caption">
                        📸 <b>Gambar 1.2:</b> Halaman login modern dengan switch cepat antara akun Anggota Siswa dan Administrator perpustakaan.
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- BAB 2: PANDUAN MENU ADMINISTRATOR -->
            <!-- ================================================================= -->
            <div class="guide-section" id="bab2">
                <div class="guide-section-header">
                    <div class="section-num">2</div>
                    <h2>Panduan Lengkap Menu Administrator</h2>
                </div>

                <!-- 2.1 DASHBOARD ADMIN -->
                <div class="guide-subsection" id="bab2_1">
                    <h3>2.1 Dashboard Administrator</h3>
                    <p>
                        Setelah berhasil masuk sebagai Administrator, sistem menyajikan <b>Dashboard Admin</b> yang menampilkan metrik analitik perpustakaan secara <i>real-time</i> beserta jalan pintas operasional harian.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Dashboard Admin Perpustakaan</div>
                            <div>index.php?page=admin</div>
                        </div>
                        <img src="<?=u('assets/img/guide/03_dashboard_admin.png')?>" alt="Dashboard Admin">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.1:</b> Dashboard Admin dengan kartu KPI (Total Buku, Total Anggota, Sedang Dipinjam, Terlambat, Kunjungan Hari Ini) serta Panel Layanan Cepat.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Metrik Statistik KPI:</b> Memantau Total Judul Buku, Total Anggota Aktif, Eksemplar Sedang Dipinjam, Peminjaman Terlambat, dan Kunjungan Hari Ini.</li>
                        <li><b>Panel Layanan Administrator:</b> Tombol pintas untuk langsung Registrasi Anggota Baru, Cetak Kartu Anggota, dan Buka Data Anggota.</li>
                        <li><b>Form Registrasi Cepat:</b> Memungkinkan admin mendaftarkan siswa atau guru baru di tempat lengkap dengan opsi unggah pas foto profil langsung atau pemilihan preset avatar resmi.</li>
                    </ul>
                </div>

                <!-- 2.2 MANAJEMEN BUKU -->
                <div class="guide-subsection" id="bab2_2">
                    <h3>2.2 Manajemen Data Buku &amp; Cek Siswa Peminjam</h3>
                    <p>
                        Menu <b>Buku</b> (<code>page=admin_books</code>) digunakan untuk mengelola data koleksi pustaka fisik, mencakup klasifikasi kategori, penetapan sumber dana, breakdown stok, dan pelacakan peminjam aktif.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Katalog Buku &amp; Modal Cek Siswa Peminjam</div>
                            <div>index.php?page=admin_books</div>
                        </div>
                        <img src="<?=u('assets/img/guide/04_katalog_buku_admin.png')?>" alt="Katalog Buku Admin">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.2:</b> Tabel manajemen buku dengan filter kategori, sumber dana (Dana BOS / Hibah), pencarian judul, dan rincian ketersediaan stok fisik.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Menambah Buku Baru:</b> Klik tombol <i>+ Tambah Buku Baru</i> di atas tabel. Isi Judul Buku, Penulis, Penerbit, Tahun, Kategori, Stok Total, Sumber Dana (Dana BOS / Hibah / Beli Sendiri), dan Harga Satuan.</li>
                        <li><b>Nomor Seri Eksemplar Fisik &amp; Pop-Up Detail Seri:</b> Setiap buku dapat memiliki nomor seri unik per eksemplar fisik (cth: <code>KOM-0001</code>, <code>KOM-0002</code>, dst.). Pada tabel koleksi, kolom nomor seri menampilkan 2 seri teratas dan tombol <b>+X lainnya</b>. Jika diklik, sistem membuka jendela pop-up interaktif yang menampilkan seluruh nomor seri, live search, filter status (<i>Tersedia</i> / <i>Dipinjam</i>), nama siswa peminjam aktif, serta tombol salin per-serial dan <i>📋 Salin Semua Seri</i>.</li>
                        <li><b>Unggah Foto Sampul (Cover Depan):</b> Tersedia opsi mengunggah foto cover depan buku fisik agar katalog pencarian lebih menarik dan mudah dikenali siswa.</li>
                        <li><b>Melihat Siapa Saja Siswa yang Sedang Meminjam:</b> Pada kolom <i>Dipinjam</i>, jika angka lebih dari 0, klik tautan <b>Lihat Siswa ↗</b>. Sistem akan membuka jendela pop-up interaktif yang mencantumkan nama siswa, tanggal pinjam, batas waktu kembali, serta tombol langsung <i>📱 Chat WhatsApp</i> untuk mengirimkan pesan pengingat ke siswa.</li>
                        <li><b>Mengedit &amp; Menghapus Buku:</b> Klik tombol <i>Edit</i> untuk memperbarui data stok atau judul, dan tombol <i>Hapus</i> untuk menghapus buku (sistem mencegah penghapusan jika buku sedang dipinjam).</li>
                    </ul>
                </div>

                <!-- 2.3 DATA ANGGOTA -->
                <div class="guide-subsection" id="bab2_3">
                    <h3>2.3 Manajemen Data Anggota Perpustakaan</h3>
                    <p>
                        Menu <b>Anggota</b> (<code>page=admin_members</code>) memuat seluruh direktori siswa, tenaga pendidik (guru), staf TU, dan pengunjung luar yang terdaftar sebagai anggota perpustakaan.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Tabel Data Anggota Lengkap dengan Thumbnail Foto</div>
                            <div>index.php?page=admin_members</div>
                        </div>
                        <img src="<?=u('assets/img/guide/05_data_anggota_admin.png')?>" alt="Data Anggota Admin">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.3:</b> Data Anggota dilengkapi kolom foto profil thumbnail, tombol Cetak Kartu, Bebas Pustaka, dan Verifikasi Anggota Baru.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Pendaftaran Anggota Baru:</b> Klik <i>Registrasi Anggota Baru</i>. Isi Nama Lengkap, Kategori (Siswa RPL, TKJ, DKV, Guru, Staf, Tamu), ID Anggota / NISN, No. WhatsApp, dan pilih pas foto formal atau preset avatar siswa resmi.</li>
                        <li><b>Verifikasi Anggota Baru:</b> Jika siswa mendaftar sendiri secara online, statusnya akan <i>Pending</i>. Admin cukup klik tombol hijau <b>Verifikasi</b> agar akun langsung aktif dan berhak meminjam buku.</li>
                        <li><b>Pencarian &amp; Filter:</b> Cari anggota berdasarkan nama atau NISN, dan saring data berdasarkan kategori (Guru / Siswa per jurusan / Tamu).</li>
                    </ul>
                </div>

                <!-- 2.4 CETAK KARTU ANGGOTA (KTA) -->
                <div class="guide-subsection" id="bab2_4">
                    <h3>2.4 Cetak Kartu Tanda Anggota (KTA) Resmi</h3>
                    <p>
                        Sistem menyediakan format kartu keanggotaan standar ISO 2 Sisi (Depan &amp; Belakang) yang siap cetak ke kertas tebal, kertas foto, maupun bahan kartu PVC, lengkap dengan <b>Pas Foto 3:4 Resmi</b>, Barcode Code-128, dan QR Code identitas.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Tampilan Cetak Kartu Anggota (KTA) dengan Pas Foto &amp; Barcode</div>
                            <div>index.php?page=card&amp;id=2</div>
                        </div>
                        <img src="<?=u('assets/img/guide/06_cetak_kartu_anggota.png')?>" alt="Cetak Kartu Anggota">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.4:</b> Kartu Tanda Anggota resmi menampilkan pas foto 3:4 siswa, barcode, QR code, tata tertib, dan tanda tangan digital Kepala Perpustakaan.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Memilih Anggota:</b> Pada dropdown <i>Pilih Anggota</i> di pojok kanan atas, pilih siswa yang ingin dicetak kartunya.</li>
                        <li><b>Memasang / Mengganti Foto Profil Anggota:</b>
                            <br>- Klik tombol <b>Pasang / Ganti Foto Anggota</b> di toolbar kartu.
                            <br>- Anda dapat <b>mengunggah file pas foto resmi</b> (JPG, PNG, WEBP maks 3MB) atau memilih <b>Preset Avatar Resmi Siswa SMKN 5</b> (Siswa Laki-laki / Siswi Perempuan Berjilbab).
                            <br>- Klik <b>Simpan Foto</b>. Foto kartu akan langsung terpasang dan diperbarui seketika.
                        </li>
                        <li><b>Mencetak Kartu:</b> Klik tombol biru <b>Cetak Kartu (Print)</b>. Atur Destination ke <i>Save as PDF</i> untuk menyimpan file digital atau langsung ke mesin pencetak kartu.</li>
                    </ul>

                    <div class="warn-callout">
                        <b>⚠️ Pengaturan Wajib Saat Cetak:</b> Pada jendela dialog Print browser, pastikan opsi <b>"Background graphics" (Grafis latar belakang)</b> dicentang aktif agar warna biru kartu, logo, dan gelombang kartu tercetak tajam dan tidak pudar.
                    </div>
                </div>

                <!-- 2.5 SIRKULASI PEMINJAMAN & PENGEMBALIAN -->
                <div class="guide-subsection" id="bab2_5">
                    <h3>2.5 Sirkulasi Peminjaman &amp; Pengembalian Buku Fisik</h3>
                    <p>
                        Menu <b>Peminjaman</b> (<code>page=admin_loans</code>) mengelola seluruh alur sirkulasi buku dari pengajuan online siswa hingga penyerahan fisik di meja sirkulasi dan pengembalian.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Manajemen Peminjaman &amp; Sirkulasi Buku</div>
                            <div>index.php?page=admin_loans</div>
                        </div>
                        <img src="<?=u('assets/img/guide/07_sirkulasi_peminjaman_admin.png')?>" alt="Sirkulasi Peminjaman Admin">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.5:</b> Tabel peminjaman buku dengan tab Status (Menunggu, Dipinjam, Selesai, Terlambat) dan aksi cepat persetujuan.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Alur Pengajuan Siswa yang Praktis:</b> Siswa yang mengajukan peminjaman buku <b>tidak diwajibkan mengisi nomor seri</b>, melainkan hanya memilih judul buku yang diinginkan. Hal ini mencegah kesalahan siswa menebak serial dan mempermudah operasional.</li>
                        <li><b>Penyerahan Eksemplar Fisik &amp; Scan di Meja Sirkulasi:</b>
                            <br>- Saat siswa datang mengambil buku, pustakawan bebas mengambil eksemplar fisik buku mana saja yang tersedia di rak sesuai judul.
                            <br>- Pustakawan mengklik tombol <b>[ ✓ Setujui &amp; Scan ]</b>.
                            <br>- Pada jendela pop-up, arahkan <b>Barcode Scanner Gun USB</b> ke kode batang/QR di cover buku fisik yang dipegang (atau klik <b>📷 Kamera</b> untuk memindai dengan webcam laptop/HP).
                            <br>- Nomor seri buku fisik otomatis terdeteksi, berbunyi nada konfirmasi (*chime*), dan sistem langsung menyetujui peminjaman serta mencatat nomor seri yang diserahkan.
                            <br>- Tersedia pula pilihan manual memilih eksemplar tersedia dari dropdown rak, atau tombol <i>Lewati (Tanpa Nomor Seri)</i> untuk buku umum yang stikernya hilang/rusak.
                        </li>
                        <li><b>Input Peminjaman Langsung di Tempat:</b> Jika siswa datang langsung meminjam buku di meja sirkulasi tanpa mengajukan online sebelumnya, Admin mengisi form <i>Catat Peminjaman Buku Baru</i> (pilih anggota, judul buku, scan nomor seri, dan durasi pinjam).</li>
                        <li><b>Proses Pengembalian Buku:</b> Pada tab <i>Sedang Dipinjam</i>, klik tombol biru <b>Konfirmasi Kembali</b> saat buku fisik diterima. Sistem otomatis menghitung durasi, keterlambatan, dan denda (Rp 1.000 per hari keterlambatan jika ada).</li>
                    </ul>
                </div>

                <!-- 2.6 BEBAS PUSTAKA -->
                <div class="guide-subsection" id="bab2_6">
                    <h3>2.6 Penerbitan Surat Bebas Pustaka Online</h3>
                    <p>
                        Menu <b>Bebas Pustaka</b> (<code>page=admin_clearance</code>) digunakan untuk memverifikasi siswa tingkat akhir yang memerlukan Surat Keterangan Bebas Pustaka untuk keperluan kelulusan, pengambilan ijazah, atau pindah sekolah.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Verifikasi &amp; Cetak Surat Bebas Pustaka Resmi</div>
                            <div>index.php?page=admin_clearance</div>
                        </div>
                        <img src="<?=u('assets/img/guide/08_bebas_pustaka_admin.png')?>" alt="Bebas Pustaka Admin">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.6:</b> Panel Bebas Pustaka memeriksa otomatis tanggungan buku dan menerbitkan surat resmi ber-barcode verifikasi.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Pemeriksaan Otomatis:</b> Sistem otomatis memeriksa apakah siswa masih memiliki buku yang belum dikembalikan atau pinjaman pending. Surat hanya dapat diterbitkan jika siswa memiliki tanggungan <b>0 Buku</b>.</li>
                        <li><b>Cetak Surat Resmi:</b> Klik <b>Cetak Surat</b> untuk mencetak Surat Keterangan Bebas Pustaka berukuran A4 standar dengan kop sekolah resmi, nomor surat otomatis format Romawi, barcode verifikasi, dan tanda tangan Kepala Perpustakaan.</li>
                    </ul>
                </div>

                <!-- 2.7 LAPORAN -->
                <div class="guide-subsection" id="bab2_7">
                    <h3>2.7 Cetak Laporan, Kop Surat Resmi &amp; Kustom Kolom</h3>
                    <p>
                        Menu <b>Laporan</b> (<code>page=reports</code>) menyediakan rekapitulasi data komprehensif yang dilengkapi dengan <b>Kop Surat Resmi Pemerintah Aceh &amp; SMKN 5 Telkom</b>, kustomisasi kolom tabel cetak, pilihan orientasi kertas (A4 Portrait / Landscape), serta ekspor ke file Microsoft Excel.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Laporan Rekapitulasi Perpustakaan Siap Cetak</div>
                            <div>index.php?page=reports</div>
                        </div>
                        <img src="<?=u('assets/img/guide/09_laporan_perpustakaan.png')?>" alt="Laporan Perpustakaan">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.7:</b> Lembar laporan rekapitulasi dengan tab kategori (Buku, Anggota, Peminjaman, Pengembalian, Kunjungan) dan tombol cetak PDF &amp; Excel.
                        </div>
                    </div>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Modal Atur Kolom Cetak &amp; Layout PDF</div>
                            <div>Fitur Kustomisasi Kolom</div>
                        </div>
                        <img src="<?=u('assets/img/guide/16_kustomisasi_kolom_laporan.png')?>" alt="Atur Kolom Cetak">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.8:</b> Modal pemilihan kolom cetak interaktif dengan pilihan Preset (Hemat Kertas A4 Tegak, Stok &amp; Sirkulasi, Pengadaan &amp; Harga) serta orientasi Portrait/Landscape.
                        </div>
                    </div>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Pratinjau Cetak Laporan Resmi dengan Kop Surat</div>
                            <div>Format Cetak Dinas</div>
                        </div>
                        <img src="<?=u('assets/img/guide/17_kop_surat_laporan.png')?>" alt="Kop Surat Laporan">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.9:</b> Pratinjau dokumen cetak resmi menampilkan Kop Pemerintah Aceh &amp; SMKN 5 Telkom, garis ganda resmi, tabel rapi, dan tanda tangan dua pejabat.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Pilih Tab Laporan:</b> Pilih antara Buku, Anggota, Peminjaman, Pengembalian, Keterlambatan, atau Kunjungan.</li>
                        <li><b>Kustomisasi Kolom &amp; Layout Cetak:</b> Klik tombol <b>Cetak PDF</b> untuk membuka modal pemilihan kolom. Anda dapat menyembunyikan kolom yang tidak dibutuhkan atau memilih preset cepat (misal: <i>Hemat Kertas A4 Tegak</i>).</li>
                        <li><b>Pilih Orientasi Kertas:</b> Pilih A4 Portrait (Tegak) untuk tabel ringkas, atau A4 Landscape (Melebar) untuk tabel yang memuat banyak kolom data.</li>
                        <li><b>Ekspor ke Excel:</b> Klik tombol hijau <b>Export Excel</b> untuk mengunduh data dalam format CSV/Excel yang rapi dan terformat.</li>
                    </ul>
                </div>

                <!-- 2.8 PENGATURAN -->
                <div class="guide-subsection" id="bab2_8">
                    <h3>2.8 Pengaturan Sistem, Identitas Sekolah &amp; Pejabat</h3>
                    <p>
                        Menu <b>Pengaturan</b> (<code>page=settings</code>) memungkinkan Admin menyesuaikan identitas sekolah tanpa harus menyentuh kode program.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Panel Pengaturan Identitas &amp; Tanda Tangan</div>
                            <div>index.php?page=settings</div>
                        </div>
                        <img src="<?=u('assets/img/guide/10_pengaturan_sistem.png')?>" alt="Pengaturan Sistem">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.10:</b> Tab pengaturan Logo Sekolah, Judul Navbar, Banner Beranda, dan Data Pejabat Penandatangan Dokumen.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Tab Navbar &amp; Logo:</b> Ubah nama sekolah, sub-judul, dan upload file logo resmi sekolah (JPG/PNG).</li>
                        <li><b>Tab Foto Banner Index:</b> Ganti foto hero banner halaman depan sekolah dengan foto gedung atau kegiatan literasi sekolah terbaru.</li>
                        <li><b>Tab Kategori Anggota / Jurusan:</b> Tambah atau hapus kategori jurusan (RPL, TKJ, IPA, IPS, Mesin, Akuntansi, dll.) dan kelompok civitas sekolah untuk formulir pendaftaran anggota mandiri maupun admin saat aplikasi diinstal di sekolah lain. Dilengkapi juga dengan template 1-klik untuk berbagai jenis sekolah (SMK, SMA, SMP).</li>
                        <li><b>Tab Tanda Tangan:</b> Mengatur Nama Lengkap, NIP/NUPTK, dan Jabatan Kepala Perpustakaan serta Petugas Administrasi yang tercantum pada Kartu Anggota, Laporan, dan Surat Bebas Pustaka.</li>
                    </ul>
                </div>

                <!-- 2.9 PRESENSI KUNJUNGAN SCANNER KAMERA BERDAMPINGAN -->
                <div class="guide-subsection" id="bab2_9">
                    <h3>2.9 Presensi Kunjungan Scanner Kamera &amp; Daftar Real-Time Berdampingan</h3>
                    <p>
                        Pada <b>Dashboard Admin</b>, sistem menyediakan modul presensi kunjungan cerdas yang menampilkan <b>Pindai Kamera QR / Barcode Kartu di sisi kiri</b> dan <b>Tabel Daftar Kunjungan Hari Ini di sisi kanan secara berdampingan</b>. Saat siswa memindai kartu atau KTA di depan webcam / kamera HP, nama siswa langsung tercatat dan muncul di daftar sebelah kanan seketika tanpa perlu refresh halaman.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Presensi Kunjungan Kamera Berdampingan dengan Tabel Kunjungan</div>
                            <div>index.php?page=admin (Modul Presensi Berdampingan)</div>
                        </div>
                        <img src="<?=u('assets/img/guide/18_presensi_kamera_kunjungan.png')?>" alt="Presensi Kamera Berdampingan">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 2.11:</b> Integrasi kamera pemindai barcode/QR kartu anggota di sisi kiri dengan daftar riwayat kunjungan real-time di sisi kanan.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Mulai Kamera Pemindai:</b> Klik tombol hijau <i>Aktifkan Kamera Pemindai</i> untuk menyalakan kamera laptop/webcam di meja sirkulasi.</li>
                        <li><b>Pindai Kartu Anggota:</b> Siswa cukup mengarahkan Barcode atau QR Code pada Kartu Tanda Anggota (KTA) atau Kartu Digital HP ke sorotan kamera.</li>
                        <li><b>Notifikasi &amp; Pembaruan Instan:</b> Begitu kode terbaca, sistem membunyikan nada konfirmasi, memunculkan popup ucapan selamat datang beserta foto profil siswa, dan langsung menambahkan baris baru ke tabel riwayat kunjungan di sisi kanan.</li>
                        <li><b>Input Manual Cepat:</b> Jika siswa tidak membawa kartu, admin dapat mengetikkan nama siswa atau NISN pada kolom input cepat di bawah kamera.</li>
                    </ul>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- BAB 3: PANDUAN MENU ANGGOTA -->
            <!-- ================================================================= -->
            <div class="guide-section" id="bab3">
                <div class="guide-section-header">
                    <div class="section-num">3</div>
                    <h2>Panduan Lengkap Menu Anggota (Siswa, Guru, Tamu)</h2>
                </div>

                <!-- 3.1 DASHBOARD ANGGOTA -->
                <div class="guide-subsection" id="bab3_1">
                    <h3>3.1 Login &amp; Dashboard Anggota</h3>
                    <p>
                        Siswa atau guru masuk menggunakan NISN / ID Anggota mereka. Halaman dashboard anggota menyajikan ringkasan pinjaman aktif, status pengajuan, serta jalan pintas presensi.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Dashboard Anggota Siswa</div>
                            <div>index.php?page=member</div>
                        </div>
                        <img src="<?=u('assets/img/guide/11_dashboard_anggota.png')?>" alt="Dashboard Anggota">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 3.1:</b> Tampilan beranda anggota dengan banner Kartu Digital Peminjaman aktif dan jalan pintas ke katalog pencarian buku.
                        </div>
                    </div>
                </div>

                <!-- 3.2 CARI BUKU & PENGAJUAN PINJAM -->
                <div class="guide-subsection" id="bab3_2">
                    <h3>3.2 Cari Buku &amp; Pengajuan Peminjaman Online Mandiri</h3>
                    <p>
                        Anggota dapat mencari koleksi buku yang tersedia dan langsung mengajukan peminjaman secara mandiri melalui smartphone atau laptop mereka.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Halaman Pengajuan Peminjaman Buku Mandiri</div>
                            <div>index.php?page=member_request</div>
                        </div>
                        <img src="<?=u('assets/img/guide/12_pengajuan_pinjam_online.png')?>" alt="Pengajuan Pinjam Online">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 3.2:</b> Formulir pengajuan peminjaman buku dengan pengecekan ketersediaan stok fisik di rak secara otomatis.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li>Buka menu <b>Pengajuan Peminjaman</b> (atau klik tombol <i>Pinjam Buku Ini</i> pada katalog pencarian buku).</li>
                        <li>Pilih buku yang diinginkan (hanya buku dengan sisa stok tersedia yang dapat dipilih). <b>Anggota tidak perlu mengisi nomor seri</b> eksemplar buku; siswa cukup memilih judul buku yang ingin dipinjam.</li>
                        <li>Sistem otomatis menetapkan batas waktu pinjam (standar 7 hari kerja).</li>
                        <li>Klik <b>Ajukan Peminjaman &amp; Terbitkan Kartu Digital →</b>.</li>
                    </ul>
                </div>

                <!-- 3.3 KARTU DIGITAL PEMINJAMAN RESMI -->
                <div class="guide-subsection" id="bab3_3">
                    <h3>3.3 Kartu Digital Peminjaman Resmi (Digital Loan Pass)</h3>
                    <p>
                        Setelah pengajuan berhasil, sistem langsung menerbitkan <b>Kartu Digital Peminjaman Resmi</b> lengkap dengan Nomor Tiket, QR Verifikasi, dan Barcode.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Kartu Digital Peminjaman Resmi Siswa</div>
                            <div>index.php?page=loan_card</div>
                        </div>
                        <img src="<?=u('assets/img/guide/13_kartu_digital_pinjam.png')?>" alt="Kartu Digital Peminjaman">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 3.3:</b> Kartu digital yang ditunjukkan siswa kepada petugas perpustakaan di meja sirkulasi untuk serah terima buku fisik.
                        </div>
                    </div>

                    <div class="info-callout">
                        <b>📱 Prosedur Serah Terima Buku Fisik:</b><br>
                        1. Siswa membuka Kartu Digital Peminjaman ini di layar smartphone masing-masing.<br>
                        2. Tunjukkan tiket ini ke petugas perpustakaan di meja sirkulasi.<br>
                        3. Petugas mengambil eksemplar fisik buku mana saja di rak sesuai judul, men-scan barcode di cover buku dengan scanner gun atau kamera, dan sistem langsung menyetujui penyerahan buku secara otomatis.
                    </div>
                </div>

                <!-- 3.4 CETAK KARTU ANGGOTA & FOTO PROFIL -->
                <div class="guide-subsection" id="bab3_4">
                    <h3>3.4 Cetak Kartu Anggota Mandiri &amp; Pasang Foto Profil</h3>
                    <p>
                        Anggota dapat melihat kartu anggota perpustakaan mereka dan mengunggah pas foto formal secara mandiri kapan saja.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Halaman Profil Anggota dengan Fitur Unggah Pas Foto</div>
                            <div>index.php?page=profile</div>
                        </div>
                        <img src="<?=u('assets/img/guide/14_profil_anggota_upload_foto.png')?>" alt="Profil Anggota &amp; Pas Foto">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 3.4:</b> Anggota dapat mengunggah pas foto formal atau mengganti password akun di menu Profil.
                        </div>
                    </div>

                    <ul class="guide-steps">
                        <li>Buka menu <b>Profil</b> (<code>page=profile</code>).</li>
                        <li>Pada bagian <i>Foto Profil (Dicetak pada Kartu Anggota)</i>, klik <b>Choose File</b> dan pilih file foto wajah rapi berseragam sekolah (JPG/PNG, maks 3MB).</li>
                        <li>Klik <b>Simpan Perbarui Profil</b>.</li>
                        <li>Buka menu <b>Kartu Anggota</b> (<code>page=card</code>) untuk melihat kartu anggota Anda yang telah memuat foto tersebut dan siap dicetak!</li>
                    </ul>
                </div>

                <!-- 3.5 PRESENSI KUNJUNGAN -->
                <div class="guide-subsection" id="bab3_5">
                    <h3>3.5 Presensi Kunjungan Anggota Perpustakaan</h3>
                    <p>
                        Setiap kali berkunjung ke ruang perpustakaan, anggota dapat melakukan presensi mandiri (Buku Tamu Digital) melalui menu <b>Presensi Kunjungan</b>.
                    </p>

                    <div class="screenshot-box">
                        <div class="screenshot-header">
                            <div class="screenshot-dots"><span></span><span></span><span></span></div>
                            <div>Buku Tamu / Presensi Kunjungan Digital</div>
                            <div>index.php?page=member_visit</div>
                        </div>
                        <img src="<?=u('assets/img/guide/15_presensi_kunjungan_anggota.png')?>" alt="Presensi Kunjungan Anggota">
                        <div class="screenshot-caption">
                            📸 <b>Gambar 3.5:</b> Presensi kunjungan anggota dengan pilihan keperluan (Membaca, Meminjam, Diskusi) dan pencatatan jam masuk &amp; keluar otomatis.
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- BAB 4: SOP, TIPS CETAK PDF & FAQ -->
            <!-- ================================================================= -->
            <div class="guide-section" id="bab4">
                <div class="guide-section-header">
                    <div class="section-num">4</div>
                    <h2>SOP, Tips Cetak PDF &amp; Tanya Jawab (FAQ)</h2>
                </div>

                <div class="guide-subsection">
                    <h3>4.1 Tips Menyimpan Dokumen Menjadi File PDF di Komputer / HP</h3>
                    <p>
                        Seluruh dokumen di aplikasi ini (Buku Panduan ini, Kartu Anggota KTA, Kartu Digital Peminjaman, Surat Bebas Pustaka, dan Laporan) siap disimpan menjadi file PDF asli tanpa aplikasi tambahan:
                    </p>
                    <ul class="guide-steps">
                        <li>Klik tombol <b>Cetak / Simpan ke PDF</b> (atau tekan tombol kombinasi <code>Ctrl + P</code> di keyboard).</li>
                        <li>Pada bagian <b>Destination / Tujuan Printer</b>, pilih <b>"Save as PDF" / "Simpan sebagai PDF"</b>.</li>
                        <li>Buka <i>More settings / Setelan lainnya</i>, lalu pastikan opsi <b>"Background graphics" (Grafis latar belakang)</b> dalam keadaan <b>DICENTANG / DIAKTIFKAN</b>.</li>
                        <li>Pilih Margin: <i>Default</i> atau <i>None</i>, lalu klik <b>Save / Simpan</b>. File PDF resmi akan langsung tersimpan di komputer Anda.</li>
                    </ul>
                </div>

                <div class="guide-subsection">
                    <h3>4.2 Standar Operasional Prosedur (SOP) Sirkulasi</h3>
                    <table class="guide-table">
                        <thead>
                            <tr>
                                <th>Alur Kerja</th>
                                <th>Pihak Terkait</th>
                                <th>Deskripsi Prosedur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Peminjaman Online</b></td>
                                <td>Siswa &amp; Petugas</td>
                                <td>Siswa memesan buku via gawai -> Mendapatkan Tiket Kartu Digital QR -> Menunjukkan ke petugas -> Petugas menyerahkan buku fisik dan mengklik "Setujui".</td>
                            </tr>
                            <tr>
                                <td><b>Pengembalian Buku</b></td>
                                <td>Siswa &amp; Petugas</td>
                                <td>Siswa mengembalikan buku fisik ke meja perpustakaan -> Petugas memeriksa kondisi fisik buku -> Petugas mengklik "Konfirmasi Kembali" di sistem.</td>
                            </tr>
                            <tr>
                                <td><b>Bebas Pustaka</b></td>
                                <td>Siswa Tingkat Akhir</td>
                                <td>Siswa memastikan tidak ada pinjaman buku tertunggak -> Admin membuka menu Bebas Pustaka -> Mencetak Surat Bebas Pustaka resmi ber-barcode untuk syarat ijazah.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="guide-subsection">
                    <h3>4.3 Tanya Jawab Masalah Umum (FAQ)</h3>
                    
                    <p><b>Q: Mengapa foto profil atau warna latar belakang kartu tidak muncul saat diprint?</b></p>
                    <p style="color:#0284c7;">A: Hal ini disebabkan opsi <i>Background graphics</i> di dialog print browser belum diaktifkan. Centang kotak "Background graphics", maka seluruh warna, logo, dan foto akan tampil sempurna.</p>

                    <p><b>Q: Berapa lama batas waktu peminjaman buku?</b></p>
                    <p style="color:#0284c7;">A: Batas waktu peminjaman standar adalah 7 hari kerja sejak tanggal disetujui oleh admin.</p>

                    <p><b>Q: Apa yang harus dilakukan jika lupa password akun anggota?</b></p>
                    <p style="color:#0284c7;">A: Silakan lapor kepada Petugas Perpustakaan. Administrator dapat mereset password akun Anda kapan saja melalui menu Data Anggota -> Edit.</p>

                    <p><b>Q: Siapa yang dapat menerbitkan Surat Bebas Pustaka?</b></p>
                    <p style="color:#0284c7;">A: Surat Bebas Pustaka hanya dapat diterbitkan secara resmi jika anggota memiliki 0 tanggungan peminjaman aktif. Petugas perpustakaan akan mencetak surat yang dilengkapi barcode verifikasi dan tanda tangan pejabat.</p>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- BAB 5: PANDUAN INSTALASI & PINDAH KOMPUTER -->
            <!-- ================================================================= -->
            <div class="guide-section" id="bab5">
                <div class="guide-section-header">
                    <div class="section-num">5</div>
                    <h2>Panduan Instalasi &amp; Pemindahan Aplikasi ke Komputer Lain</h2>
                </div>

                <p>
                    Aplikasi <b>Sistem Informasi Perpustakaan Digital SMKN 5 Telkom Banda Aceh</b> dirancang sangat portabel dan modular. Anda dapat memindahkan aplikasi ini beserta seluruh basis data dan berkas fotonya dari satu komputer ke komputer lain (misalnya ke laptop baru pengelola perpustakaan atau server sekolah) hanya dengan prosedur <i>Copy-Paste</i> sederhana menggunakan <b>XAMPP</b>.
                </p>

                <!-- 5.1 KEBUTUHAN PERANGKAT -->
                <div class="guide-subsection" id="bab5_1">
                    <h3>5.1 Kebutuhan Perangkat Keras &amp; Perangkat Lunak</h3>
                    <p>
                        Berikut adalah spesifikasi perangkat yang dibutuhkan untuk menjalankan sistem perpustakaan dengan lancar:
                    </p>

                    <table class="guide-table">
                        <thead>
                            <tr>
                                <th style="width:28%;">Kategori</th>
                                <th style="width:36%;">Spesifikasi Minimum</th>
                                <th style="width:36%;">Spesifikasi Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Komputer Server / Host</b></td>
                                <td>Processor Intel/AMD Dual Core, RAM 4 GB, Penyimpanan Kosong 10 GB</td>
                                <td>Processor Intel Core i3/i5 atau AMD Ryzen 3/5, RAM 8 GB, SSD 120+ GB</td>
                            </tr>
                            <tr>
                                <td><b>Sistem Operasi Server</b></td>
                                <td>Windows 10 / Windows 11 (64-bit)</td>
                                <td>Windows 10 / 11 Pro 64-bit atau Linux Ubuntu Server</td>
                            </tr>
                            <tr>
                                <td><b>Paket Web Server</b></td>
                                <td>XAMPP for Windows dengan PHP versi 8.0, 8.1, atau 8.2</td>
                                <td>XAMPP for Windows dengan PHP 8.1 atau 8.2 (Apache + MySQL aktif)</td>
                            </tr>
                            <tr>
                                <td><b>Perangkat Klien (Pengguna)</b></td>
                                <td>Smartphone Android / iOS, Tablet, atau Laptop Siswa &amp; Guru</td>
                                <td>Smartphone dengan kamera fokus cepat untuk scan Kartu Digital QR</td>
                            </tr>
                            <tr>
                                <td><b>Jaringan Penghubung</b></td>
                                <td>Kabel LAN atau Router Wi-Fi Standar 2.4 GHz</td>
                                <td>Router Wi-Fi Dual-Band (2.4 &amp; 5 GHz) di area ruang perpustakaan</td>
                            </tr>
                            <tr>
                                <td><b>Perangkat Input / Pindai</b></td>
                                <td>Webcam laptop bawaan / Smartphone kamera</td>
                                <td>Kamera Webcam USB HD 1080p atau Barcode Scanner USB (1D/2D QR)</td>
                            </tr>
                            <tr>
                                <td><b>Perangkat Cetak (Printer)</b></td>
                                <td>Printer Inkjet standar untuk dokumen A4</td>
                                <td>Printer Inkjet dengan opsi cetak kertas foto tebal / Printer Kartu PVC</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 5.2 INSTALASI XAMPP -->
                <div class="guide-subsection" id="bab5_2">
                    <h3>5.2 Langkah Instalasi XAMPP di Komputer Baru</h3>
                    <p>
                        Jika komputer tujuan belum memiliki lingkungan server lokal, lakukan instalasi XAMPP terlebih dahulu:
                    </p>

                    <ul class="guide-steps">
                        <li><b>Unduh Master XAMPP:</b> Kunjungi situs resmi <code>https://www.apachefriends.org</code> dan unduh <b>XAMPP for Windows</b> (pilih versi dengan PHP 8.1 atau PHP 8.2).</li>
                        <li><b>Jalankan File Installer:</b> Klik ganda pada berkas installer yang telah diunduh (contoh: <code>xampp-windows-x64-8.2.12-0-VS16-installer.exe</code>). Jika muncul peringatan User Account Control (UAC), klik <b>Yes</b>.</li>
                        <li><b>Pilih Komponen:</b> Pada layar pemilihan komponen (*Select Components*), pastikan mencentang minimal:
                            <br>- <b>Server:</b> Apache, MySQL
                            <br>- <b>Program Languages:</b> PHP
                            <br>- <b>Program Tools:</b> phpMyAdmin
                            <br>Komponen lain seperti FileZilla, Mercury, Tomcat boleh dihilangkan centangnya jika tidak digunakan.
                        </li>
                        <li><b>Tentukan Folder Instalasi:</b> Biarkan lokasi instalasi pada folder default: <code>C:\xampp</code> lalu klik <b>Next</b> sampai proses instalasi berjalan hingga selesai (*Finish*).</li>
                        <li><b>Izin Windows Defender Firewall:</b> Saat pertama kali Apache dan MySQL dijalankan, Windows Defender akan menampilkan jendela peringatan firewall. Pastikan mencentang <b>Private networks</b> (Jaringan privat) lalu klik <b>Allow access</b> agar aplikasi dapat diakses oleh perangkat lain di jaringan Wi-Fi sekolah.</li>
                    </ul>
                </div>

                <!-- 5.3 CARA PINDAH FOLDER (COPY-PASTE) -->
                <div class="guide-subsection" id="bab5_3">
                    <h3>5.3 Cara Pemindahan Folder Aplikasi (Prosedur Salin-Tempel / Copy-Paste)</h3>
                    <p>
                        Aplikasi tidak memerlukan kompilasi rumit. Seluruh script PHP, CSS, icon, pustaka lokal, dan gambar foto anggota tersimpan rapi di dalam satu direktori.
                    </p>

                    <div class="info-callout">
                        <b>📁 Alur Prosedur Salin-Tempel (Copy-Paste):</b><br>
                        <b>Komputer Lama</b> (<code>C:\xampp\htdocs\perpustakaan_2</code>) &nbsp;➡&nbsp; <b>Flashdisk / Harddisk Eksternal</b> &nbsp;➡&nbsp; <b>Komputer Baru</b> (<code>C:\xampp\htdocs\perpustakaan_2</code>)
                    </div>

                    <ul class="guide-steps">
                        <li><b>Langkah 1 (Di Komputer Lama):</b> Tancapkan flashdisk ke komputer lama. Buka File Explorer, masuk ke folder <code>C:\xampp\htdocs\</code>.</li>
                        <li><b>Langkah 2:</b> Klik kanan pada folder <b><code>perpustakaan_2</code></b>, lalu pilih <b>Copy</b> (Salin). Masuk ke flashdisk, lalu klik kanan dan pilih <b>Paste</b> (Tempel). Tunggu hingga seluruh file selesai disalin.</li>
                        <li><b>Langkah 3 (Di Komputer Baru):</b> Lepas flashdisk dan tancapkan ke komputer baru yang telah terinstal XAMPP. Buka File Explorer di komputer baru dan masuk ke direktori: <code>C:\xampp\htdocs\</code>.</li>
                        <li><b>Langkah 4:</b> Paste (tempelkan) folder <b><code>perpustakaan_2</code></b> ke dalam folder <code>htdocs</code> tersebut. Pastikan struktur foldernya tepat berada di <code>C:\xampp\htdocs\perpustakaan_2\</code>.</li>
                    </ul>

                    <div class="warn-callout">
                        <b>⚠️ Catatan Penting Penamaan Folder:</b> Pastikan nama folder tetap <code>perpustakaan_2</code> (huruf kecil semua tanpa spasi) agar seluruh tautan internal, cookie session, dan auto-routing bekerja secara presisi tanpa perlu mengubah baris kode apa pun.
                    </div>
                </div>

                <!-- 5.4 PEMINDAHAN DATABASE -->
                <div class="guide-subsection" id="bab5_4">
                    <h3>5.4 Pemindahan Basis Data MySQL (Export &amp; Import Lengkap)</h3>
                    <p>
                        Agar seluruh data buku, data anggota, riwayat transaksi peminjaman, surat bebas pustaka, dan pengaturan sekolah berpindah secara utuh 100%, gunakan berkas cadangan database yang telah disediakan di dalam folder aplikasi:
                    </p>

                    <div class="tip-callout">
                        <b>📦 File SQL Database Cadangan Lengkap:</b><br>
                        File cadangan database siap pakai telah disertakan pada lokasi:<br>
                        <code>C:\xampp\htdocs\perpustakaan_2\database\perpustakaan_smkn5_lengkap.sql</code>
                    </div>

                    <ul class="guide-steps">
                        <li><b>Jalankan Apache &amp; MySQL:</b> Buka <b>XAMPP Control Panel</b> di komputer baru. Klik tombol <b>Start</b> pada <b>Apache</b> dan tombol <b>Start</b> pada <b>MySQL</b> hingga kedua indikator berwarna hijau.</li>
                        <li><b>Buka phpMyAdmin:</b> Buka peramban (Chrome / Edge), ketikkan alamat: <code>http://localhost/phpmyadmin/</code> lalu tekan Enter.</li>
                        <li><b>Buat Database Baru:</b>
                            <br>- Klik menu <b>Baru / New</b> di bilah sisi kiri atas.
                            <br>- Masukkan nama database: <code>perpustakaan_smkn5</code>
                            <br>- Pada collation pilih: <code>utf8mb4_unicode_ci</code> (atau biarkan default).
                            <br>- Klik tombol <b>Buat / Create</b>.
                        </li>
                        <li><b>Impor File SQL Cadangan:</b>
                            <br>- Klik nama database <code>perpustakaan_smkn5</code> yang baru dibuat di bilah sisi kiri.
                            <br>- Klik tab menu <b>Impor / Import</b> pada baris atas.
                            <br>- Klik tombol <b>Pilih Berkas / Choose File</b>.
                            <br>- Telusuri dan pilih file: <code>C:\xampp\htdocs\perpustakaan_2\database\perpustakaan_smkn5_lengkap.sql</code>
                            <br>- Gulir ke bagian paling bawah halaman phpMyAdmin, lalu klik tombol biru <b>Impor / Kirim</b>.
                        </li>
                        <li><b>Verifikasi Berhasil:</b> Sistem phpMyAdmin akan menampilkan pesan sukses berwarna hijau: <i>"Impor berhasil diselesaikan..."</i>. Seluruh tabel (buku, anggota, pinjaman, kunjungan, pengaturan) telah aktif sepenuhnya!</li>
                    </ul>

                    <div class="info-callout">
                        <b>💡 Fitur Auto-Recovery Basis Data:</b><br>
                        Jika Anda lupa mengimpor file SQL di atas, aplikasi memiliki fitur pemulihan mandiri (*Self-Healing*). Ketika Anda pertama kali membuka <code>http://localhost/perpustakaan_2/</code>, skrip <code>config.php</code> akan mendeteksi ketiadaan database dan otomatis membuat database serta struktur tabel dasarnya secara otomatis!
                    </div>
                </div>

                <!-- 5.5 MENJALANKAN & JARINGAN LOKAL -->
                <div class="guide-subsection" id="bab5_5">
                    <h3>5.5 Menjalankan Aplikasi &amp; Konfigurasi Jaringan Wi-Fi Sekolah</h3>
                    <p>
                        Setelah folder aplikasi dan database terpasang, aplikasi siap dioperasikan baik di komputer server itu sendiri maupun diakses ramai-ramai oleh siswa melalui smartphone mereka:
                    </p>

                    <ul class="guide-steps">
                        <li><b>Akses di Komputer Server Sendiri:</b>
                            <br>- Buka browser, akses: <code>http://localhost/perpustakaan_2/</code>
                            <br>- Masuk sebagai Administrator dengan Username: <code>admin</code> dan Password: <code>admin123</code>.
                        </li>
                        <li><b>Mengetahui Alamat IP Komputer Server:</b>
                            <br>- Di komputer server, tekan tombol kombinasi <code>Windows + R</code> di keyboard, ketik <code>cmd</code> lalu tekan Enter.
                            <br>- Pada jendela Command Prompt hitam, ketik perintah: <code>ipconfig</code> lalu tekan Enter.
                            <br>- Perhatikan bagian <b>IPv4 Address</b> pada adapter Wi-Fi atau Ethernet Anda (Contoh: <code>192.168.1.15</code>).
                        </li>
                        <li><b>Akses dari Smartphone Siswa &amp; Laptop Guru:</b>
                            <br>- Pastikan smartphone siswa dan laptop guru telah terhubung ke jaringan Wi-Fi yang sama dengan komputer server perpustakaan.
                            <br>- Buka browser di smartphone (Chrome, Safari, dsb), lalu ketikkan alamat IP server tersebut:
                            <br><code style="font-size:14.5px;font-weight:700;color:#0284c7;background:#e0f2fe;padding:4px 10px;border-radius:6px;display:inline-block;margin:6px 0;">http://192.168.1.15/perpustakaan_2/</code>
                            <br>*(Ganti 192.168.1.15 dengan alamat IP komputer server Anda)*.
                        </li>
                        <li><b>Selesai!</b> Siswa dapat langsung melihat katalog buku, melakukan pengajuan pinjam buku, presensi kunjungan, dan mengakses kartu digital mereka secara simultan tanpa memerlukan kuota internet!</li>
                    </ul>

                    <div class="tip-callout">
                        <b>📞 Bantuan Teknis Migrasi &amp; Konsultasi:</b><br>
                        Apabila Anda mengalami kendala saat memindahkan aplikasi atau membutuhkan pendampingan teknis jaringan sekolah, silakan langsung menghubungi pengembang aplikasi:<br>
                        👨‍💻 <b>Heri Susanto</b> · 📱 <b>WhatsApp: 081362733320</b> · ✉️ <b>Email: itemsusanto@gmail.com</b> (SMKN 5 Telkom Banda Aceh).
                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER -->
        <div class="guide-footer">
            <div style="font-weight:800;color:#0f172a;font-size:14px;margin-bottom:4px;"><?=e($schoolTitle)?> · <?=e($schoolSub)?></div>
            <div style="color:#64748b;"><?=e($schoolAddress)?> · Telepon: <?=e($schoolPhone)?> · Email: <?=e($schoolEmail)?></div>
            
            <div style="margin-top:16px;padding-top:14px;border-top:1px dashed #cbd5e1;display:flex;justify-content:center;gap:24px;flex-wrap:wrap;font-size:12.5px;color:#334155;">
                <span>👨‍💻 <b>Pembuat Aplikasi:</b> <b style="color:#0284c7;">Heri Susanto</b> (SMKN 5 Telkom Banda Aceh)</span>
                <span>📱 <b>Kontak / WA:</b> <b style="color:#0284c7;">081362733320</b></span>
                <span>✉️ <b>Email:</b> <b style="color:#0284c7;">itemsusanto@gmail.com</b></span>
            </div>
            <div style="margin-top:6px;font-size:11.5px;color:#94a3b8;">
                Dokumen Resmi Buku Panduan Penggunaan Sistem Perpustakaan Sekolah · Hak Cipta &copy; <?=date('Y')?> Heri Susanto &amp; SMKN 5 Telkom Banda Aceh
            </div>
        </div>

    </div>

</body>
</html>
