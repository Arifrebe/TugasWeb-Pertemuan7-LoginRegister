<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

[$user, $index] = findUserById($_SESSION['user_id']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    // Cek duplikasi email (kecuali email milik sendiri)
    $existing = findUserByEmail($email);
    if ($existing && $existing['id'] !== $user['id']) {
        $errors[] = 'Email sudah digunakan oleh akun lain.';
    }

    if ($newPassword !== '' && strlen($newPassword) < 6) {
        $errors[] = 'Password baru minimal 6 karakter.';
    }

    if (empty($errors)) {
        $users = getUsers();
        $users[$index]['name'] = $name;
        $users[$index]['email'] = $email;
        if ($newPassword !== '') {
            $users[$index]['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        saveUsers($users);

        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        setFlash('success', 'Profil berhasil diperbarui.');
        header('Location: dashboard.php');
        exit;
    }

    // supaya form tetap tampil data yang baru diinput jika gagal
    $user['name'] = $name;
    $user['email'] = $email;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Tugas Rutin 7</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="card">
        <h1>Edit Profile</h1>
        <p class="subtitle">Perbarui data akun kamu.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="edit_profile.php">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" id="new_password" name="new_password" minlength="6">
            </div>
            <button type="submit" class="btn-primary">Simpan Perubahan</button>
        </form>

        <p class="switch-link"><a href="dashboard.php">← Kembali ke Dashboard</a></p>
    </div>
</body>
</html>
