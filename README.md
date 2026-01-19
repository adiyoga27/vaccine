# TANDU GEMAS - Sistem Manajemen Vaksinasi Digital

![TANDU GEMAS Hero](https://via.placeholder.com/1200x400?text=TANDU+GEMAS+Banner)

**TANDU GEMAS** (digiTAl posyaNDu Untuk Generasi kEluarga eMAS) adalah aplikasi berbasis web yang dirancang untuk mendigitalisasi proses manajemen posyandu, mulai dari pendaftaran peserta, penjadwalan vaksinasi, hingga pencatatan riwayat imunisasi anak. Sistem ini memudahkan orang tua dalam memantau jadwal vaksin buah hati dan membantu petugas posyandu dalam mengelola data secara efisien.

## 🚀 Fitur Utama

### 🌟 Halaman Publik (Guest)
- **Edukasi**: Informasi interaktif mengenai pentingnya imunisasi.
- **Ajakan Bertindak**: Desain landing page modern (Tailwind CSS + AOS Animations) untuk menarik minat pendaftaran.

### 👨‍👩‍👧 Dashboard Orang Tua (User)
- **Registrasi Mudah**: Sistem *One-to-One* (Akun User otomatis terhubung dengan Data Anak/Pasien).
- **Kalender Imunisasi**: Visualisasi jadwal vaksin menggunakan **FullCalendar.js**.
- **Pengajuan Vaksin**: Fitur untuk memilih jadwal dan jenis vaksin yang diinginkan.
- **Status Tracking**: Memantau status pengajuan (Menunggu/Selesai) secara *real-time*.

### 🛡️ Dashboard Admin (Petugas)
- **Monitoring Data**:
    - Data Peserta (Orang tua & Anak).
    - Riwayat Transaksi Vaksinasi lengkap.
- **Manajemen Master Data**:
    - Kelola Data Desa/Posyandu.
    - Kelola Jenis Vaksin & Batas Usia Minimal.
    - Kelola Jadwal Kegiatan Posyandu.
- **Approval System**: Menyetujui atau menolak pengajuan vaksin dari pengguna.
- **Activity Log**: Pantauan keamanan sistem dengan mencatat setiap aktivitas perubahan data.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: [Laravel 11](https://laravel.com) (PHP Framework)
- **Frontend**: 
    - [Tailwind CSS](https://tailwindcss.com) (Styling)
    - [Alpine.js](https://alpinejs.dev) (Interaktivitas Ringan)
    - [FullCalendar.js](https://fullcalendar.io) (Manajemen Jadwal)
- **Database**: MySQL 8.0+
- **Packages**:
    - `spatie/laravel-activitylog` (Logging System)

---

## 📂 Struktur Proyek & Layout

Aplikasi ini menggunakan struktur MVC Laravel standar dengan penambahan folder khusus untuk kerapian tata letak:

```
/app
  /Http/Controllers
    - AuthController.php    # Menangani Login/Register Dual Role
    - AdminController.php   # Logika Dashboard Admin (CRUD & Approval)
    - UserController.php    # Logika Dashboard User (Request & Calendar)
  /Models
    - User, Patient, Village, Vaccine, VaccinePatient, etc.

/resources/views
  /auth
    - login.blade.php       # Halaman Login
    - register.blade.php    # Form Registrasi Dual Data (User+Patient)
  /dashboard
    /admin                  # Views khusus Admin
      /villages             # CRUD Desa
      /vaccines             # CRUD Vaksin
      /history              # Riwayat Vaksinasi
      - index.blade.php     # Dashboard Admin Utama
    /user                   # Views khusus User
      - index.blade.php     # Dashboard User (Calendar & Request)
  /layouts
    - admin.blade.php       # Master Layout Admin (Sidebar & Navbar)
  - welcome.blade.php       # Landing Page Tamu
```

---

## ⚙️ Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di komputer lokal Anda:

1. **Clone Repository**
   ```bash
   git clone https://github.com/username/posyandu-care.git
   cd posyandu-care
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```
   *(Catatan: Frontend menggunakan CDN, jadi tidak perlu `npm install`)*

3. **Setup Environment**
   Salin file contoh `.env` dan atur konfigurasi database.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database**
   Buka file `.env` dan sesuaikan dengan kredensial MySQL Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=posyandu_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migrasi & Seeding Data**
   Jalankan perintah ini untuk membuat tabel dan mengisi data dummy (Desa, Vaksin, Admin, User).
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Jalankan Server**
   ```bash
   php artisan serve
   ```
   Buka `http://localhost:8000` di browser Anda.

---

## 🔐 Akun Demo

Gunakan akun berikut untuk pengujian:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@admin.com` | `password` |
| **User** | *(Silakan daftar baru)* | *(Sesuai input)* |

---

## 📱 Tampilan Responsif

Aplikasi ini didesain *Mobile-First*, sehingga tampilan tetap rapi dan fungsional baik diakses melalui Smartphone, Tablet, maupun Desktop.

---

Dibuat dengan ❤️ untuk Kesehatan Ibu dan Anak Indonesia.
