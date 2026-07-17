<?php
// ===========================
// MY EVENTS PAGE
// ===========================
// File: pages/my_events.php
// Fungsi: Menampilkan event yang sudah didaftar

// db.php dan session.php sudah di-include oleh index.php

// Get event saya
$my_events = $conn->query("SELECT e.*, c.nama_kategori, r.tanggal_daftar, r.status 
                          FROM registrations r
                          JOIN events e ON r.event_id = e.id
                          LEFT JOIN categories c ON e.kategori_id = c.id
                          WHERE r.user_id = {$_SESSION['user_id']}
                          ORDER BY e.tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Saya - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-bookmark"></i> Event Saya</h1>
    <p class="mb-4">Daftar event yang telah kamu ikuti</p>

    <div class="row">
        <?php
        if ($my_events->num_rows > 0) {
            while ($event = $my_events->fetch_assoc()) {
                $tanggal = date('d M Y', strtotime($event['tanggal']));
                $tanggal_daftar = date('d M Y H:i', strtotime($event['tanggal_daftar']));
                
                // Status badge
                $status_class = $event['status'] == 'terdaftar' ? 'bg-success' : 'bg-warning';
                $status_icon = $event['status'] == 'terdaftar' ? 'fa-check' : 'fa-hourglass-end';
        ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 fade-in">
                        <div style="background: linear-gradient(135deg, #1a5f3e 0%, #2d8f5e 100%); height: 150px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($event['judul']); ?></h5>
                            
                            <div class="mb-2">
                                <span class="badge-category"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                            </div>

                            <p class="mb-2" style="font-size: 0.9rem; color: #666;">
                                <i class="fas fa-calendar-days"></i> <?php echo $tanggal; ?>
                            </p>

                            <p class="mb-2" style="font-size: 0.9rem; color: #666;">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lokasi']); ?>
                            </p>

                            <p class="mb-3" style="font-size: 0.9rem;">
                                <strong>Terdaftar:</strong> <?php echo $tanggal_daftar; ?><br>
                                <span class="badge <?php echo $status_class; ?>">
                                    <i class="fas <?php echo $status_icon; ?>"></i> <?php echo ucfirst($event['status']); ?>
                                </span>
                            </p>

                            <div class="d-grid gap-2">
                                <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info"><i class="fas fa-info-circle"></i> Anda belum mendaftar event apapun. <a href="index.php?page=events">Daftar sekarang!</a></div></div>';
        }
        ?>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
