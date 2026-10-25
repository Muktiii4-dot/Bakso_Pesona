-- BAKSO PESONA: contoh CRUD, TRANSACTION, COMMIT, ROLLBACK, FUNCTION,
-- STORED PROCEDURE, dan TRIGGER untuk MySQL 8 / MariaDB.
-- Jalankan hanya pada database development/backup terlebih dahulu.
-- Migration Laravel membuat fn_product_stock_value, sp_adjust_product_stock,
-- trg_products_audit_insert/update/delete, dan tabel product_audit_logs.

-- =============================== CRUD =====================================
-- CREATE (contoh kategori; slug harus unik)
-- INSERT INTO categories (name, slug, description, created_at, updated_at)
-- VALUES ('Bakso Favorit', 'bakso-favorit', 'Kategori contoh', NOW(), NOW());

-- READ
-- SELECT id, name, slug, description FROM categories ORDER BY name;
-- SELECT id, name, price, stock, is_available FROM products ORDER BY name;

-- UPDATE (ganti ID sesuai data development yang memang ingin diubah)
-- UPDATE products SET price = 27000, updated_at = NOW() WHERE id = 1;

-- DELETE: hati-hati, produk yang sudah direferensikan pesanan dapat ditolak FK.
-- DELETE FROM categories WHERE id = 999;

-- ========================== FUNCTION ======================================
-- Menghitung nilai stok (harga x stok) untuk satu produk.
-- SELECT fn_product_stock_value(1) AS nilai_stok;
-- Fungsi yang sudah ada dari migration terdahulu:
-- SELECT fn_calculate_order_subtotal(1) AS subtotal_pesanan;
-- SELECT fn_get_customer_total_spent(1) AS total_belanja_lunas;

-- ======================== STORED PROCEDURE ================================
-- Penyesuaian stok positif untuk menambah, negatif untuk mengurangi.
-- CALL sp_adjust_product_stock(1, 5, @code, @message);
-- SELECT @code AS result_code, @message AS result_message;
-- Contoh kegagalan validasi: pengurangan yang membuat stok negatif akan ROLLBACK.
-- CALL sp_adjust_product_stock(1, -999999, @code, @message);
-- SELECT @code AS result_code, @message AS result_message;

-- ======================= TRIGGER / AUDIT LOG ===============================
-- INSERT/UPDATE/DELETE pada products otomatis mencatat product_audit_logs
-- pada MySQL/MariaDB. Tidak ada trigger di sini yang mengubah stok pesanan,
-- sehingga tidak menggandakan mekanisme reservasi stok milik aplikasi.
-- SELECT * FROM product_audit_logs ORDER BY created_at DESC LIMIT 50;

-- ======================= COMMIT / ROLLBACK ================================
-- Jalankan blok ini satu per satu di database development.
-- COMMIT menyimpan perubahan:
-- START TRANSACTION;
-- UPDATE products SET price = price + 1000 WHERE id = 1;
-- COMMIT;

-- ROLLBACK membatalkan perubahan yang belum di-COMMIT:
-- START TRANSACTION;
-- UPDATE products SET price = price + 999999 WHERE id = 1;
-- SELECT id, name, price FROM products WHERE id = 1; -- lihat perubahan sementara
-- ROLLBACK;
-- SELECT id, name, price FROM products WHERE id = 1; -- perubahan dibatalkan
