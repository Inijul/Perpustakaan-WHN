<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Perpustakaan WHN - Frontend

## Integrasi Modal Detail dengan Data Terbaru

Sistem ini telah diintegrasikan untuk memastikan modal detail menampilkan data terbaru setelah melakukan edit koleksi. Berikut adalah penjelasan cara kerjanya:

### 1. Event System

Sistem menggunakan custom events untuk komunikasi antar komponen:

- `show-detail-koleksi`: Menampilkan modal detail dengan data koleksi
- `show-edit-koleksi`: Menampilkan modal edit dengan data koleksi
- `koleksi-updated`: Dipanggil setelah edit berhasil untuk update data
- `refresh-koleksi-data`: Meminta refresh data dari server

### 2. Flow Integrasi

1. **User membuka detail koleksi**
   - Event `show-detail-koleksi` dipanggil dengan data koleksi
   - Modal detail menampilkan data saat ini

2. **User melakukan edit**
   - Event `show-edit-koleksi` dipanggil dengan data koleksi
   - Modal edit menampilkan form dengan data yang ada

3. **User menyimpan perubahan**
   - Data dikirim ke server via AJAX
   - Jika berhasil, event `koleksi-updated` dipanggil dengan data terbaru

4. **Update otomatis**
   - Modal detail mendengarkan event `koleksi-updated`
   - Data di modal detail diperbarui secara real-time
   - Tabel juga diperbarui dengan data terbaru

### 3. Komponen yang Terlibat

#### Modal Detail (`modal-detail.blade.php`)
- Mendengarkan event `koleksi-updated` untuk update data
- Mendengarkan event `refresh-koleksi-data` untuk refresh dari server
- Memiliki fungsi helper untuk format data

#### Modal Edit (`modal-edit.blade.php`)
- Mengirim event `koleksi-updated` setelah edit berhasil
- Menangani form submission via AJAX
- Menampilkan pesan sukses/error

#### KoleksiController
- Method `show()` untuk API endpoint `/api/koleksi/{kode}`
- Method `update()` untuk handle edit koleksi
- Logging untuk debugging

#### JavaScript (app.js)
- Event listener untuk `koleksi-updated`
- Event listener untuk `refresh-koleksi-data`
- Fetch data dari server jika diperlukan

### 4. Fitur Utama

- **Real-time Update**: Data di modal detail terupdate otomatis setelah edit
- **Server Sync**: Data bisa di-refresh dari server jika diperlukan
- **Error Handling**: Pesan error yang informatif
- **Logging**: Log untuk debugging dan monitoring
- **Responsive**: Modal responsive untuk berbagai ukuran layar

### 5. Cara Penggunaan

1. Klik tombol detail (ikon mata) pada tabel koleksi
2. Modal detail akan terbuka dengan data koleksi
3. Klik tombol "Edit Koleksi" di modal detail
4. Modal edit akan terbuka dengan data yang sama
5. Lakukan perubahan dan klik "Update Koleksi"
6. Modal detail akan otomatis terupdate dengan data terbaru
7. Tabel juga akan terupdate dengan data terbaru

### 6. Troubleshooting

Jika data tidak terupdate:
1. Periksa console browser untuk error
2. Periksa log Laravel di `storage/logs/laravel.log`
3. Pastikan route API `/api/koleksi/{kode}` berfungsi
4. Periksa koneksi ke backend Node.js

### 7. File yang Dimodifikasi

- `resources/views/components/modal-detail.blade.php`
- `resources/views/components/modal-edit.blade.php`
- `resources/views/koleksi.blade.php`
- `app/Http/Controllers/KoleksiController.php`
- `routes/api.php`
- `routes/web.php`
- `resources/js/app.js`

Sistem ini memastikan bahwa data yang ditampilkan di modal detail selalu akurat dan terbaru setelah melakukan edit koleksi.
