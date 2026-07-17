<?php
// ===========================
// ADMIN USER APPROVAL PAGE
// ===========================

requireAdmin();
global $conn;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)($_POST['user_id'] ?? 0);
    if ($user_id > 0) {
        if (isset($_POST['approve_user'])) {
            $conn->query("UPDATE users SET account_status = 'approved', approved_at = NOW(), approved_by = {$_SESSION['user_id']} WHERE id = $user_id AND role = 'mahasiswa'");
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Akun pengguna berhasil disetujui.</div>';
        } elseif (isset($_POST['reject_user'])) {
            $conn->query("UPDATE users SET account_status = 'rejected', approved_at = NOW(), approved_by = {$_SESSION['user_id']} WHERE id = $user_id AND role = 'mahasiswa'");
            $message = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Akun pengguna ditolak.</div>';
        }
    }
}

$users = $conn->query("SELECT id, nama, email, account_status, created_at FROM users WHERE role = 'mahasiswa' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve User - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include 'pages/components/navbar.php'; ?>
<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-user-shield"></i> Kelola Approval User</h1>
    <?php echo $message; ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status Akun</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users->fetch_assoc()): ?>
                            <?php
                            $status_class = 'bg-secondary';
                            if ($user['account_status'] === 'approved') $status_class = 'bg-success';
                            elseif ($user['account_status'] === 'rejected') $status_class = 'bg-danger';
                            else $status_class = 'bg-warning text-dark';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($user['account_status'] ?? 'pending'); ?></span></td>
                                <td><?php echo date('d M Y H:i', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <form method="POST" class="d-inline-block">
                                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="submit" name="approve_user" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</button>
                                            <button type="submit" name="reject_user" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'pages/components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
