# 🎯 Campus Event Hub - Dokumentasi Proyek

**Platform Terintegrasi Manajemen Kegiatan Mahasiswa**

---

## 📋 Ringkasan Proyek

Campus Event Hub adalah sebuah aplikasi web yang dirancang untuk memudahkan manajemen kegiatan/event di lingkungan kampus. Platform ini menghubungkan mahasiswa dengan berbagai acara yang diselenggarakan oleh institusi, sekaligus memberikan kemudahan bagi admin/panitia dalam mengelola event dan peserta.

### 🎯 Tujuan
- Menyediakan platform terpusat untuk informasi event kampus
- Memudahkan mahasiswa mendaftar event
- Memberikan sistem manajemen peserta yang efisien bagi admin
- Mengotomasi proses penerbitkan sertifikat

---

## 👥 Aktor Sistem

### 1. **Mahasiswa**
Pengguna utama yang dapat:
- Registrasi dan login
- Melihat daftar event
- Mencari event berdasarkan kategori, nama, lokasi
- Mendaftar event
- Melihat event yang sudah diikuti
- Download sertifikat
- Kelola profil

### 2. **Admin/Panitia**
Pengguna dengan privilese khusus yang dapat:
- Login dengan role admin
- Membuat event baru
- Edit dan hapus event
- Melihat daftar peserta event
- Upload sertifikat untuk peserta
- Kelola kategori event

---

## 🗄️ Struktur Database

### Tabel 1: `users`
Menyimpan data pengguna (mahasiswa dan admin)
```
- id (INT, PK)
- nama (VARCHAR)
- email (VARCHAR, UNIQUE)
- password (VARCHAR, hashed)
- role (ENUM: 'admin', 'mahasiswa')
- created_at (TIMESTAMP)
```

### Tabel 2: `categories`
Menyimpan kategori event
```
- id (INT, PK)
- nama_kategori (VARCHAR)
- created_at (TIMESTAMP)
```

Contoh kategori:
- Seminar
- Workshop
- Kompetisi
- Webinar
- Pelatihan
- Diskusi Panel

### Tabel 3: `events`
Menyimpan informasi event
```
- id (INT, PK)
- judul (VARCHAR)
- deskripsi (TEXT)
- tanggal (DATETIME)
- lokasi (VARCHAR)
- kuota (INT)
- kategori_id (INT, FK)
- created_at (TIMESTAMP)
```

### Tabel 4: `registrations`
Menyimpan pendaftaran mahasiswa ke event
```
- id (INT, PK)
- user_id (INT, FK)
- event_id (INT, FK)
- tanggal_daftar (DATETIME)
- status (VARCHAR)
- UNIQUE: (user_id, event_id)
```

### Tabel 5: `certificates`
Menyimpan data sertifikat peserta
```
- id (INT, PK)
- user_id (INT, FK)
- event_id (INT, FK)
- file_sertifikat (VARCHAR)
- created_at (TIMESTAMP)
- UNIQUE: (user_id, event_id)
```

### 🔗 Relasi Database
```
users ──┐
        ├─→ registrations ←─ events ─→ categories
        └─→ certificates  ←─ events
```

---

## 📄 Halaman-Halaman Sistem

### Halaman Mahasiswa (8 Halaman Minimal)

#### 1. **Login** (`pages/login.php`)
- Form login untuk mahasiswa dan admin
- Validasi email dan password
- Session management
- Link ke halaman registrasi

#### 2. **Register** (`pages/register.php`)
- Form registrasi akun mahasiswa baru
- Validasi email unik
- Validasi panjang password (min 6 karakter)
- Enkripsi password menggunakan bcrypt

#### 3. **Dashboard** (`pages/dashboard.php`)
- Menampilkan statistik: Total Event, Event Saya, Sertifikat
- Event terbaru (6 event)
- Event populer (3 event paling banyak peserta)
- Link cepat ke halaman lainnya

#### 4. **Daftar Event** (`pages/events.php`) ⭐ **Fitur SEARCHING**
- List semua event dengan pagination
- Fitur search berdasarkan:
  - Nama event (LIKE query)
  - Lokasi
  - Deskripsi
- Fitur filter berdasarkan kategori
- Progress bar kuota peserta
- Detail card event: judul, tanggal, lokasi, kategori

#### 5. **Detail Event** (`pages/event_detail.php`)
- Tampilan lengkap event dengan deskripsi panjang
- Informasi: tanggal, waktu, lokasi, kuota, peserta terdaftar
- Tombol "Daftar Sekarang" (jika belum terdaftar dan kuota tersedia)
- Tombol "Batalkan Registrasi" (jika sudah terdaftar)
- Event serupa (berdasarkan kategori yang sama)

#### 6. **Event Saya** (`pages/my_events.php`)
- List event yang sudah diikuti mahasiswa
- Info: nama event, kategori, tanggal, status pendaftaran
- Link ke detail event

#### 7. **Sertifikat Saya** (`pages/certificates.php`)
- List sertifikat yang telah diterima
- Tombol download file sertifikat
- Tombol preview/view sertifikat

#### 8. **Profil** (`pages/profile.php`)
- Edit data profil (nama, email)
- Form ubah password dengan verifikasi password lama
- Statistik: event diikuti, sertifikat diterima, event aktif

### Halaman Admin (Bonus)

#### 1. **Admin Dashboard** (`admin/dashboard.php`)
- Statistik sistem: Total Event, Mahasiswa, Registrasi, Kategori
- Event terpopuler (top 5)
- Informasi sistem (PHP version, MySQL version)
- Quick action buttons

#### 2. **Kelola Event** (`admin/events.php`) ⭐ **CRUD**
- Form tambah event baru
- List event dengan pagination
- Edit event (form pre-filled)
- Hapus event (dengan konfirmasi)

#### 3. **Kelola Kategori** (`admin/categories.php`) ⭐ **CRUD**
- Tambah kategori baru
- List kategori dengan jumlah event
- Edit kategori (modal)
- Hapus kategori (dengan cascade update)

#### 4. **Kelola Peserta** (`admin/participants.php`)
- List semua registrasi
- Filter berdasarkan event
- Lihat: nama peserta, event, tanggal daftar, status

#### 5. **Upload Sertifikat** (`admin/certificates.php`)
- Upload file sertifikat per peserta
- Pilih peserta dan event
- Validasi file (PDF/JPG/PNG)
- Validasi sertifikat tidak duplikat

---

## 🎨 UI/UX - Bootstrap Styling

### Komponen UI
```
┌─────────────────────────────────┐
│ NAVBAR (Hijau tua)              │
│ Logo | Home | Event | Profil... │
└─────────────────────────────────┘

CONTENT AREA:
┌─────────────────────────────────┐
│ STAT CARDS                      │
│ ┌──────┐ ┌──────┐ ┌──────┐    │
│ │  25  │ │  4   │ │  2   │    │
│ │Event │ │Event │ │Cert. │    │
│ └──────┘ └──────┘ └──────┘    │
└─────────────────────────────────┘

┌─────────────────────────────────┐
│ EVENT CARDS (Grid/Responsive)   │
│ ┌──────────┐ ┌──────────┐      │
│ │ Title    │ │ Title    │      │
│ │Date/Loc  │ │Date/Loc  │      │
│ │Kategori  │ │Kategori  │      │
│ │[Btn]     │ │[Btn]     │      │
│ └──────────┘ └──────────┘      │
└─────────────────────────────────┘

FOOTER (Dark)
```

### Warna Theme
- Primary (Hijau): `#1a5f3e`
- Secondary (Orange): `#ff7f00`
- Success (Hijau): `#28a745`
- Danger (Merah): `#dc3545`

### Features
- Responsive design (Mobile, Tablet, Desktop)
- Smooth animations & transitions
- Bootstrap 5 grid system
- FontAwesome icons
- Bootstrap modals untuk dialog

---

## 🔐 Fitur Security

1. **Password Hashing**
   - Menggunakan `password_hash()` dengan BCRYPT
   - Verifikasi dengan `password_verify()`

2. **SQL Injection Prevention**
   - Menggunakan `mysqli_real_escape_string()` atau prepared statements

3. **Session Management**
   - Session timeout setelah 30 menit inaktif
   - Validasi role untuk akses halaman admin

4. **CSRF Protection** (dapat ditambahkan)
   - Token validation untuk form submission

---

## 🔄 Workflow Sistem

### Workflow Mahasiswa
```
Register → Login → Dashboard → Cari Event → Lihat Detail 
→ Daftar Event → Registrasi Tercatat → Ikuti Event 
→ Terima Sertifikat → Download
```

### Workflow Admin
```
Login → Admin Dashboard → Tambah Event → Event Live 
→ Mahasiswa Daftar → Lihat Peserta → Upload Sertifikat 
→ Mahasiswa Terima Sertifikat
```

---

## ⭐ Fitur Searching & CRUD

### Searching Capabilities
✅ Search event berdasarkan **judul** (LIKE query)
✅ Search event berdasarkan **lokasi** (LIKE query)
✅ Filter event berdasarkan **kategori** (WHERE query)
✅ Search menampilkan info hasil dalam alert

**Query Example:**
```sql
SELECT * FROM events 
WHERE judul LIKE '%seminar%' 
  OR lokasi LIKE '%lab%'
  AND kategori_id = 1
```

### CRUD Operations
✅ **Create**: Tambah event baru (admin)
✅ **Read**: List dan detail event
✅ **Update**: Edit event (admin), Edit profil (mahasiswa)
✅ **Delete**: Hapus event (admin)

---

## 🎯 Fitur-Fitur Tambahan

1. **Progres Bar Kuota** - Visualisasi peserta vs kuota
2. **Event Terbaru & Populer** - Dashboard enhancement
3. **Related Events** - Rekomendasi event serupa
4. **Status Badge** - Visual indikator status registrasi
5. **Quick Actions** - Button cepat akses fitur utama
6. **Responsive Tables** - Admin dashboard dengan data realtime

---

## 📊 Statistik Database

### Dummy Data Included
- 1 Admin user
- 5 Mahasiswa users
- 8 Events dengan kategori beragam
- 12 Registrasi sampel
- 3 Sertifikat sampel

---

## 🚀 Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, Bootstrap 5, CSS3
- **JavaScript**: Vanilla JS (Bootstrap Bundle)
- **Icons**: FontAwesome 6.4.0

---

## 📁 Struktur Folder

```
campusevent/
├── config/
│   ├── db.php           (Database connection)
│   └── session.php      (Session management)
├── pages/
│   ├── login.php        (Login form)
│   ├── register.php     (Registrasi)
│   ├── dashboard.php    (Dashboard)
│   ├── events.php       (List events + search)
│   ├── event_detail.php (Detail & register)
│   ├── my_events.php    (Event saya)
│   ├── certificates.php (Sertifikat saya)
│   ├── profile.php      (Profil user)
│   └── components/
│       ├── navbar.php   (Navbar component)
│       └── footer.php   (Footer component)
├── admin/
│   ├── dashboard.php    (Admin dashboard)
│   ├── events.php       (CRUD events)
│   ├── categories.php   (CRUD categories)
│   ├── participants.php (Lihat peserta)
│   └── certificates.php (Upload sertifikat)
├── assets/
│   └── css/
│       └── style.css    (Custom styles)
├── uploads/
│   └── certificates/    (Folder untuk file sertifikat)
├── index.php            (Router utama)
└── database.sql         (Database structure)
```

---

## 🔧 Cara Setup & Testing

Lihat file `SETUP.md` untuk panduan instalasi lengkap.

---

## 📝 Notes untuk Presentasi UAS

### Syarat yang Dipenuhi
✅ Bootstrap HTML-CSS-JavaScript
✅ PHP (semua materi dosen)
✅ 5+ Tabel database dengan relasi jelas
✅ Login dengan Session (30 menit timeout)
✅ Minimal 8 halaman + halaman admin
✅ Searching/Filter di CRUD
✅ Hosting siap di c:/xampp/htdocs/
✅ Dokumentasi lengkap dengan screenshots
✅ File PHP coding terstruktur rapi
✅ Database SQL terpisah dan lengkap

### File-File Penting
- `index.php` - Router utama dengan 500+ baris
- `pages/events.php` - CRUD + Searching
- `admin/events.php` - Admin CRUD
- `config/db.php` - Database connection
- `database.sql` - SQL dump lengkap
- `assets/css/style.css` - Styling Bootstrap custom

---

## 📞 Support & Troubleshooting

**Jika ada masalah:**
1. Pastikan MySQL running di XAMPP
2. Pastikan database sudah di-import
3. Pastikan folder `uploads/certificates/` writable
4. Check browser console untuk JavaScript errors
5. Check PHP error logs untuk server errors

---

**Dikembangkan untuk Project UAS** | 2024 | Campus Event Hub
