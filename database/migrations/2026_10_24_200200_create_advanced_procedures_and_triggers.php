<?php

use App\Support\DatabaseDriver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Jalankan Stored Procedures, Functions, dan Triggers jika koneksi database adalah MySQL/MariaDB
        if (DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            // 1. DROP EXISTING OBJECTS JIKA ADA
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_confirm_order_shipping;');
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_cancel_order;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_order_subtotal;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_get_customer_total_spent;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_reduce_product_stock_on_order_item;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_restore_product_stock_on_cancel;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_log_order_status_change;');

            // 2. STORED FUNCTION: fn_calculate_order_subtotal
            DB::unprepared('
                CREATE FUNCTION fn_calculate_order_subtotal(p_order_id BIGINT)
                RETURNS DECIMAL(12, 2)
                DETERMINISTIC
                READS SQL DATA
                BEGIN
                    DECLARE v_subtotal DECIMAL(12, 2) DEFAULT 0.00;
                    SELECT COALESCE(SUM(subtotal), 0.00) INTO v_subtotal
                    FROM order_items
                    WHERE order_id = p_order_id;
                    RETURN v_subtotal;
                END
            ');

            // 3. STORED FUNCTION: fn_get_customer_total_spent
            DB::unprepared("
                CREATE FUNCTION fn_get_customer_total_spent(p_user_id BIGINT)
                RETURNS DECIMAL(12, 2)
                DETERMINISTIC
                READS SQL DATA
                BEGIN
                    DECLARE v_total DECIMAL(12, 2) DEFAULT 0.00;
                    SELECT COALESCE(SUM(total_amount), 0.00) INTO v_total
                    FROM orders
                    WHERE user_id = p_user_id AND payment_status = 'paid';
                    RETURN v_total;
                END
            ");

            // 4. STORED PROCEDURE DENGAN COMMIT & ROLLBACK: sp_confirm_order_shipping
            DB::unprepared("
                CREATE PROCEDURE sp_confirm_order_shipping(
                    IN p_order_id BIGINT,
                    IN p_shipping_cost DECIMAL(12, 2),
                    OUT p_result_code INT,
                    OUT p_result_message VARCHAR(255)
                )
                proc_label: BEGIN
                    DECLARE v_subtotal DECIMAL(12, 2);
                    DECLARE v_total_amount DECIMAL(12, 2);
                    DECLARE v_current_status VARCHAR(50);

                    -- Handler untuk rollback otomatis jika terjadi SQL Exception
                    DECLARE EXIT HANDLER FOR SQLEXCEPTION
                    BEGIN
                        ROLLBACK;
                        SET p_result_code = 500;
                        SET p_result_message = 'Terjadi kesalahan sistem database saat konfirmasi ongkir. Transaksi di-rollback.';
                    END;

                    -- Cek pesanan
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

                    -- Memulai transaksi eksplisit
                    START TRANSACTION;

                    UPDATE orders
                    SET shipping_cost = p_shipping_cost,
                        total_amount = v_total_amount,
                        status = 'awaiting_payment',
                        payment_status = 'awaiting_payment',
                        updated_at = NOW()
                    WHERE id = p_order_id;

                    -- Commit transaksi jika berhasil
                    COMMIT;

                    SET p_result_code = 200;
                    SET p_result_message = 'Ongkir dan total pesanan berhasil dikonfirmasi.';
                END
            ");

            // 5. STORED PROCEDURE DENGAN COMMIT & ROLLBACK: sp_cancel_order
            DB::unprepared("
                CREATE PROCEDURE sp_cancel_order(
                    IN p_order_id BIGINT,
                    IN p_reason VARCHAR(255),
                    OUT p_result_code INT,
                    OUT p_result_message VARCHAR(255)
                )
                proc_label: BEGIN
                    DECLARE v_current_status VARCHAR(50);
                    DECLARE v_current_payment VARCHAR(50);

                    DECLARE EXIT HANDLER FOR SQLEXCEPTION
                    BEGIN
                        ROLLBACK;
                        SET p_result_code = 500;
                        SET p_result_message = 'Gagal membatalkan pesanan. Transaksi di-rollback.';
                    END;

                    SELECT status, payment_status INTO v_current_status, v_current_payment
                    FROM orders
                    WHERE id = p_order_id;

                    IF v_current_status IS NULL THEN
                        SET p_result_code = 404;
                        SET p_result_message = 'Pesanan tidak ditemukan.';
                        LEAVE proc_label;
                    END IF;

                    START TRANSACTION;

                    -- Jika belum bayar, jadikan unpaid; jika sudah bayar, pertahankan untuk refund
                    UPDATE orders
                    SET status = 'cancelled',
                        payment_status = CASE 
                            WHEN payment_status IN ('paid', 'refunded') THEN payment_status 
                            ELSE 'unpaid' 
                        END,
                        customer_note = CONCAT(COALESCE(customer_note, ''), ' [Dibatalkan: ', p_reason, ']'),
                        updated_at = NOW()
                    WHERE id = p_order_id;

                    COMMIT;

                    SET p_result_code = 200;
                    SET p_result_message = 'Pesanan berhasil dibatalkan.';
                END
            ");

            // 6. TRIGGER 1: trg_reduce_product_stock_on_order_item
            DB::unprepared('
                CREATE TRIGGER trg_reduce_product_stock_on_order_item
                AFTER INSERT ON order_items
                FOR EACH ROW
                BEGIN
                    IF NEW.product_id IS NOT NULL THEN
                        UPDATE products
                        SET stock = GREATEST(0, stock - NEW.quantity)
                        WHERE id = NEW.product_id;
                    END IF;
                END
            ');

            // 7. TRIGGER 2: trg_restore_product_stock_on_cancel
            DB::unprepared("
                CREATE TRIGGER trg_restore_product_stock_on_cancel
                AFTER UPDATE ON orders
                FOR EACH ROW
                BEGIN
                    IF NEW.status = 'cancelled' AND OLD.status != 'cancelled' THEN
                        UPDATE products p
                        JOIN order_items oi ON oi.product_id = p.id
                        SET p.stock = p.stock + oi.quantity
                        WHERE oi.order_id = NEW.id;
                    END IF;
                END
            ");

            // 8. TRIGGER 3: trg_log_order_status_change
            DB::unprepared("
                CREATE TRIGGER trg_log_order_status_change
                AFTER UPDATE ON orders
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
                            'Pembaruan status otomatis oleh sistem database / admin',
                            NOW()
                        );
                    END IF;
                END
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_log_order_status_change;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_restore_product_stock_on_cancel;');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_reduce_product_stock_on_order_item;');
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_cancel_order;');
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_confirm_order_shipping;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_get_customer_total_spent;');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_calculate_order_subtotal;');
        }
    }
};
