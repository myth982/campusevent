<?php
// ===========================
// EVENTS LIST PAGE
// ===========================
// File: pages/events.php
// Fungsi: Menampilkan list semua event dengan fitur search & filter

// db.php dan session.php sudah di-include oleh index.php

// Get categories untuk filter
$categories = $conn->query("SELECT id, nama_kategori FROM categories");

$stats = $conn->query("SELECT 
    (SELECT COUNT(*) FROM events) AS total_events,
    (SELECT COUNT(DISTINCT kategori_id) FROM events WHERE kategori_id IS NOT NULL) AS total_categories,
    (SELECT COUNT(*) FROM events WHERE tanggal >= CURDATE()) AS upcoming_events,
    (SELECT COUNT(*) FROM registrations) AS total_registrations")->fetch_assoc();

function getEventPalette($categoryName) {
    $category = strtolower(trim($categoryName));
    $palettes = [
        'seminar' => ['#8b5e3c', '#b7791f', '#f7efe6'],
        'workshop' => ['#4b5563', '#64748b', '#f3f4f6'],
        'olahraga' => ['#5b7c5b', '#7a9c74', '#f2f7f0'],
        'festival' => ['#6b7280', '#9ca3af', '#f5f5f4'],
        'kompetisi' => ['#6b7280', '#7c8b8f', '#f4f5f6'],
        'pelatihan' => ['#4b5563', '#64748b', '#f6f7fb'],
        'music' => ['#8b5e3c', '#a1784d', '#f7f2eb'],
        'teknologi' => ['#475569', '#64748b', '#f5f7fa'],
        'seni' => ['#7c6f57', '#a39174', '#f7f3eb']
    ];

    foreach ($palettes as $key => $palette) {
        if (strpos($category, $key) !== false) {
            return $palette;
        }
    }

    return ['#64748b', '#94a3b8', '#f8fafc'];
}

function getEventPoster($categoryName, $eventTitle) {
    $category = strtolower(trim($categoryName));
    $title = strtolower(trim($eventTitle));

    $posters = [
        'seminar' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=900&q=80',
        'workshop' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=900&q=80',
        'olahraga' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=900&q=80',
        'festival' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=900&q=80',
        'kompetisi' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80',
        'pelatihan' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80',
        'music' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=900&q=80',
        'teknologi' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=900&q=80',
        'seni' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=900&q=80'
    ];

    foreach ($posters as $key => $poster) {
        if (strpos($category, $key) !== false || strpos($title, $key) !== false) {
            return $poster;
        }
    }

    return 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=900&q=80';
}

function getEventImage($event) {
    if (!empty($event['poster_url'])) {
        if (filter_var($event['poster_url'], FILTER_VALIDATE_URL)) {
            return $event['poster_url'];
        }
        return 'uploads/events/' . $event['poster_url'];
    }
    return getEventPoster($event['nama_kategori'], $event['judul']);
}

function getEventIcon($categoryName) {
    $category = strtolower(trim($categoryName));
    $icons = [
        'seminar' => 'fa-microphone',
        'workshop' => 'fa-laptop-code',
        'olahraga' => 'fa-running',
        'festival' => 'fa-music',
        'kompetisi' => 'fa-trophy',
        'pelatihan' => 'fa-chalkboard-teacher',
        'music' => 'fa-music',
        'teknologi' => 'fa-code',
        'seni' => 'fa-palette'
    ];

    foreach ($icons as $key => $icon) {
        if (strpos($category, $key) !== false) {
            return $icon;
        }
    }

    return 'fa-calendar-days';
}

// Get search & filter parameters
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';

// Build query dengan search & filter
$query = "SELECT e.*, c.nama_kategori, COUNT(r.id) as jml_peserta FROM events e 
          LEFT JOIN categories c ON e.kategori_id = c.id
          LEFT JOIN registrations r ON e.id = r.event_id
          WHERE 1=1";

if ($search) {
    $query .= " AND (e.judul LIKE '%$search%' OR e.deskripsi LIKE '%$search%' OR e.lokasi LIKE '%$search%')";
}

if ($category_filter > 0) {
    $query .= " AND e.kategori_id = $category_filter";
}

switch ($sort) {
    case 'popular':
        $query .= " GROUP BY e.id ORDER BY jml_peserta DESC, e.tanggal ASC";
        break;
    case 'soon':
        $query .= " GROUP BY e.id ORDER BY e.tanggal ASC";
        break;
    default:
        $query .= " GROUP BY e.id ORDER BY e.id DESC";
        break;
}
$events = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255,247,237,0.95), transparent 28%),
                radial-gradient(circle at top right, rgba(236,253,245,0.95), transparent 32%),
                linear-gradient(135deg, #fdf8f2 0%, #f3f7ff 45%, #f8fafc 100%);
            color: #1f2937;
            min-height: 100vh;
            line-height: 1.6;
        }

        .events-page {
            padding: 2.5rem max(2rem, calc((100vw - 1200px) / 2)) 4rem;
            width: 100%;
        }

        .hero-panel {
            background: linear-gradient(135deg, #314a62 0%, #5b7ea0 45%, #8fb0c9 100%);
            border-radius: 28px;
            padding: 2.2rem 2.4rem;
            color: white;
            box-shadow: 0 24px 60px rgba(15,23,42,0.16);
            margin-bottom: 1.8rem;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .hero-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 30%);
            pointer-events: none;
        }

        .hero-panel h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.6rem;
            position: relative;
            z-index: 1;
            letter-spacing: -0.4px;
        }

        .hero-panel p {
            margin: 0;
            opacity: 0.96;
            position: relative;
            z-index: 1;
            font-size: 1.02rem;
            max-width: 760px;
        }

        .filter-card {
            background: rgba(255,255,255,0.98);
            border: 1px solid #e5eefc;
            border-radius: 28px;
            padding: 1.6rem 1.4rem;
            box-shadow: 0 24px 48px rgba(15,23,42,0.08);
            margin-bottom: 1.6rem;
        }

        .filter-card .form-control,
        .filter-card .form-select {
            border-radius: 18px;
            border: 1px solid #d7e7ff;
            padding: 0.95rem 1rem;
            color: #334155;
            min-height: 56px;
        }

        .filter-card .form-control::placeholder {
            color: #94a3b8;
        }

        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #6b8fb3;
            box-shadow: 0 0 0 0.18rem rgba(107, 143, 179, 0.22);
        }

        .filter-card .input-group {
            gap: 0.75rem;
        }

        .filter-card .btn-search {
            border-radius: 18px;
            padding: 0.92rem 1.4rem;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            border: none;
            color: white;
            font-weight: 700;
            box-shadow: 0 10px 22px rgba(59,130,246,0.18);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .filter-card .btn-search:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(59,130,246,0.2);
        }

        .filter-note {
            font-size: 0.92rem;
            color: #64748b;
            margin-top: 0.8rem;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.9rem;
            margin-bottom: 1.2rem;
        }

        .stat-pill {
            background: rgba(255,255,255,0.95);
            border: 1px solid #e5eefc;
            border-radius: 18px;
            padding: 0.95rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        }

        .stat-pill i {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            color: #5b6474;
        }

        .stat-pill strong {
            display: block;
            font-size: 1rem;
            color: #111827;
        }

        .stat-pill span {
            font-size: 0.82rem;
            color: #64748b;
        }

        .alert-modern {
            background: rgba(255,255,255,0.96);
            border: 1px solid #e5eefc;
            border-radius: 16px;
            color: #475569;
            box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        }

        .event-card {
            border: 1px solid #e5eefc;
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 16px 36px rgba(15,23,42,0.06);
            transition: all 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 20px 42px rgba(15,23,42,0.12);
            border-color: #94a3b8;
        }

        .event-card-media {
            position: relative;
            overflow: hidden;
            height: 210px;
        }

        .event-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .event-card-media::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15,23,42,0.05) 0%, rgba(15,23,42,0.35) 100%);
            pointer-events: none;
        }

        .event-card-badge {
            position: absolute;
            top: 0.9rem;
            left: 0.9rem;
            z-index: 2;
            padding: 0.4rem 0.7rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(6px);
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .event-card-badge .badge-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            background: rgba(255,255,255,0.18);
            color: white;
        }

        .event-card-body {
            padding: 1.2rem 1.2rem 1.3rem;
        }

        .event-title {
            font-size: 1.08rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
            line-height: 1.35;
        }

        .event-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #4b5563;
            font-size: 0.9rem;
            margin-bottom: 0.48rem;
        }

        .event-meta i {
            color: #6b8fb3;
            min-width: 15px;
        }

        .badge-category {
            display: inline-block;
            margin-top: 0.4rem;
            background: rgba(107, 143, 179, 0.14);
            color: #4f6f8f;
            padding: 0.38rem 0.8rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1px solid rgba(107, 143, 179, 0.18);
        }

        .progress {
            border-radius: 999px;
            overflow: hidden;
            background: #eef2f7;
            height: 20px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #4f6f8f 0%, #8fb0c9 100%);
            color: #fff;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .btn-event {
            background: linear-gradient(135deg, #4f6f8f 0%, #8fb0c9 100%);
            color: white;
            border: none;
            border-radius: 999px;
            padding: 0.74rem 1rem;
            font-weight: 700;
        }

        .btn-event:hover {
            filter: brightness(1.04);
            transform: translateY(-1px);
            color: white;
            box-shadow: 0 10px 20px rgba(71, 85, 105, 0.16);
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<div class="container-lg events-page">
    <div class="hero-panel">
        <h1><i class="fas fa-calendar-days"></i> Temukan Event yang Cocok untukmu</h1>
        <p>Jelajahi acara kampus yang menarik, nyaman dibaca, dan mudah dipilih sesuai minat serta kegiatanmu.</p>
    </div>

    <div class="stats-row">
        <div class="stat-pill">
            <i class="fas fa-calendar-check"></i>
            <div>
                <strong><?php echo (int)$stats['total_events']; ?></strong>
                <span>Total Event</span>
            </div>
        </div>
        <div class="stat-pill">
            <i class="fas fa-layer-group"></i>
            <div>
                <strong><?php echo (int)$stats['total_categories']; ?></strong>
                <span>Kategori</span>
            </div>
        </div>
        <div class="stat-pill">
            <i class="fas fa-clock"></i>
            <div>
                <strong><?php echo (int)$stats['upcoming_events']; ?></strong>
                <span>Akan Datang</span>
            </div>
        </div>
        <div class="stat-pill">
            <i class="fas fa-users"></i>
            <div>
                <strong><?php echo (int)$stats['total_registrations']; ?></strong>
                <span>Peserta Terdaftar</span>
            </div>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="">
            <input type="hidden" name="page" value="events">
            <div class="row g-3 align-items-end">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">Cari Event</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:18px 0 0 18px; background:#f8fbff; border-color:#d7e7ff;"><i class="fas fa-search text-primary"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Masukkan nama, lokasi, atau kata kunci" value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-search" type="submit">Cari</button>
                    </div>
                    <div class="filter-note">Tekan Enter atau klik Cari untuk menemukan event yang cocok.</div>
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        <?php
                        $categories->data_seek(0);
                        while ($cat = $categories->fetch_assoc()) {
                            $selected = ($category_filter == $cat['id']) ? 'selected' : '';
                            echo "<option value='{$cat['id']}' $selected>{$cat['nama_kategori']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label fw-semibold">Urutkan</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="latest" <?php echo ($sort === 'latest') ? 'selected' : ''; ?>>Paling Baru</option>
                        <option value="popular" <?php echo ($sort === 'popular') ? 'selected' : ''; ?>>Paling Populer</option>
                        <option value="soon" <?php echo ($sort === 'soon') ? 'selected' : ''; ?>>Akan Datang</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filters Info -->
    <?php if ($search || $category_filter > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-modern">
                    <i class="fas fa-filter"></i> Filter aktif:
                    <?php if ($search): ?>
                        <span class="badge bg-primary">Pencarian: "<?php echo htmlspecialchars($search); ?>"</span>
                    <?php endif; ?>
                    <?php if ($category_filter > 0): ?>
                        <span class="badge bg-primary">Kategori: 
                            <?php 
                            $cat = $conn->query("SELECT nama_kategori FROM categories WHERE id = $category_filter")->fetch_assoc();
                            echo $cat['nama_kategori'];
                            ?>
                        </span>
                    <?php endif; ?>
                    <a href="index.php?page=events" class="ms-2">Hapus Filter</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Events List -->
    <div class="row">
        <?php
        if ($events->num_rows > 0) {
            while ($event = $events->fetch_assoc()) {
                $tanggal = date('d M Y', strtotime($event['tanggal']));
                $kuota_tersisa = $event['kuota'] > 0 ? ($event['kuota'] - $event['jml_peserta']) : 'Unlimited';
        ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card event-card h-100 fade-in">
                        <?php
                        $palette = getEventPalette($event['nama_kategori']);
                        $poster = getEventImage($event);
                        $icon = getEventIcon($event['nama_kategori']);
                        $progressWidth = ($event['kuota'] > 0) ? min(100, (($event['jml_peserta'] / $event['kuota']) * 100)) : 100;
                        ?>
                        <div class="event-card-media" style="background: linear-gradient(135deg, <?php echo $palette[0]; ?> 0%, <?php echo $palette[1]; ?> 100%);">
                            <img src="<?php echo htmlspecialchars($poster); ?>" alt="Poster <?php echo htmlspecialchars($event['judul']); ?>">
                            <div class="event-card-badge">
                                <span class="badge-icon" style="background: rgba(255,255,255,0.24);"><i class="fas <?php echo $icon; ?>"></i></span>
                                <?php echo htmlspecialchars($event['nama_kategori']); ?>
                            </div>
                        </div>
                        <div class="event-card-body">
                            <h5 class="event-title"><?php echo htmlspecialchars($event['judul']); ?></h5>
                            
                            <div class="event-meta">
                                <span><i class="fas fa-calendar-days"></i> <?php echo $tanggal; ?></span>
                            </div>
                            <div class="event-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lokasi']); ?></span>
                            </div>
                            <div class="event-meta">
                                <span><i class="fas fa-users"></i> Peserta: <?php echo $event['jml_peserta']; ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge-category" style="background: <?php echo $palette[2]; ?>; color: <?php echo $palette[0]; ?>;"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                            </div>
                            
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?php echo $progressWidth; ?>%;" 
                                     aria-valuenow="<?php echo $event['jml_peserta']; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="<?php echo $event['kuota']; ?>">
                                    <?php echo $event['jml_peserta']; ?>/<?php echo $event['kuota']; ?>
                                </div>
                            </div>
                            
                            <p style="font-size: 0.9rem; color: #666;">
                                <?php echo substr(htmlspecialchars($event['deskripsi']), 0, 80) . '...'; ?>
                            </p>
                            
                            <div class="d-grid gap-2">
                                <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" class="btn btn-event btn-sm">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-modern">Tidak ada event yang sesuai dengan pencarian Anda.</div></div>';
        }
        ?>
    </div>
</div>

<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
