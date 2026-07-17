<?php
// ===========================
// MY CERTIFICATES PAGE
// ===========================
// File: pages/certificates.php
// Fungsi: Menampilkan sertifikat yang diterima

// db.php dan session.php sudah di-include oleh index.php

// Get sertifikat saya
$certificates = $conn->query("SELECT c.*, e.judul as event_name, e.tanggal 
                             FROM certificates c
                             JOIN events e ON c.event_id = e.id
                             WHERE c.user_id = {$_SESSION['user_id']}
                             ORDER BY c.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Saya - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-certificate"></i> Sertifikat Saya</h1>
    <p class="mb-4">Koleksi sertifikat dari event yang telah kamu ikuti</p>

    <div class="row">
        <?php
        if ($certificates->num_rows > 0) {
            while ($cert = $certificates->fetch_assoc()) {
                $tanggal = date('d M Y', strtotime($cert['tanggal']));
        ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 fade-in">
                        <div style="background: linear-gradient(135deg, #ffc107 0%, #ff7f00 100%); height: 200px; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                            <i class="fas fa-award"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title" style="color: #ff7f00;">
                                <i class="fas fa-star"></i> <?php echo htmlspecialchars($cert['event_name']); ?>
                            </h5>

                            <p class="mb-3" style="font-size: 0.9rem; color: #666;">
                                <i class="fas fa-calendar-days"></i> <?php echo $tanggal; ?>
                            </p>

                            <div class="alert alert-success mb-3" style="font-size: 0.85rem;">
                                <i class="fas fa-check-circle"></i> Sertifikat Resmi
                            </div>

                            <div class="d-grid gap-2">
                                <a href="uploads/certificates/<?php echo htmlspecialchars($cert['file_sertifikat']); ?>" class="btn btn-warning" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <a href="uploads/certificates/<?php echo htmlspecialchars($cert['file_sertifikat']); ?>" class="btn btn-outline-warning" target="_blank">
                                    <i class="fas fa-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info"><i class="fas fa-info-circle"></i> Anda belum memiliki sertifikat. Ikuti event dan tunggu panitia menerbitkan sertifikat.</div></div>';
        }
        ?>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
