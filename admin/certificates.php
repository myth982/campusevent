<?php
// ===========================
// ADMIN CERTIFICATES PAGE
// ===========================
// File: admin/certificates.php
// Fungsi: Upload dan kelola sertifikat

// db.php dan session.php sudah di-include oleh index.php
// requireAdmin() juga sudah di-handle di index.php

$message = '';

// Handle upload sertifikat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_cert'])) {
    $user_id = (int)$_POST['user_id'];
    $event_id = (int)$_POST['event_id'];
    
    // Check apakah sudah ada sertifikat
    $check = $conn->query("SELECT id FROM certificates WHERE user_id = $user_id AND event_id = $event_id")->num_rows;
    
    if ($check > 0) {
        $message = '<div class="alert alert-warning">⚠ Sertifikat untuk peserta ini sudah ada!</div>';
    } elseif (!isset($_FILES['file_sertifikat']) || $_FILES['file_sertifikat']['error'] != 0) {
        $message = '<div class="alert alert-danger">❌ Silakan pilih file!</div>';
    } else {
        // Generate nama file unik
        $ext = pathinfo($_FILES['file_sertifikat']['name'], PATHINFO_EXTENSION);
        $filename = 'cert_' . $user_id . '_' . $event_id . '_' . time() . '.' . $ext;
        $upload_path = 'uploads/certificates/' . $filename;
        
        if (move_uploaded_file($_FILES['file_sertifikat']['tmp_name'], $upload_path)) {
            $conn->query("INSERT INTO certificates (user_id, event_id, file_sertifikat) 
                         VALUES ($user_id, $event_id, '$filename')");
            $message = '<div class="alert alert-success">✅ Sertifikat berhasil diupload!</div>';
        } else {
            $message = '<div class="alert alert-danger">❌ Gagal upload file!</div>';
        }
    }
}

// Get registrasi yang belum ada sertifikat
$registrations = $conn->query("SELECT r.id, u.nama as user_nama, e.judul as event_judul, e.id as event_id, u.id as user_id,
                              CASE WHEN c.id IS NOT NULL THEN 'Ada' ELSE 'Belum' END as status_cert
                              FROM registrations r
                              JOIN users u ON r.user_id = u.id
                              JOIN events e ON r.event_id = e.id
                              LEFT JOIN certificates c ON r.user_id = c.user_id AND r.event_id = c.event_id
                              ORDER BY r.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Sertifikat - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'pages/components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-certificate"></i> Upload Sertifikat</h1>

    <?php echo $message; ?>

    <!-- Form Upload -->
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-cloud-upload-alt"></i> Upload Sertifikat Peserta</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Pilih Peserta *</label>
                        <select name="user_id" class="form-select" id="user_select" required>
                            <option value="">-- Pilih Peserta --</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Event *</label>
                        <input type="text" class="form-control" id="event_name" disabled placeholder="Event akan ditampilkan">
                        <input type="hidden" name="event_id" id="event_id">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">File Sertifikat (PDF/Image) *</label>
                        <input type="file" class="form-control" name="file_sertifikat" accept=".pdf,.jpg,.png,.jpeg" required>
                    </div>
                </div>
                <button type="submit" name="upload_cert" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Sertifikat
                </button>
            </form>
        </div>
    </div>

    <!-- List Registrasi -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Status Sertifikat Peserta</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Peserta</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $data_array = [];
                    while ($reg = $registrations->fetch_assoc()) {
                        $data_array[] = $reg;
                    }
                    
                    foreach ($data_array as $reg) {
                        $status_badge = $reg['status_cert'] == 'Ada' ? 'bg-success' : 'bg-warning';
                        $status_icon = $reg['status_cert'] == 'Ada' ? 'fa-check-circle' : 'fa-hourglass-end';
                        
                        echo "<tr>
                            <td>{$reg['user_nama']}</td>
                            <td>{$reg['event_judul']}</td>
                            <td>
                                <span class='badge {$status_badge}'>
                                    <i class='fas {$status_icon}'></i> {$reg['status_cert']}
                                </span>
                            </td>
                            <td>
                                <button class='btn btn-sm btn-info' data-bs-toggle='modal' data-bs-target='#uploadModal' 
                                        onclick=\"setModalData({$reg['user_id']}, {$reg['event_id']}, '{$reg['user_nama']}', '{$reg['event_judul']}')\">
                                    <i class='fas fa-upload'></i> Upload
                                </button>
                            </td>
                        </tr>";
                    }
                    
                    // Generate script untuk populate select
                    echo "<script>
                    const registrationsData = " . json_encode($data_array) . ";
                    const selectEl = document.getElementById('user_select');
                    
                    registrationsData.forEach(reg => {
                        const option = document.createElement('option');
                        option.value = reg['user_id'];
                        option.textContent = reg['user_nama'];
                        option.dataset.eventId = reg['event_id'];
                        option.dataset.eventName = reg['event_judul'];
                        selectEl.appendChild(option);
                    });
                    
                    selectEl.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        document.getElementById('event_name').value = selectedOption.dataset.eventName || '';
                        document.getElementById('event_id').value = selectedOption.dataset.eventId || '';
                    });
                    
                    function setModalData(userId, eventId, userName, eventName) {
                        document.getElementById('user_select').value = userId;
                        document.getElementById('event_id').value = eventId;
                        document.getElementById('event_name').value = eventName;
                    }
                    </script>";
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
