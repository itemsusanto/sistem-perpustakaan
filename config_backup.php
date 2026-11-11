<?php
/**
 * config_backup.php
 * Sistem Cadangan (Backup) & Pemulihan (Restore) Database Otomatis Multi-Folder
 * Perpustakaan SMKN 5 Telkom Banda Aceh
 */

if (!defined('PERPUS_BACKUP_EXT_1')) {
    define('PERPUS_BACKUP_EXT_1', 'C:\\Backup_Perpustakaan');
}
if (!defined('PERPUS_BACKUP_EXT_2')) {
    $uDocs = 'C:\\Users\\' . get_current_user() . '\\Documents\\Backup_Perpustakaan';
    if (!is_dir($uDocs)) @mkdir($uDocs, 0777, true);
    define('PERPUS_BACKUP_EXT_2', $uDocs);
}
if (!defined('PERPUS_BACKUP_INT')) {
    define('PERPUS_BACKUP_INT', __DIR__ . DIRECTORY_SEPARATOR . 'backups');
}
if (!defined('PERPUS_BACKUP_MAX_FILES')) {
    define('PERPUS_BACKUP_MAX_FILES', 5);
}

/**
 * Mengambil daftar 3 lokasi penyimpanan folder backup aman
 */
function get_perpus_backup_dirs() {
    $dirs = [
        'eksternal_utama' => PERPUS_BACKUP_EXT_1,
        'eksternal_dokumen' => PERPUS_BACKUP_EXT_2,
        'internal_proyek' => PERPUS_BACKUP_INT
    ];
    foreach ($dirs as $d) {
        if (!is_dir($d)) @mkdir($d, 0777, true);
    }
    return $dirs;
}

/**
 * Format ukuran file ke bentuk ramah dibaca (KB / MB)
 */
function format_ukuran_file_perpus($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 1) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Deteksi lokasi binary MySQL (mysqldump & mysql CLI) di Laragon, XAMPP, atau sistem PATH
 */
function get_perpus_mysql_binaries() {
    $mysqldump = null;
    $mysql = null;

    // 1. Cek Laragon (MySQL 8 / MariaDB)
    $laragonBld = glob('C:\\laragon\\bin\\mysql\\*\\bin', GLOB_ONLYDIR);
    if (!empty($laragonBld)) {
        foreach ($laragonBld as $dir) {
            if (!$mysqldump && file_exists($dir . DIRECTORY_SEPARATOR . 'mysqldump.exe')) {
                $mysqldump = $dir . DIRECTORY_SEPARATOR . 'mysqldump.exe';
            }
            if (!$mysql && file_exists($dir . DIRECTORY_SEPARATOR . 'mysql.exe')) {
                $mysql = $dir . DIRECTORY_SEPARATOR . 'mysql.exe';
            }
        }
    }

    // 2. Cek XAMPP
    if (!$mysqldump && file_exists('C:\\xampp\\mysql\\bin\\mysqldump.exe')) {
        $mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
    }
    if (!$mysql && file_exists('C:\\xampp\\mysql\\bin\\mysql.exe')) {
        $mysql = 'C:\\xampp\\mysql\\bin\\mysql.exe';
    }

    // 3. Fallback jika ada di environment PATH
    if (!$mysqldump) $mysqldump = 'mysqldump';
    if (!$mysql) $mysql = 'mysql';

    return ['mysqldump' => $mysqldump, 'mysql' => $mysql];
}

/**
 * Generator Dump SQL Native PHP (Bekerja 100% tanpa dependensi mysqldump eksternal)
 */
function generate_native_sql_dump_perpustakaan($pdo) {
    try {
        $out = "-- ==========================================================\n";
        $out .= "-- Backup Otomatis Perpustakaan SMKN 5 Telkom Banda Aceh\n";
        $out .= "-- Waktu Pembuatan : " . date('Y-m-d H:i:s') . "\n";
        $out .= "-- Host             : localhost\n";
        $out .= "-- Database         : nafisah_nasyirah_perpustakaan\n";
        $out .= "-- ==========================================================\n\n";
        $out .= "SET FOREIGN_KEY_CHECKS = 0;\n";
        $out .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
        $out .= "SET NAMES utf8mb4;\n\n";

        $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $tbl) {
            $out .= "-- ----------------------------------------------------------\n";
            $out .= "-- Struktur tabel untuk `{$tbl}`\n";
            $out .= "-- ----------------------------------------------------------\n";
            $out .= "DROP TABLE IF EXISTS `{$tbl}`;\n";

            $cr = $pdo->query("SHOW CREATE TABLE `{$tbl}`")->fetch(PDO::FETCH_ASSOC);
            if (!empty($cr['Create Table'])) {
                $out .= $cr['Create Table'] . ";\n\n";
            }

            $rows = $pdo->query("SELECT * FROM `{$tbl}`")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $out .= "-- Dumping data untuk tabel `{$tbl}`\n";
                $cols = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $cols) . '`';

                $batch = [];
                foreach ($rows as $row) {
                    $vals = [];
                    foreach ($row as $v) {
                        if ($v === null) {
                            $vals[] = 'NULL';
                        } else {
                            $vals[] = $pdo->quote($v);
                        }
                    }
                    $batch[] = "(" . implode(", ", $vals) . ")";
                    if (count($batch) >= 100) {
                        $out .= "INSERT INTO `{$tbl}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n";
                        $batch = [];
                    }
                }
                if (!empty($batch)) {
                    $out .= "INSERT INTO `{$tbl}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n";
                }
                $out .= "\n";
            }
        }

        $out .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        return $out;
    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Menghasilkan cadangan database perpustakaan ke 3 lokasi aman + file master SQL
 */
function buat_backup_perpustakaan($alasan = 'manual') {
    global $pdo;
    $dirs = get_perpus_backup_dirs();
    $target_dir = $dirs['internal_proyek'];

    $tanggal_str = date('Y-m-d_H-i-s');
    $filename = "perpustakaan_backup_{$tanggal_str}.sql";
    $primary_file = $target_dir . DIRECTORY_SEPARATOR . $filename;

    $success = false;
    $method_used = '';

    // METODE 1: Coba mysqldump CLI (Laragon / XAMPP)
    $bins = get_perpus_mysql_binaries();
    $mysqldump_bin = $bins['mysqldump'];
    if ($mysqldump_bin) {
        $dbUser = defined('DB_USER') ? DB_USER : 'root';
        $dbPass = defined('DB_PASS') ? DB_PASS : '';
        $passOpt = ($dbPass !== '') ? ' -p' . escapeshellarg($dbPass) : '';
        $cmd = "\"{$mysqldump_bin}\" -u {$dbUser}{$passOpt} --default-character-set=utf8mb4 --single-transaction nafisah_nasyirah_perpustakaan --result-file=\"{$primary_file}\" 2>&1";
        @exec("cmd /c \"{$cmd}\"", $out, $ret);
        if ($ret === 0 && file_exists($primary_file) && filesize($primary_file) > 2000) {
            $success = true;
            $method_used = 'mysqldump';
        }
    }

    // METODE 2: Fallback ke Native PHP SQL Generator jika mysqldump gagal
    if (!$success && isset($pdo)) {
        $nativeDump = generate_native_sql_dump_perpustakaan($pdo);
        if ($nativeDump !== false && strlen($nativeDump) > 2000) {
            if (@file_put_contents($primary_file, $nativeDump) !== false) {
                $success = true;
                $method_used = 'native_php';
            }
        }
    }

    if (!$success || !file_exists($primary_file)) {
        return [
            'success' => false,
            'pesan' => 'Gagal membuat file cadangan database.'
        ];
    }

    $filesize = filesize($primary_file);
    $saved = [$primary_file];

    // Salin ke folder eksternal di luar XAMPP (C:\Backup_Perpustakaan dan Dokumen Pengguna)
    foreach ($dirs as $type => $d) {
        if ($d !== $target_dir && is_dir($d)) {
            $dest = $d . DIRECTORY_SEPARATOR . $filename;
            if (@copy($primary_file, $dest)) {
                $saved[] = $dest;
            }
        }
    }

    // SELALU perbarui file master database/nafisah_nasyirah_perpustakaan.sql di proyek
    // Sehingga jika folder perpustakaan di-copy ke komputer lain, datanya 100% mutakhir!
    $master_sql = __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'nafisah_nasyirah_perpustakaan.sql';
    if (@copy($primary_file, $master_sql)) {
        $saved[] = $master_sql;
    }

    // Catat log terakhir
    $log = [
        'last_backup_time' => time(),
        'last_backup_date' => date('Y-m-d H:i:s'),
        'filename' => $filename,
        'filesize' => $filesize,
        'keterangan' => $alasan,
        'method' => $method_used,
        'locations' => $saved
    ];
    @file_put_contents($target_dir . DIRECTORY_SEPARATOR . 'last_backup.json', json_encode($log, JSON_PRETTY_PRINT));
    @file_put_contents(PERPUS_BACKUP_EXT_1 . DIRECTORY_SEPARATOR . 'last_backup.json', json_encode($log, JSON_PRETTY_PRINT));

    // Salin juga script batch darurat ke C:\Backup_Perpustakaan jika belum ada
    $batch_files = ['KLIK_UNTUK_BACKUP.bat', 'KLIK_UNTUK_PULIHKAN.bat', 'PERBAIKI_MYSQL_CRASH.bat'];
    foreach ($batch_files as $bf) {
        $src_b = $target_dir . DIRECTORY_SEPARATOR . $bf;
        $dst_b = PERPUS_BACKUP_EXT_1 . DIRECTORY_SEPARATOR . $bf;
        if (file_exists($src_b) && (!file_exists($dst_b) || filemtime($src_b) > filemtime($dst_b))) {
            @copy($src_b, $dst_b);
        }
    }

    // Rotasi: simpan hanya 5 file .sql terakhir, hapus otomatis sisanya
    bersihkan_rotasi_backup_perpustakaan(PERPUS_BACKUP_MAX_FILES);

    return [
        'success' => true,
        'filename' => $filename,
        'filesize' => $filesize,
        'filesize_format' => format_ukuran_file_perpus($filesize),
        'method' => $method_used,
        'locations' => $saved,
        'pesan' => "Cadangan database perpustakaan berhasil dibuat dan disimpan di " . count($saved) . " lokasi aman!"
    ];
}

/**
 * Membersihkan file cadangan lama: Hanya menyimpan 5 file .sql terakhir di setiap folder backup,
 * sedangkan file-file cadangan lama sebelumnya akan terhapus otomatis.
 */
function bersihkan_rotasi_backup_perpustakaan($max_keep = PERPUS_BACKUP_MAX_FILES) {
    $dirs = get_perpus_backup_dirs();
    $total_deleted = 0;
    foreach ($dirs as $d) {
        if (!is_dir($d)) continue;
        $files = glob($d . DIRECTORY_SEPARATOR . "perpustakaan_backup_*.sql");
        if ($files && count($files) > $max_keep) {
            // Urutkan file berdasarkan waktu modifikasi terbaru ke terlama (descending)
            usort($files, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            // Hapus file yang melebihi batas simpan (hanya pertahankan 5 teratas)
            $del = array_slice($files, $max_keep);
            foreach ($del as $f) {
                if (@unlink($f)) {
                    $total_deleted++;
                }
            }
        }
    }
    return $total_deleted;
}

/**
 * Mendapatkan daftar seluruh file backup perpustakaan yang tersedia di 3 folder
 */
function daftar_file_backup_perpustakaan() {
    // Jalankan rotasi otomatis agar selalu hanya 5 file cadangan terbaru yang tersimpan
    bersihkan_rotasi_backup_perpustakaan(PERPUS_BACKUP_MAX_FILES);

    $dirs = get_perpus_backup_dirs();
    $files = [];

    foreach ($dirs as $tipe => $path) {
        if (!is_dir($path)) continue;
        $items = glob($path . DIRECTORY_SEPARATOR . "perpustakaan_backup_*.sql");
        if ($items) {
            foreach ($items as $f) {
                $fname = basename($f);
                $ftime = filemtime($f);
                $fsize = filesize($f);

                if (!isset($files[$fname])) {
                    $files[$fname] = [
                        'filename' => $fname,
                        'time' => $ftime,
                        'date' => date('Y-m-d H:i:s', $ftime),
                        'size' => $fsize,
                        'size_format' => format_ukuran_file_perpus($fsize),
                        'paths' => [$tipe => $f],
                        'primary_path' => $f
                    ];
                } else {
                    $files[$fname]['paths'][$tipe] = $f;
                }
            }
        }
    }

    // Urutkan dari file paling mutakhir (terbaru) ke terlama
    usort($files, function($a, $b) {
        return $b['time'] - $a['time'];
    });

    return $files;
}

/**
 * Memulihkan (restore) database perpustakaan dari file SQL
 * Memerlukan target database eksplisit dan konfirmasi khusus jika memulihkan ke produksi.
 */
function pulihkan_database_perpustakaan($filepath, $targetDb = '', $confirmProduction = '') {
    global $pdo;

    if (!file_exists($filepath) || filesize($filepath) < 100) {
        return ['success' => false, 'pesan' => 'File cadangan tidak ditemukan atau kosong.'];
    }

    // Ambil target database jika tidak dikirim lewat argumen (misal dari form POST)
    if (empty($targetDb)) {
        $targetDb = trim($_POST['target_database'] ?? '');
    }
    if (empty($confirmProduction)) {
        $confirmProduction = trim($_POST['konfirmasi_restore_produksi'] ?? '');
    }

    // 1. Validasi: Target database harus ditentukan secara eksplisit
    if (empty($targetDb)) {
        return [
            'success' => false,
            'pesan' => 'Target database pemulihan harus dipilih atau ditentukan secara eksplisit demi keamanan.'
        ];
    }

    // Validasi format nama database (hanya alfanumerik dan garis bawah)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $targetDb)) {
        return [
            'success' => false,
            'pesan' => 'Nama target database tidak valid. Hanya karakter alfanumerik dan garis bawah (underscore) yang diizinkan.'
        ];
    }

    // 2. Proteksi database produksi: Memerlukan konfirmasi teks eksplisit
    $productionDb = 'nafisah_nasyirah_perpustakaan';
    if (strcasecmp($targetDb, $productionDb) === 0) {
        if ($confirmProduction !== 'PULIHKAN-PRODUKSI') {
            return [
                'success' => false,
                'pesan' => 'Pemulihan ke database produksi memerlukan konfirmasi eksplisit. Masukkan teks konfirmasi: PULIHKAN-PRODUKSI'
            ];
        }
    }

    $bins = get_perpus_mysql_binaries();
    $mysql = $bins['mysql'];

    // Sanitasi & Normalisasi file dump sebelum dieksekusi:
    $rawContent = file_get_contents($filepath);
    if ($rawContent === false) {
        return ['success' => false, 'pesan' => 'Gagal membaca isi file cadangan.'];
    }

    // Konversi encoding UTF-16 ke UTF-8 jika perlu
    if (substr($rawContent, 0, 2) === "\xFF\xFE" || substr($rawContent, 0, 2) === "\xFE\xFF") {
        $rawContent = mb_convert_encoding($rawContent, 'UTF-8', 'UTF-16');
    }

    $needsSanitization = false;
    $sanitized = $rawContent;

    // Normalisasi sintaks curdate() untuk MySQL 8
    if (strpos($sanitized, 'DEFAULT curdate()') !== false) {
        $sanitized = str_replace('DEFAULT curdate()', 'DEFAULT (curdate())', $sanitized);
        $needsSanitization = true;
    }

    // Netralisir pernyataan USE `...`; atau USE ...; agar dump lama tidak menimpa database lain
    if (preg_match('/^\s*USE\s+[`\'"]?[a-zA-Z0-9_]+[`\'"]?\s*;/mi', $sanitized)) {
        $sanitized = preg_replace('/^\s*USE\s+[`\'"]?[a-zA-Z0-9_]+[`\'"]?\s*;/mi', "-- [DINORMALISASI] USE dinonaktifkan demi keamanan target: {$targetDb}\n", $sanitized);
        $needsSanitization = true;
    }

    // Netralisir pernyataan CREATE DATABASE agar dump lama tidak membuat ulang database secara paksa
    if (preg_match('/^\s*CREATE\s+DATABASE\s+/mi', $sanitized)) {
        $sanitized = preg_replace('/^\s*CREATE\s+DATABASE\s+[^;]+;/mi', "-- [DINORMALISASI] CREATE DATABASE dinonaktifkan demi keamanan target: {$targetDb}\n", $sanitized);
        $needsSanitization = true;
    }

    $tempFile = null;
    $actualFile = $filepath;
    if ($needsSanitization) {
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'restore_safe_' . uniqid() . '.sql';
        @file_put_contents($tempFile, $sanitized);
        $actualFile = $tempFile;
    }

    // Pastikan database target sudah ada di MySQL sebelum proses import
    $dbUser = defined('DB_USER') ? DB_USER : 'root';
    $dbPass = defined('DB_PASS') ? DB_PASS : '';
    $passOpt = ($dbPass !== '') ? ' -p' . escapeshellarg($dbPass) : '';

    if ($mysql) {
        $createDbCmd = "\"{$mysql}\" -u {$dbUser}{$passOpt} -e \"CREATE DATABASE IF NOT EXISTS \`{$targetDb}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\" 2>&1";
        @exec("cmd /c \"{$createDbCmd}\"");
    }

    // METODE 1: Jalankan mysql.exe CLI (Laragon / XAMPP) mengarah ke database target
    $cmd = "\"{$mysql}\" -u {$dbUser}{$passOpt} --default-character-set=utf8mb4 \"{$targetDb}\" < \"{$actualFile}\" 2>&1";
    @exec("cmd /c \"{$cmd}\"", $out, $ret);

    if ($tempFile && file_exists($tempFile)) {
        @unlink($tempFile);
    }

    if ($ret === 0) {
        return [
            'success' => true,
            'pesan' => "Database `{$targetDb}` berhasil dipulihkan secara utuh 100% dari file cadangan!"
        ];
    }

    // METODE 2: Fallback query execution via PDO langsung ke target database
    try {
        $content = $sanitized;
        $targetPdo = new PDO("mysql:host=127.0.0.1;dbname={$targetDb};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $targetPdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $targetPdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

        // Split queries by semicolon line endings
        $queries = [];
        $cur = '';
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed) || strpos($trimmed, '--') === 0 || strpos($trimmed, '/*') === 0) {
                continue;
            }
            if (strpos($trimmed, 'LOCK TABLES') === 0 || strpos($trimmed, 'UNLOCK TABLES') === 0) {
                continue;
            }
            $cur .= $line . "\n";
            if (substr($trimmed, -1) === ';') {
                $queries[] = $cur;
                $cur = '';
            }
        }

        $cnt = 0;
        foreach ($queries as $q) {
            $qt = trim($q);
            if (!empty($qt)) {
                $targetPdo->exec($qt);
                $cnt++;
            }
        }

        $targetPdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        return [
            'success' => true,
            'pesan' => "Database `{$targetDb}` berhasil dipulihkan secara penuh ($cnt perintah SQL dieksekusi)!"
        ];
    } catch (Throwable $e) {
        return [
            'success' => false,
            'pesan' => "Gagal memulihkan database `{$targetDb}`: " . $e->getMessage()
        ];
    }
}

/**
 * Menghapus file backup tertentu dari seluruh folder penyimpanan
 */
function hapus_file_backup_perpustakaan($filename) {
    $filename = basename($filename);
    if (empty($filename) || !preg_match('/^perpustakaan_backup_.*\.sql$/', $filename)) {
        return false;
    }

    $dirs = get_perpus_backup_dirs();
    $cnt = 0;
    foreach ($dirs as $d) {
        $target = $d . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($target)) {
            if (@unlink($target)) $cnt++;
        }
    }
    return $cnt > 0;
}

/**
 * Pengecekan backup otomatis harian / berkala
 */
function periksa_backup_otomatis_perpustakaan() {
    $dirs = get_perpus_backup_dirs();
    $status_file = $dirs['internal_proyek'] . DIRECTORY_SEPARATOR . 'last_backup.json';

    $hari_ini = date('Y-m-d');
    $perlu = false;

    if (!file_exists($status_file)) {
        $perlu = true;
    } else {
        $data = @json_decode(@file_get_contents($status_file), true);
        $last_date = substr($data['last_backup_date'] ?? '', 0, 10);
        $last_time = (int)($data['last_backup_time'] ?? 0);

        if ($last_date !== $hari_ini || (time() - $last_time > 14400)) {
            $perlu = true;
        }
    }

    if ($perlu) {
        return buat_backup_perpustakaan('harian_aplikasi_dibuka');
    }
    return false;
}

/**
 * Trigger pencadangan instan jika ada penambahan / modifikasi data
 */
function trigger_backup_perpustakaan($alasan = 'update_buku_atau_anggota') {
    static $triggered = false;
    if ($triggered) return false;

    $dirs = get_perpus_backup_dirs();
    $status_file = $dirs['internal_proyek'] . DIRECTORY_SEPARATOR . 'last_backup.json';
    $last_time = 0;
    if (file_exists($status_file)) {
        $data = @json_decode(@file_get_contents($status_file), true);
        $last_time = (int)($data['last_backup_time'] ?? 0);
    }

    // Jeda minimal 10 detik agar tidak membebani server
    if (time() - $last_time >= 10) {
        $triggered = true;
        return buat_backup_perpustakaan($alasan);
    }
    return false;
}

// Bersihkan file backup lama saat script dimuat agar selalu maksimal 5 file terakhir
bersihkan_rotasi_backup_perpustakaan(PERPUS_BACKUP_MAX_FILES);

// Cek backup harian saat aplikasi dibuka
periksa_backup_otomatis_perpustakaan();

// Trigger backup otomatis saat ada penambahan/perubahan data buku atau anggota (POST)
register_shutdown_function(function() {
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Jangan trigger jika aksi adalah download atau hapus file backup itu sendiri
        if (!isset($_POST['hapus_backup_file']) && !isset($_POST['restore_backup_sekarang'])) {
            trigger_backup_perpustakaan('auto_update_data_post');
        }
    }
});
