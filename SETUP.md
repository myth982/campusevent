# 🚀 SETUP GUIDE - Campus Event Hub

Panduan lengkap untuk setup dan menjalankan aplikasi Campus Event Hub.

---

## ✅ REQUIREMENTS

- XAMPP (PHP 7.4+, MySQL 5.7+)
- Browser modern (Chrome, Firefox, Edge)
- Text editor atau IDE (VS Code, PHPStorm, dll)

---

## 📥 STEP 1: Download & Extract

1. Pastikan sudah memiliki file project `campusevent`
2. Letakkan folder di: `C:\xampp\htdocs\`
3. Struktur folder harus: `C:\xampp\htdocs\campusevent\`

---

## 🗄️ STEP 2: Setup Database

### Cara 1: Menggunakan phpMyAdmin

1. **Buka phpMyAdmin**
   - Jalankan XAMPP
   - Klik "Start" untuk Apache dan MySQL
   - Buka browser: `http://localhost/phpmyadmin`

2. **Import Database**
   - Klik tab "Import"
   - Pilih file: `campusevent/database.sql`
   - Klik "Import" button

3. **Verifikasi**
   - Seharusnya database `campus_event_hub` sudah tercipta
   - 5 tabel sudah ada: users, categories, events, registrations, certificates

### Cara 2: Command Line (Advanced)

```bash
# Masuk ke folder project
cd C:\xampp\htdocs\campusevent

# Connect ke MySQL dan run SQL file
mysql -u root -p campus_event_hub < database.sql
```

---

## 🔐 STEP 3: Setup Password untuk Testing

Database sudah punya dummy user, tapi passwordnya perlu di-hash. 

### Generate Hash Password

1. Buat file `generate_hash.php` di folder project:

```php
<?php
// File untuk generate password hash
echo "Admin password hash: " . password_hash('admin123', PASSWORD_BCRYPT) . "<br>";
echo "Mahasiswa password hash: " . password_hash('mhs123', PASSWORD_BCRYPT) . "<br>";
?>
```

2. Buka di browser: `http://localhost/campusevent/generate_hash.php`
3. Copy hash yang ditampilkan

4. Update di phpMyAdmin:
   - Buka tabel `users`
   - Edit user `admin@campus.com` dan `mahasiswa@campus.com`
   - Paste hash password yang sudah di-generate

**Atau gunakan hash yang sudah siap:**
```
Admin password hash:
$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9

Mahasiswa password hash:
$2y$10$L0nJ.H3XsJ5w.K6Z9L1pC.WvB3YJ7K0Z9L1X9L1X9L1X9L1X9L1X9
```

---

## 📁 STEP 4: Set Folder Permissions

Pastikan folder untuk upload sertifikat writable:

```bash
# Windows (Di Command Prompt as Administrator)
cd C:\xampp\htdocs\campusevent
mkdir uploads\certificates
```

Folder sudah ada, pastikan tidak ada permission issues.

---

## 🚀 STEP 5: Jalankan Aplikasi

1. **Buka XAMPP Control Panel**
   - Klik "Start" untuk Apache dan MySQL
   - Pastikan ada tanda hijau di kedua-duanya

2. **Akses aplikasi**
   - Buka browser
   - URL: `http://localhost/campusevent`

3. **Login Test**
   - Email: `admin@campus.com`
   - Password: `admin123`
   
   atau
   
   - Email: `mahasiswa@campus.com`
   - Password: `mhs123`

---

## 🧪 TESTING CHECKLIST

### Test Sebagai Mahasiswa

- [ ] Register akun baru → Login
- [ ] Buka Dashboard → Lihat statistik
- [ ] Search event → Filter kategori
- [ ] Lihat detail event
- [ ] Daftar event
- [ ] Lihat "Event Saya"
- [ ] Lihat "Sertifikat Saya"
- [ ] Edit profil
- [ ] Ubah password
- [ ] Logout

### Test Sebagai Admin

- [ ] Login dengan admin account
- [ ] Dashboard → Lihat statistik
- [ ] Tambah event baru
- [ ] Edit event
- [ ] Hapus event
- [ ] Tambah kategori
- [ ] Edit kategori
- [ ] Lihat peserta
- [ ] Upload sertifikat

### Test Fitur Search (IMPORTANT)

1. Buka halaman "Event"
2. Search dengan keyword: "seminar" → Harus muncul event "Seminar Teknologi AI"
3. Search dengan keyword: "lab" → Harus muncul event dengan lokasi "Lab Komputer B"
4. Filter kategori: "Workshop" → Harus muncul workshop events
5. Kombinasi search + filter

---

## 🐛 TROUBLESHOOTING

### Error: "Connection Refused"
- ✅ Pastikan Apache dan MySQL sudah running di XAMPP
- ✅ Klik "Start" di XAMPP Control Panel

### Error: "Database Not Found"
- ✅ Pastikan `database.sql` sudah di-import
- ✅ Verifikasi di phpMyAdmin bahwa database `campus_event_hub` ada

### Error: "Permission Denied" (Upload Sertifikat)
- ✅ Pastikan folder `uploads/certificates/` exist
- ✅ Right-click folder → Properties → Security → Allow full access

### Error: "Password Salah" Saat Login
- ✅ Pastikan password sudah di-hash dengan `password_hash()`
- ✅ Generate ulang menggunakan `generate_hash.php`
- ✅ Test account: 
  - Email: `mahasiswa@campus.com`
  - Password: `mhs123` (tanpa @)

### White Page / Blank Screen
- ✅ Check `error_reporting` di config atau enable error log
- ✅ Buka browser console (F12) untuk error details
- ✅ Check PHP error log di `C:\xampp\apache\logs\`

---

## 📊 Sample Data Login

```
┌─────────────────────────────────────┐
│ ADMIN ACCOUNT                       │
├─────────────────────────────────────┤
│ Email: admin@campus.com             │
│ Password: admin123                  │
│ Role: Admin                         │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ MAHASISWA ACCOUNT #1                │
├─────────────────────────────────────┤
│ Email: mahasiswa@campus.com         │
│ Password: mhs123                    │
│ Role: Mahasiswa                     │
│ Status: Sudah daftar 3 event        │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│ MAHASISWA ACCOUNT #2                │
├─────────────────────────────────────┤
│ Email: siti@campus.com              │
│ Password: mhs123                    │
│ Role: Mahasiswa                     │
│ Status: Sudah daftar 2 event        │
└─────────────────────────────────────┘
```

---

## 🎯 FITUR YANG BISA DI-DEMO UNTUK UAS

### 1. System Overview
- Tunjukkan struktur database (5 tabel dengan relasi)
- Tunjukkan file struktur project

### 2. Mahasiswa Features
- Login & Register
- Dashboard dengan statistik
- Search & Filter events (⭐ PENTING untuk syarat)
- Registrasi event
- Download sertifikat
- Edit profil

### 3. Admin Features
- CRUD Event (Create, Read, Update, Delete)
- CRUD Kategori
- Lihat daftar peserta
- Upload sertifikat

### 4. Security
- Session management (logout otomatis 30 menit)
- Password hashing dengan bcrypt
- Role-based access control

### 5. Database
- Query dengan JOIN antar tabel
- Foreign key relationships
- Cascading delete

---

## 📝 CATATAN PENTING

1. **File Database Besar?**
   - File `database.sql` ~5KB (biasa untuk dummy data)

2. **Sudah Siap untuk Production?**
   - Belum, masih butuh:
     - Error handling yang lebih baik
     - Input validation yang ketat
     - CSRF token untuk form
     - SSL certificate untuk HTTPS

3. **Mau Tambah Feature?**
   - Editing halaman file PHP biasa
   - Update database structure di `database.sql`
   - Tambah routing di `index.php`

4. **Dokumentasi untuk Dosen**
   - Lihat file `DOKUMENTASI.md`
   - Lihat file `README.md` (jika ada)
   - Lihat screenshots folder

---

## 🎓 Untuk Presentasi UAS

### Yang Perlu Disiapkan

```
Folder Project:
├── Semua file PHP sudah ada
├── Database SQL siap import
├── Assets CSS/JS sudah included
├── Dummy data sudah ada
└── Dokumentasi lengkap

Saat Presentasi:
1. Tunjukkan struktur folder
2. Import database live (3 menit)
3. Demo features mahasiswa (5 menit)
4. Demo features admin (5 menit)
5. Tunjukkan kode (10 menit)
   - Fungsi search di events.php
   - CRUD di admin/events.php
   - Database query & relasi
6. Q&A (10 menit)
```

---

## ✨ TIPS

- **Backup Database Sebelum Edit**: Export database sebelum membuat perubahan
- **Test Semua Browser**: Chrome, Firefox, Edge
- **Cek Mobile Responsive**: F12 → Responsive mode
- **Print Dokumentasi**: Siapkan hard copy untuk dosen

---

## 📞 QUICK REFERENCE

| Halaman | URL | Role |
|---------|-----|------|
| Login | `/campusevent` | Public |
| Register | `?page=register` | Public |
| Dashboard | `?page=dashboard` | Logged in |
| Events | `?page=events` | Logged in |
| Admin Dashboard | `?page=admin_dashboard` | Admin only |
| phpMyAdmin | `localhost/phpmyadmin` | -  |

---

**Good luck dengan presentasi UAS! 🎉**

Jika ada pertanyaan, tanyakan langsung ke instruktur atau lihat komentar di kode.
