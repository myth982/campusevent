<?php
// ===========================
// ADMIN DASHBOARD PAGE
// ===========================
// File: admin/dashboard.php
// Fungsi: Dashboard admin dengan statistik sistem

// db.php dan session.php sudah di-include oleh index.php
// requireAdmin() juga sudah di-handle di index.php

$flash = getFlash();

// Get statistics
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'mahasiswa'")->fetch_assoc()['count'];
$total_registrations = $conn->query("SELECT COUNT(*) as count FROM registrations")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];
$pending_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'mahasiswa' AND account_status = 'pending'")->fetch_assoc()['count'];
$pending_organizers = $conn->query("SELECT COUNT(*) as count FROM organizer_applications WHERE status = 'pending'")->fetch_assoc()['count'];

// Get events dengan peserta terbanyak
$top_events = $conn->query("SELECT e.judul, COUNT(r.id) as peserta FROM events e 
                           LEFT JOIN registrations r ON e.id = r.event_id 
                           GROUP BY e.id 
                           ORDER BY peserta DESC 
                           LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'pages/components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <?php if ($flash): ?>
        <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <h1 class="mb-2"><i class="fas fa-cog"></i> Dashboard Admin</h1>
    <p class="text-muted mb-5">Ringkas, bersih, dan fokus pada statistik utama sistem.</p>

    <!-- Statistics -->
    <div class="row mb-5">
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card">
                <h3><?php echo $total_events; ?></h3>
                <p><i class="fas fa-calendar-alt"></i> Total Event</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card secondary">
                <h3><?php echo $total_users; ?></h3>
                <p><i class="fas fa-users"></i> Mahasiswa</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                <h3><?php echo $total_registrations; ?></h3>
                <p><i class="fas fa-user-check"></i> Registrasi</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #6f42c1 0%, #7c3aed 100%);">
                <h3><?php echo $total_categories; ?></h3>
                <p><i class="fas fa-tag"></i> Kategori</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #fd7e14 0%, #ff6b6b 100%);">
                <h3><?php echo $pending_users; ?></h3>
                <p><i class="fas fa-user-clock"></i> Menunggu Approval</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #0dcaf0 0%, #6f42c1 100%);">
                <h3><?php echo $pending_organizers; ?></h3>
                <p><i class="fas fa-clock"></i> Pending Organizer</p>
            </div>
        </div>
    </div>

    <!-- Top Events -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-fire"></i> Event Terpopuler</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama Event</th>
                                <th>Peserta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($event = $top_events->fetch_assoc()) {
                                echo "<tr>
                                    <td>{$event['judul']}</td>
                                    <td><span class='badge bg-success'>{$event['peserta']}</span></td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Sistem Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nama Sistem:</strong><br>Campus Event Hub</p>
                    <p><strong>Versi:</strong><br>1.0</p>
                    <p><strong>Database:</strong><br><?php echo $conn->get_server_info(); ?></p>
                    <p><strong>PHP Version:</strong><br><?php echo phpversion(); ?></p>
                    <hr>
                    <p style="font-size: 0.85rem; color: #666;">
                        <i class="fas fa-shield-alt"></i> Platform untuk manajemen kegiatan mahasiswa
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'pages/components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
