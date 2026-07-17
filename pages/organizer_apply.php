<?php
requireLogin();
global $conn;

$message = '';
$user_id = (int)$_SESSION['user_id'];

$user = $conn->query("SELECT id, nama, email, role, organizer_status FROM users WHERE id = $user_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_organizer'])) {
    $organization_name = $conn->real_escape_string($_POST['organization_name']);
    $organization_type = $conn->real_escape_string($_POST['organization_type']);
    $university = $conn->real_escape_string($_POST['university']);
    $official_email = $conn->real_escape_string($_POST['official_email']);
    $instagram = $conn->real_escape_string($_POST['instagram']);
    $website = $conn->real_escape_string($_POST['website']);
    $description = $conn->real_escape_string($_POST['description']);
    $pic_name = $conn->real_escape_string($_POST['pic_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    $existing_application = $conn->query("SELECT id, status FROM organizer_applications WHERE user_id = $user_id ORDER BY id DESC LIMIT 1")->fetch_assoc();

    if ($existing_application && in_array($existing_application['status'], ['pending', 'revision_requested', 'rejected'], true)) {
        $update = "UPDATE organizer_applications SET
            organization_name = '$organization_name',
            organization_type = '$organization_type',
            university = '$university',
            official_email = '$official_email',
            instagram = '$instagram',
            website = '$website',
            description = '$description',
            pic_name = '$pic_name',
            phone = '$phone',
            address = '$address',
            status = 'pending',
            review_notes = NULL,
            reviewed_at = NULL,
            reviewed_by = NULL
            WHERE id = {$existing_application['id']}";
        if ($conn->query($update) === true) {
            $conn->query("UPDATE users SET organizer_status = 'pending' WHERE id = $user_id");
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Permohonan organizer berhasil diperbarui dan dikirim ulang untuk verifikasi admin.</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal memperbarui permohonan organizer.</div>';
        }
    } elseif ($existing_application && $existing_application['status'] === 'approved') {
        $message = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> Akun Anda sudah terverifikasi sebagai organizer.</div>';
    } else {
        $insert = "INSERT INTO organizer_applications (user_id, organization_name, organization_type, university, official_email, instagram, website, description, pic_name, phone, address, status) VALUES ($user_id, '$organization_name', '$organization_type', '$university', '$official_email', '$instagram', '$website', '$description', '$pic_name', '$phone', '$address', 'pending')";
        if ($conn->query($insert) === true) {
            $conn->query("UPDATE users SET organizer_status = 'pending' WHERE id = $user_id");
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Permohonan organizer berhasil dikirim. Admin akan memverifikasi akun Anda.</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal mengirim permohonan organizer.</div>';
        }
    }
}

$application = $conn->query("SELECT * FROM organizer_applications WHERE user_id = $user_id ORDER BY id DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become Organizer - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: linear-gradient(180deg, #f7fbff 0%, #eef5ff 100%); }
        .organizer-shell { padding: 2rem 0 4rem; }
        .hero-card { background: linear-gradient(135deg, #35546d 0%, #6f8fa8 100%); color: white; border-radius: 24px; padding: 2rem; box-shadow: 0 20px 45px rgba(15,23,42,0.12); }
        .info-card { border: 1px solid #e4edf9; border-radius: 20px; box-shadow: 0 12px 25px rgba(15,23,42,0.05); background: white; }
        .form-control, .form-select, .form-textarea { border-radius: 14px; border: 1px solid #dbe7f8; padding: 0.7rem 0.9rem; }
        .form-control:focus, .form-select:focus { border-color: #8fb0c9; box-shadow: 0 0 0 0.2rem rgba(143,176,201,0.2); }
    </style>
</head>
<body>
<?php include 'components/navbar.php'; ?>
<div class="container-lg organizer-shell">
    <div class="hero-card mb-4">
        <h2 class="mb-2"><i class="fas fa-users-cog"></i> Become an Organizer</h2>
        <p class="mb-0">Buka kesempatan untuk mengelola event mahasiswa, mengundang peserta, dan membangun reputasi organisasi Anda di CampusConnect.</p>
    </div>

    <?php echo $message; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card info-card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-file-signature"></i> Form Pendaftaran Organizer</h5>
                </div>
                <div class="card-body">
                    <?php if (($user['organizer_status'] ?? 'none') === 'verified'): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> Akun Anda sudah terverifikasi sebagai organizer.</div>
                        <a href="index.php?page=organizer_dashboard" class="btn btn-primary"><i class="fas fa-arrow-right"></i> Buka Dashboard Organizer</a>
                    <?php else: ?>
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Nama Organisasi</label><input type="text" class="form-control" name="organization_name" required></div>
                                <div class="col-md-6"><label class="form-label">Jenis Organisasi</label><input type="text" class="form-control" name="organization_type" placeholder="Contoh: Community / BEM / Himpunan" required></div>
                                <div class="col-md-6"><label class="form-label">Universitas</label><input type="text" class="form-control" name="university"></div>
                                <div class="col-md-6"><label class="form-label">Email Resmi</label><input type="email" class="form-control" name="official_email" required></div>
                                <div class="col-md-6"><label class="form-label">Instagram</label><input type="text" class="form-control" name="instagram"></div>
                                <div class="col-md-6"><label class="form-label">Website</label><input type="text" class="form-control" name="website"></div>
                                <div class="col-12"><label class="form-label">Deskripsi Organisasi</label><textarea class="form-control" name="description" rows="3"></textarea></div>
                                <div class="col-md-6"><label class="form-label">Penanggung Jawab</label><input type="text" class="form-control" name="pic_name" required></div>
                                <div class="col-md-6"><label class="form-label">No HP</label><input type="text" class="form-control" name="phone" required></div>
                                <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="address" rows="2"></textarea></div>
                            </div>
                            <button type="submit" name="apply_organizer" class="btn btn-primary mt-4 w-100"><i class="fas fa-paper-plane"></i> Ajukan Menjadi Organizer</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card info-card h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> Status Permohonan</h5>
                </div>
                <div class="card-body">
                    <?php if ($application): ?>
                        <p><strong>Organisasi:</strong> <?php echo htmlspecialchars($application['organization_name']); ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-warning text-dark"><?php echo ucfirst(str_replace('_', ' ', $application['status'])); ?></span></p>
                        <p><strong>Waktu:</strong> <?php echo date('d M Y H:i', strtotime($application['created_at'])); ?></p>
                        <?php if (!empty($application['review_notes'])): ?>
                            <div class="alert alert-warning mt-3 mb-0">
                                <strong>Catatan admin:</strong><br>
                                <?php echo nl2br(htmlspecialchars($application['review_notes'])); ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Belum ada permohonan organizer yang dikirim.</p>
                    <?php endif; ?>
                    <hr>
                    <div class="alert alert-light border mb-3">
                        <strong>Alur setelah submit:</strong>
                        <ol class="mb-0 ps-3 mt-2">
                            <li>Admin memeriksa data organisasi Anda.</li>
                            <li>Status <strong>verified</strong> akan membuka akses dashboard organizer.</li>
                            <li>Setelah disetujui, Anda bisa membuat event, melihat peserta, mengirim info, dan menerbitkan sertifikat.</li>
                        </ol>
                    </div>
                    <ul class="mb-0 ps-3">
                        <li>Admin akan memeriksa data organisasi Anda.</li>
                        <li>Status <strong>verified</strong> akan membuka akses dashboard organizer.</li>
                        <li>Jika ada revisi, admin akan mengirimkan catatan khusus dan Anda dapat mengirim ulang permohonan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
