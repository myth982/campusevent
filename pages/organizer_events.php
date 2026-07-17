<?php
requireLogin();
global $conn;

$user_id = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT organizer_status FROM users WHERE id = $user_id")->fetch_assoc();

if (($user['organizer_status'] ?? 'none') !== 'verified') {
    header('Location: index.php?page=organizer_apply');
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS event_announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
)");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_announcement'])) {
        $event_id = (int)$_POST['event_id'];
        $announcement_message = $conn->real_escape_string(trim($_POST['announcement_message'] ?? ''));

        $event_check = $conn->query("SELECT id FROM events WHERE id = $event_id AND organizer_id = $user_id")->num_rows;
        if ($event_check > 0 && $announcement_message !== '') {
            $conn->query("INSERT INTO event_announcements (event_id, message) VALUES ($event_id, '$announcement_message')");
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Informasi berhasil dikirim ke peserta.</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Informasi tidak boleh kosong.</div>';
        }
    } elseif (isset($_POST['upload_certificate'])) {
        $event_id = (int)$_POST['event_id'];
        $participant_id = (int)$_POST['participant_id'];

        $event_check = $conn->query("SELECT id FROM events WHERE id = $event_id AND organizer_id = $user_id")->num_rows;
        if ($event_check > 0 && isset($_FILES['certificate_file']) && $_FILES['certificate_file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['certificate_file']['name'], PATHINFO_EXTENSION);
            $filename = 'cert_' . $participant_id . '_' . $event_id . '_' . time() . '.' . $ext;
            $upload_path = 'uploads/certificates/' . $filename;

            if (move_uploaded_file($_FILES['certificate_file']['tmp_name'], $upload_path)) {
                $check_cert = $conn->query("SELECT id FROM certificates WHERE user_id = $participant_id AND event_id = $event_id")->num_rows;
                if ($check_cert === 0) {
                    $conn->query("INSERT INTO certificates (user_id, event_id, file_sertifikat) VALUES ($participant_id, $event_id, '$filename')");
                    $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Sertifikat berhasil diterbitkan.</div>';
                } else {
                    $message = '<div class="alert alert-warning"><i class="fas fa-info-circle"></i> Sertifikat untuk peserta ini sudah ada.</div>';
                }
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal mengupload sertifikat.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Pilih peserta dan file sertifikat.</div>';
        }
    }
}

$events = $conn->query("SELECT e.*, COUNT(r.id) as total_registrations FROM events e LEFT JOIN registrations r ON r.event_id = e.id WHERE e.organizer_id = $user_id GROUP BY e.id ORDER BY e.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Events - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'components/navbar.php'; ?>
<div class="container-lg py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="fas fa-calendar-alt"></i> Kelola Event Organizer</h3>
        <a href="index.php?page=organizer_create_event" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Event Baru</a>
    </div>

    <?php echo $message; ?>

    <?php if ($events->num_rows > 0): while ($event = $events->fetch_assoc()): ?>
        <?php
        $participants = $conn->query("SELECT u.id, u.nama, u.email, r.status, r.tanggal_daftar FROM registrations r JOIN users u ON u.id = r.user_id WHERE r.event_id = {$event['id']} ORDER BY r.tanggal_daftar DESC");
        $announcements = $conn->query("SELECT message, created_at FROM event_announcements WHERE event_id = {$event['id']} ORDER BY created_at DESC LIMIT 5");
        ?>
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($event['judul']); ?></h5>
                        <p class="text-muted mb-0"><?php echo date('d M Y H:i', strtotime($event['tanggal'])); ?> • <?php echo (int)$event['total_registrations']; ?> peserta</p>
                    </div>
                    <span class="badge bg-info text-dark"><?php echo ucfirst($event['status']); ?></span>
                </div>

                <div class="row mt-4 g-4">
                    <div class="col-lg-7">
                        <h6><i class="fas fa-users"></i> Peserta Terdaftar</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($participants->num_rows > 0): while ($participant = $participants->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($participant['nama']); ?></td>
                                            <td><?php echo htmlspecialchars($participant['email']); ?></td>
                                            <td><?php echo htmlspecialchars($participant['status']); ?></td>
                                        </tr>
                                    <?php endwhile; else: ?>
                                        <tr><td colspan="3" class="text-muted text-center">Belum ada peserta.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <h6><i class="fas fa-bullhorn"></i> Beri Informasi ke Peserta</h6>
                        <form method="POST">
                            <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
                            <textarea class="form-control mb-2" name="announcement_message" rows="4" placeholder="Contoh: Info selanjutnya, lokasi, dress code, atau pengumuman penting."></textarea>
                            <button type="submit" name="save_announcement" class="btn btn-outline-primary btn-sm"><i class="fas fa-paper-plane"></i> Kirim Info</button>
                        </form>

                        <h6 class="mt-3"><i class="fas fa-history"></i> Info Terkini</h6>
                        <ul class="list-group list-group-flush">
                            <?php if ($announcements->num_rows > 0): while ($announcement = $announcements->fetch_assoc()): ?>
                                <li class="list-group-item px-0">
                                    <small class="text-muted"><?php echo date('d M Y H:i', strtotime($announcement['created_at'])); ?></small>
                                    <div><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></div>
                                </li>
                            <?php endwhile; else: ?>
                                <li class="list-group-item px-0 text-muted">Belum ada pengumuman.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3">
                    <h6><i class="fas fa-certificate"></i> Terbitkan Sertifikat</h6>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="event_id" value="<?php echo (int)$event['id']; ?>">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label mb-1">Pilih Peserta</label>
                                <select class="form-select form-select-sm" name="participant_id" required>
                                    <option value="">-- Pilih peserta --</option>
                                    <?php $participant_options = $conn->query("SELECT u.id, u.nama FROM registrations r JOIN users u ON u.id = r.user_id WHERE r.event_id = {$event['id']} ORDER BY u.nama"); ?>
                                    <?php while ($opt = $participant_options->fetch_assoc()): ?>
                                        <option value="<?php echo (int)$opt['id']; ?>"><?php echo htmlspecialchars($opt['nama']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label mb-1">File Sertifikat</label>
                                <input type="file" class="form-control form-control-sm" name="certificate_file" accept=".pdf,.jpg,.png,.jpeg" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="upload_certificate" class="btn btn-success btn-sm w-100"><i class="fas fa-upload"></i> Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endwhile; else: ?>
        <div class="alert alert-info">Belum ada event yang Anda kelola.</div>
    <?php endif; ?>
</div>
<?php include 'components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
