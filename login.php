<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$oldEmail = '';
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    $oldEmail = $email;

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }
    if ($password === '') {
        $errors[] = 'Password tidak boleh kosong.';
    }

    if (empty($errors)) {
        $user = findUserByEmail($email);

        // 6. Sistem login dengan session
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            // Bonus: Remember Me dengan cookie
            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                $users = getUsers();
                foreach ($users as $index => $u) {
                    if ($u['id'] === $user['id']) {
                        $users[$index]['remember_token'] = $token;
                        break;
                    }
                }
                saveUsers($users);
                setcookie(REMEMBER_COOKIE_NAME, $token, time() + REMEMBER_COOKIE_DURATION, '/', '', false, true);
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tugas Rutin 7</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="card">
        <h1>Masuk ke Akun</h1>
        <p class="subtitle">Silakan login untuk melanjutkan.</p>

        <?php if ($flash): ?>
            <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $error): ?>
                    <div>• <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($oldEmail) ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="checkbox-group">
                <input type="checkbox" id="remember_me" name="remember_me">
                <label for="remember_me" style="margin:0;font-weight:400;">Ingat saya (Remember Me)</label>
            </div>
            <button type="submit" class="btn-primary">Login</button>
        </form>

        <p class="switch-link">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>
