<?php
// ===========================
// PASSWORD HASH GENERATOR
// ===========================
// File: generate_hash.php
// Fungsi: Generate password hash untuk testing
// Cara pakai: Buka di browser http://localhost/campusevent/generate_hash.php
// Copy hash yang ditampilkan, lalu paste ke database

header('Content-Type: text/html; charset=utf-8');

// Password yang ingin di-hash
$passwords = [
    'admin123' => 'Admin password',
    'mhs123' => 'Mahasiswa password',
    'test123' => 'Test password'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a5f3e 0%, #2d8f5e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container-md {
            max-width: 600px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .hash-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid #1a5f3e;
        }
        .hash-item code {
            display: block;
            word-break: break-all;
            font-size: 0.85rem;
            padding: 10px;
            background: white;
            border-radius: 4px;
            margin-top: 10px;
        }
        .btn-copy {
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container-md">
    <div class="card">
        <div class="card-body p-5">
            <h2 class="mb-4" style="color: #1a5f3e;">
                <i class="fas fa-lock"></i> Password Hash Generator
            </h2>
            
            <p class="text-muted mb-4">
                Berikut adalah password hash untuk akun demo. Copy hash dan paste ke database untuk update password.
            </p>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Cara menggunakan:</strong><br>
                1. Copy hash di bawah ini<br>
                2. Buka phpMyAdmin → Tabel users<br>
                3. Edit password user<br>
                4. Paste hash ke field password<br>
                5. Click "Go"
            </div>

            <?php foreach ($passwords as $password => $label): ?>
                <div class="hash-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong><?php echo $label; ?></strong>
                        <small class="text-muted">Password: <code><?php echo $password; ?></code></small>
                    </div>
                    <code id="hash_<?php echo md5($password); ?>" class="text-break" onclick="copyHash('<?php echo md5($password); ?>')">
                        <?php echo password_hash($password, PASSWORD_BCRYPT); ?>
                    </code>
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="copyHash('<?php echo md5($password); ?>')">
                        <i class="fas fa-copy"></i> Copy Hash
                    </button>
                </div>
            <?php endforeach; ?>

            <hr>

            <div class="alert alert-warning">
                <i class="fas fa-warning"></i>
                <strong>⚠️ Untuk Production:</strong><br>
                - Jangan share file ini ke public<br>
                - Hapus file ini setelah setup selesai<br>
                - Generate password hash di backend yang aman
            </div>

            <div class="mt-4">
                <p class="text-center">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Aplikasi
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function copyHash(id) {
    const element = document.getElementById('hash_' + id);
    const text = element.innerText;
    
    navigator.clipboard.writeText(text).then(() => {
        alert('Hash berhasil dicopy ke clipboard!');
    }).catch(err => {
        console.error('Gagal copy:', err);
        alert('Gagal copy hash');
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
