<?php

/*
 * Endpoint deploy via HTTPS untuk GitHub Actions.
 * Dipakai karena firewall hosting memblokir koneksi FTP dari IP luar negeri,
 * sedangkan port HTTP/HTTPS selalu terbuka.
 *
 * Autentikasi: header "X-Deploy-Token" harus sama dengan DEPLOY_TOKEN di .env server.
 *
 * Aksi (query string ?action=...):
 *   - upload  : terima file zip (field "package"), ekstrak ke root aplikasi
 *   - migrate : jalankan artisan optimize:clear + migrate --force
 */

$root = dirname(__DIR__);

header('Content-Type: application/json');
@set_time_limit(300);

function fail(int $code, string $message): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

// Baca DEPLOY_TOKEN langsung dari .env tanpa mem-boot Laravel,
// supaya autentikasi tetap jalan walau aplikasi sedang error.
$token = null;
$envFile = $root.'/.env';
if (is_file($envFile) && preg_match('/^DEPLOY_TOKEN=("?)(.+?)\1\s*$/m', (string) file_get_contents($envFile), $m)) {
    $token = $m[2];
}

if (! $token) {
    fail(503, 'DEPLOY_TOKEN belum diset di file .env server.');
}

if (! hash_equals($token, $_SERVER['HTTP_X_DEPLOY_TOKEN'] ?? '')) {
    fail(403, 'Token deploy tidak valid.');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    fail(405, 'Gunakan method POST.');
}

$action = $_GET['action'] ?? '';

if ($action === 'upload') {
    if (empty($_FILES['package']) || $_FILES['package']['error'] !== UPLOAD_ERR_OK) {
        fail(400, 'File "package" tidak diterima. Cek upload_max_filesize/post_max_size di pengaturan PHP hosting.');
    }

    $tmpZip = $root.'/storage/app/deploy-package.zip';
    if (! move_uploaded_file($_FILES['package']['tmp_name'], $tmpZip)) {
        fail(500, 'Gagal menyimpan file zip ke storage/app.');
    }

    $zip = new ZipArchive;
    if ($zip->open($tmpZip) !== true) {
        unlink($tmpZip);
        fail(500, 'File zip tidak bisa dibuka (korup?).');
    }

    $extracted = $zip->extractTo($root);
    $fileCount = $zip->numFiles;
    $zip->close();
    unlink($tmpZip);

    if (! $extracted) {
        fail(500, 'Ekstraksi zip gagal. Cek permission direktori aplikasi.');
    }

    if (function_exists('opcache_reset')) {
        @opcache_reset();
    }

    echo json_encode(['ok' => true, 'extracted_files' => $fileCount]);
    exit;
}

if ($action === 'migrate') {
    require $root.'/vendor/autoload.php';
    $app = require $root.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $output = '';
    foreach ([['optimize:clear', []], ['migrate', ['--force' => true]]] as [$command, $arguments]) {
        $status = $kernel->call($command, $arguments);
        $output .= $kernel->output();

        if ($status !== 0) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'command' => $command, 'output' => $output]);
            exit;
        }
    }

    echo json_encode(['ok' => true, 'output' => $output]);
    exit;
}

fail(400, 'Parameter action harus "upload" atau "migrate".');
