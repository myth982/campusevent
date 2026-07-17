<?php
// ===========================
// EVENT DETAIL PAGE
// ===========================
// File: pages/event_detail.php
// Fungsi: Menampilkan detail event dan tombol daftar

// db.php dan session.php sudah di-include oleh index.php

// Get event ID dari URL
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id == 0) {
    header("Location: index.php?page=events");
    exit();
}

// Get event detail
$event = $conn->query("SELECT e.*, c.nama_kategori FROM events e 
                      LEFT JOIN categories c ON e.kategori_id = c.id 
                      WHERE e.id = $event_id")->fetch_assoc();

if (!$event) {
    header("Location: index.php?page=events");
    exit();
}

$posterPath = '';
if (!empty($event['poster_url'])) {
    if (filter_var($event['poster_url'], FILTER_VALIDATE_URL)) {
        $posterPath = $event['poster_url'];
    } else {
        $posterPath = 'uploads/events/' . $event['poster_url'];
    }
}

// Get registrasi info
$is_registered = $conn->query("SELECT id FROM registrations 
                             WHERE user_id = {$_SESSION['user_id']} 
                             AND event_id = $event_id")->num_rows > 0;

// Get jumlah peserta
$peserta_count = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE event_id = $event_id")->fetch_assoc()['count'];

// Handle registrasi
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['daftar'])) {
    if (!$is_registered) {
        $user_id = $_SESSION['user_id'];
        $tanggal_daftar = date('Y-m-d H:i:s');
        $phone = $conn->real_escape_string($_POST['phone']);
        $contact_whatsapp = $conn->real_escape_string($_POST['contact_whatsapp']);
        $note = $conn->real_escape_string($_POST['note']);
        
        $insert = "INSERT INTO registrations (user_id, event_id, tanggal_daftar, status, phone, contact_whatsapp, note) 
                  VALUES ($user_id, $event_id, '$tanggal_daftar', 'terdaftar', '$phone', '$contact_whatsapp', '$note')";
        
        if ($conn->query($insert) === TRUE) {
            $is_registered = true;
            $peserta_count++;
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Berhasil terdaftar untuk event ini!</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan.</div>';
        }
    }
}

// Handle pembatalan registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['batal'])) {
    if ($is_registered) {
        $delete = "DELETE FROM registrations WHERE user_id = {$_SESSION['user_id']} AND event_id = $event_id";
        
        if ($conn->query($delete) === TRUE) {
            $is_registered = false;
            $peserta_count--;
            $message = '<div class="alert alert-warning"><i class="fas fa-info-circle"></i> Registrasi telah dibatalkan.</div>';
        }
    }
}

$tanggal = date('d M Y', strtotime($event['tanggal']));
$jam = date('H:i', strtotime($event['tanggal']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['judul']); ?> - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <!-- Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="index.php?page=events" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <?php echo $message; ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="event-detail-poster mb-3">
                    <?php if (!empty($posterPath) && (filter_var($posterPath, FILTER_VALIDATE_URL) || file_exists($posterPath))): ?>
                        <img src="<?php echo htmlspecialchars($posterPath); ?>" alt="Poster <?php echo htmlspecialchars($event['judul']); ?>" class="img-fluid rounded-top">
                    <?php else: ?>
                        <div class="event-detail-fallback">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <h1 class="mb-3"><?php echo htmlspecialchars($event['judul']); ?></h1>
                    
                    <div class="mb-4">
                        <span class="badge-category"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                    </div>

                    <h4 class="mt-4 mb-3"><i class="fas fa-info-circle"></i> Deskripsi Event</h4>
                    <p style="line-height: 1.8; color: #555;">
                        <?php echo nl2br(htmlspecialchars($event['deskripsi'])); ?>
                    </p>

                    <hr>

                    <h4 class="mt-4 mb-3"><i class="fas fa-details"></i> Detail Event</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <p><strong>Tanggal & Waktu:</strong></p>
                            <p><i class="fas fa-calendar-days text-primary"></i> <?php echo $tanggal; ?> pukul <?php echo $jam; ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p><strong>Lokasi:</strong></p>
                            <p><i class="fas fa-map-marker-alt text-danger"></i> <?php echo htmlspecialchars($event['lokasi']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p><strong>Kuota Peserta:</strong></p>
                            <p><i class="fas fa-users text-success"></i> <?php echo $event['kuota']; ?> peserta</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p><strong>Peserta Terdaftar:</strong></p>
                            <p><i class="fas fa-user-check text-info"></i> <?php echo $peserta_count; ?> peserta</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <p><strong>Status Kuota:</strong></p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo ($peserta_count / $event['kuota']) * 100; ?>%;" 
                                 aria-valuenow="<?php echo $peserta_count; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="<?php echo $event['kuota']; ?>">
                                <?php echo $peserta_count; ?>/<?php echo $event['kuota']; ?> (<?php echo round(($peserta_count / $event['kuota']) * 100, 1); ?>%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body">
                    <h4 class="mb-4"><i class="fas fa-check-square"></i> Daftar Event</h4>

                    <?php if ($is_registered): ?>
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle"></i> Anda sudah terdaftar untuk event ini!
                        </div>

                        <form method="POST" action="">
                            <button type="submit" name="batal" class="btn btn-danger w-100">
                                <i class="fas fa-times"></i> Batalkan Registrasi
                            </button>
                        </form>

                        <hr>

                        <p class="text-center text-muted small">
                            Jangan lupa hadir sesuai waktu dan lokasi event.
                        </p>
                    <?php else: ?>
                        <?php if ($peserta_count < $event['kuota']): ?>
                            <form method="POST" action="" class="event-register-form">
                                <div class="mb-3">
                                    <label class="form-label">Nomor HP</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="0812xxxxxxx" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" name="contact_whatsapp" class="form-control" placeholder="0812xxxxxxx atau @whatsapp">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan (opsional)</label>
                                    <textarea name="note" class="form-control" rows="3" placeholder="Contoh: Saya hadir bersama tim 3 orang"></textarea>
                                </div>
                                <button type="submit" name="daftar" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-paper-plane"></i> Daftar Sekarang
                                </button>
                            </form>
                            <p class="text-center text-muted small">
                                <i class="fas fa-check"></i> Kuota masih tersedia. Pastikan data kontak benar.
                            </p>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Kuota penuh
                            </div>
                            <button class="btn btn-secondary w-100" disabled>
                                <i class="fas fa-ban"></i> Kuota Penuh
                            </button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <hr>

                    <div class="text-center">
                        <p class="mb-0" style="font-size: 0.9rem; color: #666;">
                            <i class="fas fa-info-circle"></i> Pertanyaan? Hubungi panitia.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Events -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-star"></i> Event Serupa</h3>
        </div>
        
        <?php
        $related = $conn->query("SELECT e.*, c.nama_kategori FROM events e 
                               LEFT JOIN categories c ON e.kategori_id = c.id 
                               WHERE e.kategori_id = {$event['kategori_id']} 
                               AND e.id != $event_id 
                               LIMIT 3");
        
        if ($related->num_rows > 0) {
            while ($rel_event = $related->fetch_assoc()) {
                $rel_tanggal = date('d M Y', strtotime($rel_event['tanggal']));
        ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card event-card h-100">
                        <div class="event-card-img">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="event-card-body">
                            <h5 class="event-title"><?php echo htmlspecialchars($rel_event['judul']); ?></h5>
                            <div class="event-meta">
                                <span><i class="fas fa-calendar-days"></i> <?php echo $rel_tanggal; ?></span>
                            </div>
                            <div class="d-grid">
                                <a href="index.php?page=event_detail&id=<?php echo $rel_event['id']; ?>" class="btn btn-sm btn-primary">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
