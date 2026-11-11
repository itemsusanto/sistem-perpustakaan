<?php
// Enhanced Session Configuration & Security Cookie Directives
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
               || (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    session_set_cookie_params([
        'lifetime' => 0, // Cookie expires when the browser is closed
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true, // Guard against XSS session cookie theft
        'samesite' => 'Lax' // Guard against CSRF attacks
    ]);
    session_start();
}

// HTTP Security Headers
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
date_default_timezone_set('Asia/Jakarta');

// Auto-detect base directory for localhost and local network / LAN IP access
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$base = rtrim($scriptDir, '/');
if ($base === '' || $base === '.') $base = '';
define('BASE', $base);

// Server Local Network / LAN IP Resolver
function get_server_lan_ip(): string {
    $ip = '';
    if (!empty($_SERVER['SERVER_ADDR']) && $_SERVER['SERVER_ADDR'] !== '127.0.0.1' && $_SERVER['SERVER_ADDR'] !== '::1') {
        $ip = $_SERVER['SERVER_ADDR'];
    }
    if (empty($ip)) {
        $hostIp = @gethostbyname(@gethostname());
        if ($hostIp && $hostIp !== '127.0.0.1' && $hostIp !== @gethostname()) {
            $ip = $hostIp;
        }
    }
    return !empty($ip) ? $ip : '127.0.0.1';
}

// Server LAN & HP Access URL
function get_lan_access_url(): string {
    $ip = get_server_lan_ip();
    $port = $_SERVER['SERVER_PORT'] ?? '80';
    $portSuffix = ($port && $port != '80' && $port != '443') ? (':' . $port) : '';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $basePath = defined('BASE') ? BASE : '';
    return $scheme . '://' . $ip . $portSuffix . $basePath;
}

// ==============================================================================
// Hak Cipta & Integritas Pengembang Asli : Heri Susanto
// Kontak / WhatsApp : 081362733320 (https://wa.me/6281362733320)
// DILARANG DIPERJUALBELIKAN & DILARANG MENGHAPUS IDENTITAS PENGEMBANG
// ==============================================================================
if (!defined('APP_DEVELOPER_NAME')) define('APP_DEVELOPER_NAME', 'Heri Susanto');
if (!defined('APP_DEVELOPER_LINK')) define('APP_DEVELOPER_LINK', 'https://docs.google.com/document/d/1URZkS4E3CjGKhQFFtx0HA0ehoV3V1FOdujTIv7m-eIg/edit?tab=t.0');
if (!defined('APP_DEVELOPER_TEXT')) define('APP_DEVELOPER_TEXT', 'Di Ciptakan dan dikembangkan Oleh Heri Susanto, Aplikasi ini Gratis dilarang untuk di perjual belikan');
// Dynamic External Configuration Loader (untuk Standalone Windows Installer tanpa Laragon)
$isLaragonEnv = (stripos(__DIR__, 'laragon') !== false) || (stripos($_SERVER['DOCUMENT_ROOT'] ?? '', 'laragon') !== false);
$configFile = null;
if (!$isLaragonEnv) {
    if (file_exists(__DIR__ . '/config.json')) {
        $configFile = __DIR__ . '/config.json';
    } elseif (file_exists('C:\\ProgramData\\PustakaSekolah\\config\\config.json')) {
        $configFile = 'C:\\ProgramData\\PustakaSekolah\\config\\config.json';
    }
}
$appConfig = [];
if ($configFile && file_exists($configFile)) {
    $rawConfig = file_get_contents($configFile);
    $cleanConfig = preg_replace('/^\xEF\xBB\xBF/', '', (string)$rawConfig);
    $decodedConfig = json_decode($cleanConfig, true);
    if (is_array($decodedConfig)) {
        $appConfig = $decodedConfig;
    }
}

// Database configuration
if (!defined('DB_HOST')) define('DB_HOST', $appConfig['db_host'] ?? '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', (int)($appConfig['db_port'] ?? 3306));
if (!defined('DB_NAME')) define('DB_NAME', $appConfig['db_name'] ?? 'nafisah_nasyirah_perpustakaan');
if (!defined('DB_USER')) define('DB_USER', $appConfig['db_user'] ?? 'root');
if (!defined('DB_PASS')) define('DB_PASS', (is_array($appConfig) && array_key_exists('db_pass', $appConfig)) ? $appConfig['db_pass'] : 'jj220386');

// Database connection
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Throwable $e) {
    // Attempt to auto-create database if not exists
    try {
        $pdoInit = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=utf8mb4', DB_USER, DB_PASS);
        $pdoInit->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $ex) {
        http_response_code(500);
        exit('Database error: ' . htmlspecialchars($ex->getMessage()));
    }
}

// Fast & Safe Database Readiness Check (Fase 5 - Tahap B & Fase 5.1)
function ensure_database_initialized_safely(PDO $pdo, bool $forceCheck = false): void {
    if (function_exists('verify_developer_integrity')) {
        verify_developer_integrity();
    }
    static $alreadyVerified = false;
    if (!$forceCheck && $alreadyVerified) {
        return;
    }

    // Lightweight probe query: memvalidasi 14 tabel dan seluruh kolom esensial hasil migrasi
    $schemaProbeSql = "SELECT 
        u.id, u.last_login_at, u.last_login_ip, u.entry_year,
        b.id, b.code, b.available, b.source, b.price, b.tanggal_penerimaan, b.tahun_penerimaan, b.copy_serials,
        bc.id, bc.copy_code,
        l.id, l.copy_serial,
        v.id,
        s.setting_key,
        c.id, c.certificate_no,
        am.id, am.entry_year,
        ab.id,
        la.id,
        cat.id,
        mc.id,
        n.id,
        ps.id, ps.name,
        ky.id,
        bd.id
    FROM users u, books b, book_copies bc, loans l, visits v, settings s, clearances c, 
         archived_members am, archived_books ab, login_attempts la, categories cat, member_categories mc, news n, procurement_sources ps, karya ky, buku_digital bd
    LIMIT 0";

    $schemaReady = false;
    try {
        $pdo->query($schemaProbeSql);
        $schemaReady = true;
    } catch (Throwable $e) {
        $schemaReady = false;
    }

    if ($schemaReady) {
        // Skema sudah lengkap. Periksa apakah flag system_installed sudah tercatat di settings.
        // Jika belum tercatat, tandai instalasi sebagai siap tanpa menjalankan ulang setup atau sinkronisasi stok.
        try {
            $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'system_installed' LIMIT 1");
            $val = $stmt ? $stmt->fetchColumn() : false;
            if ($val !== '1') {
                $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('system_installed', '1') ON DUPLICATE KEY UPDATE setting_value = '1'")->execute();
            }
        } catch (Throwable $e) {}

        if (function_exists('sync_missing_book_serials')) {
            sync_missing_book_serials($pdo);
        }

        $alreadyVerified = true;
        return;
    }

    // Skema belum siap atau database baru / kosong.
    // Gunakan MySQL advisory lock untuk mencegah instalasi / seeding ganda dari request konkuren.
    $lockAcquired = false;
    try {
        $lockStmt = $pdo->query("SELECT GET_LOCK('perpustakaan_init_lock', 15)");
        $lockAcquired = ($lockStmt && (int)$lockStmt->fetchColumn() === 1);
    } catch (Throwable $eLock) {
        $lockAcquired = false;
        error_log('[Perpustakaan Init] Peringatan: Gagal mengeksekusi GET_LOCK: ' . $eLock->getMessage());
    }

    if (!$lockAcquired) {
        // Gagal memperoleh lock (timeout atau error koneksi lock).
        // Cek kembali apakah skema sudah selesai disiapkan oleh proses lain saat menunggu.
        try {
            $pdo->query($schemaProbeSql);
            $alreadyVerified = true;
            return;
        } catch (Throwable $eCheckAgain) {
            // Skema memang belum siap dan lock tidak berhasil diperoleh.
            // Jangan jalankan setup tanpa lock untuk mencegah race condition / duplikasi data.
            error_log('[Perpustakaan Init] Gagal memperoleh advisory lock inisialisasi database (timeout atau batas konkuren tercapai). Inisialisasi dibatalkan secara aman.');
            if (php_sapi_name() !== 'cli') {
                http_response_code(503);
                exit('Layanan perpustakaan sedang dalam proses inisialisasi atau pemeliharaan database. Silakan muat ulang halaman beberapa saat lagi.');
            } else {
                throw new RuntimeException('Layanan perpustakaan sedang dalam proses inisialisasi database oleh proses lain (lock timeout).');
            }
        }
    }

    // Hanya worker yang berhasil memperoleh lock secara eksplisit yang dapat mengeksekusi blok di bawah
    try {
        // Double-check skema setelah lock berhasil diperoleh
        try {
            $pdo->query($schemaProbeSql);
            $alreadyVerified = true;
            return;
        } catch (Throwable $eRecheck) {}

        // Jalankan seluruh setup dan migrasi skema
        require_once __DIR__ . '/database/setup.php';
        ensure_database_ready($pdo);

        // Tandai instalasi siap
        try {
            $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('system_installed', '1') ON DUPLICATE KEY UPDATE setting_value = '1'")->execute();
        } catch (Throwable $eSet) {}

        $alreadyVerified = true;
    } finally {
        if ($lockAcquired) {
            try {
                $pdo->query("SELECT RELEASE_LOCK('perpustakaan_init_lock')");
            } catch (Throwable $eRel) {}
        }
    }
}

// Fast & safe database readiness verification
ensure_database_initialized_safely($pdo);
// MySQL 8 Compatibility: Toleransi zero-date dan strict mode agar laporan tidak error
$pdo->exec("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");

// Settings Cache & Helpers
function &get_settings_cache() {
    global $pdo;
    static $cache = null;
    if ($cache === null) {
        try {
            $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
            $cache = $stmt ? $stmt->fetchAll(PDO::FETCH_KEY_PAIR) : [];
        } catch (Throwable $e) {
            $cache = [];
        }
    }
    return $cache;
}

function get_setting($key, $default = '') {
    $cache = &get_settings_cache();
    return isset($cache[$key]) ? $cache[$key] : $default;
}

function set_setting($key, $value) {
    global $pdo;
    $cache = &get_settings_cache();
    $cache[$key] = (string)$value;
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    $stmt->execute([$key, (string)$value]);
}

// ==========================================================================
// MASTER SEKOLAH INDUK: SINGLE SOURCE OF TRUTH UNTUK SELURUH SISTEM
// ==========================================================================
function get_school_name() {
    $name = trim((string)get_setting('school_name', ''));
    if ($name === '') {
        $name = trim((string)get_setting('kop_line3', ''));
    }
    if ($name === '') {
        $name = trim((string)get_setting('navbar_subtitle', ''));
    }
    if ($name === '' || stripos($name, 'sistim') !== false || stripos($name, 'perpustakaan digital') !== false) {
        $name = 'SMKN 5 TELKOM';
    }
    return $name;
}

function get_school_short() {
    $short = trim((string)get_setting('school_short', ''));
    if ($short === '') {
        $name = get_school_name();
        // Generate abbreviation (e.g., SMKN 5 TELKOM or SMAN 1)
        if (preg_match('/^(SMK|SMA|SMP|MAN|MTSN|SD|MI)\s*(?:NEGERI\s*)?(\d+)?/i', $name, $m)) {
            $short = strtoupper($m[1]) . (!empty($m[2]) ? ' ' . $m[2] : '');
        } else {
            $words = explode(' ', $name);
            $short = count($words) > 3 ? (strtoupper(substr($words[0], 0, 4) . ' ' . $words[1])) : $name;
        }
    }
    return $short;
}

function apply_school_placeholders($text) {
    if ($text === null || $text === '') return '';
    $school = get_school_name();
    $schoolShort = get_school_short();
    $city = get_setting('report_city', 'Banda Aceh');
    $head = get_setting('report_head_name', 'Kepala Perpustakaan');
    $headNip = get_setting('report_head_nip', '-');
    $staff = get_setting('report_staff_name', 'Petugas Perpustakaan');
    $staffNip = get_setting('report_staff_nip', '-');
    $addr = get_setting('info_address', '');
    $phone = get_setting('info_phone', '');
    $email = get_setting('info_email', '');

    return str_replace(
        ['{SEKOLAH}', '{SCHOOL}', '{NAMA_SEKOLAH}', '{SEKOLAH_PENDEK}', '{KOTA}', '{CITY}', '{ALAMAT}', '{KEPALA_PERPUS}', '{NIP_KEPALA}', '{PETUGAS}', '{NIP_PETUGAS}', '{TELEPON}', '{EMAIL}'],
        [$school, $school, $school, $schoolShort, $city, $city, $addr, $head, $headNip, $staff, $staffNip, $phone, $email],
        (string)$text
    );
}

// Dynamic definitions driven by settings
if (!defined('SCHOOL')) {
    define('SCHOOL', get_school_name());
}
if (!defined('SCHOOL_TITLE')) {
    define('SCHOOL_TITLE', get_setting('school_title', SCHOOL));
}
if (!defined('SCHOOL_SUB')) {
    define('SCHOOL_SUB', get_setting('navbar_subtitle', 'Sistem Informasi Perpustakaan'));
}


// Dynamic Theme & Navbar Color Generator
function render_theme_styles() {
    $primary = get_setting('theme_primary_color', '#1474dc');
    $navbarBg = get_setting('theme_navbar_bg', '#ffffff');
    $navbarText = get_setting('theme_navbar_text', '#17324d');
    $sidebarBg = get_setting('theme_sidebar_bg', '#0c2340');

    return '<style id="app_theme_custom_styles">
:root {
  --primary: ' . htmlspecialchars($primary) . ';
  --navy: ' . htmlspecialchars($sidebarBg) . ';
}
.pub-header {
  background: ' . htmlspecialchars($navbarBg) . ' !important;
  border-bottom: 1px solid rgba(0,0,0,0.08) !important;
}
.brand-text h1 {
  color: ' . htmlspecialchars($navbarText) . ' !important;
}
.brand-text small {
  color: ' . htmlspecialchars($navbarText) . ' !important;
  opacity: 0.75;
}
.pub-nav {
  gap: 5px !important;
}
.pub-nav a {
  color: ' . htmlspecialchars($navbarText) . ' !important;
  white-space: nowrap !important;
  font-size: 13.5px !important;
  padding: 6px 10px !important;
  border-radius: 6px !important;
  line-height: 1.25 !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  transition: all 0.15s ease !important;
}
.pub-nav a:hover {
  color: #ffffff !important;
  background: rgba(255, 255, 255, 0.12) !important;
}
.pub-nav a.active {
  color: #38bdf8 !important;
  background: rgba(255, 255, 255, 0.16) !important;
  font-weight: 700 !important;
}
.btn-login-nav {
  background: ' . htmlspecialchars($primary) . ' !important;
  color: #ffffff !important;
  padding: 6px 16px !important;
  font-size: 13px !important;
  white-space: nowrap !important;
  margin-left: 6px !important;
}
.app-sidebar {
  background: ' . htmlspecialchars($sidebarBg) . ' !important;
}
.sidebar-link.active {
  background: ' . htmlspecialchars($primary) . ' !important;
}
</style>';
}

// Dynamic Member Categories Helpers
function get_member_categories() {
    global $pdo;
    static $memCatCache = null;
    if ($memCatCache !== null) {
        return $memCatCache;
    }
    try {
        $stmt = $pdo->query("SELECT * FROM member_categories ORDER BY sort_order ASC, id ASC");
        $cats = $stmt ? $stmt->fetchAll() : [];
        if (!empty($cats)) {
            $memCatCache = $cats;
            return $cats;
        }
    } catch (Throwable $e) {}

    // Fallback default
    $memCatCache = [
        ['id' => 1, 'name' => 'Teknik Informatika (RPL)', 'group_type' => 'Civitas Sekolah', 'sort_order' => 1],
        ['id' => 2, 'name' => 'Teknik Komputer & Jaringan (TKJ)', 'group_type' => 'Civitas Sekolah', 'sort_order' => 2],
        ['id' => 3, 'name' => 'Multimedia (DKV)', 'group_type' => 'Civitas Sekolah', 'sort_order' => 3],
        ['id' => 4, 'name' => 'Teknik Transmisi Telekomunikasi', 'group_type' => 'Civitas Sekolah', 'sort_order' => 4],
        ['id' => 5, 'name' => 'Siswa (Lainnya)', 'group_type' => 'Civitas Sekolah', 'sort_order' => 5],
        ['id' => 6, 'name' => 'Guru / Tenaga Pendidik', 'group_type' => 'Pendidik & Pegawai', 'sort_order' => 6],
        ['id' => 7, 'name' => 'Staf / Karyawan', 'group_type' => 'Pendidik & Pegawai', 'sort_order' => 7],
        ['id' => 8, 'name' => 'Tamu / Pihak Luar', 'group_type' => 'Umum / Eksternal', 'sort_order' => 8],
    ];
    return $memCatCache;
}

function render_member_category_options($selected = '') {
    $categories = get_member_categories();
    $grouped = [];
    $allNames = [];
    foreach ($categories as $cat) {
        $grp = !empty($cat['group_type']) ? $cat['group_type'] : 'Civitas Sekolah';
        $grouped[$grp][] = $cat;
        $allNames[] = $cat['name'];
    }

    $html = '';
    // If selected value exists but is not in current categories list, add it at the top so existing data is preserved
    if ($selected !== '' && !in_array($selected, $allNames, true)) {
        $html .= '<option value="' . e($selected) . '" selected>' . e($selected) . ' (Arsip/Khusus)</option>';
    }

    if (count($grouped) <= 1) {
        foreach ($categories as $c) {
            $val = $c['name'];
            $isSelected = ($val === $selected) ? ' selected' : '';
            $html .= '<option value="' . e($val) . '"' . $isSelected . '>' . e($val) . '</option>';
        }
    } else {
        foreach ($grouped as $groupLabel => $items) {
            $html .= '<optgroup label="' . e($groupLabel) . '">';
            foreach ($items as $c) {
                $val = $c['name'];
                $isSelected = ($val === $selected) ? ' selected' : '';
                $html .= '<option value="' . e($val) . '"' . $isSelected . '>' . e($val) . '</option>';
            }
            $html .= '</optgroup>';
        }
    }
    return $html;
}

// Helper functions
function e($str) {
    return htmlspecialchars((string)($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function u($path = '') {
    $str = (string)($path ?? '');
    if (strpos($str, 'http://') === 0 || strpos($str, 'https://') === 0 || strpos($str, '//') === 0) {
        return $str;
    }
    $cleanPath = ltrim($str, '/');
    if (empty($cleanPath)) {
        return empty(BASE) ? '/' : BASE . '/';
    }
    return (empty(BASE) ? '' : BASE) . '/' . $cleanPath;
}

// ==========================================================================
// SECURITY & SESSION PROTECTION SUITE
// ==========================================================================
define('MAX_LOGIN_ATTEMPTS', 5); // Maksimal percobaan login salah beruntun sebelum freeze
define('LOGIN_LOCKOUT_SECONDS', 900); // 15 menit durasi freeze keamanan
define('SESSION_IDLE_TIMEOUT', 3600); // 60 menit inaktivitas otomatis logout

function get_client_ip() {
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // Secara default pada instalasi LAN tanpa reverse proxy, TRUSTED_PROXIES tidak didefinisikan.
    // Seluruh header forwarded (X-Forwarded-For, HTTP_CLIENT_IP, HTTP_CF_CONNECTING_IP) diabaikan total demi mencegah pemalsuan IP.
    $trustedProxies = defined('TRUSTED_PROXIES') && is_array(TRUSTED_PROXIES) ? TRUSTED_PROXIES : [];

    if (!empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true)) {
        // Jika permintaan berasal dari reverse proxy yang terpercaya:
        // 1. Periksa X-Real-IP jika reverse proxy diset untuk mencatat IP asli klien secara langsung
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $realIp = trim($_SERVER['HTTP_X_REAL_IP']);
            if (filter_var($realIp, FILTER_VALIDATE_IP)) {
                return $realIp;
            }
        }
        // 2. Periksa Cloudflare connecting IP
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $cfIp = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
            if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
                return $cfIp;
            }
        }
        // 3. Tangani rantai X-Forwarded-For dengan aman:
        // Evaluasi dilakukan dari kanan ke kiri (hop terdekat dari proxy yang dipercaya)
        // untuk mencegah penyerang menyisipkan IP palsu pada bagian awal header (leftmost header spoofing).
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $chain = array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
            for ($i = count($chain) - 1; $i >= 0; $i--) {
                $candidate = $chain[$i];
                if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                    if (!in_array($candidate, $trustedProxies, true)) {
                        return $candidate;
                    }
                }
            }
        }
    }

    if (filter_var($remoteAddr, FILTER_VALIDATE_IP)) {
        return $remoteAddr;
    }
    return '127.0.0.1';
}

function get_browser_name($ua = null) {
    $userAgent = $ua ?? ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Device');
    if (stripos($userAgent, 'Edg') !== false) return 'Microsoft Edge';
    if (stripos($userAgent, 'Chrome') !== false) return 'Google Chrome';
    if (stripos($userAgent, 'Firefox') !== false) return 'Mozilla Firefox';
    if (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false) return 'Apple Safari';
    if (stripos($userAgent, 'Opera') !== false || stripos($userAgent, 'OPR') !== false) return 'Opera';
    if (stripos($userAgent, 'Trident') !== false || stripos($userAgent, 'MSIE') !== false) return 'Internet Explorer';
    return 'Web Browser / Mobile';
}

function check_session_security() {
    if (empty($_SESSION['user'])) {
        return;
    }

    // 1. Session Hijacking Detection via User-Agent Fingerprint
    $currentUaHash = md5($_SERVER['HTTP_USER_AGENT'] ?? 'none');
    if (isset($_SESSION['ua_hash']) && $_SESSION['ua_hash'] !== $currentUaHash) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        flash('Terdeteksi perubahan browser/perangkat yang tidak dikenal. Sesi dihentikan demi keamanan.', 'error');
        header('Location: ' . u('index.php?page=login'));
        exit;
    }

    // 2. Inactivity Idle Timeout (Default 60 menit)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_IDLE_TIMEOUT)) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        flash('Sesi login Anda telah berakhir karena tidak ada aktivitas selama 60 menit. Demi keamanan, silakan login kembali.', 'warning');
        header('Location: ' . u('index.php?page=login'));
        exit;
    }

    // Perbarui waktu aktivitas terakhir
    $_SESSION['last_activity'] = time();
}

function get_login_failed_attempts($username, $ip) {
    global $pdo;
    $window = time() - LOGIN_LOCKOUT_SECONDS;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE (username = ? OR ip_address = ?) AND success = 0 AND attempt_time > ?");
        $stmt->execute([$username, $ip, $window]);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function get_login_lockout_remaining($username, $ip) {
    global $pdo;
    $window = time() - LOGIN_LOCKOUT_SECONDS;
    try {
        $stmt = $pdo->prepare("SELECT attempt_time FROM login_attempts WHERE (username = ? OR ip_address = ?) AND success = 0 AND attempt_time > ? ORDER BY attempt_time DESC LIMIT 1");
        $stmt->execute([$username, $ip, $window]);
        $lastAttempt = (int)$stmt->fetchColumn();
        if ($lastAttempt > 0) {
            $elapsed = time() - $lastAttempt;
            $remaining = LOGIN_LOCKOUT_SECONDS - $elapsed;
            return $remaining > 0 ? $remaining : 0;
        }
    } catch (Throwable $e) {}
    return 0;
}

function record_login_attempt($username, $ip, $success = false) {
    global $pdo;
    try {
        $ua = mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, username, attempt_time, success, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$ip, $username, time(), $success ? 1 : 0, $ua]);

        if ($success) {
            // Hapus rekaman gagal sebelumnya untuk IP dan username ini agar counter bersih
            $pdo->prepare("DELETE FROM login_attempts WHERE (username = ? OR ip_address = ?) AND success = 0")->execute([$username, $ip]);
        }
    } catch (Throwable $e) {}
}

function clear_all_login_lockouts() {
    global $pdo;
    try {
        $pdo->exec("DELETE FROM login_attempts WHERE success = 0");
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function auth($role = null) {
    global $pdo;
    check_session_security();

    if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
        header('Location: ' . u('index.php?page=login'));
        exit;
    }

    // Re-verifikasi akun aktif dari database agar penonaktifan akun bekerja secara instan
    try {
        $uid = (int)$_SESSION['user']['id'];
        $stmt = $pdo->prepare("SELECT id, role, username, name, member_code, department, status, photo FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$uid]);
        $liveUser = $stmt->fetch();

        if (!$liveUser || $liveUser['status'] !== 'active') {
            $msg = (!$liveUser) ? 'Akun Anda tidak ditemukan atau telah dihapus.' : 'Akun Anda saat ini dinonaktifkan oleh Administrator.';
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            flash($msg, 'error');
            header('Location: ' . u('index.php?page=login'));
            exit;
        }

        // Sinkronisasi data sesi terkini dari DB
        $_SESSION['user']['role'] = $liveUser['role'];
        $_SESSION['user']['name'] = $liveUser['name'];
        $_SESSION['user']['status'] = $liveUser['status'];
        $_SESSION['user']['photo'] = $liveUser['photo'];
    } catch (Throwable $e) {
        // Toleransi jika terjadi gangguan koneksi DB sejenak
    }

    if ($role && $_SESSION['user']['role'] !== $role) {
        header('Location: ' . u());
        exit;
    }
}

function csrf() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf()) . '">';
}

function checkcsrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        exit('Token keamanan tidak valid. Silakan muat ulang halaman.');
    }
}

function flash($message, $type = 'success') {
    $cleanMessage = trim(strip_tags((string)$message));
    $_SESSION['flash'] = ['msg' => $cleanMessage, 'type' => $type];
}

function flashes() {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function indonesian_date($dateStr = null) {
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $ts = $dateStr ? strtotime($dateStr) : time();
    $dayName = $days[date('w', $ts)];
    $day = date('j', $ts);
    $month = $months[(int)date('n', $ts)];
    $year = date('Y', $ts);
    return "$dayName, $day $month $year";
}

// Built-in Pure SVG Bar Chart Generator for Dashboard Visits (Offline, Zero-CDN)
function render_visits_bar_chart($items, $svgWidth = 400, $svgHeight = 180) {
    $counts = array_column($items, 'count');
    $maxCount = !empty($counts) ? max($counts) : 0;
    $maxVal = max(6, ceil(($maxCount ?: 1) / 5) * 5);

    $gridY = [
        ['y' => 20, 'val' => $maxVal],
        ['y' => 60, 'val' => round($maxVal * 2 / 3)],
        ['y' => 100, 'val' => round($maxVal / 3)],
        ['y' => 140, 'val' => 0]
    ];

    $n = count($items);
    $leftPad = 32;
    $rightPad = 14;
    $chartW = $svgWidth - $leftPad - $rightPad;
    $colW = $chartW / max(1, $n);
    $barW = min(28, max(12, round($colW * 0.55)));

    $colors = ['#38bdf8', '#0ea5e9', '#0284c7', '#2563eb', '#1d4ed8', '#0f766e', '#0d9488'];

    $html = '<svg viewBox="0 0 ' . $svgWidth . ' ' . $svgHeight . '" width="100%" height="' . $svgHeight . '" style="overflow:visible;display:block;">';
    
    // Grid lines & Y labels
    foreach ($gridY as $g) {
        $stroke = $g['y'] === 140 ? '#cbd5e1' : '#f1f5f9';
        $sw = $g['y'] === 140 ? '1.5' : '1';
        $html .= '<line x1="' . $leftPad . '" y1="' . $g['y'] . '" x2="' . ($svgWidth - $rightPad) . '" y2="' . $g['y'] . '" stroke="' . $stroke . '" stroke-width="' . $sw . '" />';
        $html .= '<text x="' . ($leftPad - 6) . '" y="' . ($g['y'] + 3) . '" fill="#94a3b8" font-size="9" text-anchor="end">' . $g['val'] . '</text>';
    }

    // Bars
    foreach ($items as $i => $it) {
        $cx = $leftPad + ($i * $colW) + ($colW / 2);
        $val = $it['count'];
        $h = $val > 0 ? max(6, round(($val / $maxVal) * 120)) : 3;
        $y = 140 - $h;
        $color = $val > 0 ? $colors[$i % count($colors)] : '#e2e8f0';
        $textColor = $val > 0 ? '#334155' : '#94a3b8';

        $html .= '<g class="chart-bar-group" style="cursor:pointer;">';
        $html .= '<title>' . htmlspecialchars($it['full'] . ': ' . $val . ' Kunjungan') . '</title>';
        $html .= '<rect class="chart-bar-rect" x="' . ($cx - ($barW / 2)) . '" y="' . $y . '" width="' . $barW . '" height="' . $h . '" rx="4" fill="' . $color . '" style="transition:all 0.25s ease;" />';
        if ($val > 0) {
            $html .= '<text x="' . $cx . '" y="' . ($y - 4) . '" fill="' . $color . '" font-size="9" font-weight="700" text-anchor="middle">' . $val . '</text>';
        }
        $html .= '<text x="' . $cx . '" y="158" fill="' . $textColor . '" font-size="10" text-anchor="middle" font-weight="600">' . htmlspecialchars($it['label']) . '</text>';
        $html .= '</g>';
    }

    $html .= '</svg>';
    return $html;
}

// Built-in inline SVG icons for instant LAN rendering (0ms network request)
function icon($name, $class = '', $size = 20) {
    $svgs = [
        'search' => '<circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>',
        'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>',
        'books' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H18"></path>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
        'admin' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
        'card' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line>',
        'clock' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>',
        'check' => '<polyline points="20 6 9 17 4 12"></polyline>',
        'alert' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
        'arrow-left' => '<line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
        'print' => '<polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>',
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline>',
        'history' => '<circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline><path d="M3.05 11a9 9 0 0 1 .5-2m17.4 2a9 9 0 0 0-.5-2"></path>',
        'tech' => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>',
        'science' => '<line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path>',
        'social' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>',
        'language' => '<circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>',
        'religion' => '<path d="M12 2v20M7 8h10M5 14h14"></path>',
        'skill' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>',
        'chart' => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>',
        'inbox' => '<polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>',
        'excel' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="17"></line><line x1="16" y1="13" x2="8" y2="17"></line>',
        'news' => '<path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><line x1="7" y1="8" x2="13" y2="8"></line><line x1="7" y1="12" x2="13" y2="12"></line><line x1="7" y1="16" x2="10" y2="16"></line>',
        'edit' => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>',
        'trash' => '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>',
        'plus' => '<line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>',
        'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>',
        'lock' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>',
        'key' => '<path d="M21 2l-2 2m-1.5 1.5L16 7l-1.5-1.5L13 7l-2-2-4 4a6.5 6.5 0 1 0 9 9l5-5-2-2 1.5-1.5L22 7l-1-1 1.5-1.5L21 2z"></path>',
        'certificate' => '<circle cx="12" cy="8" r="6"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>',
        'file-text' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>',
        'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path>',
        'database' => '<ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>',
        'save' => '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline>',
        'settings' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>',
        'qr' => '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>',
        'video' => '<polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>',
        'external-link' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line>',
        'award' => '<circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>',
        'drive' => '<path d="M19 13l-4-7h-6l4 7z"></path><path d="M5 21l4-7h6l-4 7z"></path><path d="M2 14l3.5-6.5 3.5 6.5-3.5 6.5z"></path>',
        'copy' => '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>',
        'book-open' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>',
    ];

    $inner = $svgs[$name] ?? $svgs['book'];
    $extraClass = $class ? ' ' . htmlspecialchars($class) : '';
    return '<svg class="icon' . $extraClass . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $inner . '</svg>';
}

// Convert Google Drive or YouTube link to responsive embed preview URL if possible
function get_drive_embed_url($url) {
    if (empty($url)) return null;
    $url = trim($url);

    // Google Drive File (Video / Document)
    if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
        return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
    }
    if (preg_match('/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/i', $url, $m)) {
        return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
    }
    // Google Drive Folder
    if (preg_match('/drive\.google\.com\/drive\/folders\/([a-zA-Z0-9_-]+)/i', $url, $m)) {
        return 'https://drive.google.com/embeddedfolderview?id=' . $m[1] . '#grid';
    }
    // YouTube Video
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    return null;
}

// Resolver for Karya Thumbnail / Cover
function get_karya_cover_url($karyaOrThumb) {
    $thumb = '';
    if (is_array($karyaOrThumb)) {
        $thumb = trim((string)($karyaOrThumb['thumbnail'] ?? ''));
    } else {
        $thumb = trim((string)$karyaOrThumb);
    }
    if (!empty($thumb)) {
        if (strpos($thumb, 'assets/') === 0 || strpos($thumb, 'http://') === 0 || strpos($thumb, 'https://') === 0) {
            return u($thumb);
        }
        if (file_exists(__DIR__ . '/uploads/' . $thumb)) {
            return u('uploads/' . $thumb);
        }
        if (file_exists(__DIR__ . '/uploads/karya/' . $thumb)) {
            return u('uploads/karya/' . $thumb);
        }
        if (file_exists(__DIR__ . '/' . $thumb)) {
            return u($thumb);
        }
    }
    return null;
}

// Resolver for Buku Digital Cover
function get_buku_digital_cover_url($bukuOrCover) {
    $thumb = '';
    if (is_array($bukuOrCover)) {
        $thumb = trim((string)($bukuOrCover['cover_image'] ?? ''));
    } else {
        $thumb = trim((string)$bukuOrCover);
    }
    if (!empty($thumb)) {
        if (strpos($thumb, 'assets/') === 0 || strpos($thumb, 'http://') === 0 || strpos($thumb, 'https://') === 0) {
            return u($thumb);
        }
        if (file_exists(__DIR__ . '/uploads/' . $thumb)) {
            return u('uploads/' . $thumb);
        }
        if (file_exists(__DIR__ . '/uploads/buku_digital/' . $thumb)) {
            return u('uploads/buku_digital/' . $thumb);
        }
        if (file_exists(__DIR__ . '/' . $thumb)) {
            return u($thumb);
        }
    }
    return null;
}

// Generate official clearance certificate number with customizable school prefix/template
function generate_clearance_number($nextId = null) {
    global $pdo;
    if ($nextId === null) {
        try {
            $nextId = ((int)$pdo->query("SELECT MAX(id) FROM clearances")->fetchColumn()) + 1;
        } catch (Throwable $e) {
            $nextId = 1;
        }
    }
    $romans = [1=>'I',2=>'II',3=>'III',4=>'IV',5=>'V',6=>'VI',7=>'VII',8=>'VIII',9=>'IX',10=>'X',11=>'XI',12=>'XII'];
    $romanMonth = $romans[(int)date('n')];
    $year = date('Y');
    $padNo = str_pad($nextId, 4, '0', STR_PAD_LEFT);

    $format = get_setting('clearance_no_format', '421.5/PERPUS-{SEKOLAH_PENDEK}/BP/{ROMAN}/{YEAR}/{NO}');
    if (trim($format) === '') {
        $format = '421.5/PERPUS-{SEKOLAH_PENDEK}/BP/{ROMAN}/{YEAR}/{NO}';
    }

    if (strpos($format, '{') === false) {
        return rtrim($format, '/') . '/' . $romanMonth . '/' . $year . '/' . $padNo;
    }

    return str_replace(
        ['{ROMAN}', '{YEAR}', '{NO}', '{ID}', '{SEKOLAH}', '{SEKOLAH_PENDEK}', '{SCHOOL}'],
        [$romanMonth, $year, $padNo, $nextId, get_school_name(), get_school_short(), get_school_name()],
        $format
    );
}

// Render official customizable letterhead (Kop Surat Resmi A4)
function render_kop_surat($context = 'clearance') {
    $mode = get_setting('kop_mode', 'custom');
    $kopImage = get_setting('kop_image', 'assets/img/kop_smkn5.png');

    if ($mode === 'image' && !empty($kopImage)) {
        return '<div class="clearance-kop-banner" style="margin-bottom:8px;text-align:center;">
            <img src="' . u($kopImage) . '" alt="Kop Surat Resmi" style="width:100%;max-width:100%;height:auto;display:block;margin:0 auto;">
        </div>';
    }

    $showLeft = get_setting('kop_show_logo_left', '1') === '1';
    $showRight = get_setting('kop_show_logo_right', '1') === '1';
    $logoLeft = get_setting('kop_logo_left', 'assets/img/logo_aceh_pancacita.png');
    $logoRight = get_setting('kop_logo_right', get_setting('navbar_logo', 'assets/img/logo_smkn5_telkom.png'));

    $line1 = get_setting('kop_line1', 'PEMERINTAH ACEH');
    $line2 = get_setting('kop_line2', 'DINAS PENDIDIKAN');
    $line3 = get_setting('kop_line3', get_school_name());
    $line4 = get_setting('kop_line4', get_setting('info_address', 'JL. Stadion H. Dimurtala No.5 Lampineung kota Banda Aceh Kode Pos 23125'));
    $contactDefault = 'Telp. ' . get_setting('info_phone', '(0651) 7552314') . ' Email: ' . get_setting('info_email', 'smkn5telkombandaaceh@gmail.com');
    $line5 = get_setting('kop_line5', $contactDefault);

    ob_start();
    ?>
    <div class="clearance-kop-custom" style="width:100%;margin-bottom:12px;font-family:'Times New Roman', Times, serif, Segoe UI, sans-serif;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <?php if ($showLeft && !empty($logoLeft)): ?>
                <div style="width:80px;min-width:80px;height:80px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <img src="<?=u($logoLeft)?>" alt="Logo Instansi" style="max-width:80px;max-height:80px;width:auto;height:auto;object-fit:contain;display:block;">
                </div>
            <?php elseif ($showRight && !empty($logoRight)): ?>
                <div style="width:80px;min-width:80px;flex-shrink:0;"></div>
            <?php endif; ?>

            <div style="flex:1;text-align:center;color:#000;padding:0 6px;">
                <?php if ($line1 !== ''): ?>
                    <div style="font-size:13.5pt;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#000;line-height:1.2;"><?=e($line1)?></div>
                <?php endif; ?>
                <?php if ($line2 !== ''): ?>
                    <div style="font-size:14pt;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;line-height:1.2;margin-top:1px;"><?=e($line2)?></div>
                <?php endif; ?>
                <?php if ($line3 !== ''): ?>
                    <div style="font-size:16pt;font-weight:900;letter-spacing:0.8px;text-transform:uppercase;color:#000;line-height:1.25;margin:2px 0;"><?=e($line3)?></div>
                <?php endif; ?>
                <?php if ($line4 !== ''): ?>
                    <div style="font-size:9.5pt;font-weight:400;color:#1e293b;line-height:1.3;margin-top:2px;letter-spacing:0.1px;"><?=e($line4)?></div>
                <?php endif; ?>
                <?php if ($line5 !== ''): ?>
                    <div style="font-size:8.5pt;font-weight:400;color:#1e293b;line-height:1.3;letter-spacing:0.1px;"><?=e($line5)?></div>
                <?php endif; ?>
            </div>

            <?php if ($showRight && !empty($logoRight)): ?>
                <div style="width:80px;min-width:80px;height:80px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <img src="<?=u($logoRight)?>" alt="Logo Sekolah" style="max-width:80px;max-height:80px;width:auto;height:auto;object-fit:contain;display:block;">
                </div>
            <?php elseif ($showLeft && !empty($logoLeft)): ?>
                <div style="width:80px;min-width:80px;flex-shrink:0;"></div>
            <?php endif; ?>
        </div>

        <div style="margin-top:8px;margin-bottom:12px;border-top:3px solid #000;position:relative;">
            <div style="border-top:1px solid #000;margin-top:2px;"></div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Generate standard ISO/IEC 15417 Code 128 Barcode (100% offline & readable by all scanners)
function generate_barcode_svg($code, $width = 220, $height = 40, $showText = false) {
    static $c128_patterns = [
        0=>"212222", 1=>"222122", 2=>"222221", 3=>"121223", 4=>"121322", 5=>"131222", 6=>"122213", 7=>"122312", 8=>"132212", 9=>"221213",
        10=>"221312", 11=>"231212", 12=>"112232", 13=>"122132", 14=>"122231", 15=>"113222", 16=>"123122", 17=>"123221", 18=>"223211", 19=>"221132",
        20=>"221231", 21=>"213212", 22=>"223112", 23=>"312131", 24=>"311222", 25=>"321122", 26=>"321221", 27=>"312212", 28=>"322112", 29=>"322211",
        30=>"212123", 31=>"212321", 32=>"232121", 33=>"111323", 34=>"131123", 35=>"131321", 36=>"112313", 37=>"132113", 38=>"132311", 39=>"211313",
        40=>"231113", 41=>"231311", 42=>"112133", 43=>"112331", 44=>"132131", 45=>"113123", 46=>"113321", 47=>"133121", 48=>"313121", 49=>"211331",
        50=>"231131", 51=>"213113", 52=>"213311", 53=>"213131", 54=>"311123", 55=>"311321", 56=>"331121", 57=>"312113", 58=>"312311", 59=>"332111",
        60=>"314111", 61=>"221411", 62=>"431111", 63=>"111224", 64=>"111422", 65=>"121124", 66=>"121421", 67=>"141122", 68=>"141221", 69=>"112214",
        70=>"112412", 71=>"122114", 72=>"122411", 73=>"142112", 74=>"142211", 75=>"241211", 76=>"221114", 77=>"413111", 78=>"241112", 79=>"134111",
        80=>"111242", 81=>"121142", 82=>"121241", 83=>"114212", 84=>"124112", 85=>"124211", 86=>"411212", 87=>"421112", 88=>"421211", 89=>"212141",
        90=>"214121", 91=>"412121", 92=>"111143", 93=>"111341", 94=>"131141", 95=>"114113", 96=>"114311", 97=>"411113", 98=>"411311", 99=>"113141",
        100=>"114131", 101=>"311141", 102=>"411131", 103=>"211412", 104=>"211214", 105=>"211232", 106=>"2331112"
    ];

    $text = (string)$code;
    $len = strlen($text);
    if ($len === 0) return '';

    // Code 128 Subset B encoding
    $checksum = 104; // Start B
    $patterns = [$c128_patterns[104]];
    for ($i = 0; $i < $len; $i++) {
        $c = ord($text[$i]);
        $val = $c - 32;
        if ($val < 0 || $val > 95) $val = 0;
        $checksum += $val * ($i + 1);
        $patterns[] = $c128_patterns[$val];
    }
    $checksum = $checksum % 103;
    $patterns[] = $c128_patterns[$checksum];
    $patterns[] = $c128_patterns[106]; // Stop pattern
    $patternStr = implode('', $patterns);

    $totalModules = 0;
    for ($i = 0; $i < strlen($patternStr); $i++) {
        $totalModules += (int)$patternStr[$i];
    }

    $quietZone = 10;
    $fullModules = $totalModules + ($quietZone * 2);
    $scale = $width / $fullModules;
    $x = $quietZone * $scale;

    $barHeight = $showText ? ($height - 13) : ($height - 4);
    $rects = '<rect width="100%" height="100%" fill="#ffffff" rx="2"/>';
    for ($i = 0; $i < strlen($patternStr); $i++) {
        $w = (int)$patternStr[$i] * $scale;
        if ($i % 2 === 0) {
            $rects .= '<rect x="' . round($x, 2) . '" y="2" width="' . round($w, 2) . '" height="' . $barHeight . '" fill="#000000"/>';
        }
        $x += $w;
    }

    if ($showText) {
        $rects .= '<text x="50%" y="' . ($height - 2) . '" text-anchor="middle" font-family="ui-monospace, SFMono-Regular, Consolas, monospace" font-size="10" font-weight="700" fill="#000000" letter-spacing="1.5">* ' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8') . ' *</text>';
    }

    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="' . $width . '" height="' . $height . '" class="barcode-svg" style="background:#fff;border-radius:3px;display:block;" shape-rendering="crispEdges">' . $rects . '</svg>';
}

// Full ISO/IEC 18004 Standard QR Code Generator (100% Offline & Pure PHP)
class AppQrCode {
    const MODE_8BIT_BYTE = 4;
    const EC_L = 1;
    const EC_M = 0;
    const EC_Q = 3;
    const EC_H = 2;

    private static $GF_EXP = null;
    private static $GF_LOG = null;

    private static function initGF() {
        if (self::$GF_EXP !== null) return;
        self::$GF_EXP = array_fill(0, 512, 0);
        self::$GF_LOG = array_fill(0, 256, 0);
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$GF_EXP[$i] = $x;
            self::$GF_LOG[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) $x ^= 0x11d;
        }
        for ($i = 255; $i < 512; $i++) {
            self::$GF_EXP[$i] = self::$GF_EXP[$i - 255];
        }
    }

    private static function glog($n) {
        self::initGF();
        if ($n < 1) throw new Exception("glog($n)");
        return self::$GF_LOG[$n];
    }

    private static function gexp($n) {
        self::initGF();
        while ($n < 0) $n += 255;
        while ($n >= 256) $n -= 255;
        return self::$GF_EXP[$n];
    }

    private static function polyMultiply($p1, $p2) {
        $len1 = count($p1);
        $len2 = count($p2);
        $res = array_fill(0, $len1 + $len2 - 1, 0);
        for ($i = 0; $i < $len1; $i++) {
            for ($j = 0; $j < $len2; $j++) {
                $res[$i + $j] ^= self::gexp(self::glog($p1[$i]) + self::glog($p2[$j]));
            }
        }
        return $res;
    }

    private static function polyMod($p, $gen) {
        $pLen = count($p);
        $gLen = count($gen);
        $diff = $pLen - $gLen;
        if ($diff < 0) return $p;
        $res = $p;
        for ($i = 0; $i <= $diff; $i++) {
            $coef = $res[$i];
            if ($coef !== 0) {
                $logC = self::glog($coef);
                for ($j = 0; $j < $gLen; $j++) {
                    $res[$i + $j] ^= self::gexp(self::glog($gen[$j]) + $logC);
                }
            }
        }
        return array_slice($res, $pLen - $gLen + 1);
    }

    private static function getErrorCorrectPolynomial($ecCount) {
        $p = [1];
        for ($i = 0; $i < $ecCount; $i++) {
            $p = self::polyMultiply($p, [1, self::gexp($i)]);
        }
        return $p;
    }

    // RS Block Table: [version][ec_level] => array of [block_count, total_count, data_count]
    private static $RS_BLOCK_TABLE = [
        1 => [
            self::EC_L => [[1, 26, 19]],
            self::EC_M => [[1, 26, 16]],
            self::EC_Q => [[1, 26, 13]],
            self::EC_H => [[1, 26, 9]],
        ],
        2 => [
            self::EC_L => [[1, 44, 34]],
            self::EC_M => [[1, 44, 28]],
            self::EC_Q => [[1, 44, 22]],
            self::EC_H => [[1, 44, 16]],
        ],
        3 => [
            self::EC_L => [[1, 70, 55]],
            self::EC_M => [[1, 70, 44]],
            self::EC_Q => [[2, 35, 17]],
            self::EC_H => [[2, 35, 13]],
        ],
        4 => [
            self::EC_L => [[1, 100, 80]],
            self::EC_M => [[2, 50, 32]],
            self::EC_Q => [[2, 50, 24]],
            self::EC_H => [[4, 25, 9]],
        ],
        5 => [
            self::EC_L => [[1, 134, 108]],
            self::EC_M => [[2, 67, 43]],
            self::EC_Q => [[2, 33, 15], [2, 34, 16]],
            self::EC_H => [[2, 33, 11], [2, 34, 12]],
        ],
        6 => [
            self::EC_L => [[2, 86, 68]],
            self::EC_M => [[4, 43, 27]],
            self::EC_Q => [[4, 43, 19]],
            self::EC_H => [[4, 43, 15]],
        ],
        7 => [
            self::EC_L => [[2, 98, 78]],
            self::EC_M => [[4, 49, 31]],
            self::EC_Q => [[2, 32, 14], [4, 33, 15]],
            self::EC_H => [[4, 39, 13], [1, 40, 14]],
        ],
        8 => [
            self::EC_L => [[2, 121, 97]],
            self::EC_M => [[2, 60, 38], [2, 61, 39]],
            self::EC_Q => [[4, 40, 18], [2, 41, 19]],
            self::EC_H => [[4, 40, 14], [2, 41, 15]],
        ],
        9 => [
            self::EC_L => [[2, 146, 116]],
            self::EC_M => [[3, 58, 36], [2, 59, 37]],
            self::EC_Q => [[4, 36, 16], [4, 37, 17]],
            self::EC_H => [[4, 36, 12], [4, 37, 13]],
        ],
        10 => [
            self::EC_L => [[2, 86, 68], [2, 87, 69]],
            self::EC_M => [[4, 69, 43], [1, 70, 44]],
            self::EC_Q => [[6, 43, 19], [2, 44, 20]],
            self::EC_H => [[6, 43, 15], [2, 44, 16]],
        ],
        11 => [
            self::EC_L => [[4, 101, 81]],
            self::EC_M => [[1, 80, 50], [4, 81, 51]],
            self::EC_Q => [[4, 50, 22], [4, 51, 23]],
            self::EC_H => [[3, 36, 12], [8, 37, 13]],
        ],
        12 => [
            self::EC_L => [[2, 116, 92], [2, 117, 93]],
            self::EC_M => [[6, 58, 36], [2, 59, 37]],
            self::EC_Q => [[4, 46, 20], [6, 47, 21]],
            self::EC_H => [[7, 42, 14], [4, 43, 15]],
        ],
        13 => [
            self::EC_L => [[4, 133, 107]],
            self::EC_M => [[8, 59, 37], [1, 60, 38]],
            self::EC_Q => [[8, 44, 20], [4, 45, 21]],
            self::EC_H => [[12, 33, 11], [4, 34, 12]],
        ],
        14 => [
            self::EC_L => [[3, 145, 115], [1, 146, 116]],
            self::EC_M => [[4, 64, 40], [5, 65, 41]],
            self::EC_Q => [[11, 36, 16], [5, 37, 17]],
            self::EC_H => [[11, 36, 12], [5, 37, 13]],
        ]
    ];

    private static $MAX_DATA_BYTES = [
        1  => [19, 16, 13, 9],
        2  => [34, 28, 22, 16],
        3  => [55, 44, 34, 26],
        4  => [80, 64, 48, 36],
        5  => [108, 86, 62, 46],
        6  => [136, 108, 76, 60],
        7  => [156, 124, 88, 66],
        8  => [194, 154, 110, 86],
        9  => [232, 182, 132, 100],
        10 => [274, 216, 154, 122],
        11 => [324, 254, 180, 140],
        12 => [370, 290, 206, 158],
        13 => [428, 334, 244, 180],
        14 => [461, 365, 261, 197],
    ];

    private static $ALIGN_PATTERNS = [
        1 => [],
        2 => [6, 18],
        3 => [6, 22],
        4 => [6, 26],
        5 => [6, 30],
        6 => [6, 34],
        7 => [6, 22, 38],
        8 => [6, 24, 42],
        9 => [6, 26, 46],
        10 => [6, 28, 50],
        11 => [6, 30, 54],
        12 => [6, 32, 58],
        13 => [6, 34, 62],
        14 => [6, 26, 46, 66],
    ];

    public static function getMinVersion($byteCount, $ecLevel) {
        $idx = 0;
        if ($ecLevel === self::EC_M) $idx = 1;
        elseif ($ecLevel === self::EC_Q) $idx = 2;
        elseif ($ecLevel === self::EC_H) $idx = 3;

        for ($v = 1; $v <= 14; $v++) {
            if (self::$MAX_DATA_BYTES[$v][$idx] >= $byteCount) {
                return $v;
            }
        }
        return 14;
    }

    public static function encodeMatrix($text, $ecLevel = self::EC_M) {
        self::initGF();
        $text = (string)$text;
        $byteLen = strlen($text);

        $version = self::getMinVersion($byteLen, $ecLevel);
        $totalModules = 17 + ($version * 4);

        $rsBlocksDef = self::$RS_BLOCK_TABLE[$version][$ecLevel];
        $totalDataBytes = 0;
        $blocks = [];
        foreach ($rsBlocksDef as $def) {
            $count = $def[0];
            $totalCount = $def[1];
            $dataCount = $def[2];
            for ($k = 0; $k < $count; $k++) {
                $blocks[] = ['total' => $totalCount, 'data' => $dataCount];
                $totalDataBytes += $dataCount;
            }
        }

        $bits = '0100'; // 8-bit byte mode
        $lenBits = ($version < 10) ? 8 : 16;
        $bits .= str_pad(decbin($byteLen), $lenBits, '0', STR_PAD_LEFT);
        for ($i = 0; $i < $byteLen; $i++) {
            $bits .= str_pad(decbin(ord($text[$i])), 8, '0', STR_PAD_LEFT);
        }

        $maxBits = $totalDataBytes * 8;
        $rem = $maxBits - strlen($bits);
        $termLen = min(4, $rem);
        if ($termLen > 0) $bits .= str_repeat('0', $termLen);

        if (strlen($bits) % 8 !== 0) {
            $bits .= str_repeat('0', 8 - (strlen($bits) % 8));
        }

        $padBytes = ['11101100', '00010001'];
        $pIdx = 0;
        while (strlen($bits) < $maxBits) {
            $bits .= $padBytes[$pIdx % 2];
            $pIdx++;
        }

        $dataCodewords = [];
        for ($i = 0; $i < strlen($bits); $i += 8) {
            $dataCodewords[] = bindec(substr($bits, $i, 8));
        }

        $offset = 0;
        $blockData = [];
        $blockEc = [];
        $maxDataLen = 0;
        $maxEcLen = 0;

        foreach ($blocks as $blk) {
            $dCount = $blk['data'];
            $ecCount = $blk['total'] - $dCount;
            $d = array_slice($dataCodewords, $offset, $dCount);
            $offset += $dCount;

            $gen = self::getErrorCorrectPolynomial($ecCount);
            $msg = array_merge($d, array_fill(0, $ecCount, 0));
            $ec = self::polyMod($msg, $gen);

            $blockData[] = $d;
            $blockEc[] = $ec;
            $maxDataLen = max($maxDataLen, $dCount);
            $maxEcLen = max($maxEcLen, $ecCount);
        }

        $finalCodewords = [];
        for ($i = 0; $i < $maxDataLen; $i++) {
            foreach ($blockData as $bd) {
                if ($i < count($bd)) {
                    $finalCodewords[] = $bd[$i];
                }
            }
        }
        for ($i = 0; $i < $maxEcLen; $i++) {
            foreach ($blockEc as $be) {
                if ($i < count($be)) {
                    $finalCodewords[] = $be[$i];
                }
            }
        }

        $allBits = '';
        foreach ($finalCodewords as $cw) {
            $allBits .= str_pad(decbin($cw), 8, '0', STR_PAD_LEFT);
        }
        $remainderTable = [
            1 => 0, 2 => 7, 3 => 7, 4 => 7, 5 => 7, 6 => 7,
            7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 3
        ];
        $allBits .= str_repeat('0', $remainderTable[$version] ?? 0);

        $matrix = array_fill(0, $totalModules, array_fill(0, $totalModules, null));

        // 1. Finder patterns
        $placeFinder = function(&$m, $r0, $c0) use ($totalModules) {
            for ($r = -1; $r <= 7; $r++) {
                for ($c = -1; $c <= 7; $c++) {
                    $rr = $r0 + $r;
                    $cc = $c0 + $c;
                    if ($rr < 0 || $rr >= $totalModules || $cc < 0 || $cc >= $totalModules) continue;
                    if ($r >= 0 && $r <= 6 && $c >= 0 && $c <= 6) {
                        if ($r === 0 || $r === 6 || $c === 0 || $c === 6 || ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)) {
                            $m[$rr][$cc] = 1;
                        } else {
                            $m[$rr][$cc] = 0;
                        }
                    } else {
                        $m[$rr][$cc] = 0;
                    }
                }
            }
        };

        $placeFinder($matrix, 0, 0);
        $placeFinder($matrix, 0, $totalModules - 7);
        $placeFinder($matrix, $totalModules - 7, 0);

        // 2. Alignment patterns
        $alignPos = self::$ALIGN_PATTERNS[$version] ?? [];
        $numAlign = count($alignPos);
        for ($i = 0; $i < $numAlign; $i++) {
            for ($j = 0; $j < $numAlign; $j++) {
                $r = $alignPos[$i];
                $c = $alignPos[$j];
                if (($r <= 8 && $c <= 8) || ($r <= 8 && $c >= $totalModules - 9) || ($r >= $totalModules - 9 && $c <= 8)) {
                    continue;
                }
                for ($dr = -2; $dr <= 2; $dr++) {
                    for ($dc = -2; $dc <= 2; $dc++) {
                        if (abs($dr) === 2 || abs($dc) === 2 || ($dr === 0 && $dc === 0)) {
                            $matrix[$r + $dr][$c + $dc] = 1;
                        } else {
                            $matrix[$r + $dr][$c + $dc] = 0;
                        }
                    }
                }
            }
        }

        // 3. Timing patterns
        for ($i = 8; $i < $totalModules - 8; $i++) {
            if ($matrix[6][$i] === null) $matrix[6][$i] = ($i % 2 === 0) ? 1 : 0;
            if ($matrix[$i][6] === null) $matrix[$i][6] = ($i % 2 === 0) ? 1 : 0;
        }

        // 4. Dark module
        $matrix[(4 * $version) + 9][8] = 1;

        // 5. Reserve format info areas
        for ($i = 0; $i <= 8; $i++) {
            if ($matrix[8][$i] === null) $matrix[8][$i] = -1;
            if ($matrix[$i][8] === null) $matrix[$i][8] = -1;
        }
        for ($i = $totalModules - 8; $i < $totalModules; $i++) {
            if ($matrix[8][$i] === null) $matrix[8][$i] = -1;
            if ($matrix[$i][8] === null) $matrix[$i][8] = -1;
        }

        // 6. Reserve version info areas (version >= 7)
        if ($version >= 7) {
            for ($r = 0; $r < 6; $r++) {
                for ($c = 0; $c < 3; $c++) {
                    $matrix[$r][$totalModules - 11 + $c] = -1;
                    $matrix[$totalModules - 11 + $c][$r] = -1;
                }
            }
        }

        $maskCondition = function($pattern, $r, $c) {
            switch ($pattern) {
                case 0: return ($r + $c) % 2 === 0;
                case 1: return $r % 2 === 0;
                case 2: return $c % 3 === 0;
                case 3: return ($r + $c) % 3 === 0;
                case 4: return (floor($r / 2) + floor($c / 3)) % 2 === 0;
                case 5: return (($r * $c) % 2) + (($r * $c) % 3) === 0;
                case 6: return ((($r * $c) % 2) + (($r * $c) % 3)) % 2 === 0;
                case 7: return ((($r * $c) % 3) + (($r + $c) % 2)) % 2 === 0;
                default: return false;
            }
        };

        $minPenalty = PHP_INT_MAX;
        $bestMatrix = null;

        for ($mask = 0; $mask < 8; $mask++) {
            $m = $matrix;
            $bitIdx = 0;
            $bitLen = strlen($allBits);
            $col = $totalModules - 1;
            $up = true;

            while ($col > 0) {
                if ($col === 6) $col--;
                $rowRange = $up ? range($totalModules - 1, 0) : range(0, $totalModules - 1);
                foreach ($rowRange as $row) {
                    for ($c = 0; $c < 2; $c++) {
                        $currCol = $col - $c;
                        if ($m[$row][$currCol] === null) {
                            $b = ($bitIdx < $bitLen) ? (int)$allBits[$bitIdx] : 0;
                            $bitIdx++;
                            $invert = $maskCondition($mask, $row, $currCol);
                            $m[$row][$currCol] = $b ^ ($invert ? 1 : 0);
                        }
                    }
                }
                $up = !$up;
                $col -= 2;
            }

            $formatData = ($ecLevel << 3) | $mask;
            $bch = $formatData << 10;
            $g = 0x537;
            for ($i = 4; $i >= 0; $i--) {
                if ($bch & (1 << ($i + 10))) {
                    $bch ^= ($g << $i);
                }
            }
            $formatBits = (($formatData << 10) | $bch) ^ 0x5412;
            $fmtStr = str_pad(decbin($formatBits), 15, '0', STR_PAD_LEFT);

            $m[8][0] = (int)$fmtStr[0];
            $m[8][1] = (int)$fmtStr[1];
            $m[8][2] = (int)$fmtStr[2];
            $m[8][3] = (int)$fmtStr[3];
            $m[8][4] = (int)$fmtStr[4];
            $m[8][5] = (int)$fmtStr[5];
            $m[8][7] = (int)$fmtStr[6];
            $m[8][8] = (int)$fmtStr[7];
            $m[7][8] = (int)$fmtStr[8];
            $m[5][8] = (int)$fmtStr[9];
            $m[4][8] = (int)$fmtStr[10];
            $m[3][8] = (int)$fmtStr[11];
            $m[2][8] = (int)$fmtStr[12];
            $m[1][8] = (int)$fmtStr[13];
            $m[0][8] = (int)$fmtStr[14];

            for ($i = 0; $i < 7; $i++) {
                $m[$totalModules - 1 - $i][8] = (int)$fmtStr[$i];
            }
            for ($i = 0; $i < 8; $i++) {
                $m[8][$totalModules - 8 + $i] = (int)$fmtStr[7 + $i];
            }

            if ($version >= 7) {
                $vData = $version << 12;
                $vg = 0x1f25;
                for ($i = 5; $i >= 0; $i--) {
                    if ($vData & (1 << ($i + 12))) {
                        $vData ^= ($vg << $i);
                    }
                }
                $vBits = ($version << 12) | $vData;
                $vStr = str_pad(decbin($vBits), 18, '0', STR_PAD_LEFT);
                for ($i = 0; $i < 18; $i++) {
                    $bit = (int)$vStr[17 - $i];
                    $r = (int)floor($i / 3);
                    $c = ($i % 3) + $totalModules - 11;
                    $m[$r][$c] = $bit;
                    $m[$c][$r] = $bit;
                }
            }

            $penalty = 0;
            for ($r = 0; $r < $totalModules; $r++) {
                $cnt = 1;
                $last = $m[$r][0];
                for ($c = 1; $c < $totalModules; $c++) {
                    if ($m[$r][$c] === $last) {
                        $cnt++;
                    } else {
                        if ($cnt >= 5) $penalty += 3 + ($cnt - 5);
                        $cnt = 1;
                        $last = $m[$r][$c];
                    }
                }
                if ($cnt >= 5) $penalty += 3 + ($cnt - 5);
            }
            for ($c = 0; $c < $totalModules; $c++) {
                $cnt = 1;
                $last = $m[0][$c];
                for ($r = 1; $r < $totalModules; $r++) {
                    if ($m[$r][$c] === $last) {
                        $cnt++;
                    } else {
                        if ($cnt >= 5) $penalty += 3 + ($cnt - 5);
                        $cnt = 1;
                        $last = $m[$r][$c];
                    }
                }
                if ($cnt >= 5) $penalty += 3 + ($cnt - 5);
            }

            for ($r = 0; $r < $totalModules - 1; $r++) {
                for ($c = 0; $c < $totalModules - 1; $c++) {
                    $clr = $m[$r][$c];
                    if ($clr === $m[$r+1][$c] && $clr === $m[$r][$c+1] && $clr === $m[$r+1][$c+1]) {
                        $penalty += 3;
                    }
                }
            }

            if ($penalty < $minPenalty) {
                $minPenalty = $penalty;
                $bestMatrix = $m;
            }
        }

        return $bestMatrix;
    }

    public static function toSVG($text, $size = 76, $ecLevel = self::EC_M) {
        $matrix = self::encodeMatrix($text, $ecLevel);
        $n = count($matrix);
        $quiet = 4;
        $total = $n + ($quiet * 2);

        $rects = '<rect width="100%" height="100%" fill="#ffffff"/>';
        for ($r = 0; $r < $n; $r++) {
            for ($c = 0; $c < $n; $c++) {
                if ($matrix[$r][$c] === 1) {
                    $x = $c + $quiet;
                    $y = $r + $quiet;
                    $rects .= '<rect x="' . $x . '" y="' . $y . '" width="1" height="1" fill="#000000"/>';
                }
            }
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $total . ' ' . $total . '" width="' . $size . '" height="' . $size . '" class="qr-svg" style="border-radius:4px;background:#fff;display:block;" shape-rendering="crispEdges">' . $rects . '</svg>';
    }
}

// Generate standard ISO/IEC 18004 QR Code in pure SVG format
function generate_qr_svg($text, $size = 72) {
    return AppQrCode::toSVG($text, $size);
}

/**
 * Sinkronisasi Otomatis Nomor Seri Eksemplar Buku (Mencegah Nomor Seri Kosong/Hilang)
 */
function sync_missing_book_serials(PDO $pdo): int {
    try {
        $stmt = $pdo->query("SELECT id, code, quantity, copy_serials FROM books WHERE copy_serials IS NULL OR copy_serials = '' OR copy_serials = '[]'");
        $missing = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($missing)) return 0;

        $upd = $pdo->prepare("UPDATE books SET copy_serials = ? WHERE id = ?");
        $insCopy = $pdo->prepare("INSERT INTO book_copies (book_id, copy_code, status) VALUES (?, ?, 'available')");
        $checkCopy = $pdo->prepare("SELECT id FROM book_copies WHERE book_id = ? AND copy_code = ? LIMIT 1");

        $fixed = 0;
        foreach ($missing as $b) {
            $qty = max(1, (int)$b['quantity']);
            $code = trim((string)$b['code']);
            if ($code === '') continue;

            $serials = [];
            for ($i = 1; $i <= $qty; $i++) {
                $serials[] = $code . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            }
            $json = json_encode($serials, JSON_UNESCAPED_UNICODE);
            $upd->execute([$json, $b['id']]);

            foreach ($serials as $sn) {
                $checkCopy->execute([$b['id'], $sn]);
                if (!$checkCopy->fetch()) {
                    $insCopy->execute([$b['id'], $sn]);
                }
            }
            $fixed++;
        }
        return $fixed;
    } catch (Throwable $e) {
        return 0;
    }
}

// Book Cover Photo URL Resolver
function get_book_cover_url($bookOrCover) {
    $cover = '';
    if (is_array($bookOrCover)) {
        $cover = trim((string)($bookOrCover['cover_image'] ?? ($bookOrCover['book_cover_image'] ?? ($bookOrCover['photo'] ?? ''))));
    } else {
        $cover = trim((string)$bookOrCover);
    }

    if (!empty($cover)) {
        if (strpos($cover, 'assets/') === 0 || strpos($cover, 'http://') === 0 || strpos($cover, 'https://') === 0) {
            return u($cover);
        }
        if (file_exists(__DIR__ . '/uploads/' . $cover)) {
            return u('uploads/' . $cover);
        }
        if (file_exists(__DIR__ . '/uploads/books/' . $cover)) {
            return u('uploads/books/' . $cover);
        }
        if (file_exists(__DIR__ . '/' . $cover)) {
            return u($cover);
        }
    }
    return null;
}

// Handle book cover upload
function handle_book_cover_upload($fileInputName = 'cover_image', $existingCover = null) {
    if (empty($_FILES[$fileInputName]['name']) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileInputName];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        flash('Format foto buku harus JPG, JPEG, PNG, atau WEBP.', 'error');
        return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        flash('Ukuran file foto buku maksimal 5MB.', 'error');
        return false;
    }

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    $newFileName = 'book_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        if ($existingCover && strpos($existingCover, 'book_') === 0 && file_exists($uploadDir . $existingCover)) {
            @unlink($uploadDir . $existingCover);
        }
        return $newFileName;
    }

    flash('Gagal menyimpan file foto buku ke server.', 'error');
    return false;
}

// Generate styled book cover graphic or display uploaded book cover
function render_book_cover($title, $category = 'Teknologi', $author = '', $coverImage = null) {
    if (is_array($title)) {
        $book = $title;
        $title = $book['title'] ?? ($book['book_title'] ?? '');
        $category = $book['category'] ?? ($book['book_category'] ?? 'Teknologi');
        $author = $book['author'] ?? ($book['book_author'] ?? '');
        $coverImage = $book['cover_image'] ?? ($book['book_cover_image'] ?? ($book['photo'] ?? null));
    }

    $coverUrl = get_book_cover_url($coverImage);
    if ($coverUrl) {
        return '
        <div class="book-cover-render has-photo" title="' . e($title) . '">
            <div class="book-spine"></div>
            <img src="' . e($coverUrl) . '" alt="' . e($title) . '" class="book-cover-img" loading="lazy">
            <div class="book-cover-overlay"></div>
        </div>';
    }

    $colors = [
        'Teknologi' => ['#0f3d75', '#1a64c4', '#38bdf8'],
        'Sains' => ['#0e5a32', '#169954', '#4ade80'],
        'Sosial' => ['#9a3412', '#ea580c', '#fb923c'],
        'Bahasa' => ['#581c87', '#9333ea', '#c084fc'],
        'Agama' => ['#115e59', '#0d9488', '#2dd4bf'],
        'Keterampilan' => ['#854d0e', '#ca8a04', '#facc15'],
        'Komputer' => ['#0f3d75', '#1a64c4', '#38bdf8'],
    ];
    $c = $colors[$category] ?? ['#0f3d75', '#1a64c4', '#38bdf8'];
    $shortTitle = e(mb_strimwidth((string)$title, 0, 36, '...'));
    $shortAuthor = e(mb_strimwidth((string)$author, 0, 24, '...'));

    return '
    <div class="book-cover-render" style="background:linear-gradient(135deg, ' . $c[0] . ' 0%, ' . $c[1] . ' 100%);" title="' . e($title) . '">
        <div class="book-spine"></div>
        <div class="book-cover-inner">
            <span class="book-cover-cat" style="color:' . $c[2] . '">' . e(strtoupper($category)) . '</span>
            <div class="book-cover-title">' . $shortTitle . '</div>
            <div class="book-cover-author">' . $shortAuthor . '</div>
        </div>
        <div class="book-cover-accent" style="background:' . $c[2] . '"></div>
    </div>';
}

// Member Profile Photo URL Resolver
function get_member_photo_url($user) {
    if (is_array($user) && !empty($user['photo'])) {
        $photo = trim((string)$user['photo']);
        // If it starts with assets/ or http, return directly
        if (strpos($photo, 'assets/') === 0 || strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0) {
            return u($photo);
        }
        // If file exists in uploads/
        if (file_exists(__DIR__ . '/uploads/' . $photo)) {
            return u('uploads/' . $photo);
        }
        if (file_exists(__DIR__ . '/' . $photo)) {
            return u($photo);
        }
    }
    return u('assets/img/avatar_student_default.svg');
}

// Handle member photo upload
function handle_member_photo_upload($fileInputName, $existingPhoto = null) {
    if (empty($_FILES[$fileInputName]['name']) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileInputName];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        flash('Format foto harus JPG, JPEG, PNG, atau WEBP.', 'error');
        return false;
    }

    if ($file['size'] > 3 * 1024 * 1024) {
        flash('Ukuran file foto maksimal 3MB.', 'error');
        return false;
    }

    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0777, true);
    }

    $newFileName = 'member_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        if ($existingPhoto && strpos($existingPhoto, 'member_') === 0 && file_exists($uploadDir . $existingPhoto)) {
            @unlink($uploadDir . $existingPhoto);
        }
        return $newFileName;
    }

    flash('Gagal menyimpan file foto ke server.', 'error');
    return false;
}

// Auto-verify active session integrity on every request
if (!empty($_SESSION['user'])) {
    check_session_security();
}

// Auto Backup Database Harian Multi-Folder
require_once __DIR__ . '/config_backup.php';
periksa_backup_otomatis_perpustakaan();

/**
 * ==============================================================================
 * INFORMASI HAK CIPTA & LISENSI APLIKASI PERPUSTAKAAN DIGITAL
 * ==============================================================================
 * Hak Cipta & Pengembang Asli : Heri Susanto
 * Kontak / WhatsApp           : 081362733320 (https://wa.me/6281362733320)
 * Status Distribusi           : Gratis untuk Pendidikan & Sekolah
 * Ketentuan Lisensi           : DILARANG DIPERJUALBELIKAN.
 *                               Dilarang menghapus, memodifikasi, atau mengklaim
 *                               kepemilikan atas nama pengembang asli.
 * ==============================================================================
 */
if (!defined('APP_DEVELOPER_NAME')) define('APP_DEVELOPER_NAME', 'Heri Susanto');
if (!defined('APP_DEVELOPER_LINK')) define('APP_DEVELOPER_LINK', 'https://docs.google.com/document/d/1URZkS4E3CjGKhQFFtx0HA0ehoV3V1FOdujTIv7m-eIg/edit?tab=t.0');
if (!defined('APP_DEVELOPER_TEXT')) define('APP_DEVELOPER_TEXT', 'Di Ciptakan dan dikembangkan Oleh Heri Susanto, Aplikasi ini Gratis dilarang untuk di perjual belikan');

/**
 * Render HTML Atribusi Pengembang Asli (Permanen & Terproteksi)
 */
function get_developer_attribution_html($context = 'public') {
    $devName = APP_DEVELOPER_NAME;
    $devLink = function_exists('u') ? u(APP_DEVELOPER_LINK) : APP_DEVELOPER_LINK;
    $safeLink = htmlspecialchars($devLink, ENT_QUOTES, 'UTF-8');
    $safeName = htmlspecialchars($devName, ENT_QUOTES, 'UTF-8');

    if ($context === 'public') {
        return '<div class="app-dev-credit-public" id="appDevCredit" style="max-width:1200px;margin:16px auto 0;padding-top:14px;border-top:1px solid rgba(255,255,255,0.08);font-size:11.5px;color:rgba(255,255,255,0.55);text-align:center;line-height:1.6;letter-spacing:0.2px;">'
             . 'Di Ciptakan dan dikembangkan Oleh <a href="' . $safeLink . '" target="_blank" rel="noopener noreferrer" title="Buka Dokumen Profil & Lisensi Heri Susanto" style="color:#38bdf8;font-weight:700;text-decoration:none;white-space:nowrap;" onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'">' . $safeName . '</a>, Aplikasi ini Gratis dilarang untuk di perjual belikan'
             . '</div>';
    } elseif ($context === 'sidebar') {
        return '<div class="sidebar-dev-credit no-print" id="sidebarDevCredit" style="padding:14px 16px;border-top:1px solid rgba(255,255,255,0.08);font-size:10.5px;color:rgba(255,255,255,0.5);line-height:1.5;text-align:center;">'
             . 'Di Ciptakan dan dikembangkan Oleh<br>'
             . '<a href="' . $safeLink . '" target="_blank" rel="noopener noreferrer" title="Buka Dokumen Profil & Lisensi Heri Susanto" style="color:#38bdf8;font-weight:800;font-size:11.5px;white-space:nowrap;display:inline-block;margin:2px 0;text-decoration:none;">' . $safeName . '</a><br>'
             . '<span style="font-size:9.5px;color:rgba(255,255,255,0.35);display:block;margin-top:2px;line-height:1.3;">Aplikasi ini Gratis dilarang untuk di perjual belikan</span>'
             . '</div>';
    }
    return '';
}

/**
 * Core System Integrity & Copyright Verification Lock
 * Memastikan hak cipta pengembang asli (Heri Susanto) terlindungi secara kriptografis.
 * Jika file dimodifikasi atau teks nama diganti/dihapus, sistem otomatis HALT (Error 500).
 */
function verify_developer_integrity(): bool {
    static $verified = null;
    if ($verified !== null) {
        return $verified;
    }

    $expectedNameHash = '68d9c297b3d55fe895b165f3051dd69c9c330257e754bf94d52464b0d9cecae3'; // SHA-256('Heri Susanto')
    $isTampered = false;

    if (!defined('APP_DEVELOPER_NAME') || !defined('APP_DEVELOPER_TEXT')) {
        $isTampered = true;
    } else {
        if (trim((string)APP_DEVELOPER_NAME) !== 'Heri Susanto' || hash('sha256', trim((string)APP_DEVELOPER_NAME)) !== $expectedNameHash) {
            $isTampered = true;
        }
        if (stripos((string)APP_DEVELOPER_TEXT, 'Heri Susanto') === false || stripos((string)APP_DEVELOPER_TEXT, 'Gratis dilarang untuk di perjual belikan') === false) {
            $isTampered = true;
        }
    }

    if ($isTampered) {
        $verified = false;
        trigger_developer_integrity_violation();
        return false;
    }

    $verified = true;
    return true;
}

/**
 * Trigger Fatal Error & Lock Screen saat hak cipta dimodifikasi
 */
function trigger_developer_integrity_violation(): void {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>500 - System Integrity Violation</title>
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                background: #090d16;
                color: #f1f5f9;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 24px;
            }
            .violation-card {
                background: #111827;
                border: 1.5px solid #ef4444;
                border-radius: 14px;
                max-width: 620px;
                width: 100%;
                padding: 32px 28px;
                box-shadow: 0 20px 50px rgba(239, 68, 68, 0.25);
                text-align: center;
            }
            .badge {
                display: inline-block;
                background: rgba(239, 68, 68, 0.15);
                color: #f87171;
                border: 1px solid rgba(239, 68, 68, 0.4);
                padding: 6px 14px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                margin-bottom: 18px;
            }
            h1 {
                font-size: 22px;
                font-weight: 800;
                color: #ffffff;
                margin-bottom: 14px;
                line-height: 1.4;
            }
            p {
                font-size: 13.5px;
                color: #94a3b8;
                line-height: 1.7;
                margin-bottom: 18px;
                text-align: justify;
            }
            .notice-box {
                background: rgba(15, 23, 42, 0.9);
                border-left: 4px solid #ef4444;
                padding: 14px 16px;
                text-align: left;
                font-size: 13px;
                color: #cbd5e1;
                border-radius: 0 8px 8px 0;
                margin-bottom: 24px;
                line-height: 1.6;
            }
            .notice-box strong { color: #f87171; }
            .footer-note {
                font-size: 12px;
                color: #64748b;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                padding-top: 16px;
            }
        </style>
    </head>
    <body>
        <div class="violation-card">
            <span class="badge">⚠️ System Integrity Trap</span>
            <h1>Sistem Dimatikan: Pelanggaran Integritas Hak Cipta</h1>
            <p>
                Aplikasi Sistem Informasi Perpustakaan ini dilindungi oleh ketentuan lisensi dan hak cipta pengembang asli. Dilarang keras memodifikasi, menghapus, atau mengalihkan identitas pengembang asli untuk tujuan apa pun.
            </p>
            <div class="notice-box">
                <strong>Pemberitahuan Sistem:</strong><br>
                Terdeteksi modifikasi atau penghapusan pada atribusi pengembang asli (<b>Heri Susanto</b>). Seluruh modul controller, transaksi database, dan antarmuka aplikasi telah dinonaktifkan secara otomatis sampai integritas kode dipulihkan ke versi aslinya.
            </div>
            <div class="footer-note">
                Sistem Manajemen Perpustakaan Sekolah • Hak Cipta Asli: <b>Heri Susanto</b>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit(1);
}

/**
 * Script DOM Guard & Anti-Tamper Proteksi Hak Cipta (Frontend Real-Time Lock)
 */
function render_developer_guard_script() {
    ?>
    <script>
    (function() {
        var targets = ['appDevCredit', 'sidebarDevCredit'];
        var expected = 'Heri Susanto';

        function triggerClientLock() {
            if (document.getElementById('antiTamperOverlay')) return;
            var overlay = document.createElement('div');
            overlay.id = 'antiTamperOverlay';
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(15,23,42,0.98);z-index:9999999;display:flex;align-items:center;justify-content:center;padding:24px;color:#fff;text-align:center;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;';
            overlay.innerHTML = '<div style="max-width:540px;background:#111827;border:2px solid #ef4444;border-radius:14px;padding:30px 24px;box-shadow:0 15px 50px rgba(239,68,68,0.3);">'
                + '<div style="font-size:36px;margin-bottom:12px;">🔒</div>'
                + '<h2 style="font-size:20px;font-weight:800;color:#f87171;margin-bottom:10px;">Pelanggaran Hak Cipta Terdeteksi</h2>'
                + '<p style="font-size:13px;color:#94a3b8;line-height:1.6;margin-bottom:18px;">Atribut hak cipta pengembang asli (<b>Heri Susanto</b>) telah disembunyikan atau dimanipulasi. Halaman sistem ini dikunci secara otomatis demi perlindungan hak cipta.</p>'
                + '<button onclick="location.reload()" style="background:#ef4444;color:#fff;border:none;padding:10px 22px;border-radius:6px;font-weight:700;cursor:pointer;font-size:13px;">Muat Ulang Halaman</button>'
                + '</div>';
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';
        }

        function guardCredit() {
            targets.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    var style = window.getComputedStyle(el);
                    if (style.display === 'none' || style.visibility === 'hidden' || parseFloat(style.opacity) < 0.1) {
                        triggerClientLock();
                    } else if (!el.innerText || !el.innerText.includes(expected)) {
                        triggerClientLock();
                    }
                }
            });
        }

        if (typeof window !== 'undefined') {
            window.addEventListener('DOMContentLoaded', guardCredit);
            setTimeout(guardCredit, 1000);
            setInterval(guardCredit, 2500);
            try {
                targets.forEach(function(id) {
                    var el = document.getElementById(id);
                    if (el && typeof MutationObserver !== 'undefined') {
                        var obs = new MutationObserver(guardCredit);
                        obs.observe(el, { attributes: true, childList: true, subtree: true });
                    }
                });
            } catch(e) {}
        }
    })();
    </script>
    <?php
}

// Inisialisasi verifikasi integritas hak cipta langsung saat konfigurasi dimuat
verify_developer_integrity();


