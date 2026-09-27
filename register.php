<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $old['name'] = $name;
    $old['email'] = $email;

    // 1. Validasi form registrasi (nama, email, password)
    if ($name === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }
    if ($email === '') {
        $errors[] = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // 2. Validasi email dengan filter_var()
        $errors[] = 'Format email tidak valid.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    // 5. Cek duplikasi email saat registrasi
    if (empty($errors) && findUserByEmail($email) !== null) {
        $errors[] = 'Email sudah terdaftar. Silakan gunakan email lain.';
    }

    if (empty($errors)) {
        $users = getUsers();
        $users[] = [
            'id' => generateUserId(),
            'name' => $name,
            'email' => $email,
            // 3. Password di-hash dengan password_hash()
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'remember_token' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if (saveUsers($users)) {
            setFlash('success', 'Registrasi berhasil! Silakan login.');
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Terjadi kesalahan saat menyimpan data. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tugas Rutin 7</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="card">
        <h1>Buat Akun Baru</h1>
        <p class="subtitle">Daftar untuk mengakses dashboard.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            <button type="submit" class="btn-primary">Daftar</button>
        </form>

        <p class="switch-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>
