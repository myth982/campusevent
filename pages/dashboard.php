<?php
// Dashboard Modern dengan Design Cliento-style
$total_events = $conn->query("SELECT COUNT(*) as count FROM events")->fetch_assoc()['count'];
$my_events = $conn->query("SELECT COUNT(*) as count FROM registrations WHERE user_id = {$_SESSION['user_id']}")->fetch_assoc()['count'];
$my_certificates = $conn->query("SELECT COUNT(*) as count FROM certificates WHERE user_id = {$_SESSION['user_id']}")->fetch_assoc()['count'];

$latest_events = $conn->query("SELECT e.*, c.nama_kategori FROM events e 
                              LEFT JOIN categories c ON e.kategori_id = c.id 
                              ORDER BY e.id DESC LIMIT 6");

$popular_events = $conn->query("SELECT e.*, c.nama_kategori, COUNT(r.id) as jml_peserta FROM events e 
                               LEFT JOIN categories c ON e.kategori_id = c.id
                               LEFT JOIN registrations r ON e.id = r.event_id
                               GROUP BY e.id
                               ORDER BY jml_peserta DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Campus Event Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255, 248, 240, 0.98), transparent 30%),
                radial-gradient(circle at top right, rgba(224, 242, 254, 0.9), transparent 35%),
                linear-gradient(135deg, #f9f3ea 0%, #eef5ff 45%, #f8fafc 100%);
            min-height: 100vh;
            color: #334155;
            overflow-x: hidden;
        }

        .navbar-modern {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(14px);
            padding: 1rem max(2rem, calc((100vw - 1200px) / 2));
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            width: 100%;
            box-sizing: border-box;
        }

        .navbar-modern::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(245,158,11,0.04), rgba(37,99,235,0.04));
            pointer-events: none;
        }

        .navbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
            color: #0f172a;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .navbar-logo img {
            height: 42px;
        }

        .navbar-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex: 1;
            justify-content: center;
        }

        .navbar-menu a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: color 0.3s ease;
            position: relative;
        }

        .navbar-menu a:hover,
        .navbar-menu a.active {
            color: #f59e0b;
        }

        .navbar-menu a.active::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -0.35rem;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #f59e0b, #f97316);
            border-radius: 999px;
        }

        .navbar-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-nav {
            padding: 0.6rem 1.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }

        .btn-nav-account {
            background: #f8fafc;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-nav-account:hover {
            border-color: #f59e0b;
            color: #b45309;
        }

        .btn-nav-logout {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
        }

        .btn-nav-logout:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);
        }

        .dashboard-container {
            padding: 3.5rem max(2rem, calc((100vw - 1200px) / 2)) 4rem;
            width: 100%;
        }

        .dashboard-hero {
            background: linear-gradient(135deg, #0f5132 0%, #1f7a4a 42%, #f59e0b 100%);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 30px;
            padding: 2.4rem 2.6rem;
            box-shadow: 0 24px 60px rgba(15,23,42,0.16);
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 30%),
                linear-gradient(120deg, rgba(255,255,255,0.08), transparent 45%);
            pointer-events: none;
        }

        .dashboard-hero::after {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            right: -80px;
            bottom: -100px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            filter: blur(6px);
            pointer-events: none;
        }

        .hero-copy {
            position: relative;
            z-index: 1;
            max-width: 640px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.9rem;
        }

        .dashboard-hero h1 {
            font-size: 2.1rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.6rem;
        }

        .dashboard-hero p {
            color: rgba(255,255,255,0.92);
            font-size: 1rem;
            margin: 0;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.1rem;
        }

        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.75rem 1rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-hero-primary {
            background: white;
            color: #1a5f3e;
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.15);
        }

        .btn-hero-light {
            background: rgba(255,255,255,0.16);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .btn-hero-light:hover {
            background: rgba(255,255,255,0.28);
            transform: translateY(-2px);
        }

        .hero-highlight {
            position: relative;
            z-index: 1;
            min-width: 280px;
            background: rgba(255,255,255,0.97);
            border-radius: 20px;
            padding: 1.25rem 1.3rem;
            box-shadow: 0 16px 32px rgba(0,0,0,0.14);
            border: 1px solid rgba(255,255,255,0.7);
        }

        .hero-highlight-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #1a5f3e;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .hero-highlight-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.35rem;
        }

        .hero-highlight p {
            color: #64748b;
            font-size: 0.92rem;
            line-height: 1.6;
            margin: 0;
        }

        .stats-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .stat-card-modern {
            background: linear-gradient(145deg, rgba(255,255,255,0.97), rgba(248,250,252,0.92));
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            border-radius: 22px;
            box-shadow: 0 14px 32px rgba(15,23,42,0.06);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: -10px;
            right: -10px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(245,158,11,0.18), transparent 70%);
            pointer-events: none;
        }

        .stat-card-modern:hover .stat-icon {
            transform: rotate(6deg) scale(1.05);
        }

        .stat-card-modern:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 35px rgba(15,23,42,0.08);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, rgba(245,158,11,0.18), rgba(249,115,22,0.12));
            color: #d97706;
            font-size: 1.2rem;
        }

        .stat-card-modern h3 {
            font-size: 1.7rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.2rem;
        }

        .stat-card-modern p {
            color: #64748b;
            font-size: 0.95rem;
            margin: 0;
        }

        .content-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.98), rgba(248,250,252,0.95));
            border: 1px solid #e2e8f0;
            border-radius: 26px;
            padding: 2rem;
            box-shadow: 0 18px 40px rgba(15,23,42,0.05);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.3rem;
        }

        .content-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin: 0;
        }

        .section-link {
            text-decoration: none;
            color: #f59e0b;
            font-size: 0.9rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .section-link:hover {
            color: #d97706;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-link-card {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 1rem 1.1rem;
            border-radius: 18px;
            text-decoration: none;
            color: #0f172a;
            background: linear-gradient(145deg, rgba(255,255,255,0.98), rgba(248,250,252,0.95));
            border: 1px solid #e2e8f0;
            box-shadow: 0 12px 28px rgba(15,23,42,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .quick-link-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(245,158,11,0.08), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .quick-link-card:hover::after {
            transform: translateX(100%);
        }

        .quick-link-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 28px rgba(15,23,42,0.08);
            border-color: #f59e0b;
        }

        .quick-link-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        }

        .quick-link-card h4 {
            margin: 0 0 0.2rem;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .quick-link-card p {
            margin: 0;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.45;
        }

        .content-title i {
            color: #f59e0b;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.2rem;
        }

        .event-card-modern {
            background: linear-gradient(145deg, #ffffff 0%, #fcfdff 100%);
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 24px rgba(15,23,42,0.05);
        }

        .event-card-modern:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 32px rgba(15,23,42,0.1);
            border-color: #f59e0b;
        }

        .event-card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            padding: 1.25rem 1.25rem 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .event-icon-badge {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
            color: white;
            font-size: 1.1rem;
            box-shadow: 0 8px 16px rgba(245,158,11,0.16);
        }

        .event-card-body {
            padding: 1.2rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .event-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }

        .event-category {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .event-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.74rem;
            color: #64748b;
            background: #f8fafc;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
        }

        .event-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
        }

        .event-description {
            color: #64748b;
            font-size: 0.88rem;
            line-height: 1.5;
            margin: 0;
            flex-grow: 1;
        }

        .event-meta-list {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-top: 0.25rem;
        }

        .event-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.84rem;
        }

        .event-meta i {
            color: #f59e0b;
            min-width: 14px;
        }

        .event-btn {
            margin-top: 0.4rem;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border: none;
            padding: 0.75rem 0.95rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            font-size: 0.9rem;
        }

        .event-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37,99,235,0.16);
            filter: brightness(1.04);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .dashboard-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 1.6rem;
            }

            .hero-highlight {
                width: 100%;
                min-width: auto;
            }

            .dashboard-hero h1 {
                font-size: 1.8rem;
            }

            .navbar-menu {
                display: none;
            }

            .dashboard-container {
                padding: 2rem 1rem 2.5rem;
            }

            .content-card {
                padding: 1.3rem;
            }
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <!-- Hero Section -->
    <div class="dashboard-hero">
        <div class="hero-copy">
            <div class="hero-badge">
                <i class="fas fa-bolt"></i> Dashboard kampus yang lebih terarah
            </div>
            <h1>Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?>! 👋</h1>
            <p>Jelajahi event yang menarik, pantau pendaftaranmu, dan simpan jejak aktivitas kampus di satu tempat yang rapi.</p>
            <div class="hero-actions">
                <a href="index.php?page=events" class="btn-hero btn-hero-primary">
                    <i class="fas fa-compass"></i> Jelajahi Event
                </a>
                <a href="index.php?page=my_events" class="btn-hero btn-hero-light">
                    <i class="fas fa-list-check"></i> Event Saya
                </a>
            </div>
        </div>
        <div class="hero-highlight">
            <div class="hero-highlight-label">Ringkasan hari ini</div>
            <div class="hero-highlight-value"><?php echo $my_events; ?> event terdaftar</div>
            <p>Semakin banyak kamu ikut berpartisipasi, semakin banyak pengalaman dan pencapaian yang tersimpan.</p>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-wrapper">
        <div class="stat-card-modern">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3><?php echo $total_events; ?></h3>
            <p>Total Events</p>
        </div>
        <div class="stat-card-modern">
            <div class="stat-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3><?php echo $my_events; ?></h3>
            <p>My Registrations</p>
        </div>
        <div class="stat-card-modern">
            <div class="stat-icon">
                <i class="fas fa-certificate"></i>
            </div>
            <h3><?php echo $my_certificates; ?></h3>
            <p>Certificates</p>
        </div>
    </div>

    <div class="quick-links">
        <a href="index.php?page=events" class="quick-link-card">
            <div class="quick-link-icon"><i class="fas fa-compass"></i></div>
            <div>
                <h4>Jelajahi Event</h4>
                <p>Temukan event yang paling cocok untukmu.</p>
            </div>
        </a>
        <a href="index.php?page=my_events" class="quick-link-card">
            <div class="quick-link-icon"><i class="fas fa-list-check"></i></div>
            <div>
                <h4>Event Saya</h4>
                <p>Lihat semua pendaftaran yang sudah kamu ikuti.</p>
            </div>
        </a>
        <a href="index.php?page=certificates" class="quick-link-card">
            <div class="quick-link-icon"><i class="fas fa-certificate"></i></div>
            <div>
                <h4>Sertifikat</h4>
                <p>Simak pencapaian dan sertifikat yang kamu miliki.</p>
            </div>
        </a>
    </div>

    <div class="content-card">
        <div class="section-header">
            <div class="content-title">
                <i class="fas fa-sparkles"></i> Latest Events
            </div>
            <a href="index.php?page=events" class="section-link">Lihat semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="events-grid">
            <?php
            if ($latest_events->num_rows > 0) {
                while ($event = $latest_events->fetch_assoc()) {
                    $tanggal = date('d M Y', strtotime($event['tanggal']));
            ?>
                    <div class="event-card-modern">
                        <div class="event-card-header">
                            <div class="event-icon-badge">
                                <i class="fas fa-calendar-days"></i>
                            </div>
                        </div>
                        <div class="event-card-body">
                            <div class="event-card-top">
                                <span class="event-category"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                                <span class="event-pill"><i class="fas fa-users"></i> New</span>
                            </div>
                            <h5 class="event-title"><?php echo htmlspecialchars($event['judul']); ?></h5>
                            <p class="event-description">
                                <?php echo substr(htmlspecialchars($event['deskripsi']), 0, 95) . '...'; ?>
                            </p>
                            <div class="event-meta-list">
                                <div class="event-meta">
                                    <i class="fas fa-calendar"></i>
                                    <span><?php echo $tanggal; ?></span>
                                </div>
                                <div class="event-meta">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($event['lokasi']); ?></span>
                                </div>
                            </div>
                            <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" class="event-btn">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div style="grid-column: 1/-1; padding: 2rem; text-align: center; color: #999;">No events available yet</div>';
            }
            ?>
        </div>
    </div>

    <div class="content-card">
        <div class="section-header">
            <div class="content-title">
                <i class="fas fa-fire"></i> Trending Events
            </div>
            <a href="index.php?page=events" class="section-link">Lihat semua <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="events-grid">
            <?php
            if ($popular_events->num_rows > 0) {
                while ($event = $popular_events->fetch_assoc()) {
                    $tanggal = date('d M Y', strtotime($event['tanggal']));
            ?>
                    <div class="event-card-modern">
                        <div class="event-card-header">
                            <div class="event-icon-badge" style="background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);">
                                <i class="fas fa-crown"></i>
                            </div>
                        </div>
                        <div class="event-card-body">
                            <div class="event-card-top">
                                <span class="event-category"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                                <span class="event-pill"><i class="fas fa-users"></i> <?php echo $event['jml_peserta']; ?></span>
                            </div>
                            <h5 class="event-title"><?php echo htmlspecialchars($event['judul']); ?></h5>
                            <p class="event-description">
                                <?php echo substr(htmlspecialchars($event['deskripsi']), 0, 95) . '...'; ?>
                            </p>
                            <div class="event-meta-list">
                                <div class="event-meta">
                                    <i class="fas fa-users"></i>
                                    <span><?php echo $event['jml_peserta']; ?> participants</span>
                                </div>
                                <div class="event-meta">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($event['lokasi']); ?></span>
                                </div>
                            </div>
                            <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" class="event-btn">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<div style="grid-column: 1/-1; padding: 2rem; text-align: center; color: #999;">No trending events yet</div>';
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
