<?php
session_start();

if (!empty($_SESSION['is_logged_in'])) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === 'admin' && $password === '123456') {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit;
    }

    $errors[] = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Manajemen Transaksi</title>
    <style>
        :root {
            --primary: #b07d62;
            --primary-dark: #946652;
            --bg: #f7f3ee;
            --card: #fffdf9;
            --text: #3d3530;
            --muted: #8a7f76;
            --border: #e8dfd4;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 400px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            padding: 30px;
        }
        h1 { margin: 0 0 10px; font-size: 24px; }
        p { color: var(--muted); margin: 0 0 20px; }
        .field { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
        }
        .error {
            background: #fff1f1;
            color: #a94442;
            border: 1px solid #f3c2c2;
            padding: 10px 12px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .hint {
            margin-top: 14px;
            font-size: 13px;
            color: var(--muted);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Login</h1>
        <p>Masuk untuk mengakses halaman utama.</p>

        <?php if (!empty($errors)): ?>
            <div class="error"><?= htmlspecialchars($errors[0]) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Masuk</button>
        </form>

        <div class="hint">Default: username <strong>admin</strong> dan password <strong>123456</strong></div>
    </div>
</body>
</html>
