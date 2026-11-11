<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

// Jika diminta unduh / tampilkan PDF langsung
if (isset($_GET['download']) || (isset($_GET['pdf']) && file_exists(__DIR__ . '/profil_heri_susanto.pdf'))) {
    if (file_exists(__DIR__ . '/profil_heri_susanto.pdf')) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="profil_heri_susanto.pdf"');
        header('Content-Length: ' . filesize(__DIR__ . '/profil_heri_susanto.pdf'));
        readfile(__DIR__ . '/profil_heri_susanto.pdf');
        exit;
    }
}

// Persiapkan data gambar base64
function get_b64_img($relPath) {
    $full = __DIR__ . '/' . ltrim($relPath, '/');
    if (file_exists($full)) {
        $ext = pathinfo($full, PATHINFO_EXTENSION);
        $mime = ($ext === 'png') ? 'image/png' : (($ext === 'svg') ? 'image/svg+xml' : 'image/jpeg');
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($full));
    }
    return '';
}

$fotoHeriB64 = get_b64_img('uploads/member_1790478696_7325.jpg');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengembang - Heri Susanto</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #cbd5e1;
            color: #0f172a;
            line-height: 1.4;
            padding: 20px 0;
        }
        .screen-topbar {
            width: 210mm;
            margin: 0 auto 14px;
            background: #0f172a;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0284c7;
            color: #fff;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-action:hover {
            background: #0369a1;
        }
        .btn-secondary {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.25);
        }

        /* Lembar A4 Tunggal */
        .page-sheet {
            width: 210mm;
            height: 297mm;
            max-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 16mm 18mm 14mm 18mm;
        }

        /* Garis Aksen Modern Atas */
        .top-accent-bar {
            height: 6px;
            background: linear-gradient(90deg, #0284c7 0%, #38bdf8 50%, #0f172a 100%);
            border-radius: 4px;
            margin-bottom: 16px;
        }

        /* Hero Banner Profil */
        .hero-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 65%, #0369a1 100%);
            border-radius: 14px;
            padding: 20px 24px;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 6px 18px rgba(15,23,42,0.15);
        }
        .avatar-frame {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            border: 3px solid #38bdf8;
            overflow: hidden;
            flex-shrink: 0;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .avatar-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-info h1 {
            font-size: 26px;
            font-weight: 900;
            color: #ffffff;
            margin-bottom: 4px;
            line-height: 1.1;
            letter-spacing: 0.3px;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(56, 189, 248, 0.2);
            border: 1px solid rgba(56, 189, 248, 0.5);
            color: #38bdf8;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 800;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .hero-desc {
            font-size: 13px;
            color: #e2e8f0;
            line-height: 1.45;
        }

        /* 2 Kolom Informasi Lengkap */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 14px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
        }
        .info-card-header {
            font-size: 12px;
            font-weight: 900;
            color: #0284c7;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 6px;
        }
        .info-row {
            margin-bottom: 8px;
            font-size: 12px;
            display: flex;
            line-height: 1.4;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            width: 105px;
            color: #64748b;
            font-weight: 600;
            flex-shrink: 0;
        }
        .info-value {
            color: #0f172a;
            font-weight: 700;
            flex: 1;
        }
        .info-value a {
            color: #0284c7;
            text-decoration: none;
            font-weight: 800;
        }

        /* Card Karya & Aplikasi */
        .app-summary {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 14px;
        }
        .app-summary h4 {
            font-size: 12.5px;
            font-weight: 900;
            color: #166534;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .app-summary p {
            font-size: 11.5px;
            color: #1e293b;
            line-height: 1.5;
        }

        /* Kotak Ketentuan Lisensi */
        .license-box {
            background: #fffbeb;
            border: 1.5px dashed #f59e0b;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 14px;
        }
        .license-title {
            font-size: 12.5px;
            font-weight: 900;
            color: #b45309;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .license-badge {
            background: #f59e0b;
            color: #fff;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 800;
        }
        .license-rules {
            list-style: none;
            padding: 0;
            font-size: 11.5px;
            color: #334155;
            line-height: 1.55;
        }
        .license-rules li {
            position: relative;
            padding-left: 18px;
            margin-bottom: 5px;
        }
        .license-rules li:last-child {
            margin-bottom: 0;
        }
        .license-rules li::before {
            content: "✔";
            position: absolute;
            left: 0;
            color: #d97706;
            font-weight: 900;
            font-size: 11px;
        }
        .license-rules li.warn::before {
            content: "✖";
            color: #dc2626;
        }
        .license-rules li.warn {
            color: #991b1b;
            font-weight: 700;
        }

        /* Footer & Tanda Tangan */
        .doc-footer {
            margin-top: 16px;
            border-top: 1.5px solid #cbd5e1;
            padding-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            color: #64748b;
        }
        .signature-box {
            text-align: right;
        }
        .signature-box .sign-city {
            font-size: 11px;
            color: #475569;
            margin-bottom: 34px;
        }
        .signature-box .sign-name {
            font-size: 14.5px;
            font-weight: 900;
            color: #0f172a;
            text-decoration: underline;
        }
        .signature-box .sign-title {
            font-size: 10.5px;
            color: #64748b;
            font-weight: 600;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .screen-topbar, .no-print {
                display: none !important;
            }
            .page-sheet {
                box-shadow: none;
                margin: 0;
                width: 210mm;
                height: 297mm;
                page-break-inside: avoid;
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="screen-topbar no-print">
        <div style="font-size:13px;font-weight:700;display:flex;align-items:center;gap:8px;">
            📄 Dokumen Resmi Profil Pengembang &amp; Lisensi Aplikasi (1 Lembar)
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" class="btn-action">
                🖨️ Cetak / Simpan PDF
            </button>
            <?php if (file_exists(__DIR__ . '/profil_heri_susanto.pdf')): ?>
                <a href="<?=u('profil_heri_susanto.pdf')?>" target="_blank" class="btn-action" style="background:#10b981;">
                    ⬇️ Buka File PDF
                </a>
            <?php endif; ?>
            <a href="<?=u('index.php')?>" class="btn-action btn-secondary">
                ← Kembali ke Aplikasi
            </a>
        </div>
    </div>

    <div class="page-sheet">
        <div>
            <!-- Aksen Garis Modern Pengganti Kop -->
            <div class="top-accent-bar"></div>

            <!-- Hero Banner Profil Pengembang -->
            <div class="hero-card">
                <div class="avatar-frame">
                    <?php if ($fotoHeriB64): ?>
                        <img src="<?=$fotoHeriB64?>" alt="Heri Susanto">
                    <?php else: ?>
                        <span style="font-size:38px;">👨‍💻</span>
                    <?php endif; ?>
                </div>
                <div class="hero-info" style="flex:1;">
                    <div class="hero-badge">PENCIPTA &amp; PENGEMBANG UTAMA (CREATOR &amp; DEVELOPER)</div>
                    <h1>HERI SUSANTO</h1>
                    <div class="hero-desc">
                        <b>Guru Rekayasa Perangkat Lunak (RPL)</b> di <b>SMKN 5 Telkom Banda Aceh</b><br>
                        Pengembang Sistem Informasi &amp; Manajemen Perpustakaan Digital Berbasis Intranet Cepat
                    </div>
                </div>
            </div>

            <!-- 2 Kolom Informasi Lengkap -->
            <div class="info-grid">
                <!-- Kolom 1: Biodata & Pendidikan -->
                <div class="info-card">
                    <div class="info-card-header">
                        🎓 Biodata &amp; Riwayat Pendidikan
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nama Lengkap:</div>
                        <div class="info-value">Heri Susanto</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Pendidikan:</div>
                        <div class="info-value" style="color:#0284c7;font-weight:800;line-height:1.4;">
                            Lulusan Teknik Elektro Bidang Teknik Informatika &amp; Komputer (angkatan 2006 &amp; lulus Tahun 2011)<br>
                            <span style="font-size:11.5px;color:#334155;font-weight:700;">Universitas Syiah Kuala Banda Aceh</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Pekerjaan:</div>
                        <div class="info-value">
                            Aktif sebagai Guru Rekayasa Perangkat Lunak (RPL)
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Unit Kerja:</div>
                        <div class="info-value">
                            SMKN 5 Telkom Banda Aceh
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Bidang Keahlian:</div>
                        <div class="info-value">
                            Rekayasa Perangkat Lunak, Intranet Cepat &amp; Basis Data
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Kontak & Verifikasi -->
                <div class="info-card">
                    <div class="info-card-header">
                        📞 Kontak &amp; Saluran Resmi
                    </div>
                    <div class="info-row">
                        <div class="info-label">WhatsApp / HP:</div>
                        <div class="info-value">
                            <a href="https://wa.me/6281362733320" target="_blank">
                                081362733320
                            </a>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email Resmi:</div>
                        <div class="info-value">
                            <a href="mailto:itemsusanto@gmail.com">
                                itemsusanto@gmail.com
                            </a>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Lokasi / Kota:</div>
                        <div class="info-value">Banda Aceh, Provinsi Aceh</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status Lisensi:</div>
                        <div class="info-value" style="color:#16a34a;font-weight:800;">
                            GRATIS (Untuk Sekolah &amp; Pendidikan)
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Dukungan:</div>
                        <div class="info-value">Konsultasi Teknis &amp; Bimbingan Operasional</div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Sistem Yang Dikembangkan -->
            <div class="app-summary">
                <h4>💻 Tentang Aplikasi Perpustakaan Digital</h4>
                <p>
                    Aplikasi ini dirancang dan dikembangkan secara mandiri oleh <b>Heri Susanto</b> guna mewujudkan digitalisasi perpustakaan sekolah yang cepat, ringan, dan mandiri. Sistem berjalan 100% pada jaringan <b>Intranet Lokal (Wi-Fi / LAN Sekolah)</b> tanpa kuota internet luar dengan respons 0ms CDN, serta dilengkapi: katalog buku digital, sirkulasi peminjaman mandiri, presensi kamera QR/Barcode, cetak kartu anggota, bebas pustaka otomatis, dan rekapitulasi laporan.
                </p>
            </div>

            <!-- Ketentuan Hak Cipta & Lisensi Mutlak -->
            <div class="license-box">
                <div class="license-title">
                    <span>⚖️ KETENTUAN HAK CIPTA &amp; LISENSI DISTRIBUSI</span>
                    <span class="license-badge">GRATIS BERLISENSI</span>
                </div>
                <ul class="license-rules">
                    <li>
                        <b>Gratis Untuk Pendidikan:</b> Dibagikan cuma-cuma tanpa biaya lisensi untuk sekolah, madrasah, pesantren, dan komunitas literasi.
                    </li>
                    <li class="warn">
                        <b>Dilarang Keras Diperjualbelikan:</b> Dilarang memungut biaya, menjual, atau mengkomersialkan aplikasi ini dalam bentuk apapun.
                    </li>
                    <li class="warn">
                        <b>Dilarang Menghapus Hak Cipta Asli:</b> Nama <b>Heri Susanto</b> sebagai pencipta asli pada footer, berkas sistem, dan panduan TIDAK BOLEH dihapus, diganti, atau diakui sebagai karya pihak lain.
                    </li>
                    <li>
                        <b>Kewenangan Pihak Sekolah:</b> Sekolah penerima berhak penuh menyesuaikan nama sekolah, logo, data buku, dan data anggota sesuai operasional lembaga masing-masing.
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bagian Bawah: Tanda Tangan & Verifikasi Keaslian -->
        <div class="doc-footer">
            <div>
                <div style="font-weight:700;color:#0f172a;margin-bottom:2px;">Dokumen Pengesahan Profil Pengembang &amp; Lisensi Aplikasi</div>
                <div>Perpustakaan Digital Berbasis Intranet Cepat · Kode: <code>DEV-HERI-SUSANTO-2026</code></div>
                <div style="margin-top:2px;font-size:10px;color:#94a3b8;">
                    Dicetak secara resmi dari Sistem Manajemen Perpustakaan Sekolah.
                </div>
            </div>

            <div class="signature-box">
                <div class="sign-city">Banda Aceh, <?=date('d')?> <?=indonesian_date(date('Y-m-d'), true)?></div>
                <div class="sign-name">HERI SUSANTO</div>
                <div class="sign-title">Guru Rekayasa Perangkat Lunak (RPL) / Pengembang Asli</div>
            </div>
        </div>
    </div>

</body>
</html>
