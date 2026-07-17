-- ===========================
-- CAMPUS EVENT HUB - DATABASE
-- ===========================
-- Database: campus_event_hub
-- Dibuat untuk project UAS Sistem Informasi Manajemen Event Kampus

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS campus_event_hub;
USE campus_event_hub;

-- ===========================
-- TABEL 1: USERS (Pengguna)
-- ===========================
-- Menyimpan data pengguna (mahasiswa dan admin)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'mahasiswa') DEFAULT 'mahasiswa',
    organizer_status ENUM('none', 'pending', 'verified', 'rejected') DEFAULT 'none',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_organizer_status (organizer_status)
);

-- ===========================
-- TABEL 2: CATEGORIES (Kategori Event)
-- ===========================
-- Menyimpan kategori-kategori event seperti Seminar, Workshop, dll
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nama (nama_kategori)
);

-- ===========================
-- TABEL 3: EVENTS (Acara/Event)
-- ===========================
-- Menyimpan informasi event yang diselenggarakan
CREATE TABLE IF NOT EXISTS events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(255) NOT NULL,
    deskripsi TEXT NOT NULL,
    tanggal DATETIME NOT NULL,
    lokasi VARCHAR(200) NOT NULL,
    kuota INT NOT NULL,
    kategori_id INT,
    organizer_id INT,
    status ENUM('published', 'draft', 'finished', 'cancelled') DEFAULT 'draft',
    event_type VARCHAR(50),
    location_details TEXT,
    registration_deadline DATETIME,
    poster_url VARCHAR(255),
    benefits TEXT,
    requirements TEXT,
    contact_name VARCHAR(100),
    contact_whatsapp VARCHAR(20),
    contact_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_kategori (kategori_id),
    INDEX idx_organizer (organizer_id),
    INDEX idx_tanggal (tanggal),
    INDEX idx_status (status)
);

-- ===========================
-- TABEL 4: REGISTRATIONS (Pendaftaran)
-- ===========================
-- Menyimpan data pendaftaran mahasiswa ke event
CREATE TABLE IF NOT EXISTS registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'terdaftar',
    phone VARCHAR(30),
    contact_whatsapp VARCHAR(30),
    note TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (user_id, event_id),
    INDEX idx_user (user_id),
    INDEX idx_event (event_id)
);

-- ===========================
-- TABEL 5: CERTIFICATES (Sertifikat)
-- ===========================
-- Menyimpan data sertifikat yang diterima mahasiswa
CREATE TABLE IF NOT EXISTS certificates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    file_sertifikat VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_certificate (user_id, event_id),
    INDEX idx_user (user_id),
    INDEX idx_event (event_id)
);

-- ===========================
-- TABEL 6: ORGANIZER_APPLICATIONS (Permohonan Organizer)
-- ===========================
-- Menyimpan data permohonan untuk menjadi organizer
CREATE TABLE IF NOT EXISTS organizer_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    organization_name VARCHAR(200) NOT NULL,
    organization_type VARCHAR(100),
    university VARCHAR(150),
    official_email VARCHAR(100),
    instagram VARCHAR(100),
    website VARCHAR(255),
    description TEXT,
    pic_name VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    status ENUM('pending', 'approved', 'rejected', 'revision_requested') DEFAULT 'pending',
    review_notes TEXT,
    reviewed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- ===========================
-- TABEL 7: EVENT_ANNOUNCEMENTS (Pengumuman untuk Peserta)
-- ===========================
CREATE TABLE IF NOT EXISTS event_announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id)
);

-- ===========================
-- DATA DUMMY / TESTING
-- ===========================

-- Insert Admin
INSERT INTO users (nama, email, password, role) VALUES
('Admin Campus', 'admin@campus.com', '$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9', 'admin');

-- Insert Mahasiswa Demo
INSERT INTO users (nama, email, password, role) VALUES
('Budi Santoso', 'mahasiswa@campus.com', '$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9', 'mahasiswa'),
('Siti Nurhaliza', 'siti@campus.com', '$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9', 'mahasiswa'),
('Ahmad Wijaya', 'ahmad@campus.com', '$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9', 'mahasiswa'),
('Dewi Lestari', 'dewi@campus.com', '$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9', 'mahasiswa'),
('Rendra Pratama', 'rendra@campus.com', '$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9', 'mahasiswa');

-- Insert Kategori
INSERT INTO categories (nama_kategori) VALUES
('Seminar'),
('Workshop'),
('Kompetisi'),
('Webinar'),
('Pelatihan'),
('Diskusi Panel');

-- Insert Events
INSERT INTO events (judul, deskripsi, tanggal, lokasi, kuota, kategori_id) VALUES
('Seminar Teknologi AI 2024', 'Seminar mendalam tentang perkembangan kecerdasan buatan dan aplikasinya di industri modern. Pembicara dari praktisi terkemuka akan berbagi pengalaman.', '2024-07-15 10:00:00', 'Aula Utama Kampus', 150, 1),
('Workshop Web Development', 'Workshop praktis belajar membuat website modern menggunakan HTML, CSS, JavaScript dan Framework terbaru. Peserta akan membuat project nyata.', '2024-07-20 14:00:00', 'Lab Komputer B', 50, 2),
('Kompetisi Coding National', 'Kompetisi programming tingkat nasional dengan hadiah jutaan rupiah. Kategori: Web, Mobile, dan Desktop.', '2024-08-05 08:00:00', 'Convention Center', 200, 3),
('Webinar Entrepreneurship', 'Webinar online tentang memulai bisnis dari nol. Peserta akan belajar strategi marketing, financial management, dan growth hacking.', '2024-07-25 19:00:00', 'Online (Zoom)', 500, 4),
('Pelatihan Fotografi Profesional', 'Pelatihan intensif fotografi dengan fokus pada teknik komposisi, pencahayaan, dan editing menggunakan Lightroom & Photoshop.', '2024-08-10 09:00:00', 'Studio Fotografi', 30, 5),
('Panel Diskusi Karir di IT', 'Diskusi bersama para profesional dari perusahaan besar tentang prospek karir di bidang teknologi informasi dan tips interview.', '2024-07-28 15:00:00', 'Aula Sisi B', 100, 6),
('Workshop Digital Marketing', 'Belajar strategi marketing digital yang efektif untuk media sosial, SEO, dan email marketing. Praktek langsung dengan tools profesional.', '2024-08-15 13:00:00', 'Ruang Seminar A', 60, 2),
('Seminar Cloud Computing', 'Pelajari teknologi cloud terkini termasuk AWS, Google Cloud, dan Azure. Cocok untuk developer dan sysadmin yang ingin upgrade skill.', '2024-08-20 10:00:00', 'Aula Utama Kampus', 120, 1);

-- Insert Registrations (Peserta untuk setiap event)
INSERT INTO registrations (user_id, event_id, tanggal_daftar, status) VALUES
(2, 1, '2024-06-20 09:30:00', 'terdaftar'),
(3, 1, '2024-06-21 10:15:00', 'terdaftar'),
(4, 1, '2024-06-22 11:00:00', 'terdaftar'),
(5, 2, '2024-06-20 14:20:00', 'terdaftar'),
(6, 2, '2024-06-21 15:30:00', 'terdaftar'),
(2, 3, '2024-06-25 08:00:00', 'terdaftar'),
(3, 3, '2024-06-25 08:30:00', 'terdaftar'),
(4, 3, '2024-06-26 09:00:00', 'terdaftar'),
(5, 4, '2024-06-23 19:15:00', 'terdaftar'),
(6, 4, '2024-06-24 20:00:00', 'terdaftar'),
(2, 5, '2024-06-27 10:00:00', 'terdaftar'),
(3, 6, '2024-06-28 14:30:00', 'terdaftar');

-- Insert Certificates (Sample - data sertifikat yang sudah diterbitkan)
INSERT INTO certificates (user_id, event_id, file_sertifikat) VALUES
(2, 1, 'cert_2_1_1234567890.pdf'),
(3, 1, 'cert_3_1_1234567891.pdf'),
(4, 1, 'cert_4_1_1234567892.pdf');

-- ===========================
-- CATATAN PENTING
-- ===========================
-- 1. Password untuk testing semua akun: 
--    - Admin: admin123
--    - Mahasiswa: mhs123
--    Jika menggunakan password_verify(), gunakan hash yang sesuai
--
-- 2. Untuk testing, gunakan password_hash() di PHP:
--    echo password_hash('admin123', PASSWORD_BCRYPT);
--    echo password_hash('mhs123', PASSWORD_BCRYPT);
--
-- 3. File sertifikat harus diletakkan di folder: uploads/certificates/
--
-- 4. Pastikan folder berikut memiliki write permission:
--    - uploads/certificates/
--
-- 5. WORKFLOW ORGANIZER:
--    - Mahasiswa apply organizer -> organizer_applications dibuat, organizer_status = 'pending'
--    - Admin approve -> organizer_status = 'verified', bisa create event
--    - Event yang dibuat organizer punya organizer_id di kolom events.organizer_id
--    - Event organizer wajib punya status 'published' atau 'draft'
--
-- ===========================
