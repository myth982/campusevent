<?php
// ===========================
// PROFILE PAGE
// ===========================
// File: pages/profile.php
// Fungsi: Menampilkan dan edit profil pengguna

// db.php dan session.php sudah di-include oleh index.php

$message = '';
$user_id = $_SESSION['user_id'];

$photoColumnCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'foto'");
if ($photoColumnCheck && $photoColumnCheck->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN foto VARCHAR(255) DEFAULT NULL");
}

// Get user data
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Handle update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $photoPath = $user['foto'] ?? '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $tmpName = $_FILES['foto']['tmp_name'];
        $fileName = basename($_FILES['foto']['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $size = $_FILES['foto']['size'];

        if (!in_array($ext, $allowedExt, true)) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Format foto tidak didukung!</div>';
        } elseif ($size > 2097152) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Ukuran foto maksimal 2 MB!</div>';
        } else {
            $uploadDir = __DIR__ . '/../uploads/profile/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $newFileName;
            if (move_uploaded_file($tmpName, $targetPath)) {
                $photoPath = 'uploads/profile/' . $newFileName;
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal mengunggah foto.</div>';
            }
        }
    }

    if ($message === '') {
        // Check apakah email sudah digunakan user lain
        $check = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != $user_id")->num_rows;

        if ($check > 0) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Email sudah digunakan!</div>';
        } else {
            $update = "UPDATE users SET nama = '$nama', email = '$email', foto = '$photoPath' WHERE id = $user_id";
            if ($conn->query($update) === TRUE) {
                $_SESSION['nama'] = $nama;
                $_SESSION['email'] = $email;
                $user['nama'] = $nama;
                $user['email'] = $email;
                $user['foto'] = $photoPath;
                $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Profil berhasil diperbarui!</div>';
            }
        }
    }
}

// Handle change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify old password
    if (!password_verify($old_password, $user['password'])) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Password lama salah!</div>';
    } elseif (strlen($new_password) < 6) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Password minimal 6 karakter!</div>';
    } elseif ($new_password !== $confirm_password) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Password baru tidak sesuai!</div>';
    } else {
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        $update = "UPDATE users SET password = '$hashed' WHERE id = $user_id";
        if ($conn->query($update) === TRUE) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Password berhasil diubah!</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<style>
    body {
        background: linear-gradient(180deg, #f6fbff 0%, #eef5ff 100%);
        color: #1f2937;
    }

    .profile-shell {
        padding: 2rem 0 4rem;
    }

    .profile-hero {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #35546d 0%, #6f8da8 45%, #9bc2d9 100%);
        border-radius: 32px;
        padding: 2rem;
        box-shadow: 0 22px 50px rgba(31, 41, 55, 0.16);
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        color: white;
        isolation: isolate;
    }

    .profile-hero::before,
    .profile-hero::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(8px);
        opacity: 0.35;
        z-index: 0;
    }

    .profile-hero::before {
        width: 220px;
        height: 220px;
        background: rgba(255,255,255,0.2);
        top: -70px;
        right: -40px;
    }

    .profile-hero::after {
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.14);
        bottom: -60px;
        left: -30px;
    }

    .profile-hero > * {
        position: relative;
        z-index: 1;
    }

    .profile-avatar {
        width: 122px;
        height: 122px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid rgba(255,255,255,0.9);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.22);
        background: linear-gradient(135deg, #f8fbff 0%, #e4eef8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .avatar-fallback {
        font-size: 2.4rem;
        font-weight: 800;
        color: #4f6f8f;
        letter-spacing: 0.05em;
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.25);
        backdrop-filter: blur(8px);
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 0.7rem;
    }

    .profile-hero h1 {
        font-size: 1.7rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.25rem;
    }

    .profile-hero p {
        margin-bottom: 0;
        color: rgba(255,255,255,0.92);
    }

    .profile-card {
        border: 1px solid #e5eefc;
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.05);
        background: rgba(255, 255, 255, 0.95);
        overflow: hidden;
    }

    .profile-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eef4ff;
        padding: 1.15rem 1.25rem 0.8rem;
    }

    .profile-card .card-body {
        padding: 1.25rem;
    }

    .profile-card .form-control,
    .profile-card .form-select {
        border-radius: 14px;
        border: 1px solid #dbe7f8;
        padding: 0.7rem 0.9rem;
    }

    .profile-card .form-control:focus,
    .profile-card .form-select:focus {
        border-color: #8fb0c9;
        box-shadow: 0 0 0 0.2rem rgba(143, 176, 201, 0.2);
    }

    .profile-upload {
        border: 1px dashed #b9d0e7;
        border-radius: 16px;
        padding: 0.85rem 1rem;
        background: #f8fbff;
        color: #4b5563;
    }

    .stat-card {
        background: linear-gradient(135deg, #4f6f8f 0%, #8fb0c9 100%);
        color: white;
        border-radius: 22px;
        padding: 1.25rem;
        box-shadow: 0 16px 34px rgba(79, 111, 143, 0.2);
        height: 100%;
    }

    .stat-card.secondary {
        background: linear-gradient(135deg, #5b8fbf 0%, #93c5fd 100%);
    }

    .stat-card h3 {
        font-size: 1.6rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
    }

    .stat-card p {
        margin-bottom: 0;
        opacity: 0.95;
    }
</style>

<div class="container-lg profile-shell">
    <div class="profile-hero">
        <div class="profile-avatar">
            <?php if (!empty($user['foto'])): ?>
                <img src="<?php echo htmlspecialchars($user['foto']); ?>" alt="Foto profil <?php echo htmlspecialchars($user['nama']); ?>">
            <?php else: ?>
                <div class="avatar-fallback"><?php echo strtoupper(substr($user['nama'], 0, 1)); ?></div>
            <?php endif; ?>
        </div>
        <div>
            <div class="profile-badge"><i class="fas fa-shield-alt"></i> <?php echo ucfirst($user['role']); ?></div>
            <h1><?php echo htmlspecialchars($user['nama']); ?></h1>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card profile-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-image"></i> Foto Profil</label>
                            <div class="profile-upload">
                                <input type="file" class="form-control" name="foto" accept="image/*">
                                <small class="d-block mt-2">Format: JPG, PNG, WEBP, GIF. Maksimal 2 MB.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-shield-alt"></i> Role</label>
                            <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" disabled>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card profile-card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> Ubah Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" name="old_password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" name="new_password" placeholder="Min. 6 karakter" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-warning w-100">
                            <i class="fas fa-key"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <h3>
                    <?php 
                    $count = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE user_id = $user_id")->fetch_assoc()['count'];
                    echo $count;
                    ?>
                </h3>
                <p><i class="fas fa-calendar-alt"></i> Event Diikuti</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card secondary">
                <h3>
                    <?php 
                    $count = $conn->query("SELECT COUNT(*) as count FROM certificates WHERE user_id = $user_id")->fetch_assoc()['count'];
                    echo $count;
                    ?>
                </h3>
                <p><i class="fas fa-certificate"></i> Sertifikat</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #22c55e 0%, #4ade80 100%);">
                <h3>
                    <?php 
                    $count = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE user_id = $user_id AND status = 'terdaftar'")->fetch_assoc()['count'];
                    echo $count;
                    ?>
                </h3>
                <p><i class="fas fa-check"></i> Aktif</p>
            </div>
        </div>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
