<?php
// ===========================
// LANDING PAGE
// ===========================
// File: pages/landing.php
// Fungsi: Halaman sambutan untuk pengunjung yang belum login
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Event Hub - Platform Manajemen Event Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="assets/images/logo-green.svg">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0f3d2b 0%, #1a5f3e 45%, #2d8f5e 100%);
            color: white;
            padding: 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 150px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 150" preserveAspectRatio="none"><path d="M0,60 Q300,120 600,60 T1200,60 L1200,150 L0,150 Z" fill="white"/><path d="M0,70 Q300,130 600,70 T1200,70 L1200,150 L0,150 Z" fill="white" opacity="0.5"/><path d="M0,80 Q300,140 600,80 T1200,80 L1200,150 L0,150 Z" fill="white" opacity="0.3"/></svg>') repeat-x;
            background-size: 600px 150px;
            animation: wave 20s linear infinite;
        }

        @keyframes wave {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 600px 0;
            }
        }

        .hero-carousel {
            position: relative;
        }

        .hero-carousel .carousel-indicators {
            bottom: 24px;
        }

        .hero-carousel .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.8);
            border: none;
            margin: 0 6px;
        }

        .hero-slide {
            min-height: 560px;
            display: flex;
            align-items: center;
            padding: 90px 24px;
            position: relative;
            background-size: cover;
            background-position: center;
        }

        .hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(4, 27, 18, 0.86) 0%, rgba(10, 58, 39, 0.7) 45%, rgba(26, 95, 62, 0.35) 100%);
            z-index: 0;
        }

        .hero-slide-1 {
            background-image: linear-gradient(120deg, rgba(8, 35, 24, 0.92) 0%, rgba(26, 95, 62, 0.74) 50%, rgba(255, 127, 0, 0.22) 100%), radial-gradient(circle at top right, rgba(255, 183, 77, 0.25), transparent 35%);
        }

        .hero-slide-2 {
            background-image: linear-gradient(135deg, rgba(9, 38, 25, 0.9) 0%, rgba(41, 105, 69, 0.76) 48%, rgba(255, 127, 0, 0.2) 100%), radial-gradient(circle at left center, rgba(255, 183, 77, 0.2), transparent 28%);
        }

        .hero-slide-3 {
            background-image: linear-gradient(120deg, rgba(12, 44, 29, 0.92) 0%, rgba(45, 143, 94, 0.72) 45%, rgba(255, 127, 0, 0.25) 100%), radial-gradient(circle at bottom right, rgba(255, 183, 77, 0.28), transparent 34%);
        }

        .hero-slide-content {
            position: relative;
            z-index: 1;
            max-width: 660px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .hero-badge {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .hero-slide-content h1 {
            font-size: clamp(2rem, 3.4vw, 3.3rem);
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.2;
        }

        .hero-slide-content p {
            font-size: 1.08rem;
            margin-bottom: 28px;
            opacity: 0.96;
            line-height: 1.8;
        }

        .cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .cta-buttons .btn {
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 46px;
            height: 46px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            background: rgba(255,255,255,0.16);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
        }

        .carousel-control-prev {
            left: 20px;
        }

        .carousel-control-next {
            right: 20px;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            text-align: left;
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e8f2eb;
        }

        .event-poster {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 14px;
            margin-bottom: 16px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.12);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 30px rgba(0,0,0,0.12);
        }
        
        .feature-icon {
            font-size: 2.2rem;
            color: #ff7f00;
            margin-bottom: 14px;
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,127,0,0.12);
        }
        
        .feature-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a5f3e;
            margin-bottom: 10px;
        }
        
        .feature-text {
            color: #666;
            line-height: 1.6;
            margin-bottom: 0;
        }

        .event-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.7rem;
            border-radius: 999px;
            background: #f5f9f6;
            color: #1a5f3e;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .event-meta-row {
            display: grid;
            gap: 0.45rem;
            margin: 14px 0 16px;
            color: #64748b;
            font-size: 0.92rem;
        }

        .event-meta-row div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stats-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1a5f3e;
        }
        
        .stat-label {
            color: #666;
            font-size: 1rem;
            margin-top: 10px;
        }
        
        .benefits-section {
            padding: 60px 0;
            background: white;
        }
        
        .benefit-item {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .benefit-icon {
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1a5f3e 0%, #2d8f5e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .benefit-text h5 {
            color: #1a5f3e;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .benefit-text p {
            color: #666;
            margin: 0;
        }
    </style>
</head>
<body>
<?php
function getLandingEventPoster($event) {
    $poster = $event['poster_url'] ?? '';
    if (!empty($poster)) {
        if (filter_var($poster, FILTER_VALIDATE_URL)) {
            return $poster;
        }
        return 'uploads/events/' . $poster;
    }

    $category = strtolower(trim($event['nama_kategori'] ?? ''));
    $title = strtolower(trim($event['judul'] ?? ''));

    $fallbacks = [
        'seminar' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=900&q=80',
        'workshop' => 'https://images.unsplash.com/photo-1516321497487-e288fb19713f?auto=format&fit=crop&w=900&q=80',
        'kompetisi' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80',
        'webinar' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80',
        'pelatihan' => 'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=900&q=80',
        'diskusi' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=900&q=80',
        'seni' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=900&q=80',
        'teknologi' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=900&q=80'
    ];

    foreach ($fallbacks as $keyword => $image) {
        if (strpos($category, $keyword) !== false || strpos($title, $keyword) !== false) {
            return $image;
        }
    }

    return 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=900&q=80';
}

$landingEvents = $conn->query("SELECT e.id, e.judul, e.deskripsi, e.tanggal, e.lokasi, e.poster_url, c.nama_kategori FROM events e LEFT JOIN categories c ON e.kategori_id = c.id WHERE e.status != 'cancelled' ORDER BY e.tanggal ASC LIMIT 6");
?>

<!-- HERO SECTION -->
<div class="hero-section">
    <div class="hero-carousel">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="hero-slide hero-slide-1">
                        <div class="container-lg">
                            <div class="hero-slide-content">
                                <span class="hero-badge"><i class="fas fa-bolt"></i> Platform event kampus</span>
                                <h1>Temukan acara yang memperluas wawasan dan koneksimu.</h1>
                                <p>Jelajahi seminar, workshop, dan kegiatan komunitas dalam satu tempat yang rapi, cepat, dan mudah dipakai.</p>
                                <div class="cta-buttons">
                                    <a href="index.php?page=login" class="btn btn-light btn-lg">
                                        <i class="fas fa-sign-in-alt"></i> Masuk
                                    </a>
                                    <a href="index.php?page=register" class="btn btn-outline-light btn-lg">
                                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="hero-slide hero-slide-2">
                        <div class="container-lg">
                            <div class="hero-slide-content">
                                <span class="hero-badge"><i class="fas fa-calendar-check"></i> Pendaftaran praktis</span>
                                <h1>Daftar event dengan alur yang sederhana dan nyaman.</h1>
                                <p>Tak perlu bingung mencari informasi. Semua detail event, kuota, dan status pendaftaran tersaji jelas di satu layar.</p>
                                <div class="cta-buttons">
                                    <a href="index.php?page=register" class="btn btn-light btn-lg">
                                        <i class="fas fa-user-plus"></i> Coba Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="hero-slide hero-slide-3">
                        <div class="container-lg">
                            <div class="hero-slide-content">
                                <span class="hero-badge"><i class="fas fa-award"></i> Sertifikat terkelola</span>
                                <h1>Kelola partisipasimu dan simpan sertifikat dengan lebih rapi.</h1>
                                <p>Setiap kegiatan yang kamu ikuti bisa diakses kembali, sehingga jejak pengalamanmu tetap terorganisir dengan baik.</p>
                                <div class="cta-buttons">
                                    <a href="index.php?page=login" class="btn btn-light btn-lg">
                                        <i class="fas fa-arrow-right"></i> Lihat Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Sebelumnya</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Berikutnya</span>
            </button>
        </div>
    </div>
</div>

<!-- AVAILABLE EVENTS SECTION -->
<div class="container-lg py-5">
    <div class="text-center mb-4">
        <h2 class="mb-3" style="color: #1a5f3e;"><i class="fas fa-calendar-alt"></i> Event yang Tersedia Saat Ini</h2>
        <p class="text-muted">Temukan event kampus yang menarik dan ikuti kegiatan yang paling sesuai dengan minatmu.</p>
    </div>

    <div class="row g-4">
        <?php if ($landingEvents && $landingEvents->num_rows > 0): ?>
            <?php while ($event = $landingEvents->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <img src="<?php echo htmlspecialchars(getLandingEventPoster($event)); ?>" alt="Poster event <?php echo htmlspecialchars($event['judul']); ?>" class="event-poster">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="event-chip">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($event['nama_kategori'] ?? 'Umum'); ?>
                        </div>
                        <div class="feature-title"><?php echo htmlspecialchars($event['judul']); ?></div>
                        <p class="feature-text mb-3"><?php echo htmlspecialchars(substr($event['deskripsi'], 0, 110)); ?><?php echo strlen($event['deskripsi']) > 110 ? '...' : ''; ?></p>
                        <div class="event-meta-row">
                            <div><i class="fas fa-clock"></i> <?php echo date('d M Y H:i', strtotime($event['tanggal'])); ?></div>
                            <div><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lokasi']); ?></div>
                        </div>
                        <a href="index.php?page=register" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-arrow-right"></i> Daftar
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="feature-card">
                    <p class="feature-text mb-0">Belum ada event yang dipublikasikan saat ini. Silakan cek kembali nanti.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ABOUT WEBSITE SECTION -->
<div class="benefits-section">
    <div class="container-lg">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 style="color: #1a5f3e; margin-bottom: 20px;"><i class="fas fa-building"></i> Tentang Campus Event Hub</h2>
                <p class="text-muted">Campus Event Hub adalah platform yang membantu mahasiswa menemukan, mengikuti, dan mengelola event kampus secara lebih teratur. Website ini dirancang agar organisasi, panitia, dan peserta bisa saling terhubung dalam satu ekosistem yang simpel.</p>
                <p class="text-muted">Tujuan utamanya adalah memudahkan akses informasi event, menjaga partisipasi tetap terpantau, serta memberi ruang bagi pengguna untuk berkembang melalui kegiatan yang relevan dengan minat dan bakat mereka.</p>
            </div>
            <div class="col-lg-6">
                <div class="feature-card text-start">
                    <h5 class="feature-title mb-3"><i class="fas fa-bullseye"></i> Fokus kami</h5>
                    <ul class="feature-text mb-0 ps-3">
                        <li>Menghubungkan mahasiswa dengan event yang sesuai kebutuhan.</li>
                        <li>Mempermudah organisasi dalam mengelola pendaftaran dan peserta.</li>
                        <li>Memberikan jejak partisipasi yang jelas lewat dashboard pribadi.</li>
                        <li>Mendukung proses verifikasi akun dan organizer secara lebih tertib.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEATURES SECTION -->
<div class="container-lg py-5">
    <div class="text-center mb-5">
        <h2 class="mb-3" style="color: #1a5f3e;"><i class="fas fa-star"></i> Fitur yang Bikin Pengalamanmu Lebih Seru</h2>
        <p class="text-muted">Nikmati proses menemukan, mengikuti, dan mengelola event kampus dengan cara yang lebih praktis, cepat, dan menyenangkan.</p>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="feature-title">Temukan Event yang Sesuai</div>
                <p class="feature-text">Cari event berdasarkan nama, lokasi, atau kategori. Filter sesuai minatmu dan temukan kegiatan yang benar-benar cocok.</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="feature-title">Daftar Tanpa Ribet</div>
                <p class="feature-text">Ikut event dengan proses yang simpel dan cepat. Lihat kuota peserta serta status pendaftaran secara langsung.</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="feature-title">Simpan Sertifikat dengan Rapi</div>
                <p class="feature-text">Kumpulkan dan unduh sertifikat dari semua event yang telah kamu ikuti di satu tempat yang teratur.</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-history"></i>
                </div>
                <div class="feature-title">Lacak Perjalananmu</div>
                <p class="feature-text">Pantau semua event yang pernah kamu ikuti, status pendaftaran, dan perkembangan partisipasimu dengan jelas.</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="feature-title">Kelola Profilmu</div>
                <p class="feature-text">Atur profil dengan lebih nyaman, ubah password, dan lihat jejak keikutsertaan eventmu dengan mudah.</p>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <div class="feature-title">Akses di Mana Saja</div>
                <p class="feature-text">Nikmati pengalaman yang nyaman di smartphone, tablet, maupun desktop. Platform ini selalu siap menemanimu.</p>
            </div>
        </div>
    </div>
</div>

<!-- BENEFITS SECTION -->
<div class="benefits-section">
    <div class="container-lg">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 style="color: #1a5f3e; margin-bottom: 30px;">
                    <i class="fas fa-check-circle"></i> Kenapa Mahasiswa Suka
                </h2>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Info Event yang Selalu Terkini</h5>
                        <p>Jangan sampai ketinggalan event menarik karena semua informasi terbaru tersedia dengan jelas.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Proses Daftar yang Sangat Mudah</h5>
                        <p>Ikut event dengan langkah yang simpel, cepat, dan tanpa hambatan.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Unduh Sertifikat dengan Praktis</h5>
                        <p>Semua sertifikatmu bisa disimpan dan diunduh kapan saja dengan lebih praktis.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Jejak Partisipasi yang Jelas</h5>
                        <p>Lihat perkembangan aktivitasmu dan pantau event yang sudah pernah kamu ikuti dengan lebih terarah.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <h2 style="color: #1a5f3e; margin-bottom: 30px;">
                    <i class="fas fa-cog"></i> Keunggulan untuk Organizer
                </h2>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Atur Event dengan Lebih Mudah</h5>
                        <p>Kelola informasi event, update poster, dan atur detail acara tanpa perlu bingung.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Promosi Event Lebih Cepat</h5>
                        <p>Bagikan informasi event kepada peserta dengan desain yang menarik dan mudah dilihat.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Kelola Peserta dengan Jelas</h5>
                        <p>Lihat jumlah peserta, kuota, dan status pendaftaran secara cepat dan rapi.</p>
                    </div>
                </div>
                
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <div class="benefit-text">
                        <h5>Tingkatkan Kepercayaan Peserta</h5>
                        <p>Tampilkan detail lengkap seperti poster, kontak, dan benefit agar peserta semakin yakin.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- STATS SECTION -->
<div class="stats-section">
    <div class="container-lg">
        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">8+</div>
                    <div class="stat-label">Event Tersedia</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">6</div>
                    <div class="stat-label">Kategori Event</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Pengguna Terdaftar</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">Akses Kapan Saja</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA FINAL -->
<div style="background: linear-gradient(135deg, #1a5f3e 0%, #2d8f5e 100%); color: white; padding: 60px 0; text-align: center;">
    <div class="container-lg">
        <h2 class="mb-4">Siap Menyusun Pengalaman Kampusmu?</h2>
        <p class="mb-4" style="font-size: 1.1rem;">Buat akun sekarang dan temukan event yang sesuai dengan minat, bakat, serta kebutuhan berkembangmu.</p>
        <div class="cta-buttons">
            <a href="index.php?page=register" class="btn btn-light btn-lg">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
            <a href="index.php?page=login" class="btn btn-outline-light btn-lg">
                <i class="fas fa-sign-in-alt"></i> Sudah Punya Akun?
            </a>
        </div>
    </div>
</div>

<!-- FOOTER -->
<?php include 'components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
