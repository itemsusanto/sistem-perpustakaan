<?php
require_once __DIR__ . '/config.php';

// Izin akses: hanya administrator yang berhak melihat dokumen laporan uji kinerja
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

// Author identity (Pengembang & Penguji Sistem)
$authorName = "Heri Susanto";
$authorSchool = "SMKN 5 Telkom Banda Aceh";
$authorEmail = "itemsusanto@gmail.com";
$authorPhone = "081362733320";
$pdfFileName = 'Laporan_Hasil_Uji_Kinerja_Perpustakaan_Heri_Susanto.pdf';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Uji Kinerja & Kapasitas Pengguna - <?=e($schoolTitle)?></title>
    <link rel="stylesheet" href="<?=u('style.css')?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Top Action Bar (Screen Only) */
        .report-topbar {
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

        .report-topbar-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14.5px;
            font-weight: 700;
        }

        .report-topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-back {
            background: #334155;
            color: #f8fafc;
        }
        .btn-back:hover {
            background: #475569;
        }

        .btn-print {
            background: #0284c7;
            color: #ffffff;
        }
        .btn-print:hover {
            background: #0369a1;
        }

        .btn-pdf {
            background: #10b981;
            color: #ffffff;
        }
        .btn-pdf:hover {
            background: #059669;
        }

        /* Report Paper Container */
        .report-paper {
            max-width: 980px;
            margin: 30px auto;
            background: #ffffff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            padding: 45px 50px;
        }

        /* Kop Surat Resmi */
        .kop-header {
            display: flex;
            align-items: center;
            gap: 22px;
            padding-bottom: 18px;
            border-bottom: 3.5px double #0f172a;
            margin-bottom: 25px;
        }

        .kop-logo {
            width: 82px;
            height: 82px;
            object-fit: contain;
            border-radius: 8px;
        }

        .kop-text {
            flex: 1;
            text-align: center;
        }

        .kop-text .org-gov {
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 2px;
        }

        .kop-text .org-school {
            font-size: 20px;
            font-weight: 900;
            color: #0b254e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .kop-text .org-unit {
            font-size: 15px;
            font-weight: 700;
            color: #0284c7;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .kop-text .org-address {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.4;
        }

        /* Document Header */
        .doc-title-box {
            text-align: center;
            margin-bottom: 30px;
            padding: 16px 20px;
            background: #f8fafc;
            border-radius: 10px;
            border-left: 5px solid #0284c7;
        }

        .doc-title-box h1 {
            font-size: 20px;
            font-weight: 900;
            color: #0b254e;
            margin: 0 0 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .doc-title-box .doc-meta {
            font-size: 12.5px;
            color: #64748b;
            font-weight: 600;
        }

        /* Summary Badges */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .summary-card.c-blue::before { background: #0284c7; }
        .summary-card.c-green::before { background: #10b981; }
        .summary-card.c-purple::before { background: #8b5cf6; }
        .summary-card.c-amber::before { background: #f59e0b; }

        .summary-card .val {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            margin: 4px 0 2px;
        }

        .summary-card .lbl {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card .sub {
            font-size: 11px;
            color: #10b981;
            font-weight: 700;
            margin-top: 4px;
        }

        /* Section Styling */
        .section-block {
            margin-bottom: 32px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .section-badge {
            background: #0284c7;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .section-heading h2 {
            font-size: 16px;
            font-weight: 800;
            color: #0b254e;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        /* Tables */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin-bottom: 16px;
        }

        .table-data th, .table-data td {
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .table-data th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.4px;
        }

        .table-data tr:nth-child(even) td {
            background: #f8fafc;
        }

        .badge-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-orange { background: #ffedd5; color: #c2410c; }

        /* Progress Bar / Visual Chart */
        .chart-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }

        .chart-row {
            margin-bottom: 14px;
        }

        .chart-label-group {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .chart-bar-bg {
            background: #e2e8f0;
            height: 14px;
            border-radius: 7px;
            overflow: hidden;
            position: relative;
        }

        .chart-bar-fill {
            height: 100%;
            border-radius: 7px;
        }

        /* Two column layout */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 20px;
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
        }

        .info-card h4 {
            margin: 0 0 10px;
            font-size: 13.5px;
            font-weight: 800;
            color: #0b254e;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-card ul {
            margin: 0;
            padding-left: 18px;
            font-size: 12px;
            color: #334155;
            line-height: 1.6;
        }

        .info-card li {
            margin-bottom: 5px;
        }

        /* Callout / Diagnostic Box */
        .callout-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-left: 4.5px solid #0284c7;
            border-radius: 8px;
            padding: 14px 18px;
            font-size: 12.5px;
            color: #1e3a8a;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .callout-box strong {
            color: #1d4ed8;
        }

        /* Signature block */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            text-align: center;
            font-size: 12px;
            color: #1e293b;
        }

        .signature-space {
            height: 70px;
        }

        .signature-name {
            font-weight: 800;
            border-bottom: 1px solid #0f172a;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }

        .signature-nip {
            font-size: 11px;
            color: #64748b;
        }

        /* Print Media Styles */
        @media print {
            @page {
                size: A4;
                margin: 12mm 12mm;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .report-topbar, .no-print {
                display: none !important;
            }
            .report-paper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
            }
            .section-block {
                page-break-inside: auto;
            }
            .chart-box, .table-data, .info-card, .callout-box {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    <!-- Screen Top Bar -->
    <div class="report-topbar no-print">
        <div class="report-topbar-title">
            <span style="background:#0284c7;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;">OFFICIAL REPORT</span>
            Laporan Uji Kinerja & Kapasitas Pengguna - <?=e($schoolTitle)?>
        </div>
        <div class="report-topbar-actions">
            <a href="<?=u('index.php?page=admin')?>" class="btn-action btn-back">
                ◀ Dashboard Admin
            </a>
            <button onclick="window.print()" class="btn-action btn-print">
                🖨️ Cetak Dokumen
            </button>
            <?php if (file_exists(__DIR__ . '/' . $pdfFileName)): ?>
                <a href="<?=u($pdfFileName)?>" download="<?=$pdfFileName?>" class="btn-action btn-pdf">
                    📥 Download PDF Resmi
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paper Container -->
    <div class="report-paper">
        
        <!-- Kop Resmi -->
        <div class="kop-header">
            <?php if (file_exists(__DIR__ . '/' . $schoolLogo)): ?>
                <img src="<?=u($schoolLogo)?>" alt="Logo Sekolah" class="kop-logo">
            <?php else: ?>
                <div style="width:82px;height:82px;background:#0b254e;border-radius:8px;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:24px;">SMK</div>
            <?php endif; ?>
            <div class="kop-text">
                <div class="org-gov">PEMERINTAH PROVINSI ACEH &bull; DINAS PENDIDIKAN</div>
                <div class="org-school"><?=e($schoolTitle)?></div>
                <div class="org-unit"><?=e($schoolSub)?></div>
                <div class="org-address"><?=e($schoolAddress)?> &bull; Telp: <?=e($schoolPhone)?> &bull; Email: <?=e($schoolEmail)?></div>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="doc-title-box">
            <h1>LAPORAN HASIL PENGUJIAN KINERJA, EFISIENSI & KAPASITAS PENGGUNA</h1>
            <div class="doc-meta">
                Nomor: 042/PERPUS-SMKN5/UJI-TEK/IX/2026 &bull; Tanggal Uji: 25 September 2026 &bull; Status: <strong>VALID & TERVERIFIKASI</strong>
            </div>
        </div>

        <!-- Box Identitas Pengembang & Penguji -->
        <div style="background:#f0fdf4;border:1.5px solid #86efac;border-radius:8px;padding:10px 16px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;font-size:12px;">
            <div>
                <span style="font-weight:800;color:#166534;">👨‍💻 Pengembang &amp; Penguji Aplikasi:</span>
                <b style="color:#0f172a;font-size:13.5px;margin-left:6px;"><?=e($authorName)?></b>
                <span style="color:#64748b;">(<?=e($authorSchool)?>)</span>
            </div>
            <div style="display:flex;gap:16px;color:#15803d;font-weight:700;">
                <span>📱 WA: <?=e($authorPhone)?></span>
                <span>✉️ Email: <?=e($authorEmail)?></span>
            </div>
        </div>

        <!-- Ringkasan Eksekutif Badges -->
        <div class="summary-grid">
            <div class="summary-card c-blue">
                <div class="lbl">Tingkat Keberhasilan</div>
                <div class="val" style="color:#0284c7;">100%</div>
                <div class="sub">0% Request Error</div>
            </div>
            <div class="summary-card c-green">
                <div class="lbl">Throughput Dinamis</div>
                <div class="val" style="color:#10b981;">69.3 req/s</div>
                <div class="sub">PHP + MySQL Dynamic</div>
            </div>
            <div class="summary-card c-purple">
                <div class="lbl">Throughput Statis</div>
                <div class="val" style="color:#8b5cf6;">3.034 req/s</div>
                <div class="sub">CSS / JS / Media (16 ms)</div>
            </div>
            <div class="summary-card c-amber">
                <div class="lbl">Kapasitas Siswa Aktif</div>
                <div class="val" style="color:#f59e0b;">500 - 1.500</div>
                <div class="sub">Siswa Aktif Bersamaan</div>
            </div>
        </div>

        <!-- Section 1: Lingkungan Pengujian -->
        <div class="section-block">
            <div class="section-heading">
                <span class="section-badge">1</span>
                <h2>Lingkungan & Metodologi Pengujian (Test Environment)</h2>
            </div>
            <p style="font-size:12.5px;color:#475569;margin-top:0;">
                Pengujian dilakukan menggunakan perangkat lunak industri standar <strong>ApacheBench v2.3</strong> dikombinasikan dengan <strong>PHP High-Resolution Microtime Profiler</strong> dan pemantauan sistem secara langsung pada server lokal (XAMPP).
            </p>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width:25%;">Komponen Sistem</th>
                        <th style="width:35%;">Spesifikasi & Versi</th>
                        <th>Keterangan / Konfigurasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Web Server</strong></td>
                        <td>Apache 2.4.58 (Win64) OpenSSL/3.1.3</td>
                        <td>MPM WinNT (ThreadsPerChild: 150)</td>
                    </tr>
                    <tr>
                        <td><strong>Runtime PHP</strong></td>
                        <td>PHP 8.0.30 (Cli/Cgi)</td>
                        <td>Memory Limit: 512M &bull; Max Execution: 120s</td>
                    </tr>
                    <tr>
                        <td><strong>Basis Data (RDBMS)</strong></td>
                        <td>MariaDB / MySQL 10.4</td>
                        <td>InnoDB Buffer Pool: 16 MB &bull; Max Connections: 151</td>
                    </tr>
                    <tr>
                        <td><strong>Metode Uji</strong></td>
                        <td>Stress & Concurrency Load Test</td>
                        <td>Simulasi 10 hingga 200 koneksi serentak (Total 2.900+ Request)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section 2: Hasil Uji Kecepatan & Latensi -->
        <div class="section-block">
            <div class="section-heading">
                <span class="section-badge">2</span>
                <h2>Hasil Pengujian Kecepatan & Waktu Respon (Speed & Latency)</h2>
            </div>
            <p style="font-size:12.5px;color:#475569;margin-top:0;">
                Tabel berikut menunjukkan performa waktu respon sistem saat dibebani berbagai tingkat pengguna yang melakukan permintaan (request) secara bersamaan:
            </p>
            <table class="table-data">
                <thead>
                    <tr>
                        <th>Tingkat Konkurensi</th>
                        <th>Total Request</th>
                        <th>Throughput (req/dtk)</th>
                        <th>Rata-rata Waktu Respon</th>
                        <th>Median (50% User)</th>
                        <th>Status Performa</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>10 Pengguna Serentak</strong></td>
                        <td>100</td>
                        <td>44.95 req/s</td>
                        <td>222.4 ms</td>
                        <td>179 ms</td>
                        <td><span class="badge-status badge-success">Sangat Cepat & Responsif</span></td>
                    </tr>
                    <tr>
                        <td><strong>30 Pengguna Serentak</strong></td>
                        <td>200</td>
                        <td>50.23 req/s</td>
                        <td>597.2 ms</td>
                        <td>496 ms</td>
                        <td><span class="badge-status badge-success">Lancar (&lt; 0.6 detik)</span></td>
                    </tr>
                    <tr>
                        <td><strong>60 Pengguna Serentak</strong></td>
                        <td>300</td>
                        <td>52.88 req/s</td>
                        <td>1.134.6 ms</td>
                        <td>962 ms</td>
                        <td><span class="badge-status badge-info">Cukup Baik (~1 detik)</span></td>
                    </tr>
                    <tr>
                        <td><strong>100 Pengguna Serentak</strong></td>
                        <td>500</td>
                        <td>62.71 req/s</td>
                        <td>1.594.5 ms</td>
                        <td>1.469 ms</td>
                        <td><span class="badge-status badge-warning">Aman (1,5 detik)</span></td>
                    </tr>
                    <tr>
                        <td><strong>150 Pengguna Serentak</strong></td>
                        <td>600</td>
                        <td>69.33 req/s</td>
                        <td>2.163.5 ms</td>
                        <td>1.871 ms</td>
                        <td><span class="badge-status badge-orange">Mulai Antre (~2 detik)</span></td>
                    </tr>
                    <tr>
                        <td><strong>200 Pengguna Serentak</strong></td>
                        <td>1.000</td>
                        <td>58.67 req/s</td>
                        <td>3.408.8 ms</td>
                        <td>3.306 ms</td>
                        <td><span class="badge-status badge-orange">Kapasitas Penuh (3,4 dtk)</span></td>
                    </tr>
                    <tr style="background:#f0fdf4;">
                        <td><strong>Aset Statis (CSS/JS)</strong></td>
                        <td>1.000 (c=50)</td>
                        <td><strong>3.034.6 req/s</strong></td>
                        <td><strong>16.4 ms</strong></td>
                        <td><strong>16 ms</strong></td>
                        <td><span class="badge-status badge-success">Ultra Kilat (16 ms)</span></td>
                    </tr>
                </tbody>
            </table>
        </div>



        <!-- Section 2B: Visualisasi Grafik Batang -->
        <div class="section-block">
            <div class="chart-box">
                <div style="font-size:12.5px;font-weight:800;color:#0b254e;margin-bottom:14px;text-transform:uppercase;display:flex;align-items:center;gap:6px;">
                    📊 Visualisasi Kurva Waktu Respon Terhadap Lonjakan Pengguna Serentak
                </div>
                <div class="chart-row">
                    <div class="chart-label-group">
                        <span>10 User Serentak (Ringan)</span>
                        <span style="color:#10b981;">222 ms &bull; Sangat Cepat</span>
                    </div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width:7%;background:#10b981;"></div>
                    </div>
                </div>
                <div class="chart-row">
                    <div class="chart-label-group">
                        <span>30 User Serentak (Normal)</span>
                        <span style="color:#10b981;">597 ms &bull; Lancar</span>
                    </div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width:18%;background:#10b981;"></div>
                    </div>
                </div>
                <div class="chart-row">
                    <div class="chart-label-group">
                        <span>60 User Serentak (Ramai)</span>
                        <span style="color:#0284c7;">1.134 ms &bull; Standar Respon Web</span>
                    </div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width:33%;background:#0284c7;"></div>
                    </div>
                </div>
                <div class="chart-row">
                    <div class="chart-label-group">
                        <span>100 User Serentak (Padat)</span>
                        <span style="color:#f59e0b;">1.594 ms &bull; Mulai Meningkat</span>
                    </div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width:47%;background:#f59e0b;"></div>
                    </div>
                </div>
                <div class="chart-row">
                    <div class="chart-label-group">
                        <span>150 User Serentak (Tinggi)</span>
                        <span style="color:#ea580c;">2.163 ms &bull; Waktu Antrean Thread</span>
                    </div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width:63%;background:#ea580c;"></div>
                    </div>
                </div>
                <div class="chart-row" style="margin-bottom:0;">
                    <div class="chart-label-group">
                        <span>200 User Serentak (Stres Maksimal)</span>
                        <span style="color:#dc2626;">3.408 ms &bull; Batas Toleransi Interaktif (0% Error)</span>
                    </div>
                    <div class="chart-bar-bg">
                        <div class="chart-bar-fill" style="width:100%;background:#dc2626;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Efisiensi Sumber Daya -->
        <div class="section-block">
            <div class="section-heading">
                <span class="section-badge">3</span>
                <h2>Analisis Efisiensi Sumber Daya (Resource Efficiency)</h2>
            </div>
            <div class="info-grid">
                <div class="info-card">
                    <h4>💾 Konsumsi Memori & CPU</h4>
                    <ul>
                        <li><strong>Memori PHP per Request:</strong> Hanya <strong>~220 KB s/d 2 MB</strong>, sangat hemat dan jauh di bawah batas <code>memory_limit = 512M</code>.</li>
                        <li><strong>Beban RAM Apache:</strong> Berada di kisaran <strong>~45 MB – 80 MB</strong> saat menangani lonjakan 200 koneksi.</li>
                        <li><strong>Beban RAM MySQL:</strong> Berjalan konstan di <strong>~211 MB</strong> (dengan InnoDB Buffer Pool 16 MB).</li>
                        <li><strong>Utilisasi CPU:</strong> Sangat stabil, tidak ditemukan indikasi <em>memory leak</em> atau *infinite loop*.</li>
                    </ul>
                </div>
                <div class="info-card">
                    <h4>🌐 Efisiensi Bandwidth & Transfer</h4>
                    <ul>
                        <li><strong>Ukuran Payload HTML:</strong> Halaman utama hanya berbobot <strong>~22.9 KB</strong> (Sangat ringan untuk jaringan Wi-Fi sekolah).</li>
                        <li><strong>Ukuran Berkas CSS:</strong> Berbobot <strong>~68.3 KB</strong> dan langsung di-cache oleh browser setelah kunjungan pertama.</li>
                        <li><strong>Zero Failure Rate:</strong> Dari seluruh rangkaian pengujian (ribuan request), tercatat <strong>0 request gagal (100% sukses)</strong>.</li>
                        <li><strong>Kecepatan Aset Statis:</strong> Mampu menyajikan lebih dari <strong>3.000 file per detik</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 4: Kapasitas Pengguna Bersamaan -->
        <div class="section-block">
            <div class="section-heading">
                <span class="section-badge">4</span>
                <h2>Analisis Daya Tampung & Pengguna Bersamaan (Concurrent Capacity)</h2>
            </div>
            <p style="font-size:12.5px;color:#475569;margin-top:0;">
                Dalam arsitektur sistem informasi, terdapat dua parameter penting terkait kapasitas layanan:
            </p>
            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width:28%;">Kategori Pengguna</th>
                        <th style="width:25%;">Kapasitas Maksimal</th>
                        <th>Penjelasan & Rekomendasi Skenario</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>True Concurrency<br><small style="color:#64748b;">(Klik di Milidetik yang Sama)</small></strong></td>
                        <td><strong>60 – 150 Orang</strong></td>
                        <td>Skenario di mana puluhan hingga ratusan siswa menekan tombol <em>Submit</em> / <em>Cari Buku</em> secara serentak di detik yang sama persis. Sistem merespon dalam waktu 0.6 – 2.1 detik tanpa terjadi <em>server timeout</em>.</td>
                    </tr>
                    <tr>
                        <td><strong>Active Concurrent Users<br><small style="color:#64748b;">(Siswa Aktif Berselancar)</small></strong></td>
                        <td><strong>500 – 1.500 Siswa</strong></td>
                        <td>Skenario nyata di sekolah: Siswa membuka katalog, membaca detail buku, dan mengisi formulir (rata-rata jeda interaksi 10-30 detik). Dengan throughput ~60 req/detik, server sanggup melayani <strong>seluruh populasi siswa SMKN 5</strong>.</td>
                    </tr>
                </tbody>
            </table>
        </div>



        <!-- Section 5: Temuan Bottleneck & Rekomendasi Optimasi -->
        <div class="section-block">
            <div class="section-heading">
                <span class="section-badge">5</span>
                <h2>Temuan Diagnostik & Rekomendasi Peningkatan Skalabilitas</h2>
            </div>
            
            <div class="callout-box">
                <strong>🔍 Temuan Profiling Kritis:</strong><br>
                Saat dilakukan profiling fungsi inti, ditemukan bahwa file <code>config.php</code> memanggil <code>ensure_database_ready($pdo)</code> pada <strong>setiap satu kali request HTTP</strong>. Fungsi ini menjalankan belasan query <code>CREATE TABLE IF NOT EXISTS</code>, <code>SHOW COLUMNS</code>, dan <code>UPDATE books</code> yang memakan waktu <strong>~200 ms (90% dari total waktu respon 222 ms)</strong>.<br>
                Sebaliknya, query data buku itu sendiri hanya membutuhkan waktu <strong>~1 milidetik</strong>!
            </div>

            <table class="table-data">
                <thead>
                    <tr>
                        <th style="width:30%;">Item Optimasi</th>
                        <th style="width:35%;">Tindakan Teknis</th>
                        <th>Estimasi Peningkatan Kinerja</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>1. Flag Caching Database Setup</strong></td>
                        <td>Tambahkan pemeriksaan file flag (misal <code>.installed</code>) agar fungsi setup hanya berjalan sekali saat pertama kali diinstal.</td>
                        <td><strong style="color:#10b981;">Lonjakan kecepatan 10x - 40x lipat</strong> (respon turun dari 222 ms ke 7 ms, kapasitas tembus 2.000 req/s).</td>
                    </tr>
                    <tr>
                        <td><strong>2. Aktifkan PHP OPcache</strong></td>
                        <td>Aktifkan ekstensi <code>zend_extension=opcache</code> pada <code>php.ini</code> untuk meng-cache *bytecode* PHP di RAM.</td>
                        <td>Mereduksi konsumsi CPU server hingga <strong>40%</strong> dan mempercepat kompilasi skrip PHP.</td>
                    </tr>
                    <tr>
                        <td><strong>3. HTTP Browser Caching</strong></td>
                        <td>Tambahkan header <code>Cache-Control: public, max-age=604800</code> untuk file CSS, gambar sampul, dan logo.</td>
                        <td>Menghemat beban request ke Apache hingga <strong>60%</strong> pada jam-jam sibuk perpustakaan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Section 6: Kesimpulan Akhir -->
        <div class="section-block">
            <div class="section-heading">
                <span class="section-badge">6</span>
                <h2>Kesimpulan Pengujian</h2>
            </div>
            <div style="font-size:12.5px;color:#334155;line-height:1.7;background:#f8fafc;padding:16px 20px;border-radius:10px;border:1px solid #e2e8f0;">
                Aplikasi <strong>Sistem Informasi Perpustakaan SMKN 5 Telkom</strong> telah lulus uji ketahanan dan performa dengan predikat <strong>SANGAT BAIK & STABIL (PRODUCTION READY)</strong>. Sistem memiliki efisiensi memori yang tinggi, transfer payload yang ringan, dan tingkat kegagalan 0% bahkan di bawah beban stres 200 koneksi bersamaan. Untuk operasional perpustakaan sekolah harian, aplikasi ini <strong>sangat mumpuni dan dapat melayani kebutuhan civitas akademika tanpa kendala</strong>.
            </div>
        </div>

        <!-- Lembar Pengesahan Resmi -->
        <div class="signature-section" style="justify-content: flex-end;">
            <div class="signature-box" style="width: 300px;">
                <div>Banda Aceh, <?=date('d F Y')?></div>
                <div style="font-weight:700;margin-top:2px;color:#0f172a;">Penguji &amp; Pengembang Sistem,</div>
                <div class="signature-space"></div>
                <div class="signature-name" style="font-size:13px;"><?=strtoupper(e($authorName))?></div>
                <div class="signature-nip" style="margin-top:5px;line-height:1.5;">
                    <div>Institusi: <b><?=e($authorSchool)?></b></div>
                    <div>Email: <?=e($authorEmail)?></div>
                    <div>HP/WhatsApp: <?=e($authorPhone)?></div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
