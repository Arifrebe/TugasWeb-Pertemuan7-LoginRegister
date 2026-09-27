<?php
// includes/functions.php - Kumpulan fungsi bantu

/**
 * Membaca seluruh data user dari file JSON.
 * @return array
 */
function getUsers(): array
{
    $json = file_get_contents(USERS_FILE);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/**
 * Menyimpan seluruh data user ke file JSON.
 * @param array $users
 * @return bool
 */
function saveUsers(array $users): bool
{
    return file_put_contents(
        USERS_FILE,
        json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    ) !== false;
}

/**
 * Mencari user berdasarkan email.
 * @param string $email
 * @return array|null
 */
function findUserByEmail(string $email): ?array
{
    foreach (getUsers() as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            return $user;
        }
    }
    return null;
}

/**
 * Mencari user berdasarkan id, mengembalikan juga index-nya.
 * @param string $id
 * @return array{0: array|null, 1: int|null}
 */
function findUserById(string $id): array
{
    $users = getUsers();
    foreach ($users as $index => $user) {
        if ($user['id'] === $id) {
            return [$user, $index];
        }
    }
    return [null, null];
}

/**
 * Generate ID unik sederhana untuk user baru.
 * @return string
 */
function generateUserId(): string
{
    return uniqid('user_', true);
}

/**
 * Sanitasi input string dengan htmlspecialchars + trim.
 * @param string $input
 * @return string
 */
function sanitize(string $input): string
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Mengecek apakah user sedang login (via session).
 * @return bool
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Redirect ke jika belum login. Dipanggil di halaman yang diproteksi.
 * Juga mencoba login otomatis via cookie "Remember Me".
 */
function requireLogin(): void
{
    if (isLoggedIn()) {
        return;
    }

    // Coba auto-login dari cookie remember me
    if (isset($_COOKIE[REMEMBER_COOKIE_NAME])) {
        $token = $_COOKIE[REMEMBER_COOKIE_NAME];
        $users = getUsers();
        foreach ($users as $index => $user) {
            if (!empty($user['remember_token']) && hash_equals($user['remember_token'], $token)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                return;
            }
        }
        // Token tidak valid/kadaluarsa -> hapus cookie
        setcookie(REMEMBER_COOKIE_NAME, '', time() - 3600, '/');
    }

    header('Location: login.php');
    exit;
}

/**
 * Set pesan flash (sukses/error) ke session, ditampilkan sekali lalu dihapus.
 * @param string $type 'success' | 'error'
 * @param string $message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Ambil dan hapus pesan flash dari session.
 * @return array|null
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
