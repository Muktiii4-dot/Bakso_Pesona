# Bakso Pesona — Catatan Perubahan Checkout

## Perubahan yang sudah diterapkan

- Ongkir checkout otomatis dihitung oleh Laravel berdasarkan provinsi yang divalidasi dari daftar provinsi Indonesia di `config/shipping.php`.
- Tarif: Rp3.000 untuk Banten, DKI Jakarta, Jawa Barat, Jawa Tengah, DI Yogyakarta, dan Jawa Timur; Rp5.000 untuk provinsi valid lainnya.
- Form checkout memiliki field terstruktur provinsi, kota/kabupaten, alamat lengkap, dan kode pos 5 digit.
- Nilai ongkir dan total final disimpan pada pesanan ketika checkout dibuat. Harga produk dan jumlah tetap dihitung ulang di backend dengan penguncian produk dan transaksi database yang sudah ada.
- Halaman checkout menampilkan perkiraan ongkir/total; angka final tetap dihitung server.
- Halaman keberhasilan dan detail pesanan menampilkan ongkir/total dan alamat terstruktur.
- Form admin untuk menetapkan ongkir manual hanya ditampilkan bagi pesanan lama yang ongkirnya masih NULL.
- Migration tambahan hanya menambahkan kolom alamat; tidak menghapus atau menimpa data historis.

## Batasan pembayaran yang disengaja

Proyek ini masih menggunakan QRIS statis dan belum diketahui bank/aplikasi merchant penerbitnya maupun apakah akun menyediakan API. Karena itu, integrasi QRIS dinamis, webhook, polling status, dan konfirmasi otomatis belum diaktifkan. Sistem mempertahankan instruksi QRIS manual dari admin dan tidak menandai pembayaran lunas hanya dari sisi browser. Jangan menganggap paket ini sudah memenuhi pembayaran otomatis end-to-end.

Untuk mengaktifkan pembayaran otomatis, pemilik perlu mengidentifikasi penyedia QRIS dan memastikan akun merchant memiliki API QRIS dinamis, endpoint pemeriksaan transaksi, mekanisme webhook resmi, serta kredensial sandbox/produksi. Implementasi provider-specific harus dilakukan setelah dokumentasi tersebut teridentifikasi.

## Cara menerapkan

1. Buat backup database dan file proyek.
2. Pastikan `.env` lokal sudah tersedia dan berisi konfigurasi lingkungan milik sendiri.
3. Jalankan `php artisan migrate --pretend` untuk meninjau migration baru.
4. Jika SQL sesuai dan backup sudah tersedia, jalankan `php artisan migrate`.
5. Jalankan `php artisan config:clear` dan `php artisan view:clear`.
6. Uji checkout dengan provinsi Jawa dan luar Jawa; cocokkan subtotal, ongkir, dan total pada database.
7. Uji pembayaran manual yang ada dan alur admin sebelum dipakai pelanggan.

Migration yang ditambahkan: `2026_10_25_130000_add_structured_shipping_address_to_orders.php`.

## Status pengujian

PHP syntax checks untuk controller, model, konfigurasi, migration, test file, dan view yang disentuh berhasil. PHPUnit belum bisa dijalankan pada lingkungan penyuntingan ini karena ekstensi PHP `dom`, `mbstring`, dan `xmlwriter` tidak tersedia. Karena itu, test suite belum dinyatakan lulus.
