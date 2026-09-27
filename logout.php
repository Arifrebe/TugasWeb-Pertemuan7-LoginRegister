<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Hapus remember token dari data user (jika ada) supaya cookie lama tidak bisa dipakai lagi
if (isLoggedIn() && isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
    $users = getUsers();
    foreach ($users as $index => $u) {
        if ($u['id'] === $_SESSION['user_id']) {
            $users[$index]['remember_token'] = null;
            break;
        }
    }
    saveUsers($users);
}

// Hapus cookie remember me
if (isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
    setcookie(REMEMBER_COOKIE_NAME, '', time() - 3600, '/');
}

// 8. Logout functionality (session_destroy)
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
