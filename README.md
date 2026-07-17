# Campus Event Hub

**Platform Terintegrasi Manajemen Kegiatan Mahasiswa**

![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-green.svg)
![License](https://img.shields.io/badge/License-Project%20UAS-red.svg)

---

## 📖 Tentang Project

Campus Event Hub adalah platform web yang mengintegrasikan manajemen kegiatan/event di lingkungan kampus. Aplikasi ini memudahkan mahasiswa untuk menemukan dan mendaftar event, sekaligus memberi admin/panitia tools lengkap untuk mengelola event dan peserta.

### 🎯 Fitur Utama

- ✅ **Registrasi & Login** dengan session management
- ✅ **8+ Halaman** untuk mahasiswa dan admin
- ✅ **CRUD Operations** untuk event dan kategori
- ✅ **Search & Filter** event berdasarkan nama, lokasi, kategori
- ✅ **Responsive Design** dengan Bootstrap 5
- ✅ **Database Relational** dengan 5 tabel terintegrasi
- ✅ **Sistem Sertifikat** upload dan download
- ✅ **Admin Dashboard** dengan statistik realtime

---

## 🚀 Quickstart

### Minimal Requirements
- PHP 7.4+
- MySQL 5.7+
- XAMPP atau sejenisnya
- Browser modern

### Setup (3 Langkah)

1. **Clone/Download Project**
   ```bash
   # Project sudah ada di C:\xampp\htdocs\campusevent
   ```

2. **Import Database**
   ```bash
   # Buka phpMyAdmin → Import file database.sql
   # Atau gunakan command line
   mysql -u root -p < database.sql
   ```

3. **Jalankan Aplikasi**
   ```
   URL: http://localhost/campusevent
   ```

**→ Lihat file `SETUP.md` untuk setup lengkap**

---

## 📱 Halaman-Halaman

### Untuk Mahasiswa (8 Halaman)

| Halaman | Deskripsi | URL |
|---------|-----------|-----|
| Login | Form login | `?page=login` |
| Register | Registrasi akun | `?page=register` |
| Dashboard | Dashboard utama | `?page=dashboard` |
| Event | List event + search | `?page=events` |
| Detail Event | Detail event + daftar | `?page=event_detail&id=1` |
| Event Saya | Event yang diikuti | `?page=my_events` |
| Sertifikat | Sertifikat diterima | `?page=certificates` |
| Profil | Edit profil & password | `?page=profile` |

### Untuk Admin (5 Halaman)

| Halaman | Deskripsi | URL |
|---------|-----------|-----|
| Admin Dashboard | Dashboard admin | `?page=admin_dashboard` |
| Kelola Event | CRUD event | `?page=admin_events` |
| Kelola Kategori | CRUD kategori | `?page=admin_categories` |
| Kelola Peserta | List peserta | `?page=admin_participants` |
| Upload Sertifikat | Upload cert peserta | `?page=admin_certificates` |

---

## 🗄️ Database Structure

```sql
users (id, nama, email, password, role)
    ↓
registrations (id, user_id, event_id, tanggal_daftar, status)
    ↓
events (id, judul, deskripsi, tanggal, lokasi, kuota, kategori_id)
    ↓
categories (id, nama_kategori)

certificates (id, user_id, event_id, file_sertifikat)
```

**5 Tabel dengan relasi one-to-many dan many-to-many**

---

## 🔍 Fitur Search & CRUD

### Search Capabilities ⭐
```php
// Search berdasarkan judul
SELECT * FROM events WHERE judul LIKE '%keyword%'

// Search berdasarkan lokasi
SELECT * FROM events WHERE lokasi LIKE '%keyword%'

// Filter kategori
SELECT * FROM events WHERE kategori_id = $id

// Kombinasi
SELECT * FROM events 
WHERE (judul LIKE '%x%' OR lokasi LIKE '%x%') 
  AND kategori_id = $id
```

### CRUD Operations
- **CREATE**: Tambah event baru (admin)
- **READ**: List dan detail event (semua)
- **UPDATE**: Edit event (admin), Edit profil (mahasiswa)
- **DELETE**: Hapus event (admin)

---

## 🛡️ Security Features

- 🔐 Password hashing dengan BCRYPT
- 📝 Session management dengan timeout 30 menit
- 🔒 Role-based access control (admin/mahasiswa)
- 🚫 SQL injection prevention dengan `real_escape_string()`

---

## 📁 Project Structure

```
campusevent/
├── config/
│   ├── db.php              # Database connection
│   └── session.php         # Session & auth functions
├── pages/
│   ├── login.php           # Login form
│   ├── register.php        # Registrasi
│   ├── dashboard.php       # Dashboard
│   ├── events.php          # Event list + search
│   ├── event_detail.php    # Event detail
│   ├── my_events.php       # Registered events
│   ├── certificates.php    # Certificates list
│   ├── profile.php         # User profile
│   └── components/
│       ├── navbar.php      # Navigation component
│       └── footer.php      # Footer component
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── events.php          # Event CRUD
│   ├── categories.php      # Category CRUD
│   ├── participants.php    # Participant list
│   └── certificates.php    # Certificate upload
├── assets/
│   └── css/
│       └── style.css       # Custom Bootstrap styles
├── uploads/
│   └── certificates/       # Certificate storage
├── index.php               # Main router
├── database.sql            # Database schema
├── DOKUMENTASI.md          # Detailed documentation
└── SETUP.md                # Setup guide
```

---

## 👤 Demo Accounts

```
ADMIN
├── Email: admin@campus.com
└── Password: admin123

MAHASISWA
├── Email: mahasiswa@campus.com
└── Password: mhs123
```

---

## 🎨 UI Theme

- **Primary Color**: `#1a5f3e` (Dark Green)
- **Secondary Color**: `#ff7f00` (Orange)
- **Framework**: Bootstrap 5.3.0
- **Icons**: FontAwesome 6.4.0
- **Font**: Segoe UI, Tahoma

---

## ✨ Highlights

### Untuk Dosen UAS
✅ Memenuhi semua syarat:
- Bootstrap HTML-CSS-JavaScript ✓
- PHP dengan materi lengkap ✓
- 5+ tabel database dengan relasi ✓
- Login dengan session management ✓
- Minimal 8 halaman ✓
- Searching di CRUD ✓
- Hosting siap di XAMPP ✓
- Dokumentasi lengkap ✓

### Bonus Features
- Responsive design untuk mobile
- Admin dashboard dengan statistik
- Event filtering & pagination
- Progress bar kuota peserta
- Related events recommendation
- Quick action buttons

---

## 📊 Sample Data

Database sudah include:
- 1 admin user
- 5 mahasiswa users
- 8 sample events dari berbagai kategori
- 12 sample registrations
- 3 sample certificates

---

## 🔧 Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 7.4+ |
| Database | MySQL 5.7+ |
| Frontend | HTML5, CSS3, JS (Vanilla) |
| Framework | Bootstrap 5.3.0 |
| Icons | FontAwesome 6.4.0 |
| Server | Apache (XAMPP) |

---

## 📚 Documentation

- **[SETUP.md](./SETUP.md)** - Panduan instalasi & setup
- **[DOKUMENTASI.md](./DOKUMENTASI.md)** - Dokumentasi lengkap
- Inline comments di setiap file PHP

---

## 🎓 Untuk Presentasi UAS

### Demo Flow (30 menit)

1. **Intro** (2 min)
   - Jelaskan tujuan project
   - Tunjukkan apa yang dibuat

2. **Setup & Infrastructure** (3 min)
   - Tunjukkan struktur folder
   - Import database live
   - Jalankan aplikasi

3. **Mahasiswa Features** (8 min)
   - Register & login
   - Dashboard
   - Search & filter events ⭐
   - Registrasi event
   - Profile & certificate

4. **Admin Features** (8 min)
   - Admin dashboard
   - CRUD event & kategori
   - View peserta
   - Upload certificate

5. **Code Walkthrough** (7 min)
   - Database design & relasi
   - Search query
   - Authentication
   - Security features

6. **Q&A** (5 min)

---

## 📝 Notes

- Pastikan XAMPP sudah running sebelum akses
- Gunakan Firefox/Chrome untuk testing
- Test mobile responsive dengan F12
- Siapkan hard copy dokumentasi untuk dosen
- Backup database sebelum membuat perubahan

---

## 📧 Contact

Untuk pertanyaan atau issues, hubungi pengembang atau instruktur.

---

**Campus Event Hub** | Project UAS | 2024 | All Rights Reserved

#   c a m p u s e v e n t  
 