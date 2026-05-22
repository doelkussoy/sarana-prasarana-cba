# Sistem Sarana Prasarana (CBA)

Sistem Sarana Prasarana adalah aplikasi berbasis web yang dirancang untuk mengelola dan memantau pemeliharaan fasilitas perusahaan secara terpusat. Aplikasi ini membantu tim maintenance dalam mencatat pemeriksaan rutin, kondisi aset, mengunggah bukti foto, dan mencetak kartu riwayat perawatan untuk berbagai kategori sarana prasarana.

## 🚀 Fitur Utama

Aplikasi ini terdiri dari modul utama yang saling terintegrasi:

1.  **Perawatan APAR (Alat Pemadam Api Ringan)**
    *   Pendataan lokasi dan jenis APAR.
    *   Pencatatan pemeriksaan tekanan, segel, selang, dan nozzle.
    *   Cetak kartu riwayat pemeriksaan bulanan.
2.  **Perawatan Gedung**
    *   Monitoring kondisi fisik bangunan (atap, dinding, lantai, pintu, jendela).
    *   Pencatatan temuan kerusakan dan status perbaikan.
3.  **Perawatan Hydrant**
    *   Pemeriksaan rutin instalasi hydrant (valve, nozzle, hose, box).
4.  **Perawatan Grease Trap (Penyaring Lemak)**
    *   Pencatatan pembersihan mingguan.
    *   Laporan rekapitulasi semesteran.
5.  **Bukti Visual (Foto Pengecekan)**
    *   Fitur unggah foto bukti kondisi sarana saat diperiksa.
    *   Pratinjau foto secara instan melalui pop-up (modal) di kartu riwayat.
6.  **Manajemen User (Admin Only)**
    *   Halaman khusus untuk Admin menambah, mengubah, atau menghapus pengguna sistem.
    *   Pengaturan hak akses (Admin vs User).

## 🔑 Demo Akses

Anda dapat mencoba aplikasi ini dengan menggunakan akun demo berikut:

**Role User (Petugas Maintenance):**
* **Username:** `user`
* **Password:** `user`
*(Catatan: Role User hanya dapat mengisi data perawatan untuk bulan dan tahun berjalan. User tidak dapat mengedit atau menghapus data yang sudah disimpan).*

**Role Admin:**
* **Username:** `admin`
* **Password:** `admin` *(atau password admin yang Anda set up)*
*(Catatan: Admin memiliki hak penuh untuk mengedit semua data riwayat, menghapus data, dan mengelola user).*

## 🛡️ Keamanan Sistem

Sistem ini telah ditingkatkan dengan standar keamanan produksi:
*   **Password Hashing:** Menggunakan algoritma **BCrypt** untuk menyimpan password pengguna secara aman di database.
*   **SQL Injection Prevention:** Semua input diproses menggunakan escaping untuk mencegah serangan injeksi SQL.
*   **Role-Based Access Control (RBAC):** Pemisahan hak akses secara ketat antara Admin dan User biasa.

## 🛠️ Teknologi yang Digunakan

*   **Bahasa Pemrograman:** PHP (Native)
*   **Database:** MySQL / MariaDB
*   **Frontend Framework:** [Bootstrap 5](https://getbootstrap.com/)
*   **Pop-up & Notifikasi:** [SweetAlert2](https://sweetalert2.github.io/)
*   **Icons:** [FontAwesome 6](https://fontawesome.com/)
*   **Desain UI:** Modern Blue-White Branding dengan desain responsif (Mobile Friendly).

## 📋 Prasyarat Instalasi

Sebelum menjalankan aplikasi ini, pastikan Anda telah menginstal:

*   Web Server (Apache/Nginx)
*   PHP versi 7.4 atau lebih tinggi
*   MySQL Server
*   Web Browser (Chrome/Firefox/Edge)

*(Disarankan menggunakan XAMPP untuk pengguna Windows)*

## ⚙️ Cara Instalasi

1.  **Clone atau Download Project:**
    Download source code ini dan letakkan di dalam folder server Anda (misal: `C:\xampp\htdocs\sarana-prasarana-cba`).

2.  **Persiapan Database:**
    *   Buka **phpMyAdmin**.
    *   Buat database baru dengan nama `saranaprasarana`.
    *   Import file SQL terbaru.
    *   **Catatan:** Jika melakukan update dari versi lama, jalankan script `update_db_foto.php` untuk menambah kolom foto.

3.  **Konfigurasi Koneksi:**
    Buka file `config/koneksi.php` dan sesuaikan pengaturan database Anda.

4.  **Jalankan Aplikasi:**
    Akses melalui browser di URL: `http://localhost/sarana-prasarana-cba`

## 📖 Dokumentasi Penggunaan

### 1. Manajemen Pengguna (Admin)
Admin dapat mengakses menu **"Manajemen User"** di navbar dashboard untuk mendaftarkan akun baru bagi tim maintenance. Password akan otomatis di-hash saat disimpan.

### 2. Pengisian Perawatan & Unggah Foto
*   Klik tombol **"Isi Perawatan"** pada kartu riwayat.
*   Selain mengisi status Ok/Nok, Anda dapat memilih file gambar pada input **"Foto Bukti"**.
*   Setelah disimpan, thumbnail foto akan muncul di tabel riwayat.
*   **Klik pada thumbnail foto** untuk melihat bukti visual dalam ukuran penuh tanpa meninggalkan halaman.

### 3. Pencetakan Laporan
*   Gunakan tombol **"Cetak"** untuk menghasilkan laporan fisik.
*   Foto bukti dan elemen interaktif lainnya otomatis disembunyikan saat cetak agar laporan tetap bersih.

## 📁 Struktur Folder

```text
sarana-prasarana-cba/
├── assets/             # Gambar, CSS, dan logo
├── config/             # Koneksi database
├── uploads/            # Penyimpanan foto bukti pengecekan
├── apar_*.php          # Modul APAR
├── gedung_*.php        # Modul Gedung
├── hydrant_*.php       # Modul Hydrant
├── grease_trap_*.php   # Modul Grease Trap
├── users.php           # Manajemen pengguna (Admin Only)
├── dashboard.php       # Menu utama
├── index.php           # Entry point (Halaman Login)
└── update_db_foto.php  # Script migrasi database untuk fitur foto
```

---

## 📜 Lisensi

Project ini dilisensikan di bawah **MIT License**.

Anda bebas menggunakan, menyalin, memodifikasi, dan mendistribusikan project ini selama mencantumkan nama pemilik hak cipta asli.

Lihat file [LICENSE](LICENSE) untuk detail lengkap.

---

## 👨‍💻 Hak Cipta

**© 2026 Team IT Pabrik CBA — Sistem Sarana Prasarana**

> Seluruh kode sumber, desain antarmuka, dan dokumentasi pada repository ini adalah karya original dari **Team IT Pabrik CBA**. Dilarang keras mengklaim karya ini sebagai milik Anda sendiri tanpa izin tertulis dari pemilik hak cipta.