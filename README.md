# Campus Event Hub

**Platform Terintegrasi Manajemen Kegiatan Mahasiswa**

![Version](https://img.shields.io/badge/Version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-green.svg)
![License](https://img.shields.io/badge/License-Project%20UAS-red.svg)

---

## Tentang Project

Campus Event Hub adalah platform web yang mengintegrasikan manajemen kegiatan/event di lingkungan kampus. Aplikasi ini memudahkan mahasiswa untuk menemukan dan mendaftar event, sekaligus memberi admin/panitia tools lengkap untuk mengelola event dan peserta.

## Fitur Utama

- **Registrasi & Login** dengan session management
- **8+ Halaman** untuk mahasiswa dan admin
- **CRUD Operations** untuk event dan kategori
- **Search & Filter** event berdasarkan nama, lokasi, kategori
- **Responsive Design** dengan Bootstrap 5
- **Database Relational** dengan 5 tabel terintegrasi
- **Sistem Sertifikat** upload dan download
- **Admin Dashboard** dengan statistik realtime

---

## Quickstart

### Minimal Requirements

- PHP 7.4+
- MySQL 5.7+
- XAMPP atau sejenisnya
- Browser modern

### Setup (3 Langkah)

1. **Clone/Download Project**
   ```bash
   # Project sudah ada di C:/xampp/htdocs/campusevent
   ```
2. **Import Database**
   ```bash
   # Buka phpMyAdmin -> Import file database.sql
   # Atau gunakan command line
   mysql -u root -p < database.sql
   ```
3. **Jalankan Aplikasi**
   ```bash
   # Buka browser
   http://localhost/campusevent
   ```

**Lihat file `SETUP.md` untuk setup lengkap**

---

## Halaman-Halaman

### Untuk Mahasiswa (8 Halaman)

| Halaman       | Deskripsi                   | URL                         |
|--------------|-----------------------------|-----------------------------|
| Login         | Form login                 | `?page=login`               |
| Register      | Registrasi akun            | `?page=register`            |
| Dashboard     | Dashboard utama            | `?page=dashboard`           |
| Event         | List event + search        | `?page=events`              |
| Detail Event  | Detail event + daftar      | `?page=event_detail&id=1`   |
| Event Saya    | Event yang diikuti         | `?page=my_events`           |
| Sertifikat    | Sertifikat diterima        | `?page=certificates`        |
| Profil        | Edit profil & password     | `?page=profile`             |

### Untuk Admin (5 Halaman)

| Halaman            | Deskripsi          | URL                        |
|-------------------|--------------------|----------------------------|
| Admin Dashboard   | Dashboard admin    | `?page=admin_dashboard`    |
| Kelola Event      | CRUD event         | `?page=admin_events`       |
| Kelola Kategori   | CRUD kategori      | `?page=admin_categories`   |
| Kelola Peserta    | List peserta       | `?page=admin_participants` |
| Upload Sertifikat | Upload cert peserta| `?page=admin_certificates` |

---

## Struktur Database

- `users` (id, nama, email, password, role)
- `events` (id, judul, deskripsi, tanggal, lokasi, kuota, kategori_id)
- `categories` (id, nama_kategori)
- `registrations` (id, user_id, event_id, tanggal_daftar, status)
- `certificates` (id, user_id, event_id, file_sertifikat)

---

## Struktur Project

```
campusevent/
├── admin/
├── assets/
├── config/
├── docs/
├── pages/
├── setup/
├── uploads/
├── database.sql
├── DOKUMENTASI.md
├── README.md
├── SETUP.md
└── index.php
```

---

## Akun Demo

**ADMIN**
- Email: `admin@campus.com`
- Password: `admin123`

**MAHASISWA**
- Email: `mahasiswa@campus.com`
- Password: `mhs123`

---

## Tech Stack

| Component | Technology |
|-----------|------------|
| Backend   | PHP 7.4+   |
| Database  | MySQL 5.7+ |
| Frontend  | HTML5, CSS3, JS |
| Framework | Bootstrap 5.3.0 |
| Icons     | FontAwesome 6.4.0 |
| Server    | Apache (XAMPP) |

---

## Dokumentasi

- `SETUP.md` - Panduan instalasi & setup
- `DOKUMENTASI.md` - Dokumentasi lengkap

---

## Presentasi UAS

- Demo aplikasi di localhost
- Tunjukkan fitur mahasiswa dan admin
- Jelaskan struktur folder dan database
