<?php
// ===========================
// SESSION CONFIGURATION
// ===========================
// File: config/session.php
// Fungsi: Setup session dan security

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// Set session timeout 30 menit
$timeout = 30 * 60;

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_destroy();
    header("Location: index.php?message=Session expired");
    exit();
}

$_SESSION['last_activity'] = time();

// Function untuk check apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function untuk check apakah user adalah admin
function isAdmin() {
    return isset($_SESSION['role']) && strtolower(trim($_SESSION['role'])) === 'admin';
}

// Function untuk redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php?page=login");
        exit();
    }
}

// Function untuk redirect jika bukan admin
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: index.php?message=Access denied");
        exit();
    }
}

// Function untuk check apakah user sudah terverifikasi sebagai organizer
function isOrganizerVerified() {
    global $conn;
    if (!isLoggedIn()) return false;
    
    $user_id = (int)$_SESSION['user_id'];
    $result = $conn->query("SELECT organizer_status FROM users WHERE id = $user_id");
    if ($result) {
        $user = $result->fetch_assoc();
        return isset($user['organizer_status']) && $user['organizer_status'] === 'verified';
    }
    return false;
}

// Function untuk require organizer verified
function requireOrganizerVerified() {
    if (!isOrganizerVerified()) {
        header("Location: index.php?page=organizer_apply&message=Anda belum terverifikasi sebagai organizer");
        exit();
    }
}

function setFlash($type, $message) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

// Function untuk logout
function logoutUser() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    session_unset();
    session_destroy();
    header("Location: index.php?page=login");
    exit();
}

?>
