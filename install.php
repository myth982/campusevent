<?php
// ===========================
// INSTALLATION SCRIPT
// ===========================
// File: install.php
// Fungsi: Setup database otomatis untuk Campus Event Hub
// Cara pakai: Buka di browser http://localhost/campusevent/install.php

header('Content-Type: text/html; charset=utf-8');

$host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'campus_event_hub';

$success = false;
$message = '';
$step = isset($_GET['step']) ? $_GET['step'] : 1;

// Koneksi ke MySQL tanpa database
$conn = new mysqli($host, $db_user, $db_pass);

if ($conn->connect_error) {
    $message = "❌ Gagal koneksi ke MySQL: " . $conn->connect_error;
} else {
    if ($step == 1) {
        // Step 1: Create database
        $sql = "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        
        if ($conn->query($sql) === TRUE) {
            $success = true;
            $message = "✅ Database berhasil dibuat!";
        } else {
            $message = "❌ Gagal membuat database: " . $conn->error;
        }
    } elseif ($step == 2) {
        // Step 2: Import SQL schema
        $conn->select_db($db_name);
        
        // Read database.sql
        $sql_file = file_get_contents('database.sql');
        
        // Split queries
        $queries = array_filter(array_map('trim', explode(';', $sql_file)));
        
        $error_count = 0;
        foreach ($queries as $query) {
            if (!empty($query)) {
                if (!$conn->query($query)) {
                    $error_count++;
                    error_log("SQL Error: " . $conn->error . " | Query: " . substr($query, 0, 100));
                }
            }
        }
        
        if ($error_count == 0) {
            $success = true;
            $message = "✅ Database schema berhasil diimpor!";
        } else {
            $success = true; // Continue anyway
            $message = "⚠️ Database schema diimpor dengan beberapa warning";
        }
    } elseif ($step == 3) {
        // Step 3: Setup admin password hash
        $conn->select_db($db_name);
        
        // Generate password hashes
        $admin_hash = password_hash('admin123', PASSWORD_BCRYPT);
        $mhs_hash = password_hash('mhs123', PASSWORD_BCRYPT);
        
        // Update passwords
        $sql1 = "UPDATE users SET password = '$admin_hash' WHERE email = 'admin@campus.com'";
        $sql2 = "UPDATE users SET password = '$mhs_hash' WHERE email = 'mahasiswa@campus.com'";
        
        if ($conn->query($sql1) && $conn->query($sql2)) {
            $success = true;
            $message = "✅ Password berhasil di-setup!";
        } else {
            $success = true; // Continue anyway
            $message = "⚠️ Password setup selesai (beberapa user mungkin belum ada)";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a5f3e 0%, #2d8f5e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .install-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        
        .install-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .install-header h1 {
            color: #1a5f3e;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .install-header p {
            color: #666;
            margin: 0;
        }
        
        .progress-container {
            margin-bottom: 40px;
        }
        
        .progress-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .progress-number {
            width: 40px;
            height: 40px;
            background: #1a5f3e;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .progress-number.done {
            background: #28a745;
        }
        
        .progress-text h4 {
            margin: 0 0 5px 0;
            color: #1a5f3e;
        }
        
        .progress-text p {
            margin: 0;
            color: #999;
            font-size: 0.9rem;
        }
        
        .message-box {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid;
        }
        
        .message-box.success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .message-box.error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-custom {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php include 'pages/components/navbar.php'; ?>

<div class="install-container">
    <div class="install-header">
        <h1><i class="fas fa-cog"></i> Installation Wizard</h1>
        <p>Campus Event Hub - Setup Database</p>
    </div>

    <div class="progress-container">
        <div class="progress-item <?php echo ($step >= 1) ? 'done' : ''; ?>">
            <div class="progress-number <?php echo ($step >= 1) ? 'done' : ''; ?>">
                <i class="fas <?php echo ($step >= 1) ? 'fa-check' : ''; ?>">1</i>
            </div>
            <div class="progress-text">
                <h4>Create Database</h4>
                <p>Membuat database campus_event_hub</p>
            </div>
        </div>

        <div class="progress-item <?php echo ($step >= 2) ? 'done' : ''; ?>">
            <div class="progress-number <?php echo ($step >= 2) ? 'done' : ''; ?>">
                <i class="fas <?php echo ($step >= 2) ? 'fa-check' : ''; ?>">2</i>
            </div>
            <div class="progress-text">
                <h4>Import Schema</h4>
                <p>Mengimpor tabel dan struktur database</p>
            </div>
        </div>

        <div class="progress-item <?php echo ($step >= 3) ? 'done' : ''; ?>">
            <div class="progress-number <?php echo ($step >= 3) ? 'done' : ''; ?>">
                <i class="fas <?php echo ($step >= 3) ? 'fa-check' : ''; ?>">3</i>
            </div>
            <div class="progress-text">
                <h4>Setup Password</h4>
                <p>Mengenkripsi password akun demo</p>
            </div>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="message-box <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="action-buttons">
        <?php if ($step < 3): ?>
            <a href="install.php?step=<?php echo $step + 1; ?>" class="btn btn-primary btn-custom">
                <i class="fas fa-arrow-right"></i> Lanjutkan
            </a>
        <?php else: ?>
            <a href="index.php" class="btn btn-success btn-custom">
                <i class="fas fa-check-circle"></i> Selesai - Buka Aplikasi
            </a>
            <a href="install.php" class="btn btn-outline-secondary btn-custom">
                <i class="fas fa-redo"></i> Ulangi Setup
            </a>
        <?php endif; ?>
    </div>

    <hr style="margin-top: 30px; margin-bottom: 20px;">

    <div class="alert alert-info mb-0">
        <strong>💡 Info:</strong><br>
        <small>
            <strong>Demo Accounts:</strong><br>
            Admin: admin@campus.com / admin123<br>
            Mahasiswa: mahasiswa@campus.com / mhs123<br><br>
            Setelah setup selesai, file install.php dapat dihapus untuk keamanan.
        </small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
