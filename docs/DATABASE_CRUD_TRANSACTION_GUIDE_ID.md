# Panduan CRUD dan transaksi database — Bakso Pesona

## Fitur CRUD di aplikasi

- **Produk:** tambah, lihat/daftar, edit, hapus melalui panel admin.
- **Kategori:** tambah, lihat/daftar, edit, hapus melalui panel admin.
- **Pesanan:** daftar/detail, pembaruan status, konfirmasi ongkir/pembayaran, pembatalan dan refund mengikuti alur pesanan yang tersedia.
- **Pelanggan:** daftar dan pencarian; akun tidak dihapus sembarangan agar relasi pesanan/audit tetap utuh.

CRUD yang dijalankan melalui Laravel menggunakan validasi controller, model, autentikasi admin, dan foreign key database. Jangan melakukan operasi destructive langsung pada database produksi.

## Fungsi database

| Nama | Tujuan |
|---|---|
| `fn_calculate_order_subtotal(order_id)` | Menjumlah subtotal item pesanan. |
| `fn_get_customer_total_spent(user_id)` | Menjumlah pesanan pelanggan dengan status pembayaran `paid`. |
| `fn_product_stock_value(product_id)` | Menghitung `harga × stok` produk. |

## Stored procedure dan transaksi

`sp_adjust_product_stock(product_id, delta, OUT code, OUT message)` menambah/mengurangi stok. Nilai `delta` positif menambah stok; nilai negatif mengurangi. Prosedur menolak stok negatif dan menggunakan `START TRANSACTION`, `COMMIT`, serta `ROLLBACK` pada validasi gagal atau exception.

## Trigger audit

Pada MySQL/MariaDB, `trg_products_audit_insert`, `trg_products_audit_update`, dan `trg_products_audit_delete` menyimpan snapshot perubahan produk ke `product_audit_logs`. Trigger ini **tidak** mengubah stok pesanan, supaya tidak bertabrakan dengan sistem reservasi stok Laravel. SQLite tidak mendukung objek routine MySQL yang sama; migration tetap membuat tabel audit, sedangkan fungsi/prosedur/trigger database khusus hanya dipasang di MySQL/MariaDB.

## COMMIT dan ROLLBACK

- `COMMIT` menyimpan perubahan dari transaksi aktif.
- `ROLLBACK` membatalkan perubahan yang belum di-commit pada transaksi aktif.
- Setelah `COMMIT`, `ROLLBACK` tidak bisa mengembalikan perubahan tersebut; gunakan transaksi baru atau backup untuk pemulihan.
- Stored procedures menjalankan transaksi sendiri. Jangan memanggilnya di tengah transaksi aplikasi yang belum selesai karena transaksi MySQL dapat memengaruhi transaksi pemanggil.

## Instalasi

1. Backup database terlebih dahulu.
2. Pastikan `.env` menunjuk ke database yang benar, sebaiknya database development saat uji coba.
3. Jalankan `php artisan migrate`.
4. Di MySQL/MariaDB, periksa routine dan trigger melalui phpMyAdmin → database → Routines/Triggers, dan periksa audit dengan `SELECT * FROM product_audit_logs ORDER BY created_at DESC;`.
5. Contoh CRUD dan pemakaian function/procedure serta transaksi ada di `database/sql/bakso_pesona_crud_transactions.sql`.

## Rollback migration

Migration baru memiliki `down()` yang menghapus trigger/function/procedure yang ditambahkannya dan tabel `product_audit_logs`. Untuk membatalkan hanya migration terakhir, pastikan migration terakhir memang migration ini, lalu jalankan `php artisan migrate:rollback --step=1`. **Jangan jalankan rollback di produksi tanpa backup**: migration rollback dapat menghapus tabel audit dan riwayat yang tercatat di dalamnya.
