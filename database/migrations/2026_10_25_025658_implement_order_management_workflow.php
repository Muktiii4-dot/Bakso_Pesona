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
        $this->assertTransactionalLegacyTables();

        Schema::table('orders', function (Blueprint $table): void {
            $table->text('delivery_landmark')->nullable()->after('shipping_address');
            $table->uuid('checkout_token')->nullable();
            $table->timestamp('total_approved_at')->nullable();
            $table->timestamp('payment_instructions_issued_at')->nullable();
            $table->timestamp('payment_due_at')->nullable();
            $table->boolean('payment_review_required')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->unique(['user_id', 'checkout_token']);
        });

        Schema::create('order_events', function (Blueprint $table): void {
            $table->innoDb();
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_type', 20)->default('system');
            $table->string('event_type', 80);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });

        Schema::create('order_stock_reservations', function (Blueprint $table): void {
            $table->innoDb();
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('quantity');
            $table->string('state', 20)->default('reserved');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('consumed_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'state']);
            $table->index(['product_id', 'state']);
        });

        Schema::create('product_stock_movements', function (Blueprint $table): void {
            $table->innoDb();
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('movement_type', 40);
            $table->tinyInteger('direction')->default(0);
            $table->unsignedInteger('quantity')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['product_id', 'created_at']);
            $table->index(['order_id', 'created_at']);
        });

        Schema::create('order_cancellation_requests', function (Blueprint $table): void {
            $table->innoDb();
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users')->restrictOnDelete();
            $table->string('requested_by_role', 20);
            $table->text('reason');
            $table->string('status', 20)->default('pending');
            $table->foreignId('decided_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('decision_note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });

        Schema::create('order_refunds', function (Blueprint $table): void {
            $table->innoDb();
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->text('reason');
            $table->string('status', 20)->default('pending');
            $table->foreignId('processed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('transfer_reference')->nullable();
            $table->timestamp('requested_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'requested_at']);
        });

        Schema::create('order_ratings', function (Blueprint $table): void {
            $table->innoDb();
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('customer_user_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('original_comment')->nullable();
            $table->text('edited_comment')->nullable();
            $table->string('moderation_status', 20)->default('pending');
            $table->foreignId('moderated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('moderation_note')->nullable();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();

            $table->index(['moderation_status', 'created_at']);
            $table->index('customer_user_id');
        });

        $this->backfillLegacyOrderEvents();
        $this->dropLegacyMySqlTriggers();
        $this->normalizeLegacyOrderStatuses();
        $this->backfillLegacyStockReservations();
    }

    public function down(): void
    {
        $this->guardAgainstLosingNewOrderData();
        $this->restoreLegacyOrderStatuses();

        Schema::dropIfExists('order_ratings');
        Schema::dropIfExists('order_refunds');
        Schema::dropIfExists('order_cancellation_requests');
        Schema::dropIfExists('product_stock_movements');
        Schema::dropIfExists('order_stock_reservations');
        Schema::dropIfExists('order_events');

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['user_id', 'checkout_token']);
            $table->dropColumn([
                'delivery_landmark',
                'checkout_token',
                'total_approved_at',
                'payment_instructions_issued_at',
                'payment_due_at',
                'payment_review_required',
                'cancelled_at',
            ]);
        });

        $this->restoreLegacyMySqlTriggers();
    }

    private function backfillLegacyOrderEvents(): void
    {
        if (! Schema::hasTable('order_status_logs')) {
            return;
        }

        DB::table('order_status_logs')
            ->orderBy('id')
            ->chunkById(500, function ($logs): void {
                $events = $logs->map(fn (object $log): array => [
                    'order_id' => $log->order_id,
                    'actor_type' => 'unknown',
                    'event_type' => 'legacy_status_change',
                    'old_values' => json_encode([
                        'status' => $log->old_status,
                        'payment_status' => $log->old_payment_status,
                    ], JSON_THROW_ON_ERROR),
                    'new_values' => json_encode([
                        'status' => $log->new_status,
                        'payment_status' => $log->new_payment_status,
                    ], JSON_THROW_ON_ERROR),
                    'metadata' => json_encode(['note' => $log->note], JSON_THROW_ON_ERROR),
                    'created_at' => $log->created_at,
                ])->all();

                if ($events !== []) {
                    DB::table('order_events')->insert($events);
                }
            });
    }

    private function normalizeLegacyOrderStatuses(): void
    {
        DB::table('orders')
            ->where('status', 'awaiting_payment')
            ->orderBy('id')
            ->chunkById(500, function ($orders): void {
                foreach ($orders as $order) {
                    DB::table('order_events')->insert([
                        'order_id' => $order->id,
                        'actor_type' => 'system',
                        'event_type' => 'legacy_status_normalized',
                        'old_values' => json_encode(['status' => $order->status], JSON_THROW_ON_ERROR),
                        'new_values' => json_encode(['status' => 'pending_confirmation'], JSON_THROW_ON_ERROR),
                        'metadata' => json_encode(['source' => 'order_workflow_migration'], JSON_THROW_ON_ERROR),
                        'created_at' => now(),
                    ]);

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['status' => 'pending_confirmation']);
                }
            });
    }

    private function backfillLegacyStockReservations(): void
    {
        DB::table('order_items')
            ->orderBy('id')
            ->chunkById(500, function ($items): void {
                $orders = DB::table('orders')
                    ->whereIn('id', $items->pluck('order_id')->unique())
                    ->get()
                    ->keyBy('id');

                foreach ($items as $item) {
                    $order = $orders->get($item->order_id);

                    if (! $order || ! $item->product_id) {
                        continue;
                    }

                    DB::table('order_stock_reservations')->insert([
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'state' => 'legacy_unreconciled',
                        'reserved_at' => null,
                        'consumed_at' => null,
                        'released_at' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('product_stock_movements')->insert([
                        'product_id' => $item->product_id,
                        'order_id' => $order->id,
                        'order_item_id' => $item->id,
                        'movement_type' => 'legacy_stock_unreconciled',
                        'direction' => 0,
                        'quantity' => $item->quantity,
                        'metadata' => json_encode([
                            'state' => 'legacy_unreconciled',
                            'stock_changed' => false,
                            'reconciliation_required' => true,
                            'legacy_order_status' => $order->status,
                            'legacy_payment_status' => $order->payment_status,
                        ], JSON_THROW_ON_ERROR),
                        'created_at' => $order->created_at,
                    ]);
                }
            });
    }

    private function dropLegacyMySqlTriggers(): void
    {
        if (! DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_reduce_product_stock_on_order_item');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_restore_product_stock_on_cancel');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_log_order_status_change');

        $remainingTriggers = DB::table('information_schema.triggers')
            ->whereRaw('trigger_schema = DATABASE()')
            ->whereIn('trigger_name', [
                'trg_reduce_product_stock_on_order_item',
                'trg_restore_product_stock_on_cancel',
                'trg_log_order_status_change',
            ])
            ->pluck('trigger_name')
            ->all();

        if ($remainingTriggers !== []) {
            throw new RuntimeException(
                'Legacy order triggers remain installed. Keep order writes disabled and resolve database permissions before proceeding.'
            );
        }
    }

    private function assertTransactionalLegacyTables(): void
    {
        if (! DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            return;
        }

        $requiredTables = ['users', 'products', 'orders', 'order_items'];
        if (Schema::hasTable('order_status_logs')) {
            $requiredTables[] = 'order_status_logs';
        }

        $engines = DB::table('information_schema.tables')
            ->whereRaw('table_schema = DATABASE()')
            ->whereIn('table_name', $requiredTables)
            ->pluck('engine', 'table_name')
            ->map(fn (mixed $engine): string => strtolower((string) $engine));

        $missingTables = array_values(array_diff($requiredTables, $engines->keys()->all()));
        $nonTransactionalTables = $engines
            ->filter(fn (string $engine): bool => $engine !== 'innodb')
            ->keys()
            ->all();

        if ($missingTables !== [] || $nonTransactionalTables !== []) {
            throw new RuntimeException(sprintf(
                'Order workflow migration requires existing order and stock tables to use InnoDB. Missing tables: [%s]. Tables with a non-InnoDB engine: [%s].',
                implode(', ', $missingTables),
                implode(', ', $nonTransactionalTables),
            ));
        }
    }

    private function guardAgainstLosingNewOrderData(): void
    {
        if (! DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            return;
        }

        $hasNewOrderData = DB::table('orders')
            ->whereNotNull('checkout_token')
            ->orWhereNotNull('delivery_landmark')
            ->orWhereNotNull('total_approved_at')
            ->orWhereNotNull('payment_instructions_issued_at')
            ->orWhereNotNull('payment_due_at')
            ->orWhereNotNull('cancelled_at')
            ->orWhere('payment_review_required', true)
            ->exists();

        $hasNewEvents = DB::table('order_events')
            ->whereNotIn('event_type', ['legacy_status_change', 'legacy_status_normalized'])
            ->exists();

        $hasSupportingRecords = DB::table('order_cancellation_requests')->exists()
            || DB::table('order_refunds')->exists()
            || DB::table('order_ratings')->exists()
            || DB::table('product_stock_movements')
                ->whereNotIn('movement_type', ['legacy_stock_state', 'legacy_stock_unreconciled'])
                ->exists();

        if ($hasNewOrderData || $hasNewEvents || $hasSupportingRecords) {
            throw new RuntimeException(
                'This order workflow migration cannot be rolled back after new transaction records have been created. Restore from a verified backup or use a forward migration.'
            );
        }
    }

    private function restoreLegacyOrderStatuses(): void
    {
        if (! Schema::hasTable('order_events')) {
            return;
        }

        DB::table('order_events')
            ->where('event_type', 'legacy_status_normalized')
            ->orderBy('id')
            ->get()
            ->each(function (object $event): void {
                $oldValues = json_decode($event->old_values, true, flags: JSON_THROW_ON_ERROR);

                DB::table('orders')
                    ->where('id', $event->order_id)
                    ->update(['status' => $oldValues['status']]);
            });
    }

    private function restoreLegacyMySqlTriggers(): void
    {
        if (! DatabaseDriver::supportsMySqlFeatures(DB::getDriverName())) {
            return;
        }

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

        DB::unprepared("
            CREATE TRIGGER trg_log_order_status_change
            AFTER UPDATE ON orders
            FOR EACH ROW
            BEGIN
                IF (OLD.status != NEW.status) OR (OLD.payment_status != NEW.payment_status) THEN
                    INSERT INTO order_status_logs (
                        order_id, old_status, new_status, old_payment_status,
                        new_payment_status, note, created_at
                    ) VALUES (
                        NEW.id, OLD.status, NEW.status, OLD.payment_status,
                        NEW.payment_status, 'Pembaruan status otomatis oleh sistem / admin', NOW()
                    );
                END IF;
            END
        ");
    }
};
