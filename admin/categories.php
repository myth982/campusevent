<?php
// ===========================
// ADMIN CATEGORIES PAGE
// ===========================
// File: admin/categories.php
// Fungsi: CRUD untuk kelola kategori

// db.php dan session.php sudah di-include oleh index.php
// requireAdmin() juga sudah di-handle di index.php

$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Sebelum hapus kategori, set event yang menggunakan kategori ini menjadi uncategorized
    $conn->query("UPDATE events SET kategori_id = NULL WHERE kategori_id = $id");
    $conn->query("DELETE FROM categories WHERE id = $id");
    $message = '<div class="alert alert-warning">✅ Kategori berhasil dihapus!</div>';
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $nama = $conn->real_escape_string($_POST['nama_kategori']);
    $conn->query("INSERT INTO categories (nama_kategori) VALUES ('$nama')");
    $message = '<div class="alert alert-success">✅ Kategori berhasil ditambahkan!</div>';
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $id = (int)$_POST['category_id'];
    $nama = $conn->real_escape_string($_POST['nama_kategori']);
    $conn->query("UPDATE categories SET nama_kategori = '$nama' WHERE id = $id");
    $message = '<div class="alert alert-success">✅ Kategori berhasil diperbarui!</div>';
}

// Get semua kategori
$categories = $conn->query("SELECT c.*, COUNT(e.id) as jml_event FROM categories c 
                           LEFT JOIN events e ON c.id = e.kategori_id 
                           GROUP BY c.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<?php include 'pages/components/navbar.php'; ?>

<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-tag"></i> Kelola Kategori</h1>

    <?php echo $message; ?>

    <!-- Form Tambah -->
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Kategori</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="nama_kategori" placeholder="Nama Kategori" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_category" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- List Kategori -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Daftar Kategori</h5>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Event</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($cat = $categories->fetch_assoc()) {
                        echo "<tr>
                            <td>{$cat['id']}</td>
                            <td>{$cat['nama_kategori']}</td>
                            <td><span class='badge bg-info'>{$cat['jml_event']}</span></td>
                            <td>
                                <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal{$cat['id']}'>
                                    <i class='fas fa-edit'></i> Edit
                                </button>
                                <a href='index.php?page=admin_categories&delete={$cat['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin hapus?\")'>
                                    <i class='fas fa-trash'></i> Hapus
                                </a>
                            </td>
                        </tr>";
                        
                        // Modal Edit
                        echo "
                        <div class='modal fade' id='editModal{$cat['id']}' tabindex='-1'>
                            <div class='modal-dialog'>
                                <div class='modal-content'>
                                    <div class='modal-header'>
                                        <h5 class='modal-title'>Edit Kategori</h5>
                                        <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                    </div>
                                    <form method='POST' action=''>
                                        <div class='modal-body'>
                                            <input type='hidden' name='category_id' value='{$cat['id']}'>
                                            <input type='text' class='form-control' name='nama_kategori' value='{$cat['nama_kategori']}' required>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                                            <button type='submit' name='edit_category' class='btn btn-primary'>Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>";
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
