# 🎯 INSTALLATION GUIDE - Campus Event Hub

## ⚡ CARA TERCEPAT (2 MENIT)

### 1️⃣ Pastikan XAMPP Running
```
Buka XAMPP Control Panel
↓
Klik "Start" Apache & MySQL
↓
Tunggu berwarna HIJAU ✅
```

### 2️⃣ Buka Installation Wizard
```
Browser: http://localhost/campusevent/install.php
```

### 3️⃣ Ikuti Wizard (Tinggal Click "Lanjutkan" 3x)
```
Step 1: Buat Database
Step 2: Import Schema
Step 3: Setup Password
Step 4: Klik "Selesai"
```

### 4️⃣ Login & Mulai!
```
URL: http://localhost/campusevent
```

---

## 👤 Login Credentials

### Admin Account
```
Email: admin@campus.com
Password: admin123
Role: Admin (bisa CRUD event, kategori, upload sertifikat)
```

### Mahasiswa Account
```
Email: mahasiswa@campus.com
Password: mhs123
Role: Mahasiswa (bisa daftar event, download sertifikat)
```

### Akun Tambahan (juga tersedia)
- siti@campus.com / mhs123
- ahmad@campus.com / mhs123
- dewi@campus.com / mhs123
- rendra@campus.com / mhs123

---

## 🔧 Apa yang Dilakukan Install.php?

### ✅ Step 1: Create Database
- Membuat database dengan nama `campus_event_hub`
- Set charset UTF-8 untuk support character Indonesia

### ✅ Step 2: Import Schema
- Membuat 5 tabel:
  - users (pengguna)
  - categories (kategori event)
  - events (acara)
  - registrations (pendaftaran)
  - certificates (sertifikat)
- Setup foreign keys dan relasi
- Insert dummy data (8 events, 5 mahasiswa, dll)

### ✅ Step 3: Setup Password
- Hash password akun demo dengan BCRYPT
- Password sudah aman dan terenkripsi

---

## ❓ FAQ

**Q: Bagaimana jika wizard gagal?**
A: Klik "Ulangi Setup" atau refresh halaman install.php

**Q: Bisa ganti password admin?**
A: Login → Profil → Ubah Password

**Q: Ingin tambah data event?**
A: Login as admin → Admin Dashboard → Kelola Event → Tambah Event

**Q: File install.php perlu dihapus?**
A: Tidak harus, tapi bisa dihapus setelah setup untuk security

---

## 🚨 Troubleshooting

### Error: "Can't connect to MySQL"
**Solusi:** 
1. Buka XAMPP Control Panel
2. Pastikan MySQL status HIJAU
3. Klik "Start" jika belum running

### Error: "Access Denied"
**Solusi:**
Pastikan di config/db.php:
```php
$db_user = 'root';
$db_pass = '';  // kosong untuk XAMPP default
```

### Error: "Upload folder permission"
**Solusi:**
Folder `uploads/certificates/` harus writable:
```
Right-click → Properties → Security → Full Control
```

### Error: Database sudah ada
**Solusi:**
- Klik "Ulangi Setup" untuk reset
- Atau manual delete database di phpMyAdmin

---

## 📊 Database Info

### Dummy Data yang Included
- 1 Admin account
- 5 Mahasiswa accounts  
- 8 Events dari berbagai kategori
- 6 Event categories
- 12 Sample registrations
- 3 Sample certificates

### Database File
- File: `database.sql` (~10KB)
- Sudah include tabel, relasi, dan dummy data
- Auto-imported oleh install.php

---

## ✨ Next Steps

Setelah installation selesai:

1. **Test Login**
   - Login dengan mahasiswa@campus.com
   - Lihat Dashboard
   - Coba search event

2. **Test Admin**
   - Login dengan admin@campus.com
   - Lihat Admin Dashboard
   - Coba buat event baru

3. **Explore Features**
   - Search dan filter event
   - Daftar event
   - Edit profile
   - Download sertifikat (jika ada)

4. **Siap Presentasi!**
   - Dokumentasi lengkap di README.md
   - Code comment di setiap file
   - Demo data sudah siap

---

## 📝 Important Files

| File | Fungsi |
|------|--------|
| install.php | Installation wizard |
| index.php | Router utama |
| config/db.php | Database config |
| database.sql | Database schema |
| pages/ | Halaman mahasiswa |
| admin/ | Halaman admin |
| DOKUMENTASI.md | Full documentation |
| SETUP.md | Detailed setup guide |

---

## 🎓 Untuk Presentasi UAS

**Pastikan sebelum presentasi:**
- ✅ XAMPP running
- ✅ Database sudah installed
- ✅ Bisa login dengan 2 akun (admin & mahasiswa)
- ✅ Search & filter berjalan
- ✅ CRUD operations berjalan
- ✅ Responsive design ok di mobile

---

**Installation selesai! Siap untuk presentasi UAS.** 🚀

Jika ada pertanyaan, lihat file SETUP.md atau DOKUMENTASI.md
