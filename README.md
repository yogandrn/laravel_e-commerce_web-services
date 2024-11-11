<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# E-Commerce Web Service with Laravel

Proyek ini adalah layanan web e-commerce yang dibangun menggunakan Laravel. Dikembangkan sebagai bagian dari portofolio saya sebagai Backend Engineer. Proyek ini menyediakan RESTful API untuk frontend dan dilengkapi dasbor admin untuk manajemen data. Selain itu, proyek ini juga terintegrasi dengan library pihak ketiga seperti Midtrans Payment Gateway dan RajaOngkir API.

## Fitur Utama
- **Admin Dashboard**: Menggunakan **Filament** sebagai UI framework untuk dasbor admin, yang memungkinkan pengelolaan produk, kategori, pesanan, dan pengguna.
- **REST API untuk Frontend**: API RESTful yang mendukung autentikasi berbasis **JWT OAuth 2.0** untuk pengelolaan akun, produk, keranjang, pesanan, dan fungsi e-commerce lainnya.
- **Integrasi Midtrans Payment Gateway**: Menggunakan Midtrans untuk memproses pembayaran dengan aman dan mendukung berbagai metode pembayaran.
- **API RajaOngkir**: Menghitung ongkos kirim berdasarkan tujuan pengiriman menggunakan integrasi dengan API RajaOngkir.

## Teknologi yang Digunakan
- **Laravel**: Framework PHP untuk aplikasi web yang kuat dan fleksibel.
- **Filament**: Library modern untuk membangun dasbor admin yang mudah digunakan.
- **JWT (JSON Web Token)**: Protokol autentikasi yang aman untuk melindungi akses data API.
- **MySQL**: Basis data relasional yang dioptimalkan untuk aplikasi e-commerce.

## Instalasi
1. Clone repositori ini dengan perintah CLI atau mengunduh file `.zip`
2. Buka folder proyek, kemudian copy file `.env.example` ke file `.env`.
3. Pastikan key konfigurasi pada `.env` memiliki nilai, seperti kofigurasi `DB` dan `secret key`.
4. Jalankan perintah berikut untuk menginstall package yang diperlukan
   ```bash
   composer install

8. Jalankan migrasi database
    ```bash
   php artisan migrate

10. Untuk menjalankan sever, jalankan perintah
    ```bash
    php artisan serve



