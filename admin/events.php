<?php
// ===========================
// ADMIN EVENTS PAGE
// ===========================
// File: admin/events.php
// Fungsi: CRUD untuk kelola event

// db.php dan session.php sudah di-include oleh index.php
requireAdmin();

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get categories
$categories = $conn->query("SELECT id, nama_kategori FROM categories");

// Handle Delete
if ($action == 'delete' && $edit_id > 0) {
    $conn->query("DELETE FROM events WHERE id = $edit_id");
    $conn->query("DELETE FROM registrations WHERE event_id = $edit_id");
    $conn->query("DELETE FROM certificates WHERE event_id = $edit_id");
    $message = '<div class="alert alert-warning">✅ Event berhasil dihapus!</div>';
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $tanggal = $_POST['tanggal'];
    $lokasi = $conn->real_escape_string($_POST['lokasi']);
    $kuota = (int)$_POST['kuota'];
    $kategori_id = (int)$_POST['kategori_id'];
    $poster_url = $conn->real_escape_string($_POST['poster_url'] ?? '');
    $benefits = $conn->real_escape_string($_POST['benefits'] ?? '');
    $contact_whatsapp = $conn->real_escape_string($_POST['contact_whatsapp'] ?? '');
    $contact_email = $conn->real_escape_string($_POST['contact_email'] ?? '');

    if ($edit_id > 0) {
        // Update
        $query = "UPDATE events SET judul='$judul', deskripsi='$deskripsi', 
              tanggal='$tanggal', lokasi='$lokasi', kuota=$kuota, 
              kategori_id=$kategori_id, poster_url='$poster_url', benefits='$benefits', 
              contact_whatsapp='$contact_whatsapp', contact_email='$contact_email' WHERE id=$edit_id";
        $message = '<div class="alert alert-success">✅ Event berhasil diperbarui!</div>';
    } else {
        // Insert
        $query = "INSERT INTO events (judul, deskripsi, tanggal, lokasi, kuota, kategori_id, poster_url, benefits, contact_whatsapp, contact_email) 
             VALUES ('$judul', '$deskripsi', '$tanggal', '$lokasi', $kuota, $kategori_id, '$poster_url', '$benefits', '$contact_whatsapp', '$contact_email')";
        $message = '<div class="alert alert-success">✅ Event berhasil ditambahkan!</div>';
    }

    $conn->query($query);
    $edit_id = 0;
}

// Get data untuk edit
$edit_data = null;
if ($edit_id > 0) {
    $edit_data = $conn->query("SELECT * FROM events WHERE id = $edit_id")->fetch_assoc();
}

// Get semua events
$events = $conn->query("SELECT e.*, c.nama_kategori FROM events e 
                       LEFT JOIN categories c ON e.kategori_id = c.id 
                       ORDER BY e.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
        }
        .event-admin-card {
            border: 1px solid #e1ebf8;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 16px 35px rgba(15,23,42,0.06);
            background: #fff;
        }
        .event-admin-card .card-header {
            background: linear-gradient(135deg, #4f6f8f 0%, #86a9c9 100%);
            color: white;
            border: none;
            padding: 1.1rem 1.25rem;
        }
        .event-admin-table th {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            background: #f8fbff;
        }
        .event-admin-table td {
            vertical-align: middle;
            padding: 0.95rem 0.9rem;
        }
        .event-title-cell {
            min-width: 220px;
        }
        .event-title-text {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.2rem;
        }
        .event-title-sub {
            color: #64748b;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.7rem;
            border-radius: 999px;
            background: rgba(79,111,143,0.12);
            color: #4f6f8f;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .event-action-btn {
            border-radius: 999px;
            padding: 0.45rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php include 'pages/components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-calendar-alt"></i> Kelola Event</h1>

    <?php echo $message; ?>

    <!-- Form Tambah/Edit -->
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus"></i> <?php echo $edit_id > 0 ? 'Edit' : 'Tambah'; ?> Event</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul Event *</label>
                        <input type="text" class="form-control" name="judul" 
                               value="<?php echo $edit_data['judul'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori *</label>
                        <select name="kategori_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            $categories->data_seek(0);
                            while ($cat = $categories->fetch_assoc()) {
                                $selected = ($edit_data && $edit_data['kategori_id'] == $cat['id']) ? 'selected' : '';
                                echo "<option value='{$cat['id']}' $selected>{$cat['nama_kategori']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi *</label>
                    <textarea class="form-control" name="deskripsi" rows="4" required><?php echo $edit_data['deskripsi'] ?? ''; ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal & Waktu *</label>
                        <input type="datetime-local" class="form-control" name="tanggal" 
                               value="<?php echo $edit_data ? str_replace(' ', 'T', $edit_data['tanggal']) : ''; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Lokasi *</label>
                        <input type="text" class="form-control" name="lokasi" 
                               value="<?php echo $edit_data['lokasi'] ?? ''; ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Kuota Peserta *</label>
                        <input type="number" class="form-control" name="kuota" min="1"
                               value="<?php echo $edit_data['kuota'] ?? ''; ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Poster URL</label>
                        <input type="text" class="form-control" name="poster_url" 
                               value="<?php echo $edit_data['poster_url'] ?? ''; ?>" placeholder="Link poster atau path file">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kontak WhatsApp</label>
                        <input type="text" class="form-control" name="contact_whatsapp" 
                               value="<?php echo $edit_data['contact_whatsapp'] ?? ''; ?>" placeholder="0812xxxxx atau @whatsapp">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Benefit Event</label>
                    <textarea class="form-control" name="benefits" rows="3" placeholder="Contoh: Sertifikat, snack, doorprize"><?php echo $edit_data['benefits'] ?? ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Kontak</label>
                    <input type="email" class="form-control" name="contact_email" 
                           value="<?php echo $edit_data['contact_email'] ?? ''; ?>" placeholder="contoh@domain.com">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <?php if ($edit_id > 0): ?>
                        <a href="index.php?page=admin_events" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- List Events -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Event</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Kuota</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($event = $events->fetch_assoc()) {
                        $tanggal = date('d/m/Y H:i', strtotime($event['tanggal']));
                        echo "<tr>
                            <td>{$event['id']}</td>
                            <td>{$event['judul']}</td>
                            <td><span class='badge-category'>{$event['nama_kategori']}</span></td>
                            <td>{$tanggal}</td>
                            <td><span class='badge bg-info'>{$event['kuota']}</span></td>
                            <td>
                                <a href='index.php?page=admin_events&id={$event['id']}' class='btn btn-sm btn-warning'>
                                    <i class='fas fa-edit'></i> Edit
                                </a>
                                <a href='index.php?page=admin_events&action=delete&id={$event['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")'>
                                    <i class='fas fa-trash'></i> Hapus
                                </a>
                            </td>
                        </tr>";
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
