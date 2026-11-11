<?php
/**
 * Modul Buku Digital (E-Book Gratis) - Perpustakaan Digital
 * Menyediakan akses buku digital gratis, e-book pelajaran SMK, modul kejuruan,
 * tautan baca online/Drive/PDF, dan kode QR scannable & printable untuk siswa/guru.
 */

function handle_buku_digital_page($page) {
    if ($page === 'admin_buku_digital') {
        handle_admin_buku_digital();
    } elseif ($page === 'buku_digital_detail') {
        handle_buku_digital_detail();
    } elseif ($page === 'buku_digital_print_qr') {
        handle_buku_digital_print_qr();
    } else {
        handle_buku_digital_gallery();
    }
}

/**
 * 1. HALAMAN GALERI PUBLIK: BUKU DIGITAL
 */
function handle_buku_digital_gallery() {
    global $pdo;

    $cat = trim($_GET['cat'] ?? '');
    $q = trim($_GET['q'] ?? '');

    $where = ["status = 'published'"];
    $params = [];

    if ($cat !== '' && $cat !== 'Semua') {
        $where[] = "category = ?";
        $params[] = $cat;
    }

    if ($q !== '') {
        $where[] = "(title LIKE ? OR author LIKE ? OR publisher LIKE ? OR description LIKE ?)";
        $like = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $whereSql = implode(' AND ', $where);
    $stmt = $pdo->prepare("SELECT * FROM buku_digital WHERE {$whereSql} ORDER BY id DESC");
    $stmt->execute($params);
    $bukuList = $stmt->fetchAll();

    // Distinct categories for filter buttons
    $categories = $pdo->query("SELECT DISTINCT category FROM buku_digital WHERE status = 'published' ORDER BY category ASC")->fetchAll(PDO::FETCH_COLUMN);

    // Total counts
    $totalBuku = (int)$pdo->query("SELECT COUNT(*) FROM buku_digital WHERE status = 'published'")->fetchColumn();
    $totalViews = (int)$pdo->query("SELECT SUM(views) FROM buku_digital WHERE status = 'published'")->fetchColumn();

    render_public_header('buku_digital', 'Buku Digital');

    // Banner Settings (Customizable by Admin)
    $bannerBadge = get_setting('buku_digital_banner_badge', 'Koleksi E-Book & Pustaka Elektronik');
    $bannerTitle = get_setting('buku_digital_banner_title', 'Buku Digital');
    $bannerSub = get_setting('buku_digital_banner_subtitle', 'Koleksi buku elektronik gratis, modul kejuruan SMK, dan bahan ajar digital. Baca online langsung atau scan QR Code di smartphone Anda.');
    $isAdmin = !empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    ?>
    <!-- Hero / Banner Section -->
    <div style="background:linear-gradient(135deg, #1e3a8a 0%, #0369a1 55%, #0284c7 100%);color:#ffffff;padding:50px 20px 42px;position:relative;overflow:hidden;box-shadow:0 4px 20px rgba(2,132,199,0.2);">
        <div style="position:absolute;top:-50px;right:-50px;width:320px;height:320px;background:rgba(255,255,255,0.08);border-radius:50%;pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;left:8%;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;pointer-events:none;"></div>
        
        <div class="container" style="max-width:1200px;margin:auto;position:relative;z-index:2;text-align:center;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.18);padding:6px 16px;border-radius:30px;font-size:12.5px;font-weight:700;letter-spacing:0.3px;margin-bottom:14px;backdrop-filter:blur(6px);border:1px solid rgba(255,255,255,0.25);">
                <?=icon('book-open', '', 16)?>
                <span><?=e($bannerBadge)?></span>
                <?php if ($isAdmin): ?>
                    <a href="<?=u('index.php?page=admin_buku_digital#banner_settings_card')?>" style="margin-left:8px;background:rgba(255,255,255,0.25);color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;text-decoration:none;" title="Edit Teks Banner">✎ Edit Banner</a>
                <?php endif; ?>
            </div>
            <h1 style="margin:0 0 12px;font-size:32px;font-weight:900;letter-spacing:-0.5px;text-shadow:0 2px 8px rgba(0,0,0,0.2);">
                <?=e($bannerTitle)?>
            </h1>
            <p style="margin:0 auto 24px;max-width:760px;font-size:14.5px;line-height:1.6;color:rgba(255,255,255,0.92);">
                <?=e($bannerSub)?>
            </p>

            <!-- Quick Statistics Badges -->
            <div style="display:inline-flex;align-items:center;gap:18px;background:rgba(15,23,42,0.35);padding:10px 22px;border-radius:40px;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.15);flex-wrap:wrap;justify-content:center;">
                <div style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:600;">
                    <span style="background:#38bdf8;color:#0f172a;width:22px;height:22px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;"><?=$totalBuku?></span>
                    <span>Judul Buku Digital</span>
                </div>
                <div style="width:1px;height:16px;background:rgba(255,255,255,0.25);"></div>
                <div style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:600;">
                    <span style="color:#fde047;">⚡ 100% Gratis &amp; Bebas Akses</span>
                </div>
                <div style="width:1px;height:16px;background:rgba(255,255,255,0.25);"></div>
                <div style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:600;">
                    <span style="color:#86efac;">📱 Dilengkapi QR Code Scan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container" style="max-width:1200px;margin:32px auto 60px;padding:0 20px;">
        
        <!-- Search & Filter Controls -->
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:28px;box-shadow:0 4px 16px rgba(0,0,0,0.03);">
            <form method="GET" action="<?=u('index.php')?>" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;justify-content:space-between;">
                <input type="hidden" name="page" value="buku_digital">
                
                <!-- Search Input -->
                <div style="flex:1;min-width:260px;position:relative;">
                    <div style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;line-height:0;">
                        <?=icon('search', '', 18)?>
                    </div>
                    <input type="text" name="q" value="<?=e($q)?>" placeholder="Cari judul buku digital, penulis, atau materi..." class="form-control" style="padding-left:38px;height:42px;border-radius:8px;font-size:13.5px;">
                </div>

                <!-- Category Selector -->
                <div style="min-width:200px;">
                    <select name="cat" class="form-control" onchange="this.form.submit()" style="height:42px;border-radius:8px;font-size:13px;font-weight:600;">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?=e($c)?>" <?=$cat===$c?'selected':''?>><?=e($c)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Submit & Reset -->
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn btn-primary" style="height:42px;padding:0 18px;display:inline-flex;align-items:center;gap:6px;font-weight:700;border-radius:8px;">
                        <?=icon('search', '', 16)?> Cari
                    </button>
                    <?php if ($q !== '' || $cat !== ''): ?>
                        <a href="<?=u('index.php?page=buku_digital')?>" class="btn btn-outline-secondary" style="height:42px;padding:0 14px;display:inline-flex;align-items:center;border-radius:8px;" title="Reset Filter">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Category Pills Quick Filter -->
            <div style="display:flex;align-items:center;gap:8px;margin-top:16px;overflow-x:auto;padding-bottom:4px;">
                <span style="font-size:12px;font-weight:700;color:#64748b;white-space:nowrap;">Kategori:</span>
                <a href="<?=u('index.php?page=buku_digital' . ($q ? '&q='.urlencode($q) : ''))?>" class="badge <?=empty($cat)?'badge-primary':'badge-secondary'?>" style="padding:6px 12px;font-size:12px;text-decoration:none;border-radius:20px;cursor:pointer;">
                    Semua
                </a>
                <?php foreach ($categories as $c): ?>
                    <a href="<?=u('index.php?page=buku_digital&cat=' . urlencode($c) . ($q ? '&q='.urlencode($q) : ''))?>" class="badge <?=$cat===$c?'badge-primary':'badge-secondary'?>" style="padding:6px 12px;font-size:12px;text-decoration:none;border-radius:20px;white-space:nowrap;cursor:pointer;">
                        <?=e($c)?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Grid of Digital Books -->
        <?php if (empty($bukuList)): ?>
            <div style="text-align:center;padding:60px 20px;background:#ffffff;border:1px dashed #cbd5e1;border-radius:14px;">
                <div style="width:64px;height:64px;border-radius:50%;background:#f1f5f9;color:#94a3b8;display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px;">
                    <?=icon('book-open', '', 32)?>
                </div>
                <h3 style="margin:0 0 6px;font-size:17px;font-weight:800;color:var(--navy);">Buku Digital Tidak Ditemukan</h3>
                <p style="margin:0 0 16px;color:#64748b;font-size:13.5px;">Coba gunakan kata kunci pencarian yang lain atau pilih kategori yang berbeda.</p>
                <a href="<?=u('index.php?page=buku_digital')?>" class="btn btn-outline-primary btn-sm">Tampilkan Semua Buku</a>
            </div>
        <?php else: ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(270px, 1fr));gap:24px;">
                <?php foreach ($bukuList as $b): 
                    $coverUrl = get_buku_digital_cover_url($b);
                ?>
                    <div class="card-box" style="display:flex;flex-direction:column;border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;background:#ffffff;box-shadow:0 4px 14px rgba(0,0,0,0.04);transition:transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 14px rgba(0,0,0,0.04)';">
                        
                        <!-- Book Cover Display -->
                        <div style="position:relative;height:200px;background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%);overflow:hidden;display:flex;align-items:center;justify-content:center;">
                            <?php if ($coverUrl): ?>
                                <img src="<?=e($coverUrl)?>" alt="<?=e($b['title'])?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <!-- Stylized Digital Book Cover Mockup -->
                                <div style="width:125px;height:170px;background:linear-gradient(135deg, #0284c7 0%, #1e40af 100%);border-radius:4px 8px 8px 4px;box-shadow:4px 6px 15px rgba(0,0,0,0.5);border-left:5px solid rgba(255,255,255,0.3);padding:12px;display:flex;flex-direction:column;justify-content:space-between;color:#ffffff;text-align:left;">
                                    <div>
                                        <div style="font-size:8.5px;font-weight:800;letter-spacing:0.5px;text-transform:uppercase;color:#7dd3fc;margin-bottom:4px;">
                                            <?=e(mb_strimwidth($b['category'], 0, 18, '..'))?>
                                        </div>
                                        <div style="font-size:11px;font-weight:900;line-height:1.2;color:#ffffff;">
                                            <?=e(mb_strimwidth($b['title'], 0, 48, '...'))?>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.2);padding-top:6px;">
                                        <div style="font-size:8px;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:70px;">
                                            <?=e($b['author'])?>
                                        </div>
                                        <?=icon('book-open', '', 12)?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Badges on Cover -->
                            <div style="position:absolute;top:10px;left:10px;display:flex;gap:5px;flex-wrap:wrap;">
                                <span class="badge badge-green" style="font-size:10.5px;padding:3.5px 8px;font-weight:800;letter-spacing:0.3px;box-shadow:0 2px 6px rgba(0,0,0,0.2);">
                                    ✓ GRATIS
                                </span>
                            </div>

                            <div style="position:absolute;bottom:10px;right:10px;">
                                <span style="background:rgba(15,23,42,0.75);color:#ffffff;font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;backdrop-filter:blur(4px);">
                                    <?=!empty($b['year']) ? e($b['year']) : 'E-Book'?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
                            <!-- Category & Format -->
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                <span style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:0.3px;">
                                    <?=e($b['category'])?>
                                </span>
                                <?php if (!empty($b['pages'])): ?>
                                    <span style="font-size:11px;color:#64748b;">
                                        <?=$b['pages']?> Halaman
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Book Title -->
                            <h3 style="margin:0 0 8px;font-size:15px;font-weight:800;line-height:1.35;color:var(--navy);">
                                <a href="<?=u('index.php?page=buku_digital_detail&id='.$b['id'])?>" style="color:inherit;text-decoration:none;" title="<?=e($b['title'])?>">
                                    <?=e(mb_strimwidth($b['title'], 0, 68, '...'))?>
                                </a>
                            </h3>

                            <!-- Author & Publisher -->
                            <div style="font-size:12px;color:#475569;margin-bottom:10px;display:flex;align-items:center;gap:5px;">
                                <?=icon('user', '', 13)?>
                                <span style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    <?=e($b['author'])?>
                                </span>
                            </div>

                            <!-- Description Snippet -->
                            <p style="margin:0 0 14px;font-size:12.5px;color:#64748b;line-height:1.45;flex:1;">
                                <?=e(mb_strimwidth($b['description'] ?? '', 0, 105, '...'))?>
                            </p>

                            <!-- Actions Row -->
                            <div style="border-top:1px solid #f1f5f9;padding-top:12px;display:flex;align-items:center;justify-content:space-between;gap:6px;">
                                <div style="display:flex;gap:6px;">
                                    <a href="<?=u('index.php?page=buku_digital_detail&id='.$b['id'])?>" class="btn btn-primary btn-sm" style="font-size:11.5px;padding:6px 11px;display:inline-flex;align-items:center;gap:4px;">
                                        <?=icon('eye', '', 13)?> Detail
                                    </a>
                                    <!-- Safe QR Button using clean data attributes and hidden SVG container -->
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openBukuDigitalQrModal(<?=$b['id']?>, this)" data-title="<?=htmlspecialchars($b['title'], ENT_QUOTES, 'UTF-8')?>" data-author="<?=htmlspecialchars($b['author'], ENT_QUOTES, 'UTF-8')?>" data-publisher="<?=htmlspecialchars($b['publisher'] ?? '', ENT_QUOTES, 'UTF-8')?>" data-year="<?=htmlspecialchars($b['year'] ?? '', ENT_QUOTES, 'UTF-8')?>" data-url="<?=htmlspecialchars($b['file_url'], ENT_QUOTES, 'UTF-8')?>" style="font-size:11.5px;padding:6px 10px;display:inline-flex;align-items:center;gap:4px;" title="Lihat dan Cetak QR Code">
                                        <?=icon('qr', '', 14)?> QR
                                    </button>
                                    <div id="bukuQrSvgData_<?=$b['id']?>" style="display:none;"><?=generate_qr_svg($b['file_url'], 180)?></div>
                                </div>
                                <a href="<?=e($b['file_url'])?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm" style="font-size:11.5px;padding:6px 10px;display:inline-flex;align-items:center;gap:4px;" title="Buka dan Baca Buku Digital">
                                    <?=icon('book-open', '', 13)?> Baca ↗
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Interactive QR Code Quick Modal -->
    <div id="bukuQrModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.65);z-index:99999;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(3px);">
        <div style="background:#ffffff;border-radius:14px;max-width:440px;width:100%;padding:24px;box-shadow:0 20px 40px rgba(0,0,0,0.25);position:relative;text-align:center;animation:fadeInScale 0.2s ease;">
            <button type="button" onclick="closeBukuDigitalQrModal()" style="position:absolute;top:14px;right:14px;background:#f1f5f9;border:none;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;font-size:16px;">✕</button>
            
            <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                SCAN QR CODE BUKU DIGITAL
            </div>
            <h3 id="modalBukuTitle" style="margin:0 0 6px;font-size:16px;font-weight:800;color:var(--navy);line-height:1.3;"></h3>
            <div id="modalBukuAuthor" style="font-size:12.5px;color:#64748b;margin-bottom:16px;"></div>

            <!-- SVG QR Container -->
            <div style="background:#f8fafc;border:2px dashed #cbd5e1;border-radius:12px;padding:18px;display:inline-block;margin-bottom:14px;">
                <div id="modalBukuQrSvg" style="line-height:0;"></div>
            </div>

            <p style="margin:0 0 16px;font-size:12px;color:#64748b;line-height:1.45;">
                📱 Arahkan kamera smartphone Anda ke QR Code di atas untuk membaca buku digital gratis ini langsung di HP atau tablet!
            </p>

            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <a id="modalBukuPrintBtn" href="#" target="_blank" class="btn btn-primary btn-sm" style="padding:8px 14px;font-size:12px;display:inline-flex;align-items:center;gap:5px;">
                    <?=icon('print', '', 14)?> Cetak Lembar QR
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyBukuDigitalUrl()" style="padding:8px 14px;font-size:12px;display:inline-flex;align-items:center;gap:5px;">
                    <?=icon('copy', '', 14)?> Salin Link
                </button>
                <a id="modalBukuDriveBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm" style="padding:8px 14px;font-size:12px;display:inline-flex;align-items:center;gap:5px;">
                    <?=icon('book-open', '', 14)?> Buka Buku ↗
                </a>
            </div>
        </div>
    </div>

    <script>
    let currentModalBukuUrl = '';
    function openBukuDigitalQrModal(id, btn) {
        const title = btn.getAttribute('data-title') || '';
        const author = btn.getAttribute('data-author') || '';
        const publisher = btn.getAttribute('data-publisher') || '';
        const year = btn.getAttribute('data-year') || '';
        const url = btn.getAttribute('data-url') || '';

        currentModalBukuUrl = url;
        document.getElementById('modalBukuTitle').textContent = title;
        document.getElementById('modalBukuAuthor').textContent = author + (publisher ? ' · ' + publisher : '') + (year ? ' (' + year + ')' : '');

        const qrSource = document.getElementById('bukuQrSvgData_' + id);
        document.getElementById('modalBukuQrSvg').innerHTML = qrSource ? qrSource.innerHTML : '';
        document.getElementById('modalBukuPrintBtn').href = '<?=u('index.php?page=buku_digital_print_qr&id=')?>' + id;
        document.getElementById('modalBukuDriveBtn').href = url;

        const modal = document.getElementById('bukuQrModal');
        modal.style.display = 'flex';
    }
    function closeBukuDigitalQrModal() {
        document.getElementById('bukuQrModal').style.display = 'none';
    }
    document.getElementById('bukuQrModal').addEventListener('click', function(e) {
        if (e.target === this) closeBukuDigitalQrModal();
    });
    function copyBukuDigitalUrl() {
        if (!currentModalBukuUrl) return;
        navigator.clipboard.writeText(currentModalBukuUrl).then(() => {
            alert('Tautan buku digital berhasil disalin ke clipboard!');
        }).catch(() => {
            prompt('Salin link buku digital:', currentModalBukuUrl);
        });
    }
    </script>
    <?php
    render_public_footer();
    exit;
}

/**
 * 2. HALAMAN DETAIL BUKU DIGITAL & INTERACTIVE READER / VIEWER
 */
function handle_buku_digital_detail() {
    global $pdo;

    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM buku_digital WHERE id = ?");
    $stmt->execute([$id]);
    $buku = $stmt->fetch();

    if (!$buku) {
        flash('Buku digital tidak ditemukan atau telah dihapus.', 'error');
        header('Location: ' . u('index.php?page=buku_digital'));
        exit;
    }

    // Increment views safely
    try {
        $pdo->prepare("UPDATE buku_digital SET views = views + 1 WHERE id = ?")->execute([$id]);
        $buku['views']++;
    } catch (Throwable $e) {}

    // Related books from the same category
    $stmtRel = $pdo->prepare("SELECT * FROM buku_digital WHERE category = ? AND id != ? AND status = 'published' ORDER BY id DESC LIMIT 4");
    $stmtRel->execute([$buku['category'], $id]);
    $relatedBooks = $stmtRel->fetchAll();

    render_public_header('buku_digital', $buku['title']);

    $coverUrl = get_buku_digital_cover_url($buku);
    $embedUrl = get_drive_embed_url($buku['file_url']);
    ?>
    <div class="container" style="max-width:1100px;margin:30px auto 60px;padding:0 20px;">
        
        <!-- Breadcrumb Navigation -->
        <div style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:#64748b;margin-bottom:20px;">
            <a href="<?=u()?>" style="color:inherit;text-decoration:none;">Beranda</a>
            <span>/</span>
            <a href="<?=u('index.php?page=buku_digital')?>" style="color:inherit;text-decoration:none;">Buku Digital</a>
            <span>/</span>
            <span style="color:var(--primary);font-weight:700;"><?=e(mb_strimwidth($buku['title'], 0, 45, '...'))?></span>
        </div>

        <div style="display:grid;grid-template-columns:300px 1fr;gap:28px;align-items:start;" class="responsive-detail-grid">
            
            <!-- Left Column: Cover, Action Buttons & QR Card -->
            <div>
                <!-- Book Cover -->
                <div style="background:linear-gradient(135deg, #1e293b 0%, #0f172a 100%);border-radius:14px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 10px 25px rgba(0,0,0,0.08);margin-bottom:18px;position:relative;">
                    <?php if ($coverUrl): ?>
                        <img src="<?=e($coverUrl)?>" alt="<?=e($buku['title'])?>" style="width:100%;height:380px;object-fit:cover;display:block;">
                    <?php else: ?>
                        <div style="height:360px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;text-align:center;background:linear-gradient(135deg, #0284c7 0%, #1e40af 100%);color:#ffffff;">
                            <div style="width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                                <?=icon('book-open', '', 36)?>
                            </div>
                            <div style="font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#7dd3fc;margin-bottom:6px;">
                                <?=e($buku['category'])?>
                            </div>
                            <h4 style="margin:0 0 10px;font-size:16px;font-weight:900;line-height:1.3;color:#ffffff;">
                                <?=e($buku['title'])?>
                            </h4>
                            <div style="font-size:12px;color:#e2e8f0;">
                                <?=e($buku['author'])?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div style="position:absolute;top:12px;left:12px;">
                        <span class="badge badge-green" style="font-size:11px;padding:4px 10px;font-weight:800;letter-spacing:0.3px;box-shadow:0 2px 8px rgba(0,0,0,0.25);">
                            ✓ 100% GRATIS
                        </span>
                    </div>
                </div>

                <!-- Primary Action: Baca Buku Sekarang -->
                <a href="<?=e($buku['file_url'])?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-block" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px 18px;font-size:14px;font-weight:800;border-radius:10px;box-shadow:0 4px 14px rgba(2,132,199,0.3);margin-bottom:12px;text-decoration:none;">
                    <?=icon('book-open', '', 18)?> Buka &amp; Baca Buku ↗
                </a>

                <!-- QR Code Access Card (Printed / Scannable) -->
                <div class="card-box" style="border-radius:14px;border:1px solid #e2e8f0;padding:20px;text-align:center;background:#ffffff;box-shadow:0 4px 14px rgba(0,0,0,0.03);">
                    <div style="font-size:11px;font-weight:800;color:var(--primary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">
                        AKSES CEPAT SMARTPHONE
                    </div>
                    <div style="background:#f8fafc;border:2px dashed #cbd5e1;border-radius:10px;padding:14px;display:inline-block;margin-bottom:10px;">
                        <div style="line-height:0;">
                            <?=generate_qr_svg($buku['file_url'], 160)?>
                        </div>
                    </div>
                    <p style="margin:0 0 12px;font-size:11.5px;color:#64748b;line-height:1.4;">
                        Arahkan kamera HP ke QR Code di atas untuk membaca langsung buku ini.
                    </p>
                    <a href="<?=u('index.php?page=buku_digital_print_qr&id='.$buku['id'])?>" target="_blank" class="btn btn-outline-secondary btn-sm btn-block" style="font-size:12px;padding:7px;display:flex;align-items:center;justify-content:center;gap:6px;">
                        <?=icon('print', '', 14)?> Cetak Lembar QR Code
                    </a>
                </div>
            </div>

            <!-- Right Column: Metadata, Description & Reader Preview -->
            <div>
                <div class="card-box" style="border-radius:14px;border:1px solid #e2e8f0;padding:26px;background:#ffffff;box-shadow:0 4px 14px rgba(0,0,0,0.03);margin-bottom:24px;">
                    
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                        <span class="badge badge-primary" style="font-size:11.5px;padding:4px 10px;">
                            <?=e($buku['category'])?>
                        </span>
                        <?php if (!empty($buku['year'])): ?>
                            <span class="badge badge-secondary" style="font-size:11.5px;padding:4px 10px;">
                                Tahun <?=e($buku['year'])?>
                            </span>
                        <?php endif; ?>
                        <span style="font-size:12px;color:#64748b;margin-left:auto;display:inline-flex;align-items:center;gap:4px;">
                            <?=icon('eye', '', 14)?> <?=number_format($buku['views'])?>x Dilihat
                        </span>
                    </div>

                    <h1 style="margin:0 0 14px;font-size:24px;font-weight:900;color:var(--navy);line-height:1.35;">
                        <?=e($buku['title'])?>
                    </h1>

                    <!-- Book Metadata Table -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:14px;background:#f8fafc;padding:16px;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                        <div>
                            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:700;">Penulis / Pengarang</div>
                            <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?=e($buku['author'])?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:700;">Penerbit</div>
                            <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?=!empty($buku['publisher']) ? e($buku['publisher']) : '-'?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:700;">Jumlah Halaman</div>
                            <div style="font-size:13.5px;font-weight:700;color:#1e293b;"><?=!empty($buku['pages']) ? $buku['pages'].' Halaman' : '-'?></div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#64748b;text-transform:uppercase;font-weight:700;">Hak Akses</div>
                            <div style="font-size:13.5px;font-weight:700;color:#10b981;">Gratis / Open Access</div>
                        </div>
                    </div>

                    <!-- Sinopsis / Ringkasan Buku -->
                    <h3 style="margin:0 0 10px;font-size:16px;font-weight:800;color:var(--navy);">Sinopsis &amp; Deskripsi Buku</h3>
                    <div style="font-size:13.5px;color:#334155;line-height:1.7;white-space:pre-line;margin-bottom:24px;">
                        <?=e($buku['description'] ?: 'Tidak ada sinopsis atau deskripsi tambahan untuk buku digital ini.')?>
                    </div>

                    <!-- Link Info Bar -->
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;gap:12px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:#dcfce7;color:#15803d;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <?=icon('check', '', 18)?>
                            </div>
                            <div style="font-size:12.5px;color:#166534;line-height:1.4;">
                                <b>Buku Elektronik Bebas Akses</b><br>
                                Dapat diakses kapan saja secara online melalui intranet maupun internet sekolah.
                            </div>
                        </div>
                        <a href="<?=e($buku['file_url'])?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary" style="font-size:12px;padding:7px 14px;white-space:nowrap;">
                            Buka di Tab Baru ↗
                        </a>
                    </div>
                </div>

                <!-- Interactive Embedded Viewer if Google Drive or Supported Web Page -->
                <?php if ($embedUrl): ?>
                    <div class="card-box" style="border-radius:14px;border:1px solid #e2e8f0;padding:20px;background:#ffffff;box-shadow:0 4px 14px rgba(0,0,0,0.03);margin-bottom:24px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <h3 style="margin:0;font-size:16px;font-weight:800;color:var(--navy);display:flex;align-items:center;gap:6px;">
                                <?=icon('eye', '', 16)?> Pratinjau Dokumen / Reader Online
                            </h3>
                            <a href="<?=e($buku['file_url'])?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="font-size:11.5px;padding:4px 10px;">Layar Penuh ↗</a>
                        </div>
                        <div style="position:relative;width:100%;height:600px;border-radius:8px;overflow:hidden;border:1px solid #cbd5e1;background:#f8fafc;">
                            <iframe src="<?=e($embedUrl)?>" style="width:100%;height:100%;border:none;" allow="autoplay" loading="lazy"></iframe>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Rekomendasi Buku Terkait -->
                <?php if (!empty($relatedBooks)): ?>
                    <div style="margin-top:30px;">
                        <h3 style="margin:0 0 14px;font-size:17px;font-weight:800;color:var(--navy);">Buku Terkait Kategori Ini</h3>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:16px;">
                            <?php foreach ($relatedBooks as $rb): ?>
                                <a href="<?=u('index.php?page=buku_digital_detail&id='.$rb['id'])?>" class="card-box" style="text-decoration:none;color:inherit;padding:12px;border-radius:10px;border:1px solid #e2e8f0;background:#ffffff;display:flex;flex-direction:column;gap:6px;transition:transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='none'">
                                    <div style="font-size:10.5px;color:var(--primary);font-weight:700;text-transform:uppercase;">
                                        <?=e($rb['category'])?>
                                    </div>
                                    <div style="font-size:13px;font-weight:700;line-height:1.3;color:var(--navy);">
                                        <?=e(mb_strimwidth($rb['title'], 0, 48, '...'))?>
                                    </div>
                                    <div style="font-size:11.5px;color:#64748b;margin-top:auto;">
                                        <?=e($rb['author'])?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
    <?php
    render_public_footer();
    exit;
}

/**
 * 3. HALAMAN CETAK LEMBAR QR CODE BUKU DIGITAL
 */
function handle_buku_digital_print_qr() {
    global $pdo;

    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM buku_digital WHERE id = ?");
    $stmt->execute([$id]);
    $buku = $stmt->fetch();

    if (!$buku) {
        die('Buku digital tidak ditemukan.');
    }

    $schoolName = get_setting('school_name', SCHOOL);
    $schoolShort = get_setting('school_short', SCHOOL_SUB);
    $kopLogoLeft = get_setting('kop_logo_left', 'assets/img/logo_aceh_pancacita.png');
    $kopLogoRight = get_setting('kop_logo_right', 'assets/img/logo.jpg');
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cetak QR Code Buku Digital - <?=e($buku['title'])?></title>
        <style>
            @page { size: A4 portrait; margin: 15mm; }
            * { box-sizing: border-box; }
            body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; margin: 0; padding: 20px; background: #f1f5f9; color: #0f172a; }
            .print-container { max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 36px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); text-align: center; border: 2px solid #0284c7; }
            .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: #e0f2fe; color: #0369a1; margin-bottom: 12px; }
            .qr-wrapper { background: #ffffff; padding: 18px; border: 3px solid #0f172a; border-radius: 16px; display: inline-block; margin: 20px auto; box-shadow: 0 8px 16px rgba(0,0,0,0.06); }
            .actions-bar { max-width: 680px; margin: 0 auto 16px; display: flex; justify-content: space-between; align-items: center; }
            .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 13px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; }
            .btn-primary { background: #0284c7; color: #ffffff; }
            .btn-outline { background: #ffffff; color: #475569; border: 1px solid #cbd5e1; }
            @media print {
                body { background: #ffffff; padding: 0; }
                .print-container { border: 2px solid #000000; box-shadow: none; padding: 24px; }
                .no-print { display: none !important; }
            }
        </style>
    </head>
    <body>
        <div class="actions-bar no-print">
            <a href="<?=u('index.php?page=buku_digital_detail&id='.$buku['id'])?>" class="btn btn-outline">
                ← Kembali ke Detail Buku
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                Cetak Lembar QR Code
            </button>
        </div>

        <div class="print-container">
            <!-- Header Sekolah -->
            <div style="border-bottom: 2px solid #0f172a; padding-bottom: 14px; margin-bottom: 20px;">
                <div style="font-size: 13px; font-weight: 800; letter-spacing: 0.5px; color: #64748b; text-transform: uppercase;">
                    PERPUSTAKAAN DIGITAL <?=e($schoolName)?>
                </div>
                <div style="font-size: 18px; font-weight: 900; color: #0f172a; margin-top: 4px;">
                    LEMBAR SCAN AKSES BUKU DIGITAL GRATIS
                </div>
            </div>

            <div class="badge"><?=e($buku['category'])?></div>

            <h1 style="margin: 0 0 10px; font-size: 22px; font-weight: 900; line-height: 1.35; color: #0f172a;">
                <?=e($buku['title'])?>
            </h1>

            <div style="font-size: 13.5px; color: #475569; font-weight: 600; margin-bottom: 12px;">
                Penulis: <b><?=e($buku['author'])?></b>
                <?=!empty($buku['publisher']) ? ' · Penerbit: ' . e($buku['publisher']) : ''?>
                <?=!empty($buku['year']) ? ' (' . e($buku['year']) . ')' : ''?>
            </div>

            <!-- Big High-Res QR SVG Container -->
            <div class="qr-wrapper">
                <div style="line-height:0;">
                    <?=generate_qr_svg($buku['file_url'], 260)?>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; margin: 10px auto; max-width: 520px; font-size: 13px; color: #334155; line-height: 1.5;">
                <div style="font-weight: 800; color: #0284c7; margin-bottom: 4px;">📱 CARA MEMBACA:</div>
                Arahkan kamera smartphone atau aplikasi pemindai QR Code Anda ke gambar kode di atas untuk membuka dan membaca buku digital ini secara gratis.
            </div>

            <div style="margin-top: 24px; font-size: 11px; color: #94a3b8; border-top: 1px dashed #cbd5e1; padding-top: 12px;">
                Dokumen Resmi Perpustakaan · Tersimpan di Repositori Digital Perpustakaan Sekolah
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * 4. HALAMAN ADMIN: KELOLA BUKU DIGITAL (CRUD, FORM, FILTER, CETAK QR)
 */
function handle_admin_buku_digital() {
    global $pdo;
    auth('admin');

    // Handle Delete
    if (isset($_GET['delete'])) {
        $delId = (int)$_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM buku_digital WHERE id = ?");
        $stmt->execute([$delId]);
        flash('Data buku digital berhasil dihapus.');
        header('Location: ' . u('index.php?page=admin_buku_digital'));
        exit;
    }

    // Handle Save Banner Text Settings
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_banner_settings') {
        checkcsrf();
        $badge = trim($_POST['buku_digital_banner_badge'] ?? '');
        $title = trim($_POST['buku_digital_banner_title'] ?? '');
        $sub = trim($_POST['buku_digital_banner_subtitle'] ?? '');

        set_setting('buku_digital_banner_badge', $badge);
        if ($title !== '') set_setting('buku_digital_banner_title', $title);
        set_setting('buku_digital_banner_subtitle', $sub);

        flash('Pengaturan teks & banner Buku Digital berhasil disimpan dan langsung diperbarui!');
        header('Location: ' . u('index.php?page=admin_buku_digital#banner_settings_card'));
        exit;
    }

    // Handle Create & Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkcsrf();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $publisher = trim($_POST['publisher'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));
        $category = trim($_POST['category'] ?? 'Buku Pelajaran SMK');
        $fileUrl = trim($_POST['file_url'] ?? '');
        $fileType = in_array($_POST['file_type'] ?? '', ['pdf', 'drive', 'epub', 'link']) ? $_POST['file_type'] : 'pdf';
        $pages = !empty($_POST['pages']) ? (int)$_POST['pages'] : null;
        $description = trim($_POST['description'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';

        if ($title === '' || $author === '' || $fileUrl === '') {
            flash('Judul buku digital, nama penulis, dan link buku wajib diisi.', 'error');
        } else {
            // Handle Cover Image Upload
            $coverFileName = null;
            if (!empty($_FILES['cover_file']['name']) && $_FILES['cover_file']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['cover_file']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/uploads/buku_digital/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $newFile = 'buku_' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['cover_file']['tmp_name'], $uploadDir . $newFile)) {
                        $coverFileName = 'uploads/buku_digital/' . $newFile;
                    }
                }
            }

            if ($id > 0) {
                if ($coverFileName) {
                    $stmt = $pdo->prepare("UPDATE buku_digital SET title = ?, author = ?, publisher = ?, year = ?, category = ?, file_url = ?, file_type = ?, pages = ?, cover_image = ?, description = ?, status = ? WHERE id = ?");
                    $stmt->execute([$title, $author, $publisher, $year, $category, $fileUrl, $fileType, $pages, $coverFileName, $description, $status, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE buku_digital SET title = ?, author = ?, publisher = ?, year = ?, category = ?, file_url = ?, file_type = ?, pages = ?, description = ?, status = ? WHERE id = ?");
                    $stmt->execute([$title, $author, $publisher, $year, $category, $fileUrl, $fileType, $pages, $description, $status, $id]);
                }
                flash('Buku digital berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO buku_digital (title, author, publisher, year, category, file_url, file_type, pages, cover_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $author, $publisher, $year, $category, $fileUrl, $fileType, $pages, $coverFileName, $description, $status]);
                flash('Buku digital baru berhasil ditambahkan.');
            }
            header('Location: ' . u('index.php?page=admin_buku_digital'));
            exit;
        }
    }

    // Edit item retrieval
    $editItem = null;
    if (isset($_GET['edit'])) {
        $editId = (int)$_GET['edit'];
        $stmt = $pdo->prepare("SELECT * FROM buku_digital WHERE id = ?");
        $stmt->execute([$editId]);
        $editItem = $stmt->fetch();
    }

    // Admin filter & search
    $q = trim($_GET['q'] ?? '');
    $cat = trim($_GET['cat'] ?? '');

    $where = ["1=1"];
    $params = [];
    if ($q !== '') {
        $where[] = "(title LIKE ? OR author LIKE ? OR publisher LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }
    if ($cat !== '') {
        $where[] = "category = ?";
        $params[] = $cat;
    }

    $whereSql = implode(' AND ', $where);
    $stmt = $pdo->prepare("SELECT * FROM buku_digital WHERE {$whereSql} ORDER BY id DESC");
    $stmt->execute($params);
    $allBuku = $stmt->fetchAll();

    // Stats
    $totalBuku = (int)$pdo->query("SELECT COUNT(*) FROM buku_digital")->fetchColumn();
    $totalViews = (int)$pdo->query("SELECT COALESCE(SUM(views), 0) FROM buku_digital")->fetchColumn();
    $totalCategories = (int)$pdo->query("SELECT COUNT(DISTINCT category) FROM buku_digital")->fetchColumn();
    $totalDraft = (int)$pdo->query("SELECT COUNT(*) FROM buku_digital WHERE status = 'draft'")->fetchColumn();

    // Current banner text values
    $curBannerBadge = get_setting('buku_digital_banner_badge', 'Koleksi E-Book & Pustaka Elektronik');
    $curBannerTitle = get_setting('buku_digital_banner_title', 'Buku Digital');
    $curBannerSub = get_setting('buku_digital_banner_subtitle', 'Koleksi buku elektronik gratis, modul kejuruan SMK, dan bahan ajar digital. Baca online langsung atau scan QR Code di smartphone Anda.');

    render_app_layout('admin', 'buku_digital', 'Kelola Buku Digital', function() use (
        $allBuku, $editItem, $q, $cat, $totalBuku, $totalViews, $totalCategories, $totalDraft,
        $curBannerBadge, $curBannerTitle, $curBannerSub
    ) {
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <div>
                <h2 style="margin:0 0 4px;font-size:20px;font-weight:900;color:var(--navy);">Koleksi Buku Digital</h2>
                <div style="font-size:12.5px;color:#64748b;">Kelola buku digital gratis, e-book pelajaran, tautan Google Drive/PDF, dan kode QR siswa.</div>
            </div>
            <div style="display:flex;gap:8px;">
                <a href="#admin_buku_form" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 16px;font-size:12.5px;box-shadow:0 3px 10px rgba(2,132,199,0.25);">
                    <?=icon('plus', '', 14)?> Tambah Buku Digital Baru
                </a>
                <a href="<?=u('index.php?page=buku_digital')?>" target="_blank" class="btn btn-outline-secondary" style="display:inline-flex;align-items:center;gap:6px;font-size:12.5px;padding:8px 14px;">
                    <?=icon('eye', '', 14)?> Lihat Galeri Publik ↗
                </a>
            </div>
        </div>

        <!-- 4-Grid Horizontal Stats -->
        <div class="stats-grid-4" style="margin-bottom:24px;">
            <div class="stat-card-modern" style="background:#ffffff;border:1px solid #e2e8f0;border-left:4px solid var(--primary);box-shadow:var(--shadow-sm);color:#0f172a;">
                <div class="stat-icon-wrapper" style="background:#e0f2fe;color:#0284c7;">
                    <?=icon('book-open', '', 22)?>
                </div>
                <div class="stat-info">
                    <span class="stat-label" style="color:#64748b;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:0.4px;">Total Buku Digital</span>
                    <h3 class="stat-value" style="color:#0f172a;font-size:28px;font-weight:900;margin:2px 0 0;"><?=number_format($totalBuku)?></h3>
                </div>
            </div>

            <div class="stat-card-modern" style="background:#ffffff;border:1px solid #e2e8f0;border-left:4px solid #10b981;box-shadow:var(--shadow-sm);color:#0f172a;">
                <div class="stat-icon-wrapper" style="background:#d1fae5;color:#059669;">
                    <?=icon('eye', '', 22)?>
                </div>
                <div class="stat-info">
                    <span class="stat-label" style="color:#64748b;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:0.4px;">Total Pembaca / Akses</span>
                    <h3 class="stat-value" style="color:#0f172a;font-size:28px;font-weight:900;margin:2px 0 0;"><?=number_format($totalViews)?></h3>
                </div>
            </div>

            <div class="stat-card-modern" style="background:#ffffff;border:1px solid #e2e8f0;border-left:4px solid #8b5cf6;box-shadow:var(--shadow-sm);color:#0f172a;">
                <div class="stat-icon-wrapper" style="background:#ede9fe;color:#7c3aed;">
                    <?=icon('award', '', 22)?>
                </div>
                <div class="stat-info">
                    <span class="stat-label" style="color:#64748b;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:0.4px;">Kategori E-Book</span>
                    <h3 class="stat-value" style="color:#0f172a;font-size:28px;font-weight:900;margin:2px 0 0;"><?=number_format($totalCategories)?></h3>
                </div>
            </div>

            <div class="stat-card-modern" style="background:#ffffff;border:1px solid #e2e8f0;border-left:4px solid #f59e0b;box-shadow:var(--shadow-sm);color:#0f172a;">
                <div class="stat-icon-wrapper" style="background:#fef3c7;color:#d97706;">
                    <?=icon('clock', '', 22)?>
                </div>
                <div class="stat-info">
                    <span class="stat-label" style="color:#64748b;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:0.4px;">Draft / Tertunda</span>
                    <h3 class="stat-value" style="color:#0f172a;font-size:28px;font-weight:900;margin:2px 0 0;"><?=number_format($totalDraft)?></h3>
                </div>
            </div>
        </div>

        <!-- Form Tambah / Edit Buku Digital -->
        <div class="card-box" id="admin_buku_form" style="margin-bottom:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;padding-bottom:14px;margin-bottom:20px;">
                <h3 style="margin:0;font-size:16px;font-weight:800;color:var(--navy);display:flex;align-items:center;gap:8px;">
                    <?=icon($editItem ? 'edit' : 'plus', '', 18)?>
                    <?=$editItem ? 'Edit Data Buku Digital' : 'Tambah Buku Digital Gratis Baru'?>
                </h3>
                <?php if ($editItem): ?>
                    <a href="<?=u('index.php?page=admin_buku_digital')?>" class="btn btn-outline-secondary btn-sm">Batal Edit / Tambah Baru</a>
                <?php endif; ?>
            </div>

            <form method="POST" action="<?=u('index.php?page=admin_buku_digital')?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <input type="hidden" name="id" value="<?=$editItem ? $editItem['id'] : 0?>">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                    <!-- Judul Buku -->
                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-weight:700;">Judul Buku Digital <span style="color:red;">*</span></label>
                        <input type="text" name="title" class="form-control" required value="<?=e($editItem['title'] ?? '')?>" placeholder="Contoh: Dasar-Dasar Teknik Jaringan Komputer SMK Kelas X">
                    </div>

                    <!-- Penulis / Pengarang -->
                    <div>
                        <label class="form-label" style="font-weight:700;">Penulis / Pengarang <span style="color:red;">*</span></label>
                        <input type="text" name="author" class="form-control" required value="<?=e($editItem['author'] ?? '')?>" placeholder="Contoh: Pusat Kurikulum Kemendikbud / Nama Penulis">
                    </div>

                    <!-- Penerbit -->
                    <div>
                        <label class="form-label" style="font-weight:700;">Penerbit / Sumber</label>
                        <input type="text" name="publisher" class="form-control" value="<?=e($editItem['publisher'] ?? '')?>" placeholder="Contoh: Kemendikbudristek / Erlangga / Open Source">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="form-label" style="font-weight:700;">Kategori Buku <span style="color:red;">*</span></label>
                        <input type="text" name="category" class="form-control" required value="<?=e($editItem['category'] ?? 'Buku Pelajaran SMK')?>" list="cat_list" placeholder="Pilih atau ketik kategori baru">
                        <datalist id="cat_list">
                            <option value="Buku Pelajaran SMK">
                            <option value="Teknologi & Rekayasa">
                            <option value="Komputer & Informatika">
                            <option value="Seni & Desain Kreatif">
                            <option value="Bisnis & Manajemen">
                            <option value="Sains & Teknologi">
                            <option value="Sastra & Fiksi">
                            <option value="Referensi Umum">
                        </datalist>
                    </div>

                    <!-- Tahun & Jumlah Halaman -->
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label class="form-label" style="font-weight:700;">Tahun Terbit</label>
                            <input type="number" name="year" class="form-control" value="<?=e($editItem['year'] ?? date('Y'))?>" min="1980" max="<?=date('Y')+1?>">
                        </div>
                        <div>
                            <label class="form-label" style="font-weight:700;">Halaman</label>
                            <input type="number" name="pages" class="form-control" value="<?=e($editItem['pages'] ?? '')?>" placeholder="Contoh: 248">
                        </div>
                    </div>

                    <!-- Link Buku Digital (Drive/PDF/Online) -->
                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-weight:700;">Tautan / Link Akses Buku Digital (Google Drive / PDF / Web) <span style="color:red;">*</span></label>
                        <input type="url" name="file_url" class="form-control" required value="<?=e($editItem['file_url'] ?? '')?>" placeholder="https://drive.google.com/file/d/... atau https://buku.kemdikbud.go.id/...">
                        <div style="font-size:11.5px;color:#64748b;margin-top:4px;">
                            💡 Link ini yang akan diakses siswa saat mengklik tombol baca dan saat memindai kode QR.
                        </div>
                    </div>

                    <!-- Cover Image Upload -->
                    <div>
                        <label class="form-label" style="font-weight:700;">Cover Buku (Gambar JPG/PNG/WebP)</label>
                        <input type="file" name="cover_file" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <?php if (!empty($editItem['cover_image'])): ?>
                            <div style="margin-top:6px;font-size:11.5px;color:#0284c7;">
                                Cover saat ini: <b><?=e($editItem['cover_image'])?></b>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Status Publikasi -->
                    <div>
                        <label class="form-label" style="font-weight:700;">Status Publikasi</label>
                        <select name="status" class="form-control">
                            <option value="published" <?=($editItem['status']??'published')==='published'?'selected':''?>>Tayangkan (Published)</option>
                            <option value="draft" <?=($editItem['status']??'')==='draft'?'selected':''?>>Simpan Sebagai Draft (Belum Tayang)</option>
                        </select>
                    </div>

                    <!-- Deskripsi / Sinopsis -->
                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-weight:700;">Sinopsis / Keterangan Ringkas Buku</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Tuliskan gambaran ringkas isi buku, cakupan bab, atau materi yang dibahas..."><?=e($editItem['description'] ?? '')?></textarea>
                    </div>
                </div>

                <div style="margin-top:20px;display:flex;justify-content:flex-end;gap:10px;">
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                        <?=icon('save', '', 16)?> <?=$editItem ? 'Simpan Perubahan Buku' : 'Simpan Buku Digital'?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Buku Digital -->
        <div class="card-box" style="margin-bottom:28px;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                <h3 style="margin:0;font-size:16px;font-weight:800;color:var(--navy);">
                    Daftar Buku Digital Terdaftar (<?=count($allBuku)?>)
                </h3>

                <!-- Filter & Pencarian -->
                <form method="GET" action="<?=u('index.php')?>" style="display:flex;gap:8px;">
                    <input type="hidden" name="page" value="admin_buku_digital">
                    <input type="text" name="q" value="<?=e($q)?>" placeholder="Cari judul / penulis..." class="form-control" style="font-size:12.5px;padding:5px 10px;width:180px;">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Cari</button>
                    <?php if ($q !== ''): ?>
                        <a href="<?=u('index.php?page=admin_buku_digital')?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table" style="width:100%;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;text-align:left;">
                            <th style="width:50px;padding:10px;">No</th>
                            <th style="width:70px;padding:10px;">Cover</th>
                            <th style="padding:10px;">Judul Buku &amp; Kategori</th>
                            <th style="padding:10px;">Penulis &amp; Penerbit</th>
                            <th style="padding:10px;text-align:center;">QR Code</th>
                            <th style="padding:10px;text-align:center;">Dilihat</th>
                            <th style="padding:10px;text-align:center;">Status</th>
                            <th style="padding:10px;text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allBuku)): ?>
                            <tr>
                                <td colspan="8" style="text-align:center;padding:30px;color:#94a3b8;">
                                    Belum ada data buku digital yang tersimpan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allBuku as $idx => $b): 
                                $cov = get_buku_digital_cover_url($b);
                            ?>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <td style="padding:10px;color:#64748b;font-weight:600;"><?=$idx + 1?></td>
                                    <td style="padding:10px;">
                                        <?php if ($cov): ?>
                                            <img src="<?=e($cov)?>" alt="Cover" style="width:40px;height:52px;object-fit:cover;border-radius:4px;border:1px solid #cbd5e1;">
                                        <?php else: ?>
                                            <div style="width:40px;height:52px;background:#e2e8f0;color:#64748b;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;">
                                                E-BOOK
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:10px;">
                                        <div style="font-weight:700;color:var(--navy);font-size:13.5px;">
                                            <a href="<?=u('index.php?page=buku_digital_detail&id='.$b['id'])?>" target="_blank" style="color:inherit;text-decoration:none;">
                                                <?=e($b['title'])?>
                                            </a>
                                        </div>
                                        <div style="display:flex;gap:6px;align-items:center;margin-top:3px;">
                                            <span class="badge badge-primary" style="font-size:10.5px;padding:2px 7px;">
                                                <?=e($b['category'])?>
                                            </span>
                                            <?php if (!empty($b['pages'])): ?>
                                                <span style="font-size:11px;color:#64748b;"><?=$b['pages']?> Hal</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="padding:10px;">
                                        <div style="font-weight:600;color:#334155;"><?=e($b['author'])?></div>
                                        <div style="font-size:11.5px;color:#64748b;"><?=!empty($b['publisher'])?e($b['publisher']):'-'?> <?=!empty($b['year'])?'('.$b['year'].')':''?></div>
                                    </td>
                                    <td style="padding:10px;text-align:center;">
                                        <a href="<?=u('index.php?page=buku_digital_print_qr&id='.$b['id'])?>" target="_blank" title="Cetak Lembar QR" style="display:inline-block;padding:4px;border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc;">
                                            <?=generate_qr_svg($b['file_url'], 38)?>
                                        </a>
                                    </td>
                                    <td style="padding:10px;text-align:center;font-weight:700;color:#0284c7;">
                                        <?=number_format($b['views'])?>
                                    </td>
                                    <td style="padding:10px;text-align:center;">
                                        <span class="badge <?=$b['status']==='published'?'badge-green':'badge-yellow'?>" style="font-size:11px;">
                                            <?=ucfirst($b['status'])?>
                                        </span>
                                    </td>
                                    <td style="padding:10px;text-align:right;">
                                        <div style="display:inline-flex;gap:4px;">
                                            <a href="<?=e($b['file_url'])?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="padding:4px 7px;" title="Buka Link Buku">
                                                <?=icon('external-link', '', 12)?>
                                            </a>
                                            <a href="<?=u('index.php?page=buku_digital_detail&id='.$b['id'])?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="padding:4px 7px;" title="Lihat Pratinjau">
                                                <?=icon('eye', '', 12)?>
                                            </a>
                                            <a href="<?=u('index.php?page=admin_buku_digital&edit='.$b['id'].'#admin_buku_form')?>" class="btn btn-outline-primary btn-sm" style="padding:4px 7px;" title="Edit Data">
                                                <?=icon('edit', '', 12)?>
                                            </a>
                                            <a href="<?=u('index.php?page=admin_buku_digital&delete='.$b['id'])?>" class="btn btn-danger btn-sm" style="padding:4px 7px;" data-confirm="Yakin ingin menghapus buku digital ini?" title="Hapus Buku">
                                                <?=icon('trash', '', 12)?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Banner Settings Card (Editable Text for School Customization) -->
        <div class="card-box" id="banner_settings_card" style="margin-bottom:24px;border:1px solid #bfdbfe;background:#f0f9ff;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                <div style="width:36px;height:36px;border-radius:50%;background:#dbeafe;color:#1e40af;display:flex;align-items:center;justify-content:center;">
                    <?=icon('settings', '', 18)?>
                </div>
                <div>
                    <h3 style="margin:0;font-size:16px;font-weight:800;color:#1e3a8a;">Pengaturan Teks &amp; Banner Galeri Buku Digital</h3>
                    <div style="font-size:12px;color:#475569;">Sesuaikan tulisan judul, badge, dan deskripsi banner galeri buku digital publik untuk sekolah Anda.</div>
                </div>
            </div>

            <form method="POST" action="<?=u('index.php?page=admin_buku_digital')?>">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <input type="hidden" name="action" value="save_banner_settings">

                <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;">
                    <div>
                        <label class="form-label" style="font-weight:700;">Badge Label Atas</label>
                        <input type="text" name="buku_digital_banner_badge" class="form-control" value="<?=e($curBannerBadge)?>" required placeholder="Contoh: Koleksi E-Book & Pustaka Elektronik">
                    </div>

                    <div>
                        <label class="form-label" style="font-weight:700;">Judul Utama Banner</label>
                        <input type="text" name="buku_digital_banner_title" class="form-control" value="<?=e($curBannerTitle)?>" required placeholder="Contoh: Buku Digital">
                    </div>

                    <div style="grid-column:1 / -1;">
                        <label class="form-label" style="font-weight:700;">Deskripsi Subtitle Banner</label>
                        <textarea name="buku_digital_banner_subtitle" rows="2" class="form-control" required placeholder="Tuliskan deskripsi ringkas tentang layanan buku digital..."><?=e($curBannerSub)?></textarea>
                    </div>
                </div>

                <div style="margin-top:14px;display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding:8px 20px;font-weight:700;">
                        <?=icon('save', '', 14)?> Simpan Teks Banner
                    </button>
                </div>
            </form>
        </div>
        <?php
    });
}
