<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';

// Hanya administrator yang dapat mencetak label QR buku
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    flash('Akses ditolak: Hanya administrator yang dapat mencetak label QR buku.', 'error');
    header('Location: ' . u('index.php?page=login'));
    exit;
}

$headerText = trim($_GET['header_text'] ?? 'Buku Perpustakaan SMKN 5 Telkom Banda Aceh');
$schoolTitle = $headerText;

if (!function_exists('format_header_label')) {
    function format_header_label($text) {
        $clean = trim((string)$text);
        if (empty($clean)) {
            return 'BUKU PERPUSTAKAAN<br>SMKN 5 TELKOM BANDA ACEH';
        }
        if (stripos($clean, '<br>') !== false) {
            return $clean;
        }
        if (strpos($clean, "\n") !== false) {
            return nl2br(e($clean));
        }
        if (preg_match('/^(data\s+)?buku\s+(per|der)pustakaan\s+smkn\s+5\s+telkom\s+banda\s+aceh$/i', $clean)) {
            return 'BUKU PERPUSTAKAAN<br>SMKN 5 TELKOM BANDA ACEH';
        }
        if (preg_match('/^(per|der)pustakaan\s+smkn\s+5\s+telkom\s+banda\s+aceh$/i', $clean)) {
            return 'PERPUSTAKAAN SMKN 5 TELKOM<br>BANDA ACEH';
        }
        // Pisahkan menjadi 2 baris jika teks panjang agar rapi dan muat terbaca
        $words = explode(' ', $clean);
        if (count($words) >= 3 && mb_strlen($clean) > 18) {
            $mid = (int)ceil(count($words) / 2);
            $line1 = implode(' ', array_slice($words, 0, $mid));
            $line2 = implode(' ', array_slice($words, $mid));
            return e($line1) . '<br>' . e($line2);
        }
        return e($clean);
    }
}

// Ambil semua daftar buku untuk pilihan dropdown jika admin ingin ganti buku
$allBooks = [];
try {
    $stmtAll = $pdo->query("SELECT id, code, title, category, quantity, copy_serials, call_number FROM books ORDER BY title ASC");
    $allBooks = $stmtAll->fetchAll();
} catch (Throwable $e) {}

// Parameter Buku Terpilih
$bookId = (int)($_GET['id'] ?? 0);
$lastBookId = (int)($_GET['last_book_id'] ?? 0);
$selectedBook = null;

if ($bookId > 0) {
    $stmtB = $pdo->prepare("SELECT * FROM books WHERE id = ? LIMIT 1");
    $stmtB->execute([$bookId]);
    $selectedBook = $stmtB->fetch();
}

// Cek apakah admin baru saja mengganti buku dari dropdown (id != last_book_id, atau ada flag from_select, atau belum ada code di GET)
$isBookSwitched = ($bookId > 0 && ($bookId !== $lastBookId || isset($_GET['from_select']) || !isset($_GET['code'])));

if ($selectedBook && $isBookSwitched) {
    // Otomatis sinkronkan seluruh data dari buku yang dipilih
    $bookCode = trim($selectedBook['code'] ?? '');
    $bookTitle = trim($selectedBook['title'] ?? '');
    $bookCategory = trim($selectedBook['category'] ?? '');
    $bookCallNumber = trim($selectedBook['call_number'] ?? '');
    $printQty = (int)($selectedBook['quantity'] ?? 1);
} else {
    // Gunakan input manual atau nilai yang sedang aktif
    $bookCode = trim($_GET['code'] ?? ($selectedBook['code'] ?? 'AGM-116'));
    $bookTitle = trim($_GET['title'] ?? ($selectedBook['title'] ?? 'Pendidikan Agama'));
    $bookCategory = trim($_GET['category'] ?? ($selectedBook['category'] ?? 'Agama'));
    $bookCallNumber = trim($_GET['call_number'] ?? ($selectedBook['call_number'] ?? ''));
    $printQty = (int)($_GET['qty'] ?? ($selectedBook['quantity'] ?? 50));
}
if ($printQty <= 0) $printQty = 1;
if ($printQty > 500) $printQty = 500; // Batas wajar browser rendering

$formatSeq = trim($_GET['seq_format'] ?? 'dash'); // dash (AGM-116-01), slash (AGM-116/01), master (AGM-116)
$codeType = trim($_GET['code_type'] ?? 'qr'); // qr (QR Code 2D) atau barcode (Kode Batang 1D Code 128)
if ($codeType !== 'barcode') $codeType = 'qr';
$showSchoolName = isset($_GET['show_school']) ? (int)$_GET['show_school'] : 1;
$showTitle = isset($_GET['show_title']) ? (int)$_GET['show_title'] : 1;
$showCategory = isset($_GET['show_cat']) ? (int)$_GET['show_cat'] : 1;

// Ambil nomor seri yang sudah ada jika ada di database
$existingSerials = [];
if (!empty($selectedBook['copy_serials'])) {
    $decoded = json_decode($selectedBook['copy_serials'], true);
    if (is_array($decoded) && !empty($decoded)) {
        $existingSerials = array_values($decoded);
    }
}

// Generate Daftar Label
$labels = [];
$useSerials = !empty($existingSerials) && empty($_GET['force_auto']);

if ($useSerials) {
    // Gunakan nomor seri eksemplar terdaftar
    $limitCount = min(count($existingSerials), $printQty);
    for ($i = 0; $i < $limitCount; $i++) {
        $labels[] = [
            'code' => $existingSerials[$i],
            'display_code' => $existingSerials[$i],
            'title' => $bookTitle,
            'category' => $bookCategory,
            'call_number' => $bookCallNumber,
            'index' => $i + 1
        ];
    }
    // Jika qty yang diminta lebih banyak dari seri yang ada, buatkan lanjutannya
    if ($printQty > count($existingSerials)) {
        for ($i = count($existingSerials) + 1; $i <= $printQty; $i++) {
            $numPad = str_pad($i, 2, '0', STR_PAD_LEFT);
            $sn = ($formatSeq === 'slash') ? ($bookCode . '/' . $numPad) : (($formatSeq === 'master') ? $bookCode : ($bookCode . '-' . $numPad));
            $labels[] = [
                'code' => $sn,
                'display_code' => $sn,
                'title' => $bookTitle,
                'category' => $bookCategory,
                'call_number' => $bookCallNumber,
                'index' => $i
            ];
        }
    }
} else {
    // Generate nomor urut otomatis
    for ($i = 1; $i <= $printQty; $i++) {
        $numPad = str_pad($i, 2, '0', STR_PAD_LEFT);
        $sn = ($formatSeq === 'slash') ? ($bookCode . '/' . $numPad) : (($formatSeq === 'master') ? $bookCode : ($bookCode . '-' . $numPad));
        $labels[] = [
            'code' => $sn,
            'display_code' => $sn,
            'title' => $bookTitle,
            'category' => $bookCategory,
            'call_number' => $bookCallNumber,
            'index' => $i
        ];
    }
}

// Hitung estimasi kertas A4 yang dibutuhkan (20 label per lembar)
$labelsPerSheet = 20;
$sheetsNeeded = ceil(count($labels) / $labelsPerSheet);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label <?=($codeType==='barcode'?'Kode Batang (Barcode)':'QR Code')?> Buku - <?=e($bookCode)?> (<?=count($labels)?> Label)</title>
    <link rel="stylesheet" href="<?=u('style.css')?>">
    <style>
        /* Pengaturan Cetak Presisi Kertas A4 */
        @page {
            size: A4 portrait;
            margin: 8mm 6mm;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background: #e2e8f0;
            color: #0f172a;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Top Bar Kontrol Layar */
        .control-panel {
            background: #0f172a;
            color: #ffffff;
            padding: 16px 20px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .control-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .control-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .control-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            background: rgba(255,255,255,0.06);
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .form-ctrl {
            width: 100%;
            padding: 6px 10px;
            font-size: 12.5px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            font-weight: 600;
        }

        .btn-print {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 9px 20px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-print:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-back {
            background: rgba(255,255,255,0.15);
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.25);
            color: #fff;
        }

        /* Lembar Kertas Tampilan Layar */
        .print-sheet-wrapper {
            max-width: 210mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 10mm 8mm;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            border-radius: 4px;
            min-height: 297mm;
        }

        /* Grid Berjejer (4 Kolom per Baris, Maksimal Hemat Kertas) */
        .labels-grid {
            display: grid;
            grid-template-columns: repeat(4, 4cm);
            gap: 4mm 4mm;
            justify-content: center;
            margin: 0 auto;
        }

        /* Satuan Label: 4 cm × 5 cm Presisi */
        .book-label {
            width: 4cm;
            height: 5cm;
            box-sizing: border-box;
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 2.2mm 1.5mm 1.8mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            background: #ffffff;
            page-break-inside: avoid;
            break-inside: avoid;
            position: relative;
            overflow: hidden;
        }

        /* Bagian Atas: Identitas Perpustakaan */
        .label-header {
            width: 100%;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1.5px;
            margin-bottom: 1.5px;
        }

        .label-school {
            font-size: 5pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            display: block;
            line-height: 1.15;
            text-align: center;
            overflow: hidden;
            word-wrap: break-word;
        }

        .label-category {
            font-size: 5.2pt;
            color: #0284c7;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1;
            display: block;
            margin-top: 1px;
        }

        /* Bagian Tengah: QR Code Presisi 3 cm × 3 cm */
        .label-qr-wrap {
            width: 3cm;
            height: 3cm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background: #ffffff;
            flex-shrink: 0;
        }

        .label-qr-wrap svg {
            width: 3cm !important;
            height: 3cm !important;
            display: block;
        }

        /* Bagian Tengah: Kode Batang / Barcode 1D Code 128 */
        .label-barcode-wrap {
            width: 3.7cm;
            height: 2.8cm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            background: #ffffff;
            flex-shrink: 0;
            padding: 4px 0;
        }

        .label-barcode-wrap svg {
            width: 3.6cm !important;
            height: 1.8cm !important;
            display: block;
        }

        /* Bagian Bawah: Kode Buku / Eksemplar & Judul */
        .label-footer {
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5px;
            margin-top: 1.5px;
        }

        .label-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 8pt;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.5px;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .label-title {
            font-size: 6pt;
            color: #475569;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            display: block;
            max-width: 3.7cm;
            margin: 0 auto;
        }

        /* Aturan Print */
        @media print {
            body {
                background: #ffffff;
                margin: 0;
                padding: 0;
            }

            .control-panel,
            .no-print {
                display: none !important;
            }

            .print-sheet-wrapper {
                max-width: none;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .labels-grid {
                gap: 4mm 4mm;
            }

            .book-label {
                border: 0.8px dashed #64748b;
            }
        }
    </style>
</head>
<body>

    <!-- Panel Kontrol Di Layar (Tidak Tercetak) -->
    <div class="control-panel no-print">
        <div class="control-container">
            <div class="control-row">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:22px;">🏷️</span>
                    <div>
                        <h2 style="margin:0;font-size:16px;font-weight:800;">Pembuat Label QR Code &amp; Kode Batang Buku Otomatis (Ukuran 4 × 5 cm)</h2>
                        <div style="font-size:12px;color:#94a3b8;margin-top:2px;">
                            Buku: <b><?=e($bookTitle)?></b> (Kode: <b><?=e($bookCode)?></b>) · 
                            Total <b><?=count($labels)?> Label</b> (~<?=$sheetsNeeded?> Lembar Kertas A4, 20 label/lembar) ·
                            Format: <b><?=($codeType === 'barcode' ? 'Kode Batang / Barcode 1D' : 'QR Code 2D')?></b>
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <button type="button" class="btn-print" onclick="window.print()">
                        🖨️ Cetak Label Sekarang (Bisa Simpan PDF)
                    </button>
                    <a href="<?=u('index.php?page=admin_books')?>" class="btn-back">
                        ← Kembali ke Katalog Buku
                    </a>
                </div>
            </div>

            <!-- Form Penyesuaian Cepat -->
            <form method="get" class="control-form-grid" id="form_label_controls">
                <input type="hidden" name="last_book_id" id="input_last_book_id" value="<?=$bookId?>">

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Pilih Buku:</label>
                    <select name="id" id="select_book_picker" class="form-ctrl" onchange="handleBookChange(this)">
                        <option value="0" data-code="" data-title="" data-category="" data-call-number="" data-qty="1">-- Input Manual / Bebas --</option>
                        <?php foreach ($allBooks as $bk): ?>
                            <option value="<?=$bk['id']?>" 
                                    data-code="<?=e($bk['code'])?>" 
                                    data-title="<?=e($bk['title'])?>" 
                                    data-category="<?=e($bk['category'] ?? '')?>" 
                                    data-call-number="<?=e($bk['call_number'] ?? '')?>" 
                                    data-qty="<?=$bk['quantity']?>" 
                                    <?=$bookId===$bk['id']?'selected':''?>>
                                <?=e($bk['code'])?> - <?=e($bk['title'])?> <?=!empty($bk['call_number']) ? ' ['.e($bk['call_number']).']' : ''?> (Stok: <?=$bk['quantity']?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="call_number" id="input_book_call_number" value="<?=e($bookCallNumber)?>">

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Kop Atas Label:</label>
                    <input type="text" name="header_text" id="input_header_text" class="form-ctrl" value="<?=e($headerText)?>">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Kode Buku:</label>
                    <input type="text" name="code" id="input_book_code" class="form-ctrl" value="<?=e($bookCode)?>" required>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Judul Buku:</label>
                    <input type="text" name="title" id="input_book_title" class="form-ctrl" value="<?=e($bookTitle)?>">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Kategori:</label>
                    <input type="text" name="category" id="input_book_category" class="form-ctrl" value="<?=e($bookCategory)?>">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Jumlah Label (Eksemplar):</label>
                    <input type="number" name="qty" id="input_book_qty" class="form-ctrl" min="1" max="500" value="<?=$printQty?>" style="font-weight:800;color:#0284c7;">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Pilihan Jenis Kode:</label>
                    <select name="code_type" id="select_code_type" class="form-ctrl" onchange="this.form.submit()" style="font-weight:700;color:#0284c7;">
                        <option value="qr" <?=$codeType==='qr'?'selected':''?>>📱 QR Code (2D)</option>
                        <option value="barcode" <?=$codeType==='barcode'?'selected':''?>>📊 Kode Batang (Barcode 1D Code 128)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size:11px;font-weight:700;color:#94a3b8;display:block;margin-bottom:3px;">Format Nomor Urut:</label>
                    <select name="seq_format" class="form-ctrl" onchange="this.form.submit()">
                        <option value="dash" <?=$formatSeq==='dash'?'selected':''?>><?=e($bookCode)?>-01 s/d <?=e($bookCode)?>-<?=$printQty?></option>
                        <option value="slash" <?=$formatSeq==='slash'?'selected':''?>><?=e($bookCode)?>/01 s/d <?=e($bookCode)?>/<?=$printQty?></option>
                        <option value="master" <?=$formatSeq==='master'?'selected':''?>><?=e($bookCode)?> (Kode Buku Saja)</option>
                    </select>
                </div>

                <div style="display:flex;align-items:flex-end;">
                    <button type="submit" class="btn-print" style="width:100%;justify-content:center;padding:7px 10px;font-size:12px;background:#0284c7;">
                        🔄 Terapkan Pengaturan
                    </button>
                </div>
            </form>

            <div style="display:flex;justify-content:space-between;align-items:center;font-size:11.5px;color:#94a3b8;flex-wrap:wrap;gap:8px;">
                <div>
                    💡 <b>Panduan Cetak Hemat Kertas:</b> Ukuran label <b>4 cm × 5 cm</b> dengan <b><?=($codeType === 'barcode' ? 'Kode Batang (Barcode 1D Code 128)' : 'QR Code 3 cm × 3 cm')?></b>. Berjejer <b>4 kolom × 5 baris</b> (total 20 stiker per lembar A4).
                </div>
                <div style="color:#6ee7b7;font-weight:700;">
                    ✓ Siap dipotong dengan gunting/cutter mengikuti garis putus-putus.
                </div>
            </div>
        </div>
    </div>

    <!-- Lembar Cetak Label Grid Berjejer -->
    <div class="print-sheet-wrapper">
        <div class="labels-grid">
            <?php foreach ($labels as $idx => $lbl): ?>
                <div class="book-label">
                    <!-- Atas: Nama Sekolah & Kategori -->
                    <div class="label-header">
                        <span class="label-school" title="<?=e($headerText)?>">
                            <?=format_header_label($headerText)?>
                        </span>
                        <?php if ($showCategory && (!empty($lbl['category']) || !empty($lbl['call_number']))): 
                            $catLabelStr = (!empty($lbl['call_number']) ? '['.$lbl['call_number'].'] ' : '') . ($lbl['category'] ?? '');
                        ?>
                            <span class="label-category" title="<?=e($catLabelStr)?>">
                                <?=e(mb_strimwidth($catLabelStr, 0, 24, '..'))?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Bagian Tengah: QR Code 3x3 cm ATAU Kode Batang (Barcode 1D Code 128) -->
                    <?php if ($codeType === 'barcode'): ?>
                        <div class="label-barcode-wrap">
                            <?=generate_barcode_svg($lbl['code'], 130, 48, false)?>
                        </div>
                    <?php else: ?>
                        <div class="label-qr-wrap">
                            <?=generate_qr_svg($lbl['code'], 114)?>
                        </div>
                    <?php endif; ?>

                    <!-- Bawah: Kode Buku / Eksemplar & Judul -->
                    <div class="label-footer">
                        <span class="label-code" title="<?=e($lbl['display_code'])?>">
                            <?=e($lbl['display_code'])?>
                        </span>
                        <?php if ($showTitle && !empty($lbl['title'])): ?>
                            <span class="label-title" title="<?=e($lbl['title'])?>">
                                <?=e(mb_strimwidth($lbl['title'], 0, 24, '..'))?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    function handleBookChange(sel) {
        const opt = sel.options[sel.selectedIndex];
        if (!opt) return;

        if (sel.value === '0') {
            sel.form.submit();
            return;
        }

        const code = opt.getAttribute('data-code') || '';
        const title = opt.getAttribute('data-title') || '';
        const category = opt.getAttribute('data-category') || '';
        const callNumber = opt.getAttribute('data-call-number') || '';
        const qty = opt.getAttribute('data-qty') || '';

        const codeInp = document.getElementById('input_book_code');
        const titleInp = document.getElementById('input_book_title');
        const catInp = document.getElementById('input_book_category');
        const callInp = document.getElementById('input_book_call_number');
        const qtyInp = document.getElementById('input_book_qty');

        if (codeInp && code) codeInp.value = code;
        if (titleInp && title) titleInp.value = title;
        if (catInp && category) catInp.value = category;
        if (callInp) callInp.value = callNumber;
        if (qtyInp && qty) qtyInp.value = qty;

        let flag = document.getElementById('input_from_select');
        if (!flag) {
            flag = document.createElement('input');
            flag.type = 'hidden';
            flag.id = 'input_from_select';
            flag.name = 'from_select';
            sel.form.appendChild(flag);
        }
        flag.value = '1';

        sel.form.submit();
    }
    </script>
</body>
</html>
