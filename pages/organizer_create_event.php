<?php
requireLogin();
global $conn;

$user_id = (int)$_SESSION['user_id'];
$user = $conn->query("SELECT id, organizer_status FROM users WHERE id = $user_id")->fetch_assoc();

if (($user['organizer_status'] ?? 'none') !== 'verified') {
    header('Location: index.php?page=organizer_apply');
    exit();
}

$message = '';
$categories = $conn->query("SELECT id, nama_kategori FROM categories ORDER BY nama_kategori");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publish_event'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kategori_id = (int)$_POST['kategori_id'];
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $jam_mulai = $conn->real_escape_string($_POST['jam_mulai']);
    $jam_selesai = $conn->real_escape_string($_POST['jam_selesai']);
    $timezone = $conn->real_escape_string($_POST['timezone']);
    $lokasi = $conn->real_escape_string($_POST['lokasi']);
    $event_type = $conn->real_escape_string($_POST['event_type']);
    $location_details = $conn->real_escape_string($_POST['location_details']);
    $kuota = (int)$_POST['kuota'];
    $registration_deadline = $conn->real_escape_string($_POST['registration_deadline']);
    $poster_url = $conn->real_escape_string($_POST['poster_url']);
    $benefits = $conn->real_escape_string($_POST['benefits']);
    $requirements = $conn->real_escape_string($_POST['requirements']);
    $contact_name = $conn->real_escape_string($_POST['contact_name']);
    $contact_whatsapp = $conn->real_escape_string($_POST['contact_whatsapp']);
    $contact_email = $conn->real_escape_string($_POST['contact_email']);

    $poster_filename = '';
    if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['poster_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed, true)) {
            $poster_filename = 'event_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $upload_path = 'uploads/events/' . $poster_filename;
            if (!is_dir('uploads/events')) {
                mkdir('uploads/events', 0777, true);
            }
            if (move_uploaded_file($_FILES['poster_file']['tmp_name'], $upload_path)) {
                $poster_url = $poster_filename;
            } else {
                $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal mengupload poster/banner.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Format poster/banner tidak didukung. Gunakan JPG, PNG, atau WEBP.</div>';
        }
    }

    if (empty($message)) {
        $full_datetime = $tanggal . ' ' . $jam_mulai;
        $event_poster = $conn->real_escape_string($poster_url);
        if (!empty($poster_filename)) {
            $event_poster = $conn->real_escape_string($poster_filename);
        }

        $insert = "INSERT INTO events (judul, deskripsi, tanggal, lokasi, kuota, kategori_id, organizer_id, event_type, location_details, registration_deadline, poster_url, benefits, requirements, contact_name, contact_whatsapp, contact_email, status) VALUES ('$judul', '$deskripsi', '$full_datetime', '$lokasi', $kuota, $kategori_id, $user_id, '$event_type', '$location_details', '$registration_deadline', '$event_poster', '$benefits', '$requirements', '$contact_name', '$contact_whatsapp', '$contact_email', 'published')";

        if ($conn->query($insert) === true) {
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Event berhasil dipublikasikan.</div>';
        } else {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Gagal membuat event.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: linear-gradient(135deg, #f4f9ff 0%, #eef5ff 45%, #f8fbff 100%); }
        .create-shell { padding: 2rem 0 4rem; }
        .form-card { border-radius: 28px; border: 1px solid #e4edf9; box-shadow: 0 20px 45px rgba(15,23,42,0.08); background: white; overflow: hidden; }
        .hero-panel { background: linear-gradient(135deg, #35546d 0%, #6f8fa8 100%); color: white; padding: 1.7rem 2rem; }
        .hero-panel h3 { font-weight: 700; }
        .section-card { background: #f8fbff; border: 1px solid #e7eef8; border-radius: 20px; padding: 1rem 1.1rem; height: 100%; }
        .section-title { color: #35546d; font-weight: 700; font-size: 0.95rem; margin-bottom: 0.6rem; display: flex; align-items: center; gap: 0.5rem; }
        .form-control, .form-select, textarea { border-radius: 14px; border: 1px solid #dbe7f8; padding: 0.75rem 0.9rem; background: #fff; }
        .form-control:focus, .form-select:focus, textarea:focus { border-color: #8fb0c9; box-shadow: 0 0 0 0.2rem rgba(143,176,201,0.2); }
        .btn-publish { background: linear-gradient(135deg, #35546d 0%, #6f8fa8 100%); border: none; border-radius: 999px; padding: 0.8rem 1.3rem; font-weight: 700; }
        .btn-publish:hover { transform: translateY(-1px); box-shadow: 0 10px 18px rgba(53,84,109,0.18); }
        .hint-badge { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: #64748b; background: #eff6ff; border-radius: 999px; padding: 0.35rem 0.7rem; }
    </style>
</head>
<body>
<?php include 'components/navbar.php'; ?>
<div class="container-lg create-shell">
    <div class="card form-card">
        <div class="hero-panel">
            <h3 class="mb-2"><i class="fas fa-calendar-plus"></i> Buat Event Baru</h3>
            <p class="mb-0">Isi detail acara Anda dengan rapi, lalu publikasikan agar event tampil menarik dan mudah diikuti peserta.</p>
        </div>
        <div class="card-body p-4 p-lg-5">
            <?php echo $message; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-title"><i class="fas fa-info-circle"></i> Informasi Dasar</div>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Nama Event</label><input type="text" class="form-control" name="judul" placeholder="Contoh: Seminar AI untuk Mahasiswa" required></div>
                                <div class="col-md-6"><label class="form-label">Kategori</label><select class="form-select" name="kategori_id" required><?php while ($cat = $categories->fetch_assoc()): ?><option value="<?php echo (int)$cat['id']; ?>"><?php echo htmlspecialchars($cat['nama_kategori']); ?></option><?php endwhile; ?></select></div>
                                <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="4" placeholder="Jelaskan tentang event, manfaat, dan apa yang peserta dapatkan" required></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-title"><i class="fas fa-image"></i> Poster & Media</div>
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6"><label class="form-label">Upload Poster/Banner</label><input type="file" class="form-control" name="poster_file" accept=".jpg,.jpeg,.png,.webp"></div>
                                <div class="col-md-6"><label class="form-label">Poster/Banner URL (opsional)</label><input type="text" class="form-control" name="poster_url" placeholder="Bisa diisi jika ingin pakai link"></div>
                            </div>
                            <div class="mt-2"><span class="hint-badge"><i class="fas fa-circle-info"></i> Format yang didukung: JPG, PNG, WEBP.</span></div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-title"><i class="fas fa-clock"></i> Jadwal & Lokasi</div>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" required></div>
                                <div class="col-md-3"><label class="form-label">Jam Mulai</label><input type="time" class="form-control" name="jam_mulai" required></div>
                                <div class="col-md-3"><label class="form-label">Jam Selesai</label><input type="time" class="form-control" name="jam_selesai"></div>
                                <div class="col-md-4"><label class="form-label">Timezone</label><input type="text" class="form-control" name="timezone" value="Asia/Jakarta"></div>
                                <div class="col-md-4"><label class="form-label">Mode Event</label><select class="form-select" name="event_type"><option value="offline">Offline</option><option value="online">Online</option><option value="hybrid">Hybrid</option></select></div>
                                <div class="col-md-4"><label class="form-label">Lokasi</label><input type="text" class="form-control" name="lokasi" placeholder="Aula, Zoom, atau link" required></div>
                                <div class="col-12"><label class="form-label">Detail Lokasi / Link</label><textarea class="form-control" name="location_details" rows="2" placeholder="Tambahkan detail tempat, link meeting, atau petunjuk akses"></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-title"><i class="fas fa-users"></i> Peserta & Informasi Tambahan</div>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Kuota Peserta</label><input type="number" class="form-control" name="kuota" min="1" value="100" required></div>
                                <div class="col-md-6"><label class="form-label">Deadline Registrasi</label><input type="datetime-local" class="form-control" name="registration_deadline"></div>
                                <div class="col-12"><label class="form-label">Benefit</label><textarea class="form-control" name="benefits" rows="2" placeholder="Contoh: Sertifikat, Snack, Doorprize"></textarea></div>
                                <div class="col-12"><label class="form-label">Persyaratan</label><textarea class="form-control" name="requirements" rows="2" placeholder="Contoh: Mahasiswa aktif, Laptop"></textarea></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-title"><i class="fas fa-headset"></i> Kontak Panitia</div>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Contact Person</label><input type="text" class="form-control" name="contact_name"></div>
                                <div class="col-md-4"><label class="form-label">WhatsApp</label><input type="text" class="form-control" name="contact_whatsapp"></div>
                                <div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" name="contact_email"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
                    <span class="hint-badge"><i class="fas fa-shield-alt"></i> Semua data akan disimpan aman dan tampil di halaman event.</span>
                    <button type="submit" name="publish_event" class="btn btn-primary btn-publish"><i class="fas fa-paper-plane"></i> Publish Event</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'components/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
