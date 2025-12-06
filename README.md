# LOUNGE GAME ROOM

## Deskripsi Aplikasi

**Lounge Game Room** adalah aplikasi berbasis Laravel untuk mengelola penyewaan ruang bermain (gameboard maupun digital). Fungsionalitas utama meliputi:

-   Registrasi dan autentikasi pengguna (pelanggan).

-   Pencarian dan daftar game room (gameboard & digital).

-   Form reservasi (pemesanan waktu & durasi).

-   Dashboard admin untuk melihat dan mengelola reservasi, rooms, dan receipt.

-   Pembuatan/print invoice / receipt (menggunakan DOMPDF).

-   Fitur unggah gambar, dan manajemen data via panel admin.

---

## Teknologi & Dependensi Utama

1. Backend: PHP 8.2, Laravel 12

2. Frontend: Vite, Tailwind CSS, axios

3. PDF: barryvdh/laravel-dompdf

4. Dev tooling: node & npm (Vite)

5. Database: MySQL / MariaDB (atau database lain yang didukung Laravel)

6. Lain-lain: Composer untuk dependency PHP

---

## Struktur Project

```
Lounge-Game-Room/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── Controller.php
│   │   │   ├── DashboardController.php
│   │   │   ├── GameRoomController.php
│   │   │   ├── ReceiptController.php
│   │   │   └── ReservationController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── GameRoom.php
│   │   ├── Reservation.php
│   │   └── User.php
│   └── Providers/
│
├── bootstrap/
├── config/
│
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_01_01_0000001_create_cache_table.php
│   │   ├── 0001_01_01_0000002_create_jobs_table.php
│   │   ├── 2025_11_23_122659_create_game_rooms_table.php
│   │   ├── 2025_11_23_122700_create_users_table.php
│   │   ├── 2025_11_23_123300_create_reservations_table.php
│   │   ├── 2025_11_23_124443_create_sessions_table.php
│   │   └── 2025_11_24_012838_add_payment_to_reservations_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   └── database.sqlite
│
├── public/
│
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── auth/
│       ├── dashboard/
│       ├── game-rooms/
│       ├── layouts/
│       ├── receipts/
│       ├── reservations/
│       └── welcome.blade.php
│
├── routes/
│   ├── web.php
│
├── storage/
├── tests/
├── vendor/
│
├── .editorconfig
├── .env
├── .env.example
├── .gitattributes
├── .gitignore
│
├── artisan
├── composer.json
├── composer.lock
├── package.json
├── package-lock.json
├── phpunit.xml
├── README.md
└── vite.config.js
```

---

**Penjelasan Per Folder**

#### app/Http/Controllers/

Berisi seluruh controller aplikasi:

-   AuthController – login & register

-   DashboardController – dashboard user (admin, customer, resepsionis)

-   GameRoomController – CRUD room game

-   ReservationController – pemesanan

-   ReceiptController – cetak struk & invoice

#### app/Models/

Model utama aplikasi:

-   User.php

-   GameRoom.php

-   Reservation.php

#### database/migrations/

Kumpulan file migrasi untuk membuat tabel seperti:

-   game_rooms

-   users

-   reservations

-   sessions

-   payments

#### database/seeders/

Berisi seeder untuk membuat data awal otomatis (role & user default).

#### resources/views/

Folder Blade Template:

-   auth/ – halaman login, register

-   dashboard/ – dashboard semua role

-   game-rooms/ – halaman room

-   reservations/ – form & daftar reservasi

-   receipts/ – invoice & struk

-   layouts/ – template umum

-   welcome.blade.php – landing page

#### routes/web.php

Routing utama aplikasi:

-   customer route

-   admin route

-   receptionist route

---

## Instalasi & Cara Menjalankan Aplikasi

1. Clone Repository

```
git clone https://github.com/diestymendila/Lounge-Game-Room.git
```

```
cd Lounge-Game-Room
```

2. Copy .env Template

```
cp .env.example .env
```

_Windows:_

```
copy .env.example .env
```

3. Isi konfigurasi database:

```
DB_DATABASE=lounge_game_room
DB_USERNAME=root
DB_PASSWORD=
```

4. Install dependensi

-   Composer (backend)

```
composer install
```

-   Node Module (frontend)

```
npm install
```

5. Generate key

```
php artisan key:generate
```

6. Jalankan migrasi & seeder

Seeder otomatis membuat user Admin, Resepsionis, dan Customer.

```
php artisan migrate --seed
```

Jika ada duplicate, gunakan:

```
php artisan migrate:fresh --seed
```

7. Buat Storage Link

```
php artisan storage:link
```

8. Jalankan Server

```
php artisan serve
```

9. Akses aplikasi pada:

```
http://127.0.0.1:8000
```

---

## Instalasi Dependensi

1. Instalasi Dependensi Untuk Cetak Resi PDF

-   Install package melalui Composer.
    Jalankan perintah berikut:

```
composer require barryvdh/laravel-dompdf
```

-   Cara Menggunakan Dependensi

**Contoh Generate PDF pada Controller (Seperti pada ReceiptController.php)**

```php
use PDF;

public function generateReceipt($id)
{
    $reservation = Reservation::findOrFail($id);

    $pdf = PDF::loadView('receipts.invoice', compact('reservation'));

    return $pdf->stream('invoice.pdf');
}
```

-   Cara membuat file Blade PDF

_Pastikan file view Anda misalnya berada di:_

```
resources/views/receipts/invoice.blade.php
```

_Struktur HTML-nya sederhana & mendukung CSS inline._

---

2. Instalasi Dependensi Untuk Notifikasi Whatsapp

**Cara Menggunakan Fonnte (WhatsApp Notification API)**

-   Registrasi & Mendapatkan API Token

```
Daftar di https://fonnte.com
```

-   Masuk ke dashboard

-   Buka menu API Token

-   Hubungkan perangkat whatsapp anda dengan akun fonnte.com yang telah dibuat

-   Copy token, _misalnya:_

```
abc123-def456-gh789
```

-   Masukkan token ke file .env:

```.env
FONNTE_TOKEN=abc123-def456-gh789
```

**Cara Mengirim Pesan WhatsApp via Fonnte API**

-   Contoh implementasi di Laravel:

```php
function sendWhatsappMessage($target, $message)
{
    $token = env('FONNTE_TOKEN');

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: $token"
        ],
        CURLOPT_POSTFIELDS => [
            'target' => $target,
            'message' => $message
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}
```

**Contoh Pemanggilan Fungsi**

```php
sendWhatsappMessage(
    $reservation->phone,
    "Reservasi Anda berhasil!\n".
    "Ruangan: {$reservation->room->name}\n".
    "Mulai: {$reservation->start_time}\n".
    "Selesai: {$reservation->end_time}\n".
    "Total: Rp {$reservation->total_price}"
);
```

---

## Akun Default (Dari Seeder)

-   Admin : admin@test.com - password
-   Resepsionis : receptionist@test.com - password
-   Customer : customer@test.com - password

---

## Panduan Penggunaan Aplikasi

**Customer (Pengguna Biasa)**

Langkah - langkah :

1. Buka halaman utama → klik Register / Login

2. Masuk ke dashboard

3. Pilih Menu “Game Rooms”

4. Pilih Ruangan → klik “Book Now”

5. Isi form booking:

    - tanggal booking

    - jam mulai

    - durasi

    - Tekan Submit Booking

    - Pada halaman konfirmasi, klik:

    - Download Invoice

    - atau lihat detail reservasi

6. Selesai — Anda dapat melihat status booking di Riwayat Reservasi

---

**Admin**

Langkah - langkah :

1. Login dengan akun Admin.

2. Masuk ke Admin Dashboard.

3. Menu yang tersedia:

    - Manage Rooms

    - Manage Reservations

    - Manage Users

    - Generate Reports

4. Untuk tambah room:

    - Admin → Game Rooms → Add New Room

    - Isi data lengkap + upload gambar.

5. Untuk melihat semua reservasi:

    - Admin → Reservations

6. Untuk mencetak invoice:

    - Reservations → pilih reservasi → Print Invoice

7. Untuk mengelola user:

    - Users → Tambah user atau ubah role

---

**Resepsionis**
Langkah - langkah :

1. Login sebagai resepsionis.

2. Dashboard akan menampilkan:

    - Daftar booking hari ini

    - Status ruangan (Occupied / Free)

3. Untuk cek-in pelanggan:

    - Receptionist → Reservations

    - Pilih “Check-in”

4. Untuk cek-out:

    - Pilih reservasi yang sedang berlangsung

    - Klik “Check-out”

5. Untuk pelanggan datang tanpa akun:

    - Receptionist → Create Reservation

    - Isi data pelanggan + room + durasi

6. Untuk mencetak struk:

    - Klik “Print Receipt”
