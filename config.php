<?php
// config.php - Konfigurasi dasar aplikasi

// Mulai session di semua halaman yang include file ini
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path ke file JSON penyimpanan data user
define('USERS_FILE', __DIR__ . '/data/users.json');

// Nama cookie untuk fitur "Remember Me"
define('REMEMBER_COOKIE_NAME', 'remember_token');
define('REMEMBER_COOKIE_DURATION', 60 * 60 * 24 * 30); // 30 hari

// Pastikan folder data & file JSON tersedia dan writable
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}
if (!file_exists(USERS_FILE)) {
    file_put_contents(USERS_FILE, json_encode([]));
}
