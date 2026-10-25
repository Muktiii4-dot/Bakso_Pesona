<?php

use App\Support\DatabaseDriver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('action', 20);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['action', 'created_at']);
        });

        // Stored routines and triggers are MySQL/MariaDB-only. SQLite remains supported
        // for local tests; Laravel application CRUD works on both engines.
        if (! DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_products_audit_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_products_audit_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_products_audit_delete');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_adjust_product_stock');
        DB::unprepared('DROP FUNCTION IF EXISTS fn_product_stock_value');

        DB::unprepared(<<<'SQL'
            CREATE FUNCTION fn_product_stock_value(p_product_id BIGINT)
            RETURNS DECIMAL(14,2)
            READS SQL DATA
            BEGIN
                DECLARE v_value DECIMAL(14,2) DEFAULT 0.00;
                SELECT COALESCE(price * stock, 0.00) INTO v_value
                FROM products WHERE id = p_product_id;
                RETURN v_value;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_adjust_product_stock(
                IN p_product_id BIGINT,
                IN p_delta INT,
                OUT p_result_code INT,
                OUT p_result_message VARCHAR(255)
            )
            proc_label: BEGIN
                DECLARE v_stock INT DEFAULT NULL;
                DECLARE v_exists INT DEFAULT 0;
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_result_code = 500;
                    SET p_result_message = 'Penyesuaian stok gagal; transaksi di-rollback.';
                END;

                IF p_delta = 0 THEN
                    SET p_result_code = 422;
                    SET p_result_message = 'Perubahan stok tidak boleh nol.';
                    LEAVE proc_label;
                END IF;

                START TRANSACTION;
                SELECT COUNT(*) INTO v_exists FROM products WHERE id = p_product_id;
                IF v_exists = 0 THEN
                    ROLLBACK;
                    SET p_result_code = 404;
                    SET p_result_message = 'Produk tidak ditemukan.';
                    LEAVE proc_label;
                END IF;
                SELECT stock INTO v_stock FROM products WHERE id = p_product_id FOR UPDATE;
                IF v_stock IS NULL THEN
                    ROLLBACK;
                    SET p_result_code = 404;
                    SET p_result_message = 'Produk tidak ditemukan.';
                    LEAVE proc_label;
                END IF;
                IF v_stock + p_delta < 0 THEN
                    ROLLBACK;
                    SET p_result_code = 422;
                    SET p_result_message = 'Stok tidak boleh menjadi negatif; transaksi di-rollback.';
                    LEAVE proc_label;
                END IF;

                UPDATE products SET stock = stock + p_delta, updated_at = NOW()
                WHERE id = p_product_id;
                COMMIT;
                SET p_result_code = 200;
                SET p_result_message = 'Stok berhasil diperbarui (COMMIT).';
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_products_audit_insert AFTER INSERT ON products
            FOR EACH ROW
            BEGIN
                INSERT INTO product_audit_logs (product_id, action, old_values, new_values, created_at)
                VALUES (NEW.id, 'INSERT', NULL,
                    JSON_OBJECT('name', NEW.name, 'price', NEW.price, 'stock', NEW.stock, 'is_available', NEW.is_available), NOW());
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_products_audit_update AFTER UPDATE ON products
            FOR EACH ROW
            BEGIN
                INSERT INTO product_audit_logs (product_id, action, old_values, new_values, created_at)
                VALUES (NEW.id, 'UPDATE',
                    JSON_OBJECT('name', OLD.name, 'price', OLD.price, 'stock', OLD.stock, 'is_available', OLD.is_available),
                    JSON_OBJECT('name', NEW.name, 'price', NEW.price, 'stock', NEW.stock, 'is_available', NEW.is_available), NOW());
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_products_audit_delete AFTER DELETE ON products
            FOR EACH ROW
            BEGIN
                INSERT INTO product_audit_logs (product_id, action, old_values, new_values, created_at)
                VALUES (OLD.id, 'DELETE',
                    JSON_OBJECT('name', OLD.name, 'price', OLD.price, 'stock', OLD.stock, 'is_available', OLD.is_available), NULL, NOW());
            END
        SQL);
    }

    public function down(): void
    {
        if (DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            DB::unprepared('DROP TRIGGER IF EXISTS trg_products_audit_delete');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_products_audit_update');
            DB::unprepared('DROP TRIGGER IF EXISTS trg_products_audit_insert');
            DB::unprepared('DROP PROCEDURE IF EXISTS sp_adjust_product_stock');
            DB::unprepared('DROP FUNCTION IF EXISTS fn_product_stock_value');
        }

        Schema::dropIfExists('product_audit_logs');
    }
};
