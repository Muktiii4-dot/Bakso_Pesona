-- =============================================================================
-- LEGACY ONLY - DO NOT RUN AFTER THE ORDER WORKFLOW CUTOVER.
-- This script drops/recreates named routines and installs legacy order stock
-- triggers. Running it after the Laravel reservation workflow is deployed can
-- double-adjust stock. Keep it only for explicitly approved legacy setups.
-- =============================================================================
-- DATABASE OBJECTS BAKSO PESONA (MYSQL / MARIADB)
-- Memenuhi Standar Kompetensi Database:
-- 1. Stored Function
-- 2. Stored Procedure (dengan Transaction, COMMIT & ROLLBACK)
-- 3. Database Trigger (Pengurangan Stok, Pemulihan Stok, dan Audit Log)
-- =============================================================================
-- LEGACY INSTALL SCRIPT: this script drops/recreates routines and installs the
-- legacy order stock triggers. Do not run it after the order workflow migration
-- is deployed; it would re-enable stock changes that conflict with the Laravel
-- reservation workflow. Use only for an explicitly approved legacy environment.
-- =============================================================================

USE `bakso_pesona`;

-- -----------------------------------------------------------------------------
-- 1. STORED FUNCTION: fn_calculate_order_subtotal
-- Fungsi: Menghitung total subtotal dari rincian item pesanan (order_items)
-- -----------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `fn_calculate_order_subtotal`;

DELIMITER $$
CREATE FUNCTION `fn_calculate_order_subtotal`(p_order_id BIGINT)
RETURNS DECIMAL(12, 2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_subtotal DECIMAL(12, 2) DEFAULT 0.00;
    
    SELECT COALESCE(SUM(subtotal), 0.00) INTO v_subtotal
    FROM order_items
    WHERE order_id = p_order_id;
    
    RETURN v_subtotal;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- 2. STORED FUNCTION: fn_get_customer_total_spent
-- Fungsi: Menghitung total belanja akumulatif pelanggan dari pesanan lunas
-- -----------------------------------------------------------------------------
DROP FUNCTION IF EXISTS `fn_get_customer_total_spent`;

DELIMITER $$
CREATE FUNCTION `fn_get_customer_total_spent`(p_user_id BIGINT)
RETURNS DECIMAL(12, 2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(12, 2) DEFAULT 0.00;
    
    SELECT COALESCE(SUM(total_amount), 0.00) INTO v_total
    FROM orders
    WHERE user_id = p_user_id AND payment_status = 'paid';
    
    RETURN v_total;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- 3. STORED PROCEDURE: sp_confirm_order_shipping
-- Prosedur: Konfirmasi ongkir dan total pesanan oleh admin
-- Fitur: START TRANSACTION, COMMIT, dan ROLLBACK saat SQLEXCEPTION
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_confirm_order_shipping`;

DELIMITER $$
CREATE PROCEDURE `sp_confirm_order_shipping`(
    IN p_order_id BIGINT,
    IN p_shipping_cost DECIMAL(12, 2),
    OUT p_result_code INT,
    OUT p_result_message VARCHAR(255)
)
proc_label: BEGIN
    DECLARE v_subtotal DECIMAL(12, 2);
    DECLARE v_total_amount DECIMAL(12, 2);
    DECLARE v_current_status VARCHAR(50);

    -- ROLLBACK HANDLER: Membatalkan transaksi jika terjadi error SQL
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_code = 500;
        SET p_result_message = 'Terjadi kesalahan sistem database saat konfirmasi ongkir. Transaksi berhasil di-rollback.';
    END;

    -- Validasi keberadaan pesanan
    SELECT subtotal, status INTO v_subtotal, v_current_status
    FROM orders
    WHERE id = p_order_id;

    IF v_subtotal IS NULL THEN
        SET p_result_code = 404;
        SET p_result_message = 'Pesanan tidak ditemukan.';
        LEAVE proc_label;
    END IF;

    -- Hitung total akhir
    SET v_total_amount = v_subtotal + p_shipping_cost;

    -- MEMULAI TRANSAKSI
    START TRANSACTION;

    UPDATE orders
    SET shipping_cost = p_shipping_cost,
        total_amount = v_total_amount,
        status = 'awaiting_payment',
        payment_status = 'awaiting_payment',
        updated_at = NOW()
    WHERE id = p_order_id;

    -- COMMIT TRANSAKSI
    COMMIT;

    SET p_result_code = 200;
    SET p_result_message = 'Ongkos kirim dan total pesanan berhasil dikonfirmasi (COMMIT).';
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- 4. STORED PROCEDURE: sp_cancel_order
-- Prosedur: Membatalkan pesanan pelanggan oleh admin
-- Fitur: START TRANSACTION, COMMIT, dan ROLLBACK saat SQLEXCEPTION
-- -----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_cancel_order`;

DELIMITER $$
CREATE PROCEDURE `sp_cancel_order`(
    IN p_order_id BIGINT,
    IN p_reason VARCHAR(255),
    OUT p_result_code INT,
    OUT p_result_message VARCHAR(255)
)
proc_label: BEGIN
    DECLARE v_current_status VARCHAR(50);
    DECLARE v_current_payment VARCHAR(50);

    -- ROLLBACK HANDLER
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result_code = 500;
        SET p_result_message = 'Gagal membatalkan pesanan. Transaksi berhasil di-rollback.';
    END;

    SELECT status, payment_status INTO v_current_status, v_current_payment
    FROM orders
    WHERE id = p_order_id;

    IF v_current_status IS NULL THEN
        SET p_result_code = 404;
        SET p_result_message = 'Pesanan tidak ditemukan.';
        LEAVE proc_label;
    END IF;

    -- MEMULAI TRANSAKSI
    START TRANSACTION;

    UPDATE orders
    SET status = 'cancelled',
        payment_status = CASE 
            WHEN payment_status IN ('paid', 'refunded') THEN payment_status 
            ELSE 'unpaid' 
        END,
        customer_note = CONCAT(COALESCE(customer_note, ''), ' [Dibatalkan: ', p_reason, ']'),
        updated_at = NOW()
    WHERE id = p_order_id;

    -- COMMIT TRANSAKSI
    COMMIT;

    SET p_result_code = 200;
    SET p_result_message = 'Pesanan berhasil dibatalkan (COMMIT).';
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- 5. TRIGGER 1: trg_reduce_product_stock_on_order_item
-- Trigger: Mengurangi stok produk saat item pesanan baru dimasukkan
-- Event: AFTER INSERT ON order_items
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_reduce_product_stock_on_order_item`;

DELIMITER $$
CREATE TRIGGER `trg_reduce_product_stock_on_order_item`
AFTER INSERT ON `order_items`
FOR EACH ROW
BEGIN
    IF NEW.product_id IS NOT NULL THEN
        UPDATE products
        SET stock = GREATEST(0, stock - NEW.quantity)
        WHERE id = NEW.product_id;
    END IF;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- 6. TRIGGER 2: trg_restore_product_stock_on_cancel
-- Trigger: Mengembalikan stok produk ketika status pesanan diubah ke 'cancelled'
-- Event: AFTER UPDATE ON orders
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_restore_product_stock_on_cancel`;

DELIMITER $$
CREATE TRIGGER `trg_restore_product_stock_on_cancel`
AFTER UPDATE ON `orders`
FOR EACH ROW
BEGIN
    IF NEW.status = 'cancelled' AND OLD.status != 'cancelled' THEN
        UPDATE products p
        JOIN order_items oi ON oi.product_id = p.id
        SET p.stock = p.stock + oi.quantity
        WHERE oi.order_id = NEW.id;
    END IF;
END$$
DELIMITER ;


-- -----------------------------------------------------------------------------
-- 7. TRIGGER 3: trg_log_order_status_change
-- Trigger: Mencatat riwayat setiap perubahan status pesanan & pembayaran
-- Event: AFTER UPDATE ON orders
-- -----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS `trg_log_order_status_change`;

DELIMITER $$
CREATE TRIGGER `trg_log_order_status_change`
AFTER UPDATE ON `orders`
FOR EACH ROW
BEGIN
    IF (OLD.status != NEW.status) OR (OLD.payment_status != NEW.payment_status) THEN
        INSERT INTO order_status_logs (
            order_id,
            old_status,
            new_status,
            old_payment_status,
            new_payment_status,
            note,
            created_at
        ) VALUES (
            NEW.id,
            OLD.status,
            NEW.status,
            OLD.payment_status,
            NEW.payment_status,
            'Pembaruan status otomatis oleh sistem / admin',
            NOW()
        );
    END IF;
END$$
DELIMITER ;
