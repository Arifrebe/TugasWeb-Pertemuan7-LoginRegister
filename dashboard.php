<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// 7. Dashboard yang diproteksi (redirect jika belum login)
requireLogin();

$flash = getFlash();
[$user] = findUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tugas Rutin 7</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="card wide">
        <div class="dashboard-header">
            <div>
                <h1>Halo, <?= htmlspecialchars($user['name']) ?> 👋</h1>
                <span class="badge">Login berhasil</span>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <p style="color:#666; font-size:14px; margin-bottom:10px;">Ini adalah halaman yang hanya bisa diakses setelah login.</p>

        <ul class="info-list">
            <li><span>Nama</span><span><?= htmlspecialchars($user['name']) ?></span></li>
            <li><span>Email</span><span><?= htmlspecialchars($user['email']) ?></span></li>
            <li><span>Terdaftar sejak</span><span><?= htmlspecialchars($user['created_at']) ?></span></li>
        </ul>

        <div class="actions">
            <a href="edit_profile.php" class="btn-secondary">Edit Profile</a>
            <form method="POST" action="logout.php" style="flex:1;">
                <button type="submit" class="btn-danger" style="width:100%;">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
