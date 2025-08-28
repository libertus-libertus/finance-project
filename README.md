# Mini Proyek Finansial - Tes Kemampuan Teknis

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

Proyek ini adalah aplikasi web sederhana untuk akuntansi yang dibangun sebagai bagian dari tes kemampuan teknis. Aplikasi ini mencakup modul-modul dasar seperti Chart of Accounts (COA), Jurnal, Invoice, Pembayaran, dan Laporan Trial Balance.

## 🚀 Fitur Utama

- **Manajemen COA (Chart of Accounts)**: CRUD (Create, Read, Update, Delete) untuk daftar akun.
- **Manajemen Jurnal**: Membuat, melihat detail, dan menghapus entri jurnal dengan baris transaksi dinamis.
- **Manajemen Invoice & Pembayaran**: Melihat daftar invoice dan riwayat pembayarannya.
- **Laporan Trial Balance**: Menampilkan laporan neraca saldo yang dapat difilter berdasarkan periode dan diekspor ke format PDF.
- **Operasi Berbasis AJAX**: Semua interaksi data dilakukan secara *real-time* tanpa *full-page reload* untuk pengalaman pengguna yang lebih baik.
- **REST API**: Komunikasi antara frontend dan backend sepenuhnya menggunakan REST API menggunakan tools Postman.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel, PHP
- **Frontend**: HTML, Bootstrap 3, JavaScript, jQuery & Ajax
- **Database**: MySQL
- **Lainnya**: Composer, Datatables, SweetAlert2, DomPDF
- **Postman**: Untuk uji coba REST API datanya

---

## 📋 Prasyarat

Sebelum memulai, pastikan Anda sudah menginstal perangkat lunak sebagai berikut:
- PHP (versi 8.1 atau lebih baru direkomendasikan)
- Composer
- Server Database yang di gunakan (XAMPP: MySQL)
- Git (opsional, untuk kloning)
- Visual Studio Code -> Text Editor
- Postman -> Uji REST API setiap data

---

## ⚙️ Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lokal.

**1. Dapatkan Source Code**

   Jika proyek ini dalam bentuk file `.zip`, ekstrak file tersebut terlebih dahulu. Jika melalui Git, kloning repositori:
   ```bash
   git clone https://github.com/libertus-libertus/finance-project.git
   cd nama-folder-proyek
   ```

**2. Instal Dependensi PHP**

   Jalankan perintah berikut untuk menginstal semua *package* yang dibutuhkan oleh Laravel:
   ```bash
   composer install
   ```

**3. Konfigurasi Lingkungan environtment variabelnya di file (.env)**

   Salin file `.env.example` kemudian dirubah menjadi file `.env` yang baru:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi database:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=finance
   DB_USERNAME=root              
   DB_PASSWORD=                  
   ```
   **Penting**: Pastikan Anda sudah membuat database kosong (misalnya `finance`) sebelum melanjutkan.

**4. Generate Key Aplikasinya**

   Setiap aplikasi Laravel membutuhkan key untuk enkripsi unik. Jalankan perintah ini untuk membuatnya:
   ```bash
   php artisan key:generate
   ```

**5. Migrasi dan Seeding Database**

   Perintah ini akan membuat semua struktur tabel dan mengisinya dengan data *dummy* yang sudah disiapkan, sehingga aplikasi siap untuk digunakan.
   ```bash
   php artisan migrate:fresh --seed
   ```

**6. Jalankan Server Pengembangan**

   Terakhir, jalankan server pengembangan bawaan Laravel:
   ```bash
   php artisan serve
   ```
   Aplikasi sekarang akan berjalan dan dapat diakses di **http://127.0.0.1:8000**.

---

## 💡 Cara Penggunaan

Setelah aplikasi berjalan, Anda dapat langsung menavigasi melalui menu yang tersedia:
- **Chart of Accounts**: Kelola daftar akun, termasuk menambah, mengubah, dan menghapus.
- **Journals**: Lihat daftar entri jurnal, lihat detailnya, hapus, atau buat entri jurnal baru dengan transaksi yang seimbang.
- **Invoices**: Lihat daftar invoice beserta statusnya dan lihat detail riwayat pembayarannya.
- **Payments**: Lihat daftar semua pembayaran yang telah dilakukan.
- **Trial Balance**: Lihat laporan neraca saldo, filter berdasarkan periode, dan unduh laporannya dalam format PDF.

Terima kasih telah memberikan kesempatan ini.