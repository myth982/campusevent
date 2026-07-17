<?php
// ===========================
// INDEX / ROUTING PAGE
// ===========================
// File: index.php
// Fungsi: Entry point dan router untuk seluruh aplikasi

require_once 'config/db.php';
require_once 'config/session.php';

// Routing
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle logout
if ($action == 'logout') {
    logoutUser();
}

// Cek apakah user sudah login
if (!isLoggedIn() && $page != 'login' && $page != 'register' && $page != 'landing') {
    // Jika belum login dan buka halaman default, tampilkan landing page
    if ($page == 'dashboard') {
        include 'pages/landing.php';
        exit();
    }
    // Jika belum login dan coba akses halaman lain, redirect ke login
    header("Location: index.php?page=login");
    exit();
}

// Cek apakah user admin untuk akses halaman admin
if (strpos($page, 'admin_') === 0 && !isAdmin()) {
    header("Location: index.php?page=dashboard");
    exit();
}

// Route ke file yang tepat
if ($page == 'login') {
    include 'pages/login.php';
} elseif ($page == 'register') {
    include 'pages/register.php';
} elseif ($page == 'landing') {
    include 'pages/landing.php';
} elseif ($page == 'dashboard') {
    include 'pages/dashboard-modern.php';
} elseif ($page == 'dashboard-modern') {
    include 'pages/dashboard-modern.php';
} elseif ($page == 'events') {
    include 'pages/events.php';
} elseif ($page == 'event_detail') {
    include 'pages/event_detail.php';
} elseif ($page == 'my_events') {
    include 'pages/my_events.php';
} elseif ($page == 'certificates') {
    include 'pages/certificates.php';
} elseif ($page == 'profile') {
    include 'pages/profile.php';
} elseif ($page == 'organizer_apply') {
    include 'pages/organizer_apply.php';
} elseif ($page == 'organizer_dashboard') {
    include 'pages/organizer_dashboard.php';
} elseif ($page == 'organizer_create_event') {
    include 'pages/organizer_create_event.php';
} elseif ($page == 'organizer_events') {
    include 'pages/organizer_events.php';
} elseif ($page == 'admin_dashboard') {
    include 'admin/dashboard.php';
} elseif ($page == 'admin_events') {
    include 'admin/events.php';
} elseif ($page == 'admin_categories') {
    include 'admin/categories.php';
} elseif ($page == 'admin_participants') {
    include 'admin/participants.php';
} elseif ($page == 'admin_users') {
    include 'admin/users.php';
} elseif ($page == 'admin_certificates') {
    include 'admin/certificates.php';
} elseif ($page == 'admin_organizers') {
    include 'admin/organizers.php';
} else {
    // Jika tidak login dan page default, tampilkan login
    if (!isLoggedIn()) {
        include 'pages/login.php';
    } else {
        // Default ke dashboard jika sudah login
        include 'pages/dashboard.php';
    }
}
?>
