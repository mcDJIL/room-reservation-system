# room-reservation-system — Sistem Reservasi Ruangan

Deskripsi singkat:
Sebuah aplikasi web sederhana untuk memesan kamar/ruangan. Dibangun dengan PHP dan MySQL; siap dijalankan di lingkungan lokal seperti XAMPP.

**Persyaratan**
- PHP (7.x atau 8.x)
- MySQL atau MariaDB
- XAMPP / Apache + MySQL (direkomendasikan untuk pengembangan lokal)

**Langkah penggunaan (dari clone sampai berjalan)**

1. Clone repo

```bash
git clone https://github.com/mcDJIL/room-reservation-system.git
cd room-reservation-system
```

2. Letakkan folder di webroot

Jika menggunakan XAMPP, pindahkan atau clone repositori ke `C:/xampp/htdocs/` sehingga pathnya menjadi `C:/xampp/htdocs/room-reservation-system`.

3. Jalankan server lokal

Buka XAMPP Control Panel, lalu start **Apache** dan **MySQL**.

4. Buat database dan import skema

- Buka `http://localhost/phpmyadmin` atau gunakan MySQL CLI.
- Buat database baru (misal `room-reservation-system_db`).
- Import file skema SQL yang ada di repository:

```
database/schema.sql
```

Di phpMyAdmin: pilih database → Import → pilih `database/schema.sql` → Go.

5. Konfigurasi koneksi database

Edit file konfigurasi database di `config/database.php` dan sesuaikan nama database, username, dan password MySQL:

```php
// contoh: config/database.php
'DB_HOST' => 'localhost',
'DB_NAME' => 'room-reservation-system_db',
'DB_USER' => 'root',
'DB_PASS' => '',
```

6. Akses aplikasi

Buka browser dan kunjungi:

```
http://localhost/room-reservation-system/
```

7. Hal-hal lain yang berguna
- Untuk halaman login/register: buka `pages/auth/login.php` dan `pages/auth/register.php`.
- Halaman admin ada di `pages/admin/` (akses setelah login sebagai admin).
- Halaman user ada di `pages/user/`.

**Struktur penting**
- `config/` — konfigurasi koneksi database
- `database/schema.sql` — file skema database (struktur tabel + data awal jika ada)
- `pages/` — tampilan untuk `admin`, `auth`, dan `user`
- `actions/` — script aksi (login, booking, room)

**Troubleshooting singkat**
- Jika muncul error koneksi database, periksa `config/database.php` dan pastikan MySQL berjalan.
- Jika page not found, pastikan folder berada di `htdocs` dan URL sesuai nama folder.

Butuh bantuan lebih lanjut? Katakan bagian mana yang ingin kamu perjelas (mis. seed data, akun admin default, atau cara deploy ke server).
