<?php
// ===========================
// ADMIN PARTICIPANTS PAGE
// ===========================
// File: admin/participants.php
// Fungsi: Kelola peserta event

// db.php dan session.php sudah di-include oleh index.php
// requireAdmin() juga sudah di-handle di index.php

// Get event untuk filter
$event_filter = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

// Get semua events
$events = $conn->query("SELECT id, judul FROM events ORDER BY id DESC");

// Build query dengan filter
$query = "SELECT r.*, u.nama as user_nama, e.judul as event_judul FROM registrations r
         JOIN users u ON r.user_id = u.id
         JOIN events e ON r.event_id = e.id
         WHERE 1=1";

if ($event_filter > 0) {
    $query .= " AND r.event_id = $event_filter";
}

$query .= " ORDER BY r.id DESC";
$participants = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peserta - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'pages/components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-users"></i> Kelola Peserta</h1>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="page" value="admin_participants">
                <div class="col-md-8">
                    <select name="event_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Semua Event --</option>
                        <?php
                        $events->data_seek(0);
                        while ($evt = $events->fetch_assoc()) {
                            $selected = ($event_filter == $evt['id']) ? 'selected' : '';
                            echo "<option value='{$evt['id']}' $selected>{$evt['judul']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <?php if ($event_filter > 0): ?>
                        <a href="index.php?page=admin_participants" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Hapus Filter
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- List Peserta -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Peserta</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Peserta</th>
                        <th>Event</th>
                        <th>Tanggal Daftar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($participants->num_rows > 0) {
                        while ($p = $participants->fetch_assoc()) {
                            $tanggal = date('d/m/Y H:i', strtotime($p['tanggal_daftar']));
                            $status_badge = $p['status'] == 'terdaftar' ? 'bg-success' : 'bg-warning';
                            echo "<tr>
                                <td>{$p['id']}</td>
                                <td>{$p['user_nama']}</td>
                                <td>{$p['event_judul']}</td>
                                <td>{$tanggal}</td>
                                <td><span class='badge {$status_badge}'>{$p['status']}</span></td>
                            </tr>";
                        }
                    } else {
                        echo '<tr><td colspan="5" class="text-center text-muted">Belum ada peserta</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'pages/components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
