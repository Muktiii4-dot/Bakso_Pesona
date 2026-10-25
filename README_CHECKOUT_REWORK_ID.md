# Bakso Pesona — Checkout dengan Ongkir Otomatis

Paket ini memperbarui checkout agar ongkir berdasarkan provinsi dihitung di backend dan total final disimpan pada pesanan. Detail perubahan dan batasan integrasi pembayaran ada di `CHECKOUT_REWORK_NOTES.md`.

## Menyiapkan proyek

1. Ekstrak ZIP ke folder kerja.
2. Salin `.env.example` menjadi `.env` dan isi konfigurasi database lokal sendiri. ZIP ini sengaja tidak menyertakan `.env` asli.
3. Jalankan `composer install` bila dependency belum tersedia.
4. Jalankan `php artisan key:generate` hanya untuk instalasi lokal baru, bukan untuk mengganti APP_KEY aplikasi produksi yang sudah berjalan.
5. Backup database terlebih dahulu.
6. Tinjau migration dengan `php artisan migrate --pretend`, kemudian setelah disetujui jalankan `php artisan migrate`.
7. Jalankan `php artisan config:clear` dan `php artisan view:clear`.

**Pembayaran otomatis belum diaktifkan.** Proyek saat ini memiliki QRIS statis; provider/API merchant belum diketahui. Pembayaran tetap harus diverifikasi sesuai alur manual yang sudah ada. Lihat `CHECKOUT_REWORK_NOTES.md` sebelum deploy.
