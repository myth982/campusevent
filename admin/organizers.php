<?php
// ===========================
// ADMIN ORGANIZER MANAGEMENT
// ===========================

requireAdmin();
global $conn;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = (int)($_POST['application_id'] ?? 0);
    $review_notes = $conn->real_escape_string(trim($_POST['review_notes'] ?? ''));

    if ($application_id > 0) {
        if (isset($_POST['approve_application'])) {
            $conn->query("UPDATE organizer_applications SET status = 'approved', review_notes = '$review_notes', reviewed_at = NOW(), reviewed_by = {$_SESSION['user_id']} WHERE id = $application_id");
            $app = $conn->query("SELECT user_id FROM organizer_applications WHERE id = $application_id")->fetch_assoc();
            if ($app) {
                $conn->query("UPDATE users SET organizer_status = 'verified' WHERE id = {$app['user_id']}");
            }
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Permohonan organizer berhasil disetujui.</div>';
        } elseif (isset($_POST['revision_application'])) {
            $conn->query("UPDATE organizer_applications SET status = 'revision_requested', review_notes = '$review_notes', reviewed_at = NOW(), reviewed_by = {$_SESSION['user_id']} WHERE id = $application_id");
            $app = $conn->query("SELECT user_id FROM organizer_applications WHERE id = $application_id")->fetch_assoc();
            if ($app) {
                $conn->query("UPDATE users SET organizer_status = 'pending' WHERE id = {$app['user_id']}");
            }
            $message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Permohonan organizer dikembalikan untuk revisi.</div>';
        } elseif (isset($_POST['reject_application'])) {
            $conn->query("UPDATE organizer_applications SET status = 'rejected', review_notes = '$review_notes', reviewed_at = NOW(), reviewed_by = {$_SESSION['user_id']} WHERE id = $application_id");
            $app = $conn->query("SELECT user_id FROM organizer_applications WHERE id = $application_id")->fetch_assoc();
            if ($app) {
                $conn->query("UPDATE users SET organizer_status = 'rejected' WHERE id = {$app['user_id']}");
            }
            $message = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Permohonan organizer ditolak.</div>';
        }
    }
}

$applications = $conn->query("SELECT oa.*, u.nama, u.email FROM organizer_applications oa JOIN users u ON u.id = oa.user_id ORDER BY oa.created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Organizer - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include 'pages/components/navbar.php'; ?>
<div class="container-lg mt-5 mb-5">
    <h1 class="mb-4"><i class="fas fa-users-cog"></i> Kelola Organizer</h1>
    <?php echo $message; ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Organisasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($app = $applications->fetch_assoc()): ?>
                            <?php
                            $status_class = 'bg-secondary';
                            if ($app['status'] === 'approved') $status_class = 'bg-success';
                            elseif ($app['status'] === 'rejected') $status_class = 'bg-danger';
                            elseif ($app['status'] === 'revision_requested') $status_class = 'bg-warning text-dark';
                            else $status_class = 'bg-info text-dark';
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['nama']); ?></td>
                                <td><?php echo htmlspecialchars($app['email']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($app['organization_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($app['organization_type'] ?? '-'); ?> • <?php echo htmlspecialchars($app['university'] ?? '-'); ?></small>
                                </td>
                                <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?></span></td>
                                <td>
                                    <form method="POST" class="d-inline-block">
                                        <input type="hidden" name="application_id" value="<?php echo (int)$app['id']; ?>">
                                        <textarea class="form-control form-control-sm mb-2" name="review_notes" rows="2" placeholder="Catatan admin (opsional)"><?php echo htmlspecialchars($app['review_notes'] ?? ''); ?></textarea>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="submit" name="approve_application" class="btn btn-sm btn-success"><i class="fas fa-check"></i> Approve</button>
                                            <button type="submit" name="revision_application" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Need Revision</button>
                                            <button type="submit" name="reject_application" class="btn btn-sm btn-danger"><i class="fas fa-times"></i> Reject</button>
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
