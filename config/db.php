<?php
// ===========================
// DATABASE CONNECTION
// ===========================
// File: config/db.php
// Fungsi: Menghubungkan aplikasi ke database MySQL

$host = 'localhost';
$db_name = 'campus_event_hub';
$db_user = 'root';
$db_pass = '';

// Suppress error untuk first connection attempt
$conn = @new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    $error_msg = $conn->connect_error;
    
    // Cek apakah error karena database tidak ada
    if (strpos($error_msg, 'Unknown database') !== false) {
        // Database tidak ada, redirect ke install.php
        // Buat connection ke server MySQL saja (tanpa database)
        $conn_temp = @new mysqli($host, $db_user, $db_pass);
        
        // Jika berhasil koneksi ke server, redirect ke install
        if (!$conn_temp->connect_error) {
            $conn_temp->close();
            header("Location: install.php");
            exit();
        }
    }
    
    // Jika sampai sini, error lain atau MySQL tidak berjalan
    die("<div style='padding: 30px; color: #721c24; background: #f8d7da; border-radius: 8px; border-left: 4px solid #dc3545; font-family: Arial, sans-serif;'>
        <h3 style='margin-top: 0;'>❌ Koneksi Database Gagal</h3>
        <p><strong>Error:</strong> " . htmlspecialchars($error_msg) . "</p>
        <hr>
        <h5>💡 Solusi:</h5>
        <ol>
            <li><strong>Pastikan MySQL running:</strong> Buka XAMPP Control Panel, klik Start di MySQL</li>
            <li><strong>Setup Database:</strong> Buka <a href='install.php' style='color: #004085; text-decoration: underline;'>http://localhost/campusevent/install.php</a></li>
            <li><strong>Tunggu wizard selesai</strong> (klik Lanjutkan 3x)</li>
            <li><strong>Refresh halaman ini</strong></li>
        </ol>
        <hr>
        <small style='color: #666;'>Jika masalah persisten, periksa config/db.php untuk username/password</small>
    </div>");
}

// Set charset
$conn->set_charset("utf8");

function ensureOrganizerSupport($conn) {
    $conn->query("ALTER TABLE users MODIFY role VARCHAR(20) NOT NULL DEFAULT 'mahasiswa'");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS organizer_status VARCHAR(20) NOT NULL DEFAULT 'none'");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS organizer_profile VARCHAR(255) DEFAULT NULL");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS account_status VARCHAR(20) NOT NULL DEFAULT 'approved'");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL DEFAULT NULL");
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS approved_by INT NULL DEFAULT NULL");

    $conn->query("CREATE TABLE IF NOT EXISTS organizer_applications (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT NOT NULL,
        organization_name VARCHAR(150) NOT NULL,
        organization_type VARCHAR(100) NOT NULL,
        university VARCHAR(150) DEFAULT NULL,
        official_email VARCHAR(150) NOT NULL,
        instagram VARCHAR(150) DEFAULT NULL,
        website VARCHAR(255) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        pic_name VARCHAR(100) DEFAULT NULL,
        phone VARCHAR(30) DEFAULT NULL,
        address TEXT DEFAULT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        review_notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        reviewed_at TIMESTAMP NULL DEFAULT NULL,
        reviewed_by INT DEFAULT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    $conn->query("ALTER TABLE organizer_applications ADD COLUMN IF NOT EXISTS review_notes TEXT NULL");

    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS organizer_id INT NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS event_type VARCHAR(20) NOT NULL DEFAULT 'offline'");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS location_details TEXT NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS registration_deadline DATETIME NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS poster_url VARCHAR(255) NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS benefits TEXT NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS requirements TEXT NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS contact_name VARCHAR(100) NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS contact_whatsapp VARCHAR(30) NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS contact_email VARCHAR(100) NULL");
    $conn->query("ALTER TABLE events ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'draft'");
}

ensureOrganizerSupport($conn);

function ensureRegistrationSupport($conn) {
    $conn->query("ALTER TABLE registrations ADD COLUMN IF NOT EXISTS phone VARCHAR(30) NULL");
    $conn->query("ALTER TABLE registrations ADD COLUMN IF NOT EXISTS contact_whatsapp VARCHAR(30) NULL");
    $conn->query("ALTER TABLE registrations ADD COLUMN IF NOT EXISTS note TEXT NULL");
}

ensureRegistrationSupport($conn);

?>
