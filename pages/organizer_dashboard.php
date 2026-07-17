<?php
requireLogin();
global $conn;

$user_id = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT id, nama, email, organizer_status FROM users WHERE id = $user_id")->fetch_assoc();

if (($user['organizer_status'] ?? 'none') !== 'verified') {
    header('Location: index.php?page=organizer_apply');
    exit();
}

$total_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE organizer_id = $user_id")->fetch_assoc()['count'];
$active_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE organizer_id = $user_id AND status = 'published'")->fetch_assoc()['count'];
$finished_events = $conn->query("SELECT COUNT(*) as count FROM events WHERE organizer_id = $user_id AND status = 'finished'")->fetch_assoc()['count'];
$total_participants = $conn->query("SELECT COUNT(*) as count FROM registrations r JOIN events e ON e.id = r.event_id WHERE e.organizer_id = $user_id")->fetch_assoc()['count'];
$recent_events = $conn->query("SELECT * FROM events WHERE organizer_id = $user_id ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: linear-gradient(180deg, #f7fbff 0%, #eef5ff 100%); }
        .dashboard-shell { padding: 2rem 0 4rem; }
        .hero-panel { background: linear-gradient(135deg, #35546d 0%, #6f8fa8 100%); color: white; border-radius: 24px; padding: 1.5rem 1.8rem; margin-bottom: 1.5rem; }
        .stat-card { border-radius: 20px; padding: 1.2rem; box-shadow: 0 12px 25px rgba(15,23,42,0.08); background: white; }
        .table-card { border-radius: 20px; border: 1px solid #e5eefc; background: white; overflow: hidden; }
    </style>
</head>
<body>
<?php include 'components/navbar.php'; ?>
<div class="container-lg dashboard-shell">
    <div class="hero-panel">
        <h2 class="mb-2"><i class="fas fa-users-cog"></i> Dashboard Organizer</h2>
        <p class="mb-0">Kelola event yang Anda selenggarakan, pantau peserta, dan pantau reputasi organisasi Anda.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="stat-card"><h4><?php echo $total_events; ?></h4><p class="mb-0">Total Event</p></div></div>
        <div class="col-md-3"><div class="stat-card"><h4><?php echo $active_events; ?></h4><p class="mb-0">Event Aktif</p></div></div>
        <div class="col-md-3"><div class="stat-card"><h4><?php echo $finished_events; ?></h4><p class="mb-0">Event Selesai</p></div></div>
        <div class="col-md-3"><div class="stat-card"><h4><?php echo $total_participants; ?></h4><p class="mb-0">Total Peserta</p></div></div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-list"></i> Kelola Event</h6>
                    <p class="text-muted small mb-3">Lihat peserta, kirim pengumuman, dan terbitkan sertifikat dari satu tempat.</p>
                    <a href="index.php?page=organizer_events" class="btn btn-outline-primary btn-sm">Buka Halaman Kelola</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-plus-circle"></i> Tambah Event Baru</h6>
                    <p class="text-muted small mb-3">Buat event baru dengan detail lengkap, kuota, dan kontak panitia.</p>
                    <a href="index.php?page=organizer_create_event" class="btn btn-primary btn-sm">Buat Event</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-bullhorn"></i> Informasi ke Peserta</h6>
                    <p class="text-muted small mb-3">Berikan update penting seperti lokasi, dress code, dan pengumuman lanjutan.</p>
                    <a href="index.php?page=organizer_events" class="btn btn-outline-success btn-sm">Kirim Informasi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Event Saya</h4>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th>Status</th>
                        <th>Kuota</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_events->num_rows > 0): while ($event = $recent_events->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['judul']); ?></td>
                            <td><span class="badge bg-info text-dark"><?php echo ucfirst($event['status']); ?></span></td>
                            <td><?php echo (int)$event['kuota']; ?></td>
                            <td><?php echo date('d M Y', strtotime($event['tanggal'])); ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada event yang dibuat.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
