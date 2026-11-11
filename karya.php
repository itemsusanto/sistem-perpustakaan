<?php
/**
 * Modul Karya Siswa & Guru - Perpustakaan Digital
 * Fitur: Galeri Publik, Tautan Google Drive (Video/Dokumen), Pemutar Embed,
 * Cetak QR Code Otomatis, Manajemen Admin (CRUD), dan Pengajuan Siswa/Guru.
 */

require_once __DIR__ . '/config.php';

function get_karya_categories() {
    return [
        'Video Praktik / Tutorial',
        'Laporan PKL',
        'Aplikasi & Software',
        'Animasi & Multimedia',
        'Modul Ajar & Bahan Ajar',
        'Karya Tulis Ilmiah',
        'Desain & Multimedia',
        'Proyek Sains & Teknologi',
        'Lainnya'
    ];
}

function handle_karya_page($page) {
    global $pdo, $currentUser;

    if ($page === 'admin_karya') {
        handle_admin_karya();
        return;
    }
    if ($page === 'member_karya') {
        handle_member_karya();
        return;
    }
    if ($page === 'karya_print_qr') {
        handle_karya_print_qr();
        return;
    }
    if ($page === 'karya_detail') {
        handle_karya_detail();
        return;
    }
    // Default: Public gallery
    handle_karya_gallery();
}

/**
 * 1. HALAMAN GALERI PUBLIK: KARYA SISWA & GURU
 */
function handle_karya_gallery() {
    global $pdo;

    $type = trim($_GET['type'] ?? ''); // 'siswa', 'guru', or '' (all)
    $cat = trim($_GET['cat'] ?? '');
    $media = trim($_GET['media'] ?? '');
    $q = trim($_GET['q'] ?? '');

    $sql = "SELECT * FROM karya WHERE status = 'published'";
    $params = [];

    if ($type === 'siswa' || $type === 'guru') {
        $sql .= " AND author_type = ?";
        $params[] = $type;
    }
    if ($cat !== '') {
        $sql .= " AND category = ?";
        $params[] = $cat;
    }
    if ($media !== '') {
        $sql .= " AND media_type = ?";
        $params[] = $media;
    }
    if ($q !== '') {
        $sql .= " AND (title LIKE ? OR author_name LIKE ? OR author_class LIKE ? OR description LIKE ?)";
        $like = "%$q%";
        $params = array_merge($params, [$like, $like, $like, $like]);
    }

    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $karyaList = $stmt->fetchAll();

    // Counts for tabs
    $countTotal = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE status = 'published'")->fetchColumn();
    $countSiswa = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE status = 'published' AND author_type = 'siswa'")->fetchColumn();
    $countGuru  = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE status = 'published' AND author_type = 'guru'")->fetchColumn();

    // Categories list for filter dropdown
    $categories = get_karya_categories();

    render_public_header('karya', 'Karya Siswa & Guru');

    // Dynamic banner settings (dapat diubah melalui Admin Settings / Dashboard)
    $bannerBadge = get_setting('karya_banner_badge', 'Etalase Karya & Inovasi Sekolah');
    $bannerTitle = get_setting('karya_banner_title', 'Karya Siswa & Guru');
    $defaultSub = 'Pusat dokumentasi digital karya kreatif, video praktikum kejuruan, modul ajar, dan karya inovasi civitas akademika ' . SCHOOL . ' yang terhubung langsung dengan Google Drive dan dapat dipindai dengan QR Code.';
    $bannerSub = get_setting('karya_banner_subtitle', $defaultSub);
    $isAdmin = (!empty($_SESSION['user']) && $_SESSION['user']['role'] === 'admin');
    ?>
    <div class="container" style="padding-top:24px;padding-bottom:48px;">
        <!-- Banner Header (Dapat Diedit di Dashboard / Pengaturan Admin) -->
        <div style="background:linear-gradient(135deg, var(--navy-dark, #0c2340) 0%, #1e3a8a 50%, var(--primary, #1474dc) 100%);border-radius:14px;padding:28px 24px;color:#fff;margin-bottom:24px;box-shadow:0 8px 24px rgba(12,35,64,0.12);position:relative;overflow:hidden;">
            <div style="position:absolute;right:-20px;bottom:-20px;opacity:0.1;pointer-events:none;">
                <?=icon('video', '', 180)?>
            </div>
            <div style="max-width:760px;position:relative;z-index:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                    <?php if (!empty($bannerBadge)): ?>
                        <span style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.18);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;backdrop-filter:blur(4px);">
                            <?=icon('award', '', 14)?> <?=e($bannerBadge)?>
                        </span>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?=u('index.php?page=admin_karya#banner_settings_card')?>" style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,0.22);color:#ffffff;font-size:11.5px;font-weight:700;padding:3.5px 11px;border-radius:20px;text-decoration:none;backdrop-filter:blur(4px);border:1px solid rgba(255,255,255,0.3);transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.22)'" title="Klik untuk mengedit tulisan banner ini di Dashboard Admin">
                            <?=icon('edit', '', 12)?> Edit Tulisan Banner Ini (Admin)
                        </a>
                    <?php endif; ?>
                </div>
                <h1 style="margin:0 0 8px;font-size:26px;font-weight:800;color:#ffffff;line-height:1.25;"><?=e($bannerTitle)?></h1>
                <p style="margin:0;font-size:14px;opacity:0.92;line-height:1.5;">
                    <?=e($bannerSub)?>
                </p>
            </div>
        </div>

        <!-- Filter Bar & Search Form -->
        <div class="card-box" style="margin-bottom:24px;padding:16px 20px;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;">
                <!-- Type Tabs -->
                <div class="nav-tabs-pill" style="margin:0;">
                    <a href="<?=u('index.php?page=karya' . ($cat ? '&cat='.urlencode($cat) : '') . ($q ? '&q='.urlencode($q) : ''))?>" class="<?=$type===''?'active':''?>">
                        Semua Karya <span style="opacity:0.85;font-size:11px;margin-left:4px;">(<?=$countTotal?>)</span>
                    </a>
                    <a href="<?=u('index.php?page=karya&type=siswa' . ($cat ? '&cat='.urlencode($cat) : '') . ($q ? '&q='.urlencode($q) : ''))?>" class="<?=$type==='siswa'?'active':''?>">
                        🎓 Karya Siswa <span style="opacity:0.85;font-size:11px;margin-left:4px;">(<?=$countSiswa?>)</span>
                    </a>
                    <a href="<?=u('index.php?page=karya&type=guru' . ($cat ? '&cat='.urlencode($cat) : '') . ($q ? '&q='.urlencode($q) : ''))?>" class="<?=$type==='guru'?'active':''?>">
                        👨‍🏫 Karya Guru <span style="opacity:0.85;font-size:11px;margin-left:4px;">(<?=$countGuru?>)</span>
                    </a>
                </div>

                <!-- Search & Filters -->
                <form method="get" action="<?=u('index.php')?>" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <input type="hidden" name="page" value="karya">
                    <?php if ($type): ?><input type="hidden" name="type" value="<?=e($type)?>"><?php endif; ?>

                    <select name="cat" class="form-control" style="width:auto;font-size:13px;padding:7px 10px;" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?=e($c)?>" <?=$cat===$c?'selected':''?>><?=e($c)?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="media" class="form-control" style="width:auto;font-size:13px;padding:7px 10px;" onchange="this.form.submit()">
                        <option value="">-- Semua Media --</option>
                        <option value="video" <?=$media==='video'?'selected':''?>>🎬 Video Drive / Tutorial</option>
                        <option value="dokumen" <?=$media==='dokumen'?'selected':''?>>📄 Dokumen / Makalah Drive</option>
                        <option value="aplikasi" <?=$media==='aplikasi'?'selected':''?>>💻 Aplikasi / Software</option>
                    </select>

                    <input type="text" name="q" value="<?=e($q)?>" placeholder="Cari karya, siswa, guru..." class="form-control" style="width:200px;font-size:13px;padding:7px 10px;">
                    <button type="submit" class="btn btn-primary" style="padding:7px 14px;font-size:13px;">
                        <?=icon('search', '', 14)?> Cari
                    </button>
                    <?php if ($q !== '' || $cat !== '' || $media !== '' || $type !== ''): ?>
                        <a href="<?=u('index.php?page=karya')?>" class="btn btn-outline-secondary btn-sm" title="Reset Filter" style="padding:7px 10px;">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <?php if (empty($karyaList)): ?>
            <div class="card-box" style="text-align:center;padding:50px 20px;">
                <div style="width:70px;height:70px;border-radius:50%;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px;color:#64748b;">
                    <?=icon('video', '', 36)?>
                </div>
                <h3 style="margin:0 0 8px;color:var(--navy);font-size:18px;font-weight:800;">Belum Ada Karya Ditemukan</h3>
                <p style="color:var(--text-muted);font-size:13.5px;max-width:480px;margin:0 auto 16px;">
                    Tidak ditemukan karya yang sesuai dengan filter pencarian Anda. Silakan coba kata kunci lain atau reset filter.
                </p>
                <a href="<?=u('index.php?page=karya')?>" class="btn btn-primary btn-sm">Lihat Semua Karya</a>
            </div>
        <?php else: ?>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:22px;">
                <?php foreach ($karyaList as $k): 
                    $coverUrl = get_karya_cover_url($k);
                    $isSiswa = ($k['author_type'] === 'siswa');
                    $badgeBg = $isSiswa ? '#10b981' : '#8b5cf6';
                    $badgeText = $isSiswa ? '🎓 Karya Siswa' : '👨‍🏫 Karya Guru';
                ?>
                    <div class="card-box" style="padding:0;overflow:hidden;display:flex;flex-direction:column;transition:transform 0.2s, box-shadow 0.2s;border:1px solid #e2e8f0;border-radius:12px;background:#ffffff;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';">
                        <!-- Thumbnail Header -->
                        <div style="position:relative;height:165px;background:linear-gradient(135deg, #1e293b 0%, #334155 100%);overflow:hidden;">
                            <?php if ($coverUrl): ?>
                                <img src="<?=e($coverUrl)?>" alt="<?=e($k['title'])?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, <?=$isSiswa?'#065f46, #047857':'#4c1d95, #6d28d9'?>);color:#fff;">
                                    <div style="text-align:center;padding:16px;">
                                        <?=icon($k['media_type']==='video'?'video':($k['media_type']==='aplikasi'?'tech':'file-text'), '', 44)?>
                                        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-top:6px;opacity:0.85;">
                                            <?=e($k['category'])?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Badges on top -->
                            <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;flex-wrap:wrap;">
                                <span style="background:<?=$badgeBg?>;color:#ffffff;font-size:11px;font-weight:700;padding:3.5px 9px;border-radius:20px;box-shadow:0 2px 6px rgba(0,0,0,0.25);">
                                    <?=$badgeText?>
                                </span>
                                <?php if ($k['media_type'] === 'video'): ?>
                                    <span style="background:rgba(220,38,38,0.92);color:#ffffff;font-size:11px;font-weight:700;padding:3.5px 8px;border-radius:20px;display:inline-flex;align-items:center;gap:4px;box-shadow:0 2px 6px rgba(0,0,0,0.25);">
                                        ▶ Video
                                    </span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($k['year'])): ?>
                                <span style="position:absolute;bottom:10px;right:10px;background:rgba(15,23,42,0.82);color:#ffffff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;backdrop-filter:blur(4px);">
                                    <?=e($k['year'])?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div style="padding:16px 18px;display:flex;flex-direction:column;flex:1;">
                            <div style="font-size:11.5px;color:var(--text-muted);font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.5px;">
                                <?=e($k['category'])?>
                            </div>
                            <h3 style="margin:0 0 10px;font-size:15.5px;font-weight:800;line-height:1.35;color:var(--navy);">
                                <a href="<?=u('index.php?page=karya_detail&id='.$k['id'])?>" style="color:inherit;text-decoration:none;">
                                    <?=e($k['title'])?>
                                </a>
                            </h3>

                            <!-- Author Details -->
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;font-size:12.5px;color:#334155;">
                                <div style="width:24px;height:24px;border-radius:50%;background:<?=$isSiswa?'#d1fae5':'#ede9fe'?>;color:<?=$isSiswa?'#065f46':'#5b21b6'?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <?=icon('user', '', 14)?>
                                </div>
                                <div style="min-width:0;line-height:1.25;">
                                    <div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?=e($k['author_name'])?></div>
                                    <?php if (!empty($k['author_class'])): ?>
                                        <div style="font-size:11px;color:var(--text-muted);"><?=e($k['author_class'])?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p style="margin:0 0 14px;font-size:12.5px;color:#64748b;line-height:1.45;flex:1;">
                                <?=e(mb_strimwidth($k['description'] ?? '', 0, 110, '...'))?>
                            </p>

                            <!-- Actions Row -->
                            <div style="border-top:1px solid #f1f5f9;padding-top:12px;display:flex;align-items:center;justify-content:space-between;gap:6px;">
                                <div style="display:flex;gap:6px;">
                                    <a href="<?=u('index.php?page=karya_detail&id='.$k['id'])?>" class="btn btn-primary btn-sm" style="font-size:11.5px;padding:6px 11px;display:inline-flex;align-items:center;gap:4px;">
                                        <?=icon('eye', '', 13)?> Detail
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openKaryaQrModal(<?=$k['id']?>, this)" data-title="<?=htmlspecialchars($k['title'], ENT_QUOTES, 'UTF-8')?>" data-author="<?=htmlspecialchars($k['author_name'], ENT_QUOTES, 'UTF-8')?>" data-role="<?=htmlspecialchars($k['author_class'] ?? '', ENT_QUOTES, 'UTF-8')?>" data-type="<?=htmlspecialchars($k['author_type'], ENT_QUOTES, 'UTF-8')?>" data-url="<?=htmlspecialchars($k['drive_url'], ENT_QUOTES, 'UTF-8')?>" style="font-size:11.5px;padding:6px 10px;display:inline-flex;align-items:center;gap:4px;" title="Lihat dan Cetak QR Code">
                                        <?=icon('qr', '', 14)?> QR
                                    </button>
                                    <div id="qrSvgData_<?=$k['id']?>" style="display:none;"><?=generate_qr_svg($k['drive_url'], 180)?></div>
                                </div>
                                <a href="<?=e($k['drive_url'])?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm" style="font-size:11.5px;padding:6px 10px;display:inline-flex;align-items:center;gap:4px;" title="Buka file/video di Google Drive">
                                    <?=icon('external-link', '', 13)?> Drive
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Interactive QR Code Quick Modal -->
    <div id="karyaQrModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.65);z-index:99999;align-items:center;justify-content:center;padding:16px;backdrop-filter:blur(3px);">
        <div style="background:#ffffff;border-radius:14px;max-width:440px;width:100%;padding:24px;box-shadow:0 20px 40px rgba(0,0,0,0.25);position:relative;text-align:center;animation:fadeInScale 0.2s ease;">
            <button type="button" onclick="closeKaryaQrModal()" style="position:absolute;top:14px;right:14px;background:#f1f5f9;border:none;border-radius:50%;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;font-size:16px;">✕</button>
            
            <div style="font-size:11px;font-weight:700;color:var(--primary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">
                KARTU AKSES SCAN QR CODE
            </div>
            <h3 id="modalKaryaTitle" style="margin:0 0 6px;font-size:16px;font-weight:800;color:var(--navy);line-height:1.3;"></h3>
            <div id="modalKaryaAuthor" style="font-size:12.5px;color:#64748b;margin-bottom:16px;"></div>

            <!-- SVG QR Container -->
            <div style="background:#f8fafc;border:2px dashed #cbd5e1;border-radius:12px;padding:18px;display:inline-block;margin-bottom:14px;">
                <div id="modalKaryaQrSvg" style="line-height:0;"></div>
            </div>

            <p style="margin:0 0 16px;font-size:12px;color:#64748b;line-height:1.45;">
                📱 Arahkan kamera smartphone ke QR Code di atas untuk membuka video / file dokumen karya ini di Google Drive.
            </p>

            <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                <a id="modalPrintBtn" href="#" target="_blank" class="btn btn-primary btn-sm" style="padding:8px 14px;font-size:12px;display:inline-flex;align-items:center;gap:5px;">
                    <?=icon('print', '', 14)?> Cetak Lembar QR
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyKaryaDriveUrl()" style="padding:8px 14px;font-size:12px;display:inline-flex;align-items:center;gap:5px;">
                    <?=icon('copy', '', 14)?> Salin Link
                </button>
                <a id="modalDriveBtn" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm" style="padding:8px 14px;font-size:12px;display:inline-flex;align-items:center;gap:5px;">
                    <?=icon('external-link', '', 14)?> Buka Drive ↗
                </a>
            </div>
        </div>
    </div>

    <script>
    let currentModalDriveUrl = '';
    function openKaryaQrModal(id, btn) {
        const title = btn.getAttribute('data-title') || '';
        const author = btn.getAttribute('data-author') || '';
        const role = btn.getAttribute('data-role') || '';
        const type = btn.getAttribute('data-type') || '';
        const url = btn.getAttribute('data-url') || '';

        currentModalDriveUrl = url;
        document.getElementById('modalKaryaTitle').textContent = title;
        document.getElementById('modalKaryaAuthor').textContent = (type === 'siswa' ? 'Siswa: ' : 'Guru: ') + author + (role ? ' (' + role + ')' : '');
        
        const qrSource = document.getElementById('qrSvgData_' + id);
        document.getElementById('modalKaryaQrSvg').innerHTML = qrSource ? qrSource.innerHTML : '';
        document.getElementById('modalPrintBtn').href = '<?=u('index.php?page=karya_print_qr&id=')?>' + id;
        document.getElementById('modalDriveBtn').href = url;

        const modal = document.getElementById('karyaQrModal');
        modal.style.display = 'flex';
    }
    function showKaryaQrModal(data) {
        currentModalDriveUrl = data.url;
        document.getElementById('modalKaryaTitle').textContent = data.title;
        document.getElementById('modalKaryaAuthor').textContent = (data.type === 'siswa' ? 'Siswa: ' : 'Guru: ') + data.author + (data.role ? ' (' + data.role + ')' : '');
        document.getElementById('modalKaryaQrSvg').innerHTML = data.qr;
        document.getElementById('modalPrintBtn').href = '<?=u('index.php?page=karya_print_qr&id=')?>' + data.id;
        document.getElementById('modalDriveBtn').href = data.url;
        const modal = document.getElementById('karyaQrModal');
        modal.style.display = 'flex';
    }
    function closeKaryaQrModal() {
        document.getElementById('karyaQrModal').style.display = 'none';
    }
    document.getElementById('karyaQrModal').addEventListener('click', function(e) {
        if (e.target === this) closeKaryaQrModal();
    });
    function copyKaryaDriveUrl() {
        if (!currentModalDriveUrl) return;
        navigator.clipboard.writeText(currentModalDriveUrl).then(() => {
            alert('Tautan Google Drive berhasil disalin ke clipboard!');
        }).catch(() => {
            prompt('Salin link Google Drive:', currentModalDriveUrl);
        });
    }
    </script>
    <?php
    render_public_footer();
    exit;
}

/**
 * 2. HALAMAN DETAIL KARYA: PLAYER PREVIEW, LINK GOOGLE DRIVE, DAN QR CODE
 */
function handle_karya_detail() {
    global $pdo;

    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM karya WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $karya = $stmt->fetch();

    if (!$karya) {
        flash('Data karya tidak ditemukan.', 'error');
        header('Location: ' . u('index.php?page=karya'));
        exit;
    }

    // Increment views
    $upd = $pdo->prepare("UPDATE karya SET views = views + 1 WHERE id = ?");
    $upd->execute([$id]);

    $coverUrl = get_karya_cover_url($karya);
    $embedUrl = get_drive_embed_url($karya['drive_url']);
    $isSiswa = ($karya['author_type'] === 'siswa');
    $badgeBg = $isSiswa ? '#10b981' : '#8b5cf6';
    $badgeText = $isSiswa ? '🎓 Karya Siswa' : '👨‍🏫 Karya Guru';

    // Related works
    $relStmt = $pdo->prepare("SELECT * FROM karya WHERE id != ? AND (author_type = ? OR category = ?) AND status = 'published' ORDER BY id DESC LIMIT 3");
    $relStmt->execute([$id, $karya['author_type'], $karya['category']]);
    $related = $relStmt->fetchAll();

    render_public_header('karya', $karya['title']);
    ?>
    <div class="container" style="padding-top:24px;padding-bottom:48px;">
        <!-- Breadcrumb Navigation -->
        <nav style="display:flex;align-items:center;gap:8px;font-size:12.5px;color:var(--text-muted);margin-bottom:20px;">
            <a href="<?=u()?>" style="color:inherit;text-decoration:none;">Beranda</a>
            <span>/</span>
            <a href="<?=u('index.php?page=karya')?>" style="color:inherit;text-decoration:none;">Karya Siswa &amp; Guru</a>
            <span>/</span>
            <span style="color:var(--navy);font-weight:700;"><?=e(mb_strimwidth($karya['title'], 0, 40, '...'))?></span>
        </nav>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:28px;align-items:start;">
            <!-- Main Content Area -->
            <div>
                <div class="card-box" style="padding:28px;margin-bottom:24px;">
                    <!-- Category & Badges -->
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                        <span style="background:<?=$badgeBg?>;color:#ffffff;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;">
                            <?=$badgeText?>
                        </span>
                        <span class="badge badge-blue"><?=e($karya['category'])?></span>
                        <?php if ($karya['media_type'] === 'video'): ?>
                            <span class="badge badge-red">🎬 Video Google Drive</span>
                        <?php else: ?>
                            <span class="badge badge-orange">📄 Dokumen / File Drive</span>
                        <?php endif; ?>
                        <?php if (!empty($karya['year'])): ?>
                            <span style="font-size:12px;color:var(--text-muted);font-weight:600;">• Tahun <?=e($karya['year'])?></span>
                        <?php endif; ?>
                        <span style="font-size:12px;color:var(--text-muted);">• <?=icon('eye', '', 13)?> <?=$karya['views'] + 1?> views</span>
                    </div>

                    <h1 style="margin:0 0 16px;font-size:24px;font-weight:800;color:var(--navy);line-height:1.3;">
                        <?=e($karya['title'])?>
                    </h1>

                    <!-- Author Details Box -->
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:14px;margin-bottom:24px;">
                        <div style="width:44px;height:44px;border-radius:50%;background:<?=$isSiswa?'#d1fae5':'#ede9fe'?>;color:<?=$isSiswa?'#065f46':'#5b21b6'?>;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
                            <?=icon('user', '', 22)?>
                        </div>
                        <div style="line-height:1.35;">
                            <div style="font-size:11.5px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">
                                Pencipta / Pembuat Karya:
                            </div>
                            <div style="font-size:15px;font-weight:800;color:var(--navy);">
                                <?=e($karya['author_name'])?>
                            </div>
                            <?php if (!empty($karya['author_class'])): ?>
                                <div style="font-size:12.5px;color:#64748b;">
                                    <?=e($karya['author_class'])?> · <?=SCHOOL?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Video / Document Player Embed Section -->
                    <?php if ($embedUrl): ?>
                        <div style="margin-bottom:24px;">
                            <div style="font-size:13px;font-weight:700;color:var(--navy);margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                                <?=icon('video', '', 16)?> Pemutar Pratinjau Video / Dokumen:
                            </div>
                            <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;background:#0f172a;box-shadow:0 4px 14px rgba(0,0,0,0.15);">
                                <iframe src="<?=e($embedUrl)?>" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;" allow="autoplay; fullscreen" allowfullscreen></iframe>
                            </div>
                            <small style="display:block;margin-top:6px;font-size:11.5px;color:var(--text-muted);">
                                💡 Video / dokumen diputar langsung dari server Google Drive / YouTube sekolah.
                            </small>
                        </div>
                    <?php endif; ?>

                    <!-- Google Drive Callout Card -->
                    <div style="background:linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);border:1.5px solid #bfdbfe;border-radius:12px;padding:18px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
                        <div>
                            <div style="font-weight:800;color:#1e40af;font-size:14.5px;display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                <?=icon('external-link', '', 16)?> Tautan Berkas Asli di Google Drive
                            </div>
                            <div style="font-size:12.5px;color:#1e3a8a;line-height:1.4;">
                                Akses file video kualitas asli (HD/FHD), materi slide presentasi, dokumen laporan, dan folder proyek.
                            </div>
                            <div style="font-family:monospace;font-size:11px;background:#ffffff;padding:4px 8px;border-radius:4px;border:1px solid #bfdbfe;margin-top:8px;color:#3b82f6;max-width:440px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                <?=e($karya['drive_url'])?>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <a href="<?=e($karya['drive_url'])?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="padding:10px 18px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                                <?=icon('external-link', '', 16)?> Buka di Google Drive ↗
                            </a>
                            <button type="button" class="btn btn-outline-primary" onclick="copyDetailDriveUrl()" style="padding:10px 14px;" title="Salin Link">
                                <?=icon('copy', '', 16)?>
                            </button>
                        </div>
                    </div>

                    <!-- Description Section -->
                    <div>
                        <h3 style="margin:0 0 10px;font-size:16px;font-weight:800;color:var(--navy);border-bottom:2px solid #f1f5f9;padding-bottom:8px;">
                            Deskripsi &amp; Ringkasan Karya
                        </h3>
                        <div style="font-size:14px;color:#334155;line-height:1.7;white-space:pre-line;">
                            <?=nl2br(e($karya['description'] ?: 'Belum ada deskripsi rincian karya ini.'))?>
                        </div>
                    </div>
                </div>

                <!-- Related Works -->
                <?php if (!empty($related)): ?>
                    <div style="margin-top:28px;">
                        <h3 style="margin:0 0 16px;font-size:16.5px;font-weight:800;color:var(--navy);">
                            Karya Lainnya yang Terkait
                        </h3>
                        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(260px, 1fr));gap:16px;">
                            <?php foreach ($related as $r): ?>
                                <div class="card-box" style="padding:16px;">
                                    <span class="badge <?=$r['author_type']==='siswa'?'badge-green':'badge-purple'?>" style="margin-bottom:6px;font-size:10px;">
                                        <?=$r['author_type']==='siswa'?'Karya Siswa':'Karya Guru'?>
                                    </span>
                                    <h4 style="margin:0 0 6px;font-size:13.5px;font-weight:800;line-height:1.35;">
                                        <a href="<?=u('index.php?page=karya_detail&id='.$r['id'])?>" style="color:var(--navy);text-decoration:none;">
                                            <?=e($r['title'])?>
                                        </a>
                                    </h4>
                                    <div style="font-size:11.5px;color:var(--text-muted);margin-bottom:10px;">
                                        Oleh: <?=e($r['author_name'])?>
                                    </div>
                                    <a href="<?=u('index.php?page=karya_detail&id='.$r['id'])?>" style="font-size:11.5px;color:var(--primary);font-weight:700;text-decoration:none;">
                                        Lihat Karya →
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar: Interactive QR Code Box & Actions -->
            <div>
                <!-- QR Code Box Card -->
                <div class="card-box" style="text-align:center;padding:24px 20px;position:sticky;top:20px;box-shadow:0 8px 24px rgba(0,0,0,0.06);border:1.5px solid #e2e8f0;">
                    <span style="display:inline-flex;align-items:center;gap:5px;background:#eef2ff;color:#4338ca;font-size:11.5px;font-weight:700;padding:4px 10px;border-radius:20px;margin-bottom:12px;">
                        <?=icon('qr', '', 13)?> AKSES MANDIRI VIA SMARTPHONE
                    </span>
                    <h3 style="margin:0 0 6px;font-size:16px;font-weight:800;color:var(--navy);">
                        Pindai (Scan) QR Code
                    </h3>
                    <p style="margin:0 0 16px;font-size:12px;color:var(--text-muted);line-height:1.45;">
                        Arahkan kamera smartphone ke kode QR berikut untuk membuka video atau berkas di Google Drive:
                    </p>

                    <!-- Big Crisp QR Code SVG -->
                    <div style="background:#ffffff;border:2px solid #e2e8f0;border-radius:12px;padding:16px;display:inline-block;box-shadow:0 4px 12px rgba(0,0,0,0.04);margin-bottom:16px;">
                        <?=generate_qr_svg($karya['drive_url'], 190)?>
                    </div>

                    <!-- Print Button -->
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <a href="<?=u('index.php?page=karya_print_qr&id='.$karya['id'])?>" target="_blank" class="btn btn-primary" style="padding:10px 16px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;">
                            <?=icon('print', '', 16)?> Cetak Lembar QR Code
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="copyDetailDriveUrl()" style="padding:9px 16px;font-size:12.5px;display:flex;align-items:center;justify-content:center;gap:6px;">
                            <?=icon('copy', '', 14)?> Salin Link Google Drive
                        </button>
                    </div>

                    <!-- Scanner Instruction Box -->
                    <div style="margin-top:20px;padding-top:16px;border-top:1px dashed #cbd5e1;text-align:left;font-size:12px;color:#64748b;line-height:1.5;">
                        <div style="font-weight:700;color:var(--navy);margin-bottom:4px;">📌 Cara Cetak &amp; Tempel:</div>
                        Klik tombol <b>Cetak Lembar QR Code</b> untuk mencetak label QR resmi yang dapat ditempel di mading sekolah, stan pameran, perpustakaan, atau laboratorium kejuruan.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyDetailDriveUrl() {
        const url = <?=json_encode($karya['drive_url'])?>;
        navigator.clipboard.writeText(url).then(() => {
            alert('Tautan Google Drive berhasil disalin ke clipboard:\n' + url);
        }).catch(() => {
            prompt('Salin link Google Drive:', url);
        });
    }
    </script>
    <?php
    render_public_footer();
    exit;
}

/**
 * 3. HALAMAN CETAK LEMBAR / KARTU QR CODE RESMI (HIGH-RESOLUTION PRINT SHEET)
 */
function handle_karya_print_qr() {
    global $pdo;

    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM karya WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $karya = $stmt->fetch();

    if (!$karya) {
        die('Data karya tidak ditemukan.');
    }

    $isSiswa = ($karya['author_type'] === 'siswa');
    $badgeText = $isSiswa ? 'KARYA SISWA' : 'KARYA GURU';
    $navLogo = get_setting('navbar_logo', 'assets/img/logo.jpg');
    $schoolTitle = get_setting('navbar_title', SCHOOL);
    $schoolSub = get_setting('navbar_subtitle', SCHOOL_SUB);
    $headTitle = get_setting('report_head_title', 'Kepala Perpustakaan');
    $headName = get_setting('report_head_name', 'Drs. Suparman, M.Pd');
    $headNip = get_setting('report_head_nip', 'NIP. 19750815 200501 1 004');
    $staffTitle = get_setting('report_staff_title', 'Petugas Perpustakaan');
    $staffName = get_setting('report_staff_name', 'Heri Susanto, S.Kom');
    $staffNip = get_setting('report_staff_nip', 'NIP. 19880322 201503 1 002');
    $city = get_setting('report_city', 'Banda Aceh');
    ?>
    <!doctype html>
    <html lang="id">
    <head>
        <meta charset="utf-8">
        <title>Cetak QR Code Karya - <?=e($karya['title'])?></title>
        <style>
            * { box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
                background: #f1f5f9;
                color: #0f172a;
                margin: 0;
                padding: 24px;
            }
            .no-print-bar {
                max-width: 680px;
                margin: 0 auto 20px;
                background: #ffffff;
                padding: 12px 18px;
                border-radius: 8px;
                border: 1px solid #cbd5e1;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            }
            .print-card-wrapper {
                max-width: 680px;
                margin: 0 auto;
                background: #ffffff;
                border: 2px solid #0f172a;
                border-radius: 12px;
                padding: 28px 32px;
                box-shadow: 0 4px 16px rgba(0,0,0,0.08);
                position: relative;
            }
            .header-kop {
                display: flex;
                align-items: center;
                gap: 16px;
                border-bottom: 3px double #0f172a;
                padding-bottom: 14px;
                margin-bottom: 20px;
            }
            .kop-logo {
                width: 65px;
                height: 65px;
                object-fit: contain;
            }
            .kop-text h2 {
                margin: 0 0 2px;
                font-size: 17px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .kop-text h3 {
                margin: 0 0 4px;
                font-size: 14px;
                font-weight: 700;
                color: #1e3a8a;
            }
            .kop-text p {
                margin: 0;
                font-size: 11.5px;
                color: #475569;
            }
            .badge-type {
                display: inline-block;
                background: #0f172a;
                color: #ffffff;
                font-size: 12px;
                font-weight: 800;
                padding: 4px 14px;
                border-radius: 20px;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 12px;
            }
            .qr-center-box {
                text-align: center;
                margin: 18px 0;
                padding: 16px;
                background: #f8fafc;
                border: 1.5px dashed #64748b;
                border-radius: 10px;
            }
            .qr-svg-wrap {
                display: inline-block;
                padding: 12px;
                background: #ffffff;
                border: 2px solid #0f172a;
                border-radius: 8px;
                line-height: 0;
            }
            .instruction-box {
                background: #f1f5f9;
                border-left: 4px solid #1474dc;
                padding: 10px 14px;
                border-radius: 6px;
                font-size: 12px;
                line-height: 1.45;
                color: #1e293b;
                margin-bottom: 24px;
            }
            .sign-row {
                display: flex;
                justify-content: space-between;
                text-align: center;
                margin-top: 28px;
                font-size: 12px;
            }
            .sign-box {
                width: 220px;
            }
            .btn {
                background: #1474dc;
                color: #ffffff;
                border: none;
                padding: 8px 16px;
                border-radius: 6px;
                font-weight: 700;
                font-size: 13px;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-outline {
                background: #f1f5f9;
                color: #334155;
                border: 1px solid #cbd5e1;
            }

            @media print {
                @page {
                    size: A4 portrait;
                    margin: 12mm 15mm;
                }
                body {
                    background: #ffffff !important;
                    padding: 0 !important;
                }
                .no-print, .no-print-bar {
                    display: none !important;
                }
                .print-card-wrapper {
                    max-width: 100% !important;
                    border: 2px solid #000000 !important;
                    box-shadow: none !important;
                    padding: 20px 24px !important;
                    page-break-inside: avoid !important;
                }
            }
        </style>
    </head>
    <body>
        <!-- Top Toolbar for user (hidden during print) -->
        <div class="no-print-bar no-print">
            <div style="font-size:13px;font-weight:700;color:#334155;">
                Pratinjau Lembar Cetak Label QR Code Karya
            </div>
            <div style="display:flex;gap:8px;">
                <button type="button" class="btn" onclick="window.print()">
                    🖨️ Cetak Sekarang
                </button>
                <a href="<?=u('index.php?page=karya_detail&id='.$karya['id'])?>" class="btn btn-outline">
                    ← Kembali ke Detail
                </a>
            </div>
        </div>

        <!-- Official Printable Card Layout -->
        <div class="print-card-wrapper">
            <!-- Kop Sekolah Resmi -->
            <div class="header-kop">
                <img src="<?=u($navLogo)?>" class="kop-logo" alt="Logo">
                <div class="kop-text">
                    <h2>UNIT PERPUSTAKAAN DIGITAL</h2>
                    <h3><?=e($schoolTitle)?> <?=e($schoolSub)?></h3>
                    <p>Sistem Akses Mandiri Katalog &amp; Karya Digital Siswa &amp; Guru</p>
                </div>
            </div>

            <!-- Content Area -->
            <div style="text-align:center;">
                <span class="badge-type"><?=$badgeText?></span>
                <h1 style="margin:0 0 8px;font-size:19px;font-weight:800;color:#0f172a;line-height:1.35;">
                    <?=e($karya['title'])?>
                </h1>
                <div style="font-size:13.5px;color:#334155;margin-bottom:12px;font-weight:600;">
                    Pencipta: <b><?=e($karya['author_name'])?></b> <?=!empty($karya['author_class']) ? '· (' . e($karya['author_class']) . ')' : ''?>
                </div>
                <div style="font-size:12px;color:#64748b;margin-bottom:16px;">
                    Bidang / Kategori: <b><?=e($karya['category'])?></b> · Tahun: <b><?=e($karya['year'] ?: date('Y'))?></b>
                </div>
            </div>

            <!-- Big High-Resolution SVG QR Code -->
            <div class="qr-center-box">
                <div class="qr-svg-wrap">
                    <?=generate_qr_svg($karya['drive_url'], 210)?>
                </div>
                <div style="margin-top:10px;font-family:monospace;font-size:11px;color:#475569;word-break:break-all;">
                    <?=e($karya['drive_url'])?>
                </div>
            </div>

            <!-- Petunjuk Scan -->
            <div class="instruction-box">
                <b>📱 PETUNJUK AKSES MANDIRI VIA SMARTPHONE:</b><br>
                1. Buka aplikasi kamera atau pemindai QR Code di smartphone Anda.<br>
                2. Arahkan lensa kamera ke gambar QR Code di atas.<br>
                3. Klik notifikasi tautan yang muncul untuk langsung menonton video atau membuka file di Google Drive.
            </div>

            <!-- Signature Section -->
            <div class="sign-row">
                <div class="sign-box">
                    Mengetahui,<br>
                    <b><?=e($headTitle)?></b>
                    <div style="height:60px;"></div>
                    <div style="font-weight:700;text-decoration:underline;"><?=e($headName)?></div>
                    <div style="font-size:10.5px;color:#64748b;"><?=e($headNip)?></div>
                </div>
                <div class="sign-box">
                    <?=e($city)?>, <?=date('d F Y')?><br>
                    <b><?=e($staffTitle)?></b>
                    <div style="height:60px;"></div>
                    <div style="font-weight:700;text-decoration:underline;"><?=e($staffName)?></div>
                    <div style="font-size:10.5px;color:#64748b;"><?=e($staffNip)?></div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * 4. HALAMAN ADMIN: KELOLA KARYA SISWA & GURU (CRUD, FORM, FILTER, CETAK QR)
 */
function handle_admin_karya() {
    global $pdo;
    auth('admin');

    // Handle Approve (Quick publish)
    if (isset($_GET['approve'])) {
        $appId = (int)$_GET['approve'];
        $stmt = $pdo->prepare("UPDATE karya SET status = 'published', admin_note = NULL WHERE id = ?");
        $stmt->execute([$appId]);
        flash('Karya berhasil disetujui dan langsung diterbitkan ke Galeri Publik!');
        header('Location: ' . u('index.php?page=admin_karya'));
        exit;
    }

    // Handle Reject with Note
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reject_karya') {
        checkcsrf();
        $rejId = (int)($_POST['karya_id'] ?? 0);
        $note = trim($_POST['admin_note'] ?? '');
        $stmt = $pdo->prepare("UPDATE karya SET status = 'rejected', admin_note = ? WHERE id = ?");
        $stmt->execute([$note, $rejId]);
        flash('Karya telah ditolak / diminta perbaikan dengan catatan resmi untuk pembuat karya.');
        header('Location: ' . u('index.php?page=admin_karya'));
        exit;
    }

    // Handle Delete
    if (isset($_GET['delete'])) {
        $delId = (int)$_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM karya WHERE id = ?");
        $stmt->execute([$delId]);
        flash('Data karya berhasil dihapus.');
        header('Location: ' . u('index.php?page=admin_karya'));
        exit;
    }

    // Handle Save Banner Text Settings
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_banner_settings') {
        checkcsrf();
        $badge = trim($_POST['karya_banner_badge'] ?? '');
        $title = trim($_POST['karya_banner_title'] ?? '');
        $sub = trim($_POST['karya_banner_subtitle'] ?? '');

        set_setting('karya_banner_badge', $badge);
        if ($title !== '') set_setting('karya_banner_title', $title);
        set_setting('karya_banner_subtitle', $sub);

        flash('Pengaturan teks & banner Galeri Karya berhasil disimpan dan langsung diperbarui di halaman publik!');
        header('Location: ' . u('index.php?page=admin_karya#banner_settings_card'));
        exit;
    }

    // Handle Create & Update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkcsrf();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $authorType = in_array($_POST['author_type'] ?? '', ['siswa', 'guru']) ? $_POST['author_type'] : 'siswa';
        $authorName = trim($_POST['author_name'] ?? '');
        $authorClass = trim($_POST['author_class'] ?? '');
        $category = trim($_POST['category'] ?? 'Video Praktik / Tutorial');
        $mediaType = in_array($_POST['media_type'] ?? '', ['video', 'dokumen', 'aplikasi', 'lainnya']) ? $_POST['media_type'] : 'video';
        $driveUrl = trim($_POST['drive_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));
        $status = in_array($_POST['status'] ?? '', ['published', 'pending', 'rejected', 'draft']) ? $_POST['status'] : 'published';
        $adminNote = trim($_POST['admin_note'] ?? '');

        if ($title === '' || $authorName === '' || $driveUrl === '') {
            flash('Judul karya, nama pencipta, dan link Google Drive wajib diisi.', 'error');
        } else {
            // Handle Thumbnail Upload
            $thumbFileName = null;
            if (!empty($_FILES['thumbnail_file']['name']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['thumbnail_file']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/uploads/karya/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $newFile = 'karya_' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['thumbnail_file']['tmp_name'], $uploadDir . $newFile)) {
                        $thumbFileName = 'uploads/karya/' . $newFile;
                    }
                }
            }

            if ($id > 0) {
                if ($thumbFileName) {
                    $stmt = $pdo->prepare("UPDATE karya SET title = ?, author_type = ?, author_name = ?, author_class = ?, category = ?, media_type = ?, drive_url = ?, thumbnail = ?, description = ?, year = ?, status = ?, admin_note = ? WHERE id = ?");
                    $stmt->execute([$title, $authorType, $authorName, $authorClass, $category, $mediaType, $driveUrl, $thumbFileName, $description, $year, $status, $adminNote, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE karya SET title = ?, author_type = ?, author_name = ?, author_class = ?, category = ?, media_type = ?, drive_url = ?, description = ?, year = ?, status = ?, admin_note = ? WHERE id = ?");
                    $stmt->execute([$title, $authorType, $authorName, $authorClass, $category, $mediaType, $driveUrl, $description, $year, $status, $adminNote, $id]);
                }
                flash('Karya berhasil diperbarui.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO karya (title, author_type, author_name, author_class, category, media_type, drive_url, thumbnail, description, year, status, admin_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $authorType, $authorName, $authorClass, $category, $mediaType, $driveUrl, $thumbFileName, $description, $year, $status, $adminNote]);
                flash('Karya baru berhasil ditambahkan dan QR Code siap dicetak!');
            }
            header('Location: ' . u('index.php?page=admin_karya'));
            exit;
        }
    }

    // Fetch Edit Data if requested
    $editItem = null;
    if (isset($_GET['edit'])) {
        $editId = (int)$_GET['edit'];
        $eStmt = $pdo->prepare("SELECT * FROM karya WHERE id = ? LIMIT 1");
        $eStmt->execute([$editId]);
        $editItem = $eStmt->fetch();
    }

    // Filters for admin table
    $filterType = trim($_GET['filter_type'] ?? '');
    $filterStatus = trim($_GET['filter_status'] ?? '');
    $q = trim($_GET['q'] ?? '');

    $sql = "SELECT * FROM karya WHERE 1=1";
    $params = [];
    if ($filterType !== '') {
        $sql .= " AND author_type = ?";
        $params[] = $filterType;
    }
    if ($filterStatus !== '') {
        $sql .= " AND status = ?";
        $params[] = $filterStatus;
    }
    if ($q !== '') {
        $sql .= " AND (title LIKE ? OR author_name LIKE ? OR author_class LIKE ?)";
        $like = "%$q%";
        $params = array_merge($params, [$like, $like, $like]);
    }
    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $allKarya = $stmt->fetchAll();

    // Stats
    $totalKarya   = (int)$pdo->query("SELECT COUNT(*) FROM karya")->fetchColumn();
    $totalPending = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE status = 'pending'")->fetchColumn();
    $totalSiswa   = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE author_type = 'siswa'")->fetchColumn();
    $totalGuru    = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE author_type = 'guru'")->fetchColumn();
    $totalViews   = (int)$pdo->query("SELECT SUM(views) FROM karya")->fetchColumn();

    // Banner Settings (Dapat disesuaikan untuk sekolah lain)
    $curBannerBadge = get_setting('karya_banner_badge', 'Etalase Karya & Inovasi Sekolah');
    $curBannerTitle = get_setting('karya_banner_title', 'Karya Siswa & Guru');
    $defaultSub = 'Pusat dokumentasi digital karya kreatif, video praktikum kejuruan, modul ajar, dan karya inovasi civitas akademika ' . SCHOOL . ' yang terhubung langsung dengan Google Drive dan dapat dipindai dengan QR Code.';
    $curBannerSub = get_setting('karya_banner_subtitle', $defaultSub);

    render_app_layout('admin', 'karya', 'Kelola Karya Siswa & Guru', function() use (
        $allKarya, $editItem, $totalKarya, $totalPending, $totalSiswa, $totalGuru, $totalViews, $filterType, $filterStatus, $q,
        $curBannerBadge, $curBannerTitle, $curBannerSub
    ) {
        ?>
        <!-- Header Action Bar for Admin Karya -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:20px;padding:16px 20px;background:#ffffff;border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);">
            <div>
                <h2 style="margin:0 0 4px;font-size:17px;font-weight:800;color:var(--navy);display:flex;align-items:center;gap:8px;">
                    <?=icon('video', '', 20)?> Panel Manajemen Karya Siswa &amp; Guru
                </h2>
                <p style="margin:0;font-size:12.5px;color:var(--text-muted);">
                    Kelola video praktikum kejuruan, modul ajar, dokumen inovasi, dan cetak QR Code Google Drive.
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <a href="#banner_settings_card" class="btn btn-outline-secondary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 14px;font-size:12.5px;">
                    <?=icon('edit', '', 14)?> Edit Teks Banner Galeri
                </a>
                <a href="<?=u('index.php?page=karya')?>" target="_blank" class="btn btn-outline-primary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 14px;font-size:12.5px;">
                    <?=icon('external-link', '', 14)?> Buka Galeri Publik ↗
                </a>
                <a href="#admin_karya_form" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 16px;font-size:12.5px;box-shadow:0 3px 10px rgba(2,132,199,0.25);">
                    <?=icon('plus', '', 14)?> Tambah Karya Baru
                </a>
            </div>
        </div>

        <?php if ($totalPending > 0): ?>
            <!-- Alert Banner: Pending Submissions from Members -->
            <div style="background:linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);border:1.5px solid #f59e0b;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;box-shadow:0 4px 14px rgba(245,158,11,0.15);">
                <div style="display:flex;align-items:center;gap:14px;min-width:280px;flex:1;">
                    <div style="width:44px;height:44px;border-radius:10px;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">
                        ⏳
                    </div>
                    <div>
                        <div style="font-weight:800;color:#92400e;font-size:15px;margin-bottom:2px;">
                            Ada <?=$totalPending?> Karya Siswa / Guru Menunggu Verifikasi Anda!
                        </div>
                        <div style="font-size:12.5px;color:#b45309;line-height:1.4;">
                            Anggota telah mengunggah karya ke sistem. Silakan periksa karya mereka, lalu klik <b>Setujui</b> untuk menerbitkan atau <b>Minta Revisi</b> jika link/berkas belum sesuai.
                        </div>
                    </div>
                </div>
                <a href="<?=u('index.php?page=admin_karya&filter_status=pending')?>" class="btn btn-primary" style="background:#d97706;border-color:#d97706;font-size:13px;font-weight:700;white-space:nowrap;padding:9px 18px;display:inline-flex;align-items:center;gap:6px;">
                    <?=icon('check', '', 14)?> Tampilkan Karya Menunggu Verifikasi (<?=$totalPending?>) →
                </a>
            </div>
        <?php endif; ?>

        <!-- 4 Top Stat Cards (Berbaris Lurus Horizontal & Desain Cantik) -->
        <div class="stats-grid-4" style="display:grid;grid-template-columns:repeat(4, minmax(0, 1fr));gap:16px;margin-bottom:24px;">
            <!-- Card 1: Total Semua Karya -->
            <div class="stat-card-modern bg-blue" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(20,116,220,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Total Semua Karya</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($totalKarya)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('video', '', 26)?>
                </div>
            </div>

            <!-- Card 2: Karya Siswa -->
            <div class="stat-card-modern bg-green" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(21,156,80,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Karya Siswa</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($totalSiswa)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('user', '', 26)?>
                </div>
            </div>

            <!-- Card 3: Karya Guru -->
            <div class="stat-card-modern bg-purple" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(112,85,200,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Karya Guru</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($totalGuru)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('award', '', 26)?>
                </div>
            </div>

            <!-- Card 4: Total Tayangan / Views -->
            <div class="stat-card-modern bg-orange" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(234,88,12,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Total Tayangan / Views</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($totalViews)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('eye', '', 26)?>
                </div>
            </div>
        </div>

        <!-- Card Pengaturan Teks Banner Galeri Karya (Dapat Disesuaikan untuk Sekolah Lain) -->
        <div class="card-box" id="banner_settings_card" style="margin-bottom:24px;border:1.5px solid #cbd5e1;background:#ffffff;border-radius:12px;">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#e0f2fe;color:#0284c7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <?=icon('edit', '', 18)?>
                    </div>
                    <div>
                        <h3 style="margin:0;font-size:15.5px;color:var(--navy);font-weight:800;">
                            Pengaturan Teks &amp; Banner Galeri Karya (Kustomisasi Sekolah)
                        </h3>
                        <p style="margin:2px 0 0;font-size:12px;color:var(--text-muted);">
                            Sesuaikan tulisan judul, lencana, dan nama sekolah pada banner publik agar dapat digunakan di sekolah mana pun.
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="const b=document.getElementById('bannerSettingsBody');b.style.display=(b.style.display==='none'?'block':'none');" style="font-size:12px;display:inline-flex;align-items:center;gap:4px;">
                    <?=icon('settings', '', 13)?> Buka / Tutup Form Edit
                </button>
            </div>

            <div id="bannerSettingsBody">
                <form method="post" style="margin-bottom:18px;">
                    <input type="hidden" name="csrf" value="<?=csrf()?>">
                    <input type="hidden" name="action" value="save_banner_settings">

                    <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;margin-bottom:14px;">
                        <div class="form-group" style="margin:0;">
                            <label style="font-weight:700;font-size:12.5px;">Teks Lencana Atas (Badge):</label>
                            <input type="text" name="karya_banner_badge" id="inpBannerBadge" class="form-control" value="<?=e($curBannerBadge)?>" placeholder="Contoh: Etalase Karya & Inovasi Sekolah" oninput="updateLiveBannerPreview()">
                            <small style="color:var(--text-muted);font-size:11px;">Label kecil di atas judul utama banner.</small>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-weight:700;font-size:12.5px;">Judul Utama Banner: <span style="color:var(--red);">*</span></label>
                            <input type="text" name="karya_banner_title" id="inpBannerTitle" class="form-control" value="<?=e($curBannerTitle)?>" placeholder="Contoh: Karya Siswa & Guru" required oninput="updateLiveBannerPreview()" style="font-weight:700;">
                            <small style="color:var(--text-muted);font-size:11px;">Judul besar di bagian atas halaman galeri karya.</small>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="font-weight:700;font-size:12.5px;">Teks Deskripsi / Keterangan Banner: <span style="color:var(--red);">*</span></label>
                        <textarea name="karya_banner_subtitle" id="inpBannerSub" rows="3" class="form-control" placeholder="Pusat dokumentasi digital karya kreatif..." required oninput="updateLiveBannerPreview()" style="font-size:13px;line-height:1.5;"><?=e($curBannerSub)?></textarea>
                        <small style="color:var(--text-muted);font-size:11px;">Ganti nama sekolah atau kalimat penjelas agar pas dengan sekolah Anda.</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding:9px 18px;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                        <?=icon('save', '', 14)?> Simpan Perubahan Teks Banner
                    </button>
                </form>

                <!-- Pratinjau Interaktif Langsung Sesuai Tampilan Publik -->
                <div style="border-top:1px dashed #cbd5e1;padding-top:14px;margin-top:14px;">
                    <div style="font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:flex;align-items:center;gap:5px;">
                        <?=icon('eye', '', 14)?> Pratinjau Tampilan Langsung (Live Preview):
                    </div>
                    <div style="background:linear-gradient(135deg, var(--navy-dark, #0c2340) 0%, #1e3a8a 50%, var(--primary, #1474dc) 100%);border-radius:14px;padding:26px 24px;color:#fff;box-shadow:0 8px 24px rgba(12,35,64,0.12);position:relative;overflow:hidden;">
                        <div style="position:absolute;right:-20px;bottom:-20px;opacity:0.1;pointer-events:none;">
                            <?=icon('video', '', 160)?>
                        </div>
                        <div style="max-width:740px;position:relative;z-index:1;">
                            <span id="previewBannerBadge" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.18);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;margin-bottom:10px;backdrop-filter:blur(4px);">
                                <?=icon('award', '', 14)?> <span><?=e($curBannerBadge)?></span>
                            </span>
                            <h1 id="previewBannerTitle" style="margin:0 0 8px;font-size:24px;font-weight:800;color:#ffffff;line-height:1.25;">
                                <?=e($curBannerTitle)?>
                            </h1>
                            <p id="previewBannerSub" style="margin:0;font-size:13.5px;opacity:0.92;line-height:1.5;">
                                <?=e($curBannerSub)?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function updateLiveBannerPreview() {
            const badge = document.getElementById('inpBannerBadge').value;
            const title = document.getElementById('inpBannerTitle').value;
            const sub = document.getElementById('inpBannerSub').value;

            const prevBadge = document.querySelector('#previewBannerBadge span');
            if (prevBadge) prevBadge.textContent = badge || 'Etalase Karya & Inovasi';
            document.getElementById('previewBannerTitle').textContent = title || 'Karya Siswa & Guru';
            document.getElementById('previewBannerSub').textContent = sub || 'Pusat dokumentasi digital karya kreatif...';
        }
        </script>

        <!-- Add / Edit Form Box -->
        <div class="card-box" id="admin_karya_form" style="margin-bottom:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="margin:0;font-size:16px;color:var(--navy);font-weight:800;display:flex;align-items:center;gap:6px;">
                    <?=icon($editItem ? 'edit' : 'plus', '', 18)?>
                    <?=$editItem ? 'Edit Karya: ' . e($editItem['title']) : 'Tambah Karya Baru (Siswa / Guru)'?>
                </h3>
                <?php if ($editItem): ?>
                    <a href="<?=u('index.php?page=admin_karya')?>" class="btn btn-outline-secondary btn-sm">Batal Edit / Tambah Baru</a>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <input type="hidden" name="id" value="<?=$editItem ? $editItem['id'] : 0?>">

                <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Judul Karya / Proyek / Video <span style="color:var(--red);">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Video Praktik Instalasi Fiber Optic & Routing MikroTik" value="<?=e($editItem['title'] ?? '')?>" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Jenis Pembuat Karya <span style="color:var(--red);">*</span></label>
                        <select name="author_type" class="form-control" required>
                            <option value="siswa" <?=($editItem['author_type']??'siswa')==='siswa'?'selected':''?>>🎓 Siswa (Karya Siswa)</option>
                            <option value="guru" <?=($editItem['author_type']??'')==='guru'?'selected':''?>>👨‍🏫 Guru (Karya Guru)</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Nama Pembuat / Tim <span style="color:var(--red);">*</span></label>
                        <input type="text" name="author_name" class="form-control" placeholder="Contoh: Ahmad Rizky & Tim TKJ" value="<?=e($editItem['author_name'] ?? '')?>" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Kelas / Jurusan / Bidang Studi</label>
                        <input type="text" name="author_class" class="form-control" placeholder="Contoh: Kelas XII TKJ 1 / Guru RPL" value="<?=e($editItem['author_class'] ?? '')?>">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Kategori Karya</label>
                        <select name="category" class="form-control">
                            <?php 
                            $cats = get_karya_categories();
                            $curCat = $editItem['category'] ?? 'Video Praktik / Tutorial';
                            foreach ($cats as $c): ?>
                                <option value="<?=e($c)?>" <?=($curCat===$c || (stripos($curCat, 'PKL') !== false && $c === 'Laporan PKL'))?'selected':''?>><?=e($c)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Tipe Media</label>
                        <select name="media_type" class="form-control">
                            <option value="video" <?=($editItem['media_type']??'video')==='video'?'selected':''?>>🎬 Video (Google Drive / YouTube)</option>
                            <option value="dokumen" <?=($editItem['media_type']??'')==='dokumen'?'selected':''?>>📄 Dokumen / Makalah Drive</option>
                            <option value="aplikasi" <?=($editItem['media_type']??'')==='aplikasi'?'selected':''?>>💻 Aplikasi / Repository Proyek</option>
                            <option value="lainnya" <?=($editItem['media_type']??'')==='lainnya'?'selected':''?>>📁 Berkas Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Tautan Link Google Drive / Video URL <span style="color:var(--red);">*</span></label>
                        <input type="url" name="drive_url" class="form-control" placeholder="https://drive.google.com/file/d/... atau https://youtu.be/..." value="<?=e($editItem['drive_url'] ?? '')?>" required>
                        <small style="color:var(--text-muted);font-size:11.5px;display:block;margin-top:4px;">
                            💡 Pastikan link Google Drive disetel ke <b>"Siapa saja yang memiliki link dapat melihat (Viewer)"</b> agar dapat diakses siswa.
                        </small>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Foto Sampul / Thumbnail (Opsional)</label>
                        <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding:6px;">
                        <?php if (!empty($editItem['thumbnail'])): ?>
                            <small style="color:var(--primary);font-size:11px;">Thumbnail saat ini: <?=e($editItem['thumbnail'])?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Tahun Pembuatan</label>
                        <input type="number" name="year" class="form-control" value="<?=e($editItem['year'] ?? date('Y'))?>" min="2010" max="2035">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Status Publikasi &amp; Verifikasi</label>
                        <select name="status" class="form-control" id="adminKaryaStatusSelect" onchange="toggleAdminNoteField(this.value)">
                            <option value="published" <?=($editItem['status']??'published')==='published'?'selected':''?>>Diterbitkan (Tampil di Publik)</option>
                            <option value="pending" <?=($editItem['status']??'')==='pending'?'selected':''?>>⏳ Menunggu Verifikasi (Pending)</option>
                            <option value="rejected" <?=($editItem['status']??'')==='rejected'?'selected':''?>>✕ Ditolak / Perlu Revisi</option>
                            <option value="draft" <?=($editItem['status']??'')==='draft'?'selected':''?>>Draf (Disimpan Saja)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="adminNoteWrap" style="margin-bottom:16px;<?=in_array($editItem['status']??'', ['rejected','pending'])?'':'display:none;'?>">
                    <label style="font-weight:700;">Catatan Verifikasi / Alasan Penolakan (Admin Note):</label>
                    <textarea name="admin_note" class="form-control" rows="2" placeholder="Tuliskan catatan verifikasi atau arahan perbaikan untuk siswa/guru..."><?=e($editItem['admin_note'] ?? '')?></textarea>
                    <small style="color:var(--text-muted);font-size:11px;">Catatan ini akan tampil di dashboard anggota jika karya berstatus ditolak atau membutuhkan revisi.</small>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label>Deskripsi &amp; Ringkasan Karya</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan latar belakang, tujuan, langkah pembuatan, atau hasil inovasi..."><?=e($editItem['description'] ?? '')?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="padding:9px 20px;font-weight:700;">
                    <?=icon('check', '', 14)?> <?=$editItem ? 'Simpan Perubahan' : 'Terbitkan Karya & Buat QR Code'?>
                </button>
            </form>
        </div>

        <!-- Filter & Table Box -->
        <div class="card-box">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                <h3 style="margin:0;font-size:15px;color:var(--navy);font-weight:800;">
                    Daftar Semua Karya Siswa &amp; Guru (<?=count($allKarya)?>)
                </h3>

                <form method="get" action="<?=u('index.php')?>" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <input type="hidden" name="page" value="admin_karya">
                    <select name="filter_type" class="form-control" style="width:auto;font-size:12.5px;padding:6px 10px;" onchange="this.form.submit()">
                        <option value="">Semua Pembuat</option>
                        <option value="siswa" <?=$filterType==='siswa'?'selected':''?>>Siswa Saja</option>
                        <option value="guru" <?=$filterType==='guru'?'selected':''?>>Guru Saja</option>
                    </select>

                    <select name="filter_status" class="form-control" style="width:auto;font-size:12.5px;padding:6px 10px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" <?=$filterStatus==='pending'?'selected':''?>>⏳ Menunggu Verifikasi <?=$totalPending>0?'('.$totalPending.')':''?></option>
                        <option value="published" <?=$filterStatus==='published'?'selected':''?>>✓ Diterbitkan (Published)</option>
                        <option value="rejected" <?=$filterStatus==='rejected'?'selected':''?>>✕ Ditolak / Perlu Revisi</option>
                        <option value="draft" <?=$filterStatus==='draft'?'selected':''?>>Draf</option>
                    </select>

                    <input type="text" name="q" value="<?=e($q)?>" placeholder="Cari karya..." class="form-control" style="width:160px;font-size:12.5px;padding:6px 10px;">
                    <button type="submit" class="btn btn-primary btn-sm"><?=icon('search', '', 13)?> Filter</button>
                    <?php if ($filterType !== '' || $filterStatus !== '' || $q !== ''): ?>
                        <a href="<?=u('index.php?page=admin_karya')?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="width:65px;">QR Code</th>
                            <th>Judul &amp; Kategori</th>
                            <th>Pembuat / Kelas</th>
                            <th>Media &amp; Link Drive</th>
                            <th style="width:110px;">Status</th>
                            <th style="width:160px;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allKarya)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                                    Belum ada data karya yang tersimpan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($allKarya as $k): 
                                $isS = ($k['author_type'] === 'siswa');
                            ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td>
                                        <a href="<?=u('index.php?page=karya_print_qr&id='.$k['id'])?>" target="_blank" title="Klik untuk Cetak QR Code">
                                            <div style="background:#fff;border:1px solid #cbd5e1;padding:3px;border-radius:4px;display:inline-block;line-height:0;">
                                                <?=generate_qr_svg($k['drive_url'], 40)?>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;color:var(--navy);font-size:13.5px;margin-bottom:3px;">
                                            <?=e($k['title'])?>
                                        </div>
                                        <div style="display:flex;gap:6px;align-items:center;">
                                            <span class="badge badge-blue" style="font-size:10.5px;"><?=e($k['category'])?></span>
                                            <?php if (!empty($k['year'])): ?>
                                                <span style="font-size:11px;color:var(--text-muted);"><?=e($k['year'])?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?=$isS?'badge-green':'badge-purple'?>" style="font-size:10px;margin-bottom:2px;">
                                            <?=$isS?'Siswa':'Guru'?>
                                        </span>
                                        <div style="font-weight:700;font-size:13px;"><?=e($k['author_name'])?></div>
                                        <?php if (!empty($k['author_class'])): ?>
                                            <small style="color:var(--text-muted);display:block;"><?=e($k['author_class'])?></small>
                                        <?php endif; ?>
                                        <?php if (!empty($k['user_id'])): ?>
                                            <span style="display:inline-block;background:#f1f5f9;color:#475569;font-size:10px;font-weight:700;padding:2px 6px;border-radius:4px;margin-top:3px;">
                                                Unggahan Anggota
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:11.5px;font-weight:700;text-transform:uppercase;color:#475569;margin-bottom:3px;">
                                            <?=e($k['media_type'])?>
                                        </div>
                                        <a href="<?=e($k['drive_url'])?>" target="_blank" rel="noopener noreferrer" style="font-size:11.5px;color:var(--primary);display:inline-flex;align-items:center;gap:3px;text-decoration:none;" title="Buka tautan asli di Google Drive">
                                            <?=icon('external-link', '', 12)?> Buka Google Drive ↗
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($k['status'] === 'published'): ?>
                                            <span class="badge badge-green" style="font-size:11px;font-weight:700;">✓ Diterbitkan</span>
                                        <?php elseif ($k['status'] === 'pending'): ?>
                                            <span class="badge badge-orange" style="font-size:11px;font-weight:700;background:#fef3c7;color:#b45309;border:1px solid #fcd34d;">⏳ Menunggu Verifikasi</span>
                                        <?php elseif ($k['status'] === 'rejected'): ?>
                                            <span class="badge badge-red" style="font-size:11px;font-weight:700;">✕ Ditolak / Revisi</span>
                                            <?php if (!empty($k['admin_note'])): ?>
                                                <div style="font-size:11px;color:#b91c1c;margin-top:4px;line-height:1.3;max-width:180px;background:#fef2f2;padding:4px 6px;border-radius:4px;border:1px solid #fecaca;" title="<?=e($k['admin_note'])?>">
                                                    <b>Catatan:</b> <?=e(mb_strimwidth($k['admin_note'], 0, 50, '...'))?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge" style="background:#64748b;color:#fff;font-size:11px;">Draf</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                            <?php if ($k['status'] === 'pending' || $k['status'] === 'rejected'): ?>
                                                <a href="<?=u('index.php?page=admin_karya&approve='.$k['id'])?>" class="btn btn-success btn-sm" style="padding:4px 8px;background:#16a34a;color:#fff;display:inline-flex;align-items:center;gap:3px;" title="Setujui &amp; Terbitkan Karya" onclick="return confirm('Setujui karya ini dan langsung terbitkan ke galeri publik?');">
                                                    <?=icon('check', '', 13)?> <span style="font-size:11px;font-weight:700;">Setujui</span>
                                                </a>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" style="padding:4px 7px;color:#dc2626;border-color:#fca5a5;background:#fff;" title="Tolak / Minta Revisi" onclick="openRejectModal(<?=$k['id']?>, '<?=htmlspecialchars(addslashes($k['title']), ENT_QUOTES)?>')">
                                                    <?=icon('x', '', 13)?>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($k['status'] === 'published'): ?>
                                                <a href="<?=u('index.php?page=karya_print_qr&id='.$k['id'])?>" target="_blank" class="btn btn-outline-primary btn-sm" style="padding:4px 7px;" title="Cetak Lembar QR Code">
                                                    <?=icon('print', '', 13)?>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?=u('index.php?page=karya_detail&id='.$k['id'])?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="padding:4px 7px;" title="Lihat Pratinjau">
                                                <?=icon('eye', '', 13)?>
                                            </a>
                                            <a href="<?=u('index.php?page=admin_karya&edit='.$k['id'])?>" class="btn btn-outline-primary btn-sm" style="padding:4px 7px;" title="Edit Data">
                                                <?=icon('edit', '', 13)?>
                                            </a>
                                            <a href="<?=u('index.php?page=admin_karya&delete='.$k['id'])?>" class="btn btn-danger btn-sm" style="padding:4px 7px;" data-confirm="Yakin ingin menghapus karya ini?" title="Hapus Karya">
                                                <?=icon('trash', '', 13)?>
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

        <!-- Reject Modal -->
        <div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,0.65);z-index:99999;align-items:center;justify-content:center;padding:16px;">
            <div style="background:#fff;border-radius:14px;max-width:500px;width:100%;padding:24px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.25);position:relative;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <h3 style="margin:0;font-size:16px;color:#991b1b;font-weight:800;display:flex;align-items:center;gap:6px;">
                        <?=icon('x', '', 18)?> Minta Revisi / Tolak Karya
                    </h3>
                    <button type="button" onclick="closeRejectModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#64748b;">✕</button>
                </div>
                <p style="font-size:13px;color:#475569;margin:0 0 16px;line-height:1.45;" id="rejectModalTitle"></p>
                <form method="post" action="<?=u('index.php?page=admin_karya')?>">
                    <input type="hidden" name="csrf" value="<?=csrf()?>">
                    <input type="hidden" name="action" value="reject_karya">
                    <input type="hidden" name="karya_id" id="rejectKaryaId" value="0">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label style="font-weight:700;font-size:12.5px;color:var(--navy);">Catatan Revisi untuk Pembuat Karya: <span style="color:var(--red);">*</span></label>
                        <textarea name="admin_note" class="form-control" rows="3" placeholder="Contoh: Tautan Google Drive belum dapat diakses publik. Mohon ubah perizinan link ke 'Siapa saja yang memiliki link dapat melihat'..." required style="font-size:13px;line-height:1.45;"></textarea>
                        <small style="color:var(--text-muted);font-size:11.5px;display:block;margin-top:4px;">
                            💡 Catatan ini akan langsung tampil di dashboard anggota beserta tombol untuk mengedit dan mengajukan ulang karyanya.
                        </small>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;">
                        <button type="button" class="btn btn-outline-secondary" onclick="closeRejectModal()" style="font-size:13px;">Batal</button>
                        <button type="submit" class="btn btn-danger" style="background:#dc2626;color:#fff;font-weight:700;font-size:13px;padding:8px 16px;">Kirim Catatan &amp; Minta Revisi</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function toggleAdminNoteField(status) {
            const wrap = document.getElementById('adminNoteWrap');
            if (wrap) {
                wrap.style.display = (status === 'rejected' || status === 'pending') ? 'block' : 'none';
            }
        }
        function openRejectModal(id, title) {
            document.getElementById('rejectKaryaId').value = id;
            document.getElementById('rejectModalTitle').innerHTML = 'Tuliskan alasan atau arahan perbaikan untuk karya: <br><b>' + title + '</b>';
            const m = document.getElementById('rejectModal');
            m.style.display = 'flex';
        }
        function closeRejectModal() {
            const m = document.getElementById('rejectModal');
            m.style.display = 'none';
        }
        </script>
        <?php
    });
    exit;
}

/**
 * 5. HALAMAN ANGGOTA (SISWA / GURU): KARYA ANDA
 * Fitur: Dashboard Portofolio Mandiri, Formulir Unggah Karya (Sama dengan Tampilan Admin),
 * Status Verifikasi (Pending / Diterbitkan / Ditolak / Draf),
 * Catatan Perbaikan Admin, dan Akses Cetak QR Code setelah Disetujui.
 */
function handle_member_karya() {
    global $pdo, $currentUser;
    auth(); // Pastikan anggota atau pengguna telah login

    $userId = (int)($currentUser['id'] ?? 0);
    $userName = $currentUser['name'] ?? '';
    $userDept = $currentUser['department'] ?? '';
    $userRole = $currentUser['role'] ?? 'member';

    // Deteksi tipe otomatis: guru jika jabatan/role berisi 'guru', selain itu siswa
    $isTeacher = (stripos($userDept, 'Guru') !== false || stripos($userName, 'Guru') !== false);
    $defaultAuthorType = $isTeacher ? 'guru' : 'siswa';

    // Handle Hapus Karya Milik Sendiri
    if (isset($_GET['delete'])) {
        $delId = (int)$_GET['delete'];
        $delStmt = $pdo->prepare("DELETE FROM karya WHERE id = ? AND user_id = ?");
        $delStmt->execute([$delId, $userId]);
        flash('Karya Anda berhasil dihapus.');
        header('Location: ' . u('index.php?page=member_karya'));
        exit;
    }

    // Handle Simpan / Update Karya dari Anggota
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        checkcsrf();
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $authorType = in_array($_POST['author_type'] ?? '', ['siswa', 'guru']) ? $_POST['author_type'] : $defaultAuthorType;
        $authorName = trim($_POST['author_name'] ?? '');
        $authorClass = trim($_POST['author_class'] ?? '');
        $category = trim($_POST['category'] ?? 'Video Praktik / Tutorial');
        $mediaType = in_array($_POST['media_type'] ?? '', ['video', 'dokumen', 'aplikasi', 'lainnya']) ? $_POST['media_type'] : 'video';
        $driveUrl = trim($_POST['drive_url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $year = (int)($_POST['year'] ?? date('Y'));

        // Anggota bisa memilih 'pending' (Ajukan untuk Verifikasi Admin) atau 'draft' (Simpan Draf)
        $reqStatus = trim($_POST['status'] ?? 'pending');
        $status = ($reqStatus === 'draft') ? 'draft' : 'pending';

        if ($title === '' || $authorName === '' || $driveUrl === '') {
            flash('Judul karya, nama pembuat, dan tautan link Google Drive wajib diisi.', 'error');
        } else {
            // Handle Upload Foto Sampul / Thumbnail
            $thumbFileName = null;
            if (!empty($_FILES['thumbnail_file']['name']) && $_FILES['thumbnail_file']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['thumbnail_file']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($ext, $allowed, true)) {
                    $uploadDir = __DIR__ . '/uploads/karya/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                    $newFile = 'karya_' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['thumbnail_file']['tmp_name'], $uploadDir . $newFile)) {
                        $thumbFileName = 'uploads/karya/' . $newFile;
                    }
                }
            }

            if ($id > 0) {
                // Pastikan karya ini memang milik user yang sedang login
                $chk = $pdo->prepare("SELECT id FROM karya WHERE id = ? AND user_id = ?");
                $chk->execute([$id, $userId]);
                if (!$chk->fetch()) {
                    flash('Akses ditolak atau karya tidak ditemukan.', 'error');
                    header('Location: ' . u('index.php?page=member_karya'));
                    exit;
                }

                // Jika diajukan ulang, hapus catatan revisi admin lama dan setel status ke pending
                if ($thumbFileName) {
                    $stmt = $pdo->prepare("UPDATE karya SET title = ?, author_type = ?, author_name = ?, author_class = ?, category = ?, media_type = ?, drive_url = ?, thumbnail = ?, description = ?, year = ?, status = ?, admin_note = NULL WHERE id = ? AND user_id = ?");
                    $stmt->execute([$title, $authorType, $authorName, $authorClass, $category, $mediaType, $driveUrl, $thumbFileName, $description, $year, $status, $id, $userId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE karya SET title = ?, author_type = ?, author_name = ?, author_class = ?, category = ?, media_type = ?, drive_url = ?, description = ?, year = ?, status = ?, admin_note = NULL WHERE id = ? AND user_id = ?");
                    $stmt->execute([$title, $authorType, $authorName, $authorClass, $category, $mediaType, $driveUrl, $description, $year, $status, $id, $userId]);
                }
                flash($status === 'pending' ? 'Karya Anda berhasil diperbarui dan diajukan ulang ke admin untuk diverifikasi.' : 'Perubahan draf karya berhasil disimpan.');
            } else {
                $stmt = $pdo->prepare("INSERT INTO karya (title, author_type, author_name, author_class, category, media_type, drive_url, thumbnail, description, year, status, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $authorType, $authorName, $authorClass, $category, $mediaType, $driveUrl, $thumbFileName, $description, $year, $status, $userId]);
                flash($status === 'pending' ? 'Karya Anda berhasil diunggah! Admin akan segera memverifikasi karya Anda agar dapat tampil di galeri publik.' : 'Karya berhasil disimpan sebagai Draf.');
            }
            header('Location: ' . u('index.php?page=member_karya'));
            exit;
        }
    }

    // Ambil Data Edit Jika Anggota Ingin Mengubah Karya
    $editItem = null;
    if (isset($_GET['edit'])) {
        $editId = (int)$_GET['edit'];
        $eStmt = $pdo->prepare("SELECT * FROM karya WHERE id = ? AND user_id = ? LIMIT 1");
        $eStmt->execute([$editId, $userId]);
        $editItem = $eStmt->fetch();
    }

    // Filter untuk tabel karya anggota
    $filterStatus = trim($_GET['filter_status'] ?? '');
    $q = trim($_GET['q'] ?? '');

    $sql = "SELECT * FROM karya WHERE user_id = ?";
    $params = [$userId];

    if ($filterStatus !== '') {
        $sql .= " AND status = ?";
        $params[] = $filterStatus;
    }
    if ($q !== '') {
        $sql .= " AND (title LIKE ? OR category LIKE ? OR description LIKE ?)";
        $like = "%$q%";
        $params = array_merge($params, [$like, $like, $like]);
    }
    $sql .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $myKarya = $stmt->fetchAll();

    // Statistik Karya Anggota
    $myTotal     = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE user_id = $userId")->fetchColumn();
    $myPending   = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE user_id = $userId AND status = 'pending'")->fetchColumn();
    $myPublished = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE user_id = $userId AND status = 'published'")->fetchColumn();
    $myRejected  = (int)$pdo->query("SELECT COUNT(*) FROM karya WHERE user_id = $userId AND status = 'rejected'")->fetchColumn();

    // Cek apakah ada karya yang ditolak / butuh revisi
    $rejectedItems = $pdo->query("SELECT id, title, admin_note FROM karya WHERE user_id = $userId AND status = 'rejected' ORDER BY id DESC")->fetchAll();

    render_app_layout('member', 'member_karya', 'Karya Anda - Portofolio Digital', function() use (
        $myKarya, $editItem, $myTotal, $myPending, $myPublished, $myRejected, $rejectedItems,
        $filterStatus, $q, $currentUser, $defaultAuthorType, $userName, $userDept
    ) {
        ?>
        <!-- Header Action Bar for Member Karya -->
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:20px;padding:18px 20px;background:#ffffff;border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);">
            <div>
                <h2 style="margin:0 0 4px;font-size:18px;font-weight:800;color:var(--navy);display:flex;align-items:center;gap:8px;">
                    <?=icon('award', '', 22)?> Portofolio Karya Anda (Siswa / Guru)
                </h2>
                <p style="margin:0;font-size:12.5px;color:var(--text-muted);">
                    Unggah karya kreatif, video praktikum, dokumen karya ilmiah, atau modul Anda. Admin perpustakaan akan memverifikasi karya Anda sebelum dipublikasikan ke Galeri Publik.
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <a href="<?=u('index.php?page=karya')?>" target="_blank" class="btn btn-outline-primary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 14px;font-size:12.5px;">
                    <?=icon('external-link', '', 14)?> Buka Galeri Publik ↗
                </a>
                <a href="#member_karya_form" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;font-weight:700;padding:8px 16px;font-size:12.5px;box-shadow:0 3px 10px rgba(20,116,220,0.25);">
                    <?=icon('plus', '', 14)?> Unggah Karya Baru
                </a>
            </div>
        </div>

        <?php if (!empty($rejectedItems)): ?>
            <!-- Alert Box: Karya yang Perlu Perbaikan -->
            <div style="background:linear-gradient(135deg, #fff1f2 0%, #fee2e2 100%);border:1.5px solid #ef4444;border-radius:12px;padding:16px 20px;margin-bottom:20px;box-shadow:0 4px 14px rgba(239,68,68,0.12);">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <div style="width:36px;height:36px;border-radius:8px;background:#ef4444;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;">
                        ⚠️
                    </div>
                    <div style="flex:1;">
                        <h4 style="margin:0 0 6px;color:#991b1b;font-size:14.5px;font-weight:800;">
                            Pemberitahuan: Terdapat Karya Anda yang Membutuhkan Revisi / Perbaikan
                        </h4>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <?php foreach ($rejectedItems as $rej): ?>
                                <div style="background:#ffffff;border:1px solid #fca5a5;border-radius:8px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                                    <div>
                                        <div style="font-weight:700;color:var(--navy);font-size:13px;"><?=e($rej['title'])?></div>
                                        <div style="font-size:12px;color:#b91c1c;margin-top:2px;">
                                            <b>Catatan Admin:</b> <?=e($rej['admin_note'] ?: 'Mohon periksa kembali link berkas atau isi karya Anda.')?>
                                        </div>
                                    </div>
                                    <a href="<?=u('index.php?page=member_karya&edit='.$rej['id'].'#member_karya_form')?>" class="btn btn-primary btn-sm" style="background:#dc2626;border-color:#dc2626;padding:5px 12px;font-size:11.5px;font-weight:700;display:inline-flex;align-items:center;gap:4px;">
                                        <?=icon('edit', '', 12)?> Perbaiki &amp; Ajukan Ulang
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- 4 Top Stat Cards (Berbaris Lurus Horizontal & Desain Cantik) -->
        <div class="stats-grid-4" style="display:grid;grid-template-columns:repeat(4, minmax(0, 1fr));gap:16px;margin-bottom:24px;">
            <!-- Card 1: Total Karya Anda -->
            <div class="stat-card-modern bg-blue" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(20,116,220,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Total Karya Anda</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($myTotal)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('video', '', 26)?>
                </div>
            </div>

            <!-- Card 2: Menunggu Verifikasi -->
            <div class="stat-card-modern bg-orange" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(234,88,12,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Menunggu Verifikasi</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($myPending)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('calendar', '', 26)?>
                </div>
            </div>

            <!-- Card 3: Disetujui / Terbit -->
            <div class="stat-card-modern bg-green" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(21,156,80,0.22);transition:transform 0.2s, box-shadow 0.2s;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Disetujui / Terbit</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($myPublished)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('check', '', 26)?>
                </div>
            </div>

            <!-- Card 4: Perlu Revisi / Ditolak -->
            <div class="stat-card-modern bg-purple" style="border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 6px 18px rgba(112,85,200,0.22);transition:transform 0.2s, box-shadow 0.2s;background:linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;">
                <div>
                    <div class="stat-label" style="font-size:12px;font-weight:700;letter-spacing:0.4px;text-transform:uppercase;color:rgba(255,255,255,0.9);margin-bottom:6px;">Perlu Revisi</div>
                    <div class="stat-value" style="font-size:30px;font-weight:900;line-height:1;color:#ffffff;"><?=number_format($myRejected)?></div>
                </div>
                <div class="stat-icon-bg" style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;color:#ffffff;flex-shrink:0;">
                    <?=icon('x', '', 26)?>
                </div>
            </div>
        </div>

        <!-- Formulir Unggah / Edit Karya Anggota (Desain Persis dengan Form Admin) -->
        <div class="card-box" id="member_karya_form" style="margin-bottom:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <h3 style="margin:0;font-size:16px;color:var(--navy);font-weight:800;display:flex;align-items:center;gap:6px;">
                    <?=icon($editItem ? 'edit' : 'plus', '', 18)?>
                    <?=$editItem ? 'Edit Karya: ' . e($editItem['title']) : '+ Tambah Karya Baru (Siswa / Guru)'?>
                </h3>
                <?php if ($editItem): ?>
                    <a href="<?=u('index.php?page=member_karya')?>" class="btn btn-outline-secondary btn-sm">Batal Edit / Tambah Baru</a>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?=csrf()?>">
                <input type="hidden" name="id" value="<?=$editItem ? $editItem['id'] : 0?>">

                <!-- Row 1: Judul Karya & Jenis Pembuat -->
                <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Judul Karya / Proyek / Video <span style="color:var(--red);">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Video Praktik Instalasi Fiber Optic & Routing MikroTik" value="<?=e($editItem['title'] ?? '')?>" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Jenis Pembuat Karya <span style="color:var(--red);">*</span></label>
                        <select name="author_type" class="form-control" required>
                            <option value="siswa" <?=($editItem['author_type'] ?? $defaultAuthorType)==='siswa'?'selected':''?>>🎓 Siswa (Karya Siswa)</option>
                            <option value="guru" <?=($editItem['author_type'] ?? $defaultAuthorType)==='guru'?'selected':''?>>👨‍🏫 Guru (Karya Guru)</option>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Nama Pembuat, Kelas/Jurusan, Kategori -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Nama Pembuat / Tim <span style="color:var(--red);">*</span></label>
                        <input type="text" name="author_name" class="form-control" placeholder="Contoh: Ahmad Rizky & Tim TKJ" value="<?=e($editItem['author_name'] ?? $userName)?>" required>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Kelas / Jurusan / Bidang Studi</label>
                        <input type="text" name="author_class" class="form-control" placeholder="Contoh: Kelas XII TKJ 1 / Guru RPL" value="<?=e($editItem['author_class'] ?? $userDept)?>">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Kategori Karya</label>
                        <select name="category" class="form-control">
                            <?php 
                            $cats = get_karya_categories();
                            $curCat = $editItem['category'] ?? 'Video Praktik / Tutorial';
                            foreach ($cats as $c): ?>
                                <option value="<?=e($c)?>" <?=($curCat===$c || (stripos($curCat, 'PKL') !== false && $c === 'Laporan PKL'))?'selected':''?>><?=e($c)?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Row 3: Tipe Media & Tautan Google Drive -->
                <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Tipe Media</label>
                        <select name="media_type" class="form-control">
                            <option value="video" <?=($editItem['media_type']??'video')==='video'?'selected':''?>>🎬 Video (Google Drive / YouTube)</option>
                            <option value="dokumen" <?=($editItem['media_type']??'')==='dokumen'?'selected':''?>>📄 Dokumen / Makalah Drive</option>
                            <option value="aplikasi" <?=($editItem['media_type']??'')==='aplikasi'?'selected':''?>>💻 Aplikasi / Repository Proyek</option>
                            <option value="lainnya" <?=($editItem['media_type']??'')==='lainnya'?'selected':''?>>📁 Berkas Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Tautan Link Google Drive / Video URL <span style="color:var(--red);">*</span></label>
                        <input type="url" name="drive_url" class="form-control" placeholder="https://drive.google.com/file/d/... atau https://youtu.be/..." value="<?=e($editItem['drive_url'] ?? '')?>" required>
                        <small style="color:var(--text-muted);font-size:11.5px;display:block;margin-top:4px;">
                            💡 Pastikan link Google Drive disetel ke <b>"Siapa saja yang memiliki link dapat melihat (Viewer)"</b> agar dapat diakses siswa.
                        </small>
                    </div>
                </div>

                <!-- Row 4: Foto Sampul, Tahun Pembuatan, Status Publikasi -->
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:14px;">
                    <div class="form-group" style="margin:0;">
                        <label>Foto Sampul / Thumbnail (Opsional)</label>
                        <input type="file" name="thumbnail_file" accept="image/jpeg,image/png,image/webp" class="form-control" style="padding:6px;">
                        <?php if (!empty($editItem['thumbnail'])): ?>
                            <small style="color:var(--primary);font-size:11px;">Thumbnail saat ini: <?=e($editItem['thumbnail'])?></small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Tahun Pembuatan</label>
                        <input type="number" name="year" class="form-control" value="<?=e($editItem['year'] ?? date('Y'))?>" min="2010" max="2035">
                    </div>
                    <div class="form-group" style="margin:0;">
                        <label>Status Publikasi</label>
                        <select name="status" class="form-control">
                            <option value="pending" <?=($editItem['status']??'pending')!=='draft'?'selected':''?>>Diterbitkan (Tampil di Publik - Verifikasi Admin)</option>
                            <option value="draft" <?=($editItem['status']??'')==='draft'?'selected':''?>>Draf (Disimpan Saja)</option>
                        </select>
                    </div>
                </div>

                <!-- Row 5: Deskripsi & Ringkasan Karya -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label>Deskripsi &amp; Ringkasan Karya</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan latar belakang, tujuan, langkah pembuatan, atau hasil inovasi..."><?=e($editItem['description'] ?? '')?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="padding:9px 22px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    <?=icon('check', '', 14)?> <?=$editItem ? 'Simpan Perubahan Karya' : 'Terbitkan Karya & Buat QR Code'?>
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Karya yang Diunggah Anggota -->
        <div class="card-box">
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
                <h3 style="margin:0;font-size:15.5px;color:var(--navy);font-weight:800;">
                    Daftar Karya Anda (<?=count($myKarya)?>)
                </h3>

                <form method="get" action="<?=u('index.php')?>" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <input type="hidden" name="page" value="member_karya">
                    <select name="filter_status" class="form-control" style="width:auto;font-size:12.5px;padding:6px 10px;" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" <?=$filterStatus==='pending'?'selected':''?>>⏳ Menunggu Verifikasi (<?=$myPending?>)</option>
                        <option value="published" <?=$filterStatus==='published'?'selected':''?>>✓ Disetujui &amp; Terbit (<?=$myPublished?>)</option>
                        <option value="rejected" <?=$filterStatus==='rejected'?'selected':''?>>✕ Perlu Revisi (<?=$myRejected?>)</option>
                        <option value="draft" <?=$filterStatus==='draft'?'selected':''?>>Draf</option>
                    </select>

                    <input type="text" name="q" value="<?=e($q)?>" placeholder="Cari karya Anda..." class="form-control" style="width:170px;font-size:12.5px;padding:6px 10px;">
                    <button type="submit" class="btn btn-primary btn-sm"><?=icon('search', '', 13)?> Cari</button>
                    <?php if ($filterStatus !== '' || $q !== ''): ?>
                        <a href="<?=u('index.php?page=member_karya')?>" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th style="width:50px;">No</th>
                            <th style="width:65px;">QR Code</th>
                            <th>Judul &amp; Kategori</th>
                            <th>Pembuat / Kelas</th>
                            <th>Media &amp; Link Drive</th>
                            <th style="width:140px;">Status Verifikasi</th>
                            <th style="width:140px;text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($myKarya)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">
                                    <div style="font-size:36px;margin-bottom:8px;">🎨</div>
                                    <div style="font-weight:700;font-size:15px;color:var(--navy);margin-bottom:4px;">Belum Ada Karya yang Diunggah</div>
                                    <div style="font-size:13px;max-width:440px;margin:0 auto 14px;">
                                        Anda belum mengunggah karya atau proyek digital Anda. Gunakan formulir di atas untuk membagikan karya Anda kepada warga sekolah!
                                    </div>
                                    <a href="#member_karya_form" class="btn btn-primary btn-sm">Mulai Unggah Karya Pertama Anda</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($myKarya as $k): 
                                $isS = ($k['author_type'] === 'siswa');
                                $isPub = ($k['status'] === 'published');
                            ?>
                                <tr>
                                    <td><?=$no++?></td>
                                    <td>
                                        <?php if ($isPub): ?>
                                            <a href="<?=u('index.php?page=karya_print_qr&id='.$k['id'])?>" target="_blank" title="Klik untuk Cetak Lembar QR Code">
                                                <div style="background:#fff;border:1px solid #cbd5e1;padding:3px;border-radius:4px;display:inline-block;line-height:0;">
                                                    <?=generate_qr_svg($k['drive_url'], 40)?>
                                                </div>
                                            </a>
                                        <?php else: ?>
                                            <div style="width:40px;height:40px;border-radius:4px;background:#f1f5f9;border:1px dashed #cbd5e1;display:inline-flex;align-items:center;justify-content:center;color:#94a3b8;" title="QR Code aktif setelah karya disetujui admin">
                                                <?=icon('qr', '', 18)?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:700;color:var(--navy);font-size:13.5px;margin-bottom:3px;">
                                            <?php if ($isPub): ?>
                                                <a href="<?=u('index.php?page=karya_detail&id='.$k['id'])?>" target="_blank" style="color:inherit;text-decoration:none;">
                                                    <?=e($k['title'])?>
                                                </a>
                                            <?php else: ?>
                                                <?=e($k['title'])?>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                                            <span class="badge badge-blue" style="font-size:10.5px;"><?=e($k['category'])?></span>
                                            <?php if (!empty($k['year'])): ?>
                                                <span style="font-size:11px;color:var(--text-muted);"><?=e($k['year'])?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?=$isS?'badge-green':'badge-purple'?>" style="font-size:10px;margin-bottom:2px;">
                                            <?=$isS?'Siswa':'Guru'?>
                                        </span>
                                        <div style="font-weight:700;font-size:13px;"><?=e($k['author_name'])?></div>
                                        <?php if (!empty($k['author_class'])): ?>
                                            <small style="color:var(--text-muted);display:block;"><?=e($k['author_class'])?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:11.5px;font-weight:700;text-transform:uppercase;color:#475569;margin-bottom:3px;">
                                            <?=e($k['media_type'])?>
                                        </div>
                                        <a href="<?=e($k['drive_url'])?>" target="_blank" rel="noopener noreferrer" style="font-size:11.5px;color:var(--primary);display:inline-flex;align-items:center;gap:3px;text-decoration:none;" title="Buka tautan asli di Google Drive">
                                            <?=icon('external-link', '', 12)?> Buka Google Drive ↗
                                        </a>
                                    </td>
                                    <td>
                                        <?php if ($k['status'] === 'published'): ?>
                                            <span class="badge badge-green" style="font-size:11px;font-weight:700;">✓ Disetujui &amp; Terbit</span>
                                        <?php elseif ($k['status'] === 'pending'): ?>
                                            <span class="badge badge-orange" style="font-size:11px;font-weight:700;background:#fef3c7;color:#b45309;border:1px solid #fcd34d;">⏳ Menunggu Verifikasi</span>
                                            <small style="display:block;color:var(--text-muted);font-size:10.5px;margin-top:3px;">Sedang dicek oleh admin</small>
                                        <?php elseif ($k['status'] === 'rejected'): ?>
                                            <span class="badge badge-red" style="font-size:11px;font-weight:700;">✕ Perlu Revisi</span>
                                            <?php if (!empty($k['admin_note'])): ?>
                                                <div style="font-size:11px;color:#b91c1c;margin-top:4px;line-height:1.35;background:#fef2f2;padding:6px 8px;border-radius:6px;border:1px solid #fecaca;">
                                                    <b>Catatan Admin:</b><br><?=e($k['admin_note'])?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge" style="background:#64748b;color:#fff;font-size:11px;">Draf</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                            <?php if ($isPub): ?>
                                                <a href="<?=u('index.php?page=karya_print_qr&id='.$k['id'])?>" target="_blank" class="btn btn-outline-primary btn-sm" style="padding:4px 7px;" title="Cetak Lembar QR Code">
                                                    <?=icon('print', '', 13)?>
                                                </a>
                                                <a href="<?=u('index.php?page=karya_detail&id='.$k['id'])?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="padding:4px 7px;" title="Lihat di Galeri Publik">
                                                    <?=icon('eye', '', 13)?>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?=u('index.php?page=member_karya&edit='.$k['id'].'#member_karya_form')?>" class="btn btn-outline-primary btn-sm" style="padding:4px 7px;" title="Edit Karya">
                                                <?=icon('edit', '', 13)?>
                                            </a>
                                            <a href="<?=u('index.php?page=member_karya&delete='.$k['id'])?>" class="btn btn-danger btn-sm" style="padding:4px 7px;" data-confirm="Yakin ingin menghapus karya ini?" title="Hapus Karya">
                                                <?=icon('trash', '', 13)?>
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
        <?php
    });
    exit;
}

