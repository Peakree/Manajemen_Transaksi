<?php
session_start();
require_once 'includes/config.php';

if (empty($_SESSION['is_logged_in'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'] ?? 'admin';
$namaLengkap = 'Administrator';
$role = 'Admin Sistem';
$waktuLogin = date('d M Y, H:i');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - <?= APP_NAME ?></title>
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
            padding: 24px;
        }
        .container {
            max-width: 720px;
            margin: 0 auto;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: 28px;
        }
        .header h1 { margin: 0 0 8px; font-size: 24px; }
        .header p { margin: 0; opacity: 0.9; }
        .body { padding: 24px 28px 28px; }
        .avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fff;
            color: var(--primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 18px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .item {
            background: #fcfaf7;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
        }
        .label { font-size: 12px; text-transform: uppercase; color: var(--muted); letter-spacing: 0.5px; margin-bottom: 6px; }
        .value { font-weight: 700; }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
        }
        .btn-secondary {
            background: #f2e8de;
            color: var(--primary-dark);
        }
        .btn-danger {
            background: #f8e2e2;
            color: #b85a5a;
        }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Profil Pengguna</h1>
            <p>Informasi akun Anda saat ini</p>
        </div>
        <div class="body">
            <div class="avatar"><?= strtoupper(substr($username, 0, 1)) ?></div>
            <div class="grid">
                <div class="item">
                    <div class="label">Username</div>
                    <div class="value"><?= htmlspecialchars($username) ?></div>
                </div>
                <div class="item">
                    <div class="label">Nama Lengkap</div>
                    <div class="value"><?= htmlspecialchars($namaLengkap) ?></div>
                </div>
                <div class="item">
                    <div class="label">Role</div>
                    <div class="value"><?= htmlspecialchars($role) ?></div>
                </div>
                <div class="item">
                    <div class="label">Login Terakhir</div>
                    <div class="value"><?= htmlspecialchars($waktuLogin) ?></div>
                </div>
            </div>

            <div class="actions">
                <a href="index.php" class="btn btn-primary">← Kembali ke Dashboard</a>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
