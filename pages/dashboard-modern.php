<?php
// Dashboard Modern dengan Design Cliento-style
$flash = getFlash();
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
    <link rel="icon" type="image/svg+xml" href="assets/images/logo-green.svg">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255,248,240,0.95), transparent 30%),
                radial-gradient(circle at top right, rgba(224,242,254,0.9), transparent 35%),
                linear-gradient(135deg, #f7f2eb 0%, #eef4ff 45%, #f8fafc 100%);
            min-height: 100vh;
            color: #334155;
            overflow-x: hidden;
        }


        /* Dashboard Container */
        .dashboard-container {
            padding: 4rem max(2rem, calc((100vw - 1200px) / 2));
            width: 100%;
        }

        /* Hero Section */
        .dashboard-hero {
            background: linear-gradient(135deg, #314a62 0%, #5b7ea0 45%, #8fb0c9 100%);
            border-radius: 32px;
            padding: 2.5rem 2.8rem;
            color: white;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease;
            box-shadow: 0 28px 70px rgba(15,23,42,0.18);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.22);
        }

        .dashboard-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 30%),
                linear-gradient(120deg, rgba(255,255,255,0.14), transparent 45%);
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

        .dashboard-hero h1 {
            font-size: 2.4rem;
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
            letter-spacing: -1px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }

        .dashboard-hero p {
            font-size: 1.05rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0;
            font-weight: 500;
            position: relative;
            z-index: 1;
        }

        /* Stats Section */
        .stats-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }

        .stat-card-modern {
            background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            padding: 1.7rem;
            border-radius: 24px;
            text-align: center;
            backdrop-filter: blur(12px);
            box-shadow: 0 16px 36px rgba(15,23,42,0.05);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease;
            border: 1px solid #e5eefc;
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
            background: radial-gradient(circle, rgba(56,189,248,0.16), transparent 70%);
            pointer-events: none;
        }

        .stat-card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #f59e0b;
        }

        .stat-card-modern h3 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .stat-card-modern p {
            color: #666;
            font-size: 0.95rem;
            margin: 0;
            font-weight: 500;
        }

        /* Content Cards */
        .content-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 28px;
            padding: 2rem;
            box-shadow: 0 20px 45px rgba(15,23,42,0.05);
            margin-bottom: 2rem;
            border: 1px solid #e5eefc;
        }

        .content-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .content-title i {
            color: #3b82f6;
            font-size: 1.5rem;
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .event-card-modern {
            background: linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            border-radius: 22px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #e5eefc;
            display: flex;
            flex-direction: column;
            box-shadow: 0 12px 28px rgba(15,23,42,0.05);
        }

        .event-card-modern:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 36px rgba(15,23,42,0.1);
            border-color: #60a5fa;
        }

        .event-card-header {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            padding: 1.2rem 1.2rem 0.8rem;
            color: white;
            text-align: left;
            font-size: 1.1rem;
        }

        .event-card-body {
            padding: 1.5rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .event-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.8rem;
            line-height: 1.3;
        }

        .event-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .event-meta i {
            color: #3b82f6;
            min-width: 16px;
        }

        .event-description {
            color: #888;
            font-size: 0.85rem;
            line-height: 1.4;
            margin: 1rem 0;
            flex-grow: 1;
        }

        .event-category {
            display: inline-block;
            background: rgba(59, 130, 246, 0.12);
            color: #2563eb;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .event-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: white;
            border: none;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
            font-size: 0.9rem;
        }

        .event-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(79,111,143,0.18);
            filter: brightness(1.04);
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .quick-action-btn {
            background: linear-gradient(135deg, #f8fbff 0%, #eef5ff 100%);
            color: #334155;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(15,23,42,0.06);
            color: #4f6f8f;
        }

        .quick-action-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .quick-action-btn .qa-blue {
            background: linear-gradient(135deg, #4f6f8f 0%, #86a9c9 100%);
            color: white;
        }

        .quick-action-btn .qa-green {
            background: linear-gradient(135deg, #4c7a67 0%, #7fb39a 100%);
            color: white;
        }

        .quick-action-btn .qa-amber {
            background: linear-gradient(135deg, #9b7347 0%, #cda67e 100%);
            color: white;
        }

        .quick-action-btn .qa-violet {
            background: linear-gradient(135deg, #6b7aa8 0%, #9aa9db 100%);
            color: white;
        }

        .flash-alert {
            margin-bottom: 1.5rem;
            border-radius: 16px;
            border: 1px solid #dbeafe;
            background: rgba(255,255,255,0.95);
            box-shadow: 0 10px 24px rgba(15,23,42,0.06);
        }

        .profile-spotlight {
            background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(247,251,255,0.96) 100%);
            border: 1px solid #dfeaf8;
            border-radius: 24px;
            padding: 1.25rem 1.4rem;
            box-shadow: 0 14px 30px rgba(15,23,42,0.05);
            margin-bottom: 2rem;
        }

        .profile-spotlight-card {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .profile-avatar {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            background: linear-gradient(135deg, #4f6f8f 0%, #86a9c9 100%);
            color: white;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            box-shadow: 0 10px 20px rgba(79,111,143,0.2);
            flex-shrink: 0;
        }

        .profile-content h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.2rem;
        }

        .profile-content p {
            color: #64748b;
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .profile-mini-icons {
            display: flex;
            gap: 0.45rem;
            flex-wrap: wrap;
            margin-top: 0.55rem;
        }

        .profile-mini-icons span {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.35rem 0.6rem;
            border-radius: 999px;
            font-size: 0.78rem;
            color: #4f6f8f;
            background: rgba(79,111,143,0.1);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .dashboard-hero h1 {
                font-size: 2rem;
            }

            .navbar-menu {
                gap: 1rem;
                font-size: 0.85rem;
            }

            .dashboard-container {
                padding: 2rem 1rem;
            }

            .content-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <?php if ($flash): ?>
        <div class="alert flash-alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <!-- Hero Section -->
    <div class="dashboard-hero">
        <h1>Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>.</h1>
        <p>Dashboard Anda tampil lebih fokus, bersih, dan siap untuk membantu mengelola event dengan mudah.</p>
    </div>

    <div class="profile-spotlight">
        <div class="profile-spotlight-card">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-content">
                <h2><?php echo htmlspecialchars($_SESSION['nama']); ?></h2>
                <p>Siap menjelajah event kampus dan menemukan pengalaman terbaik hari ini.</p>
                <div class="profile-mini-icons">
                    <span><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
                    <span><i class="fas fa-shield-alt"></i> <?php echo htmlspecialchars(ucfirst($_SESSION['role'] ?? 'User')); ?></span>
                    <span><i class="fas fa-bolt"></i> Aktif</span>
                </div>
            </div>
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

    <!-- Latest Events -->
    <div class="content-card">
        <div class="content-title">
            <i class="fas fa-sparkles"></i> Latest Events
        </div>
        <div class="events-grid">
            <?php
            if ($latest_events->num_rows > 0) {
                while ($event = $latest_events->fetch_assoc()) {
                    $tanggal = date('d M Y', strtotime($event['tanggal']));
            ?>
                    <div class="event-card-modern">
                        <div class="event-card-header">
                            <i class="fas fa-calendar-days"></i>
                        </div>
                        <div class="event-card-body">
                            <h5 class="event-title"><?php echo htmlspecialchars($event['judul']); ?></h5>
                            <span class="event-category"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                            <div class="event-meta">
                                <i class="fas fa-calendar"></i>
                                <span><?php echo $tanggal; ?></span>
                            </div>
                            <div class="event-meta">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['lokasi']); ?></span>
                            </div>
                            <p class="event-description">
                                <?php echo substr(htmlspecialchars($event['deskripsi']), 0, 100) . '...'; ?>
                            </p>
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

    <!-- Popular Events -->
    <div class="content-card">
        <div class="content-title">
            <i class="fas fa-fire"></i> Trending Events
        </div>
        <div class="events-grid">
            <?php
            if ($popular_events->num_rows > 0) {
                while ($event = $popular_events->fetch_assoc()) {
                    $tanggal = date('d M Y', strtotime($event['tanggal']));
            ?>
                    <div class="event-card-modern">
                        <div class="event-card-header" style="background: linear-gradient(135deg, #ff7f00 0%, #ffb84d 100%);">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="event-card-body">
                            <h5 class="event-title" style="color: #ff7f00;"><?php echo htmlspecialchars($event['judul']); ?></h5>
                            <span class="event-category" style="background: rgba(255, 127, 0, 0.1); color: #ff7f00;"><?php echo htmlspecialchars($event['nama_kategori']); ?></span>
                            <div class="event-meta">
                                <i class="fas fa-users"></i>
                                <span><?php echo $event['jml_peserta']; ?> Participants</span>
                            </div>
                            <div class="event-meta">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars($event['lokasi']); ?></span>
                            </div>
                            <p class="event-description">
                                <?php echo substr(htmlspecialchars($event['deskripsi']), 0, 100) . '...'; ?>
                            </p>
                            <a href="index.php?page=event_detail&id=<?php echo $event['id']; ?>" class="event-btn" style="background: linear-gradient(135deg, #ff7f00 0%, #ffb84d 100%);">
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
