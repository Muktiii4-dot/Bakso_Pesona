<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderRating;
use App\Models\OrderStockReservation;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'role' => 'customer',
        ]);
    }

    private function createOrder(array $attributes = []): Order
    {
        return Order::create(array_merge([
            'order_number' => 'BP-TEST-'.uniqid(),
            'user_id' => $this->customer->id,
            'recipient_name' => 'Budi Santoso',
            'phone' => '08123456789',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'customer_note' => null,
            'subtotal' => 50000,
            'shipping_cost' => null,
            'total_amount' => null,
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_method' => 'manual',
            'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION,
        ], $attributes));
    }

    public function test_guest_cannot_access_admin_orders(): void
    {
        $response = $this->get('/admin/pesanan');

        $response->assertRedirect('/admin/login');
    }

    public function test_customer_cannot_access_admin_orders(): void
    {
        $response = $this->actingAs($this->customer, 'customer')->get('/admin/pesanan');

        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_orders_index_and_detail(): void
    {
        $order = $this->createOrder();

        $indexResponse = $this->actingAs($this->admin, 'admin')->get('/admin/pesanan');
        $indexResponse->assertStatus(200);

        $showResponse = $this->actingAs($this->admin, 'admin')->get("/admin/pesanan/{$order->id}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee($order->order_number);
    }

    public function test_admin_can_confirm_shipping_cost_and_update_status(): void
    {
        $order = $this->createOrder();

        $response = $this->actingAs($this->admin, 'admin')->put("/admin/pesanan/{$order->id}/konfirmasi-ongkir", [
            'shipping_cost' => 15000,
        ]);

        $response->assertRedirect("/admin/pesanan/{$order->id}");
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('15000.00', $order->shipping_cost);
        $this->assertSame('65000.00', $order->total_amount);
        $this->assertSame(Order::STATUS_PENDING_CONFIRMATION, $order->status);
        $this->assertSame(Order::PAYMENT_AWAITING_CONFIRMATION, $order->payment_status);
    }

    public function test_changing_shipping_cost_invalidates_previous_instructions_and_records_the_change(): void
    {
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'total_approved_at' => now()->subHour(),
            'payment_instructions_issued_at' => now()->subMinutes(50),
            'payment_due_at' => now()->addHours(23),
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->put("/admin/pesanan/{$order->id}/konfirmasi-ongkir", [
            'shipping_cost' => 15000,
        ]);

        $response->assertRedirect("/admin/pesanan/{$order->id}");
        $order->refresh();
        $this->assertSame('15000.00', $order->shipping_cost);
        $this->assertSame('65000.00', $order->total_amount);
        $this->assertNull($order->total_approved_at);
        $this->assertNull($order->payment_instructions_issued_at);
        $this->assertNull($order->payment_due_at);
        $this->assertSame(Order::PAYMENT_AWAITING_CONFIRMATION, $order->payment_status);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'event_type' => 'payment_instructions_invalidated',
        ]);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'event_type' => 'shipping_total_updated',
            'actor_user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_cannot_change_shipping_after_a_payment_deadline_until_late_payment_review(): void
    {
        $this->freezeTime();
        $paymentDueAt = now()->subHour();
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'payment_instructions_issued_at' => now()->subHours(25),
            'payment_due_at' => $paymentDueAt,
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.confirm-shipping', $order), [
                'shipping_cost' => 15000,
            ]);

        $response->assertSessionHasErrors('shipping_cost');
        $this->assertSame('10000.00', $order->fresh()->shipping_cost);
        $this->assertSame('60000.00', $order->fresh()->total_amount);
        $this->assertSame(Order::PAYMENT_AWAITING_PAYMENT, $order->fresh()->payment_status);
        $this->assertSame($paymentDueAt->format('Y-m-d H:i:s'), $order->fresh()->payment_due_at->format('Y-m-d H:i:s'));
    }

    public function test_admin_can_start_payment_verification_without_marking_the_order_paid(): void
    {
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'total_approved_at' => now()->subMinutes(30),
            'payment_instructions_issued_at' => now()->subMinutes(20),
            'payment_due_at' => now()->addHours(24),
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
        ]);

        // Verifikasi dalam proses
        $response1 = $this->actingAs($this->admin, 'admin')->put("/admin/pesanan/{$order->id}/status-pembayaran", [
            'payment_status' => Order::PAYMENT_UNDER_VERIFICATION,
            'verification_note' => 'Mutasi transaksi sedang dicocokkan.',
        ]);
        $response1->assertRedirect("/admin/pesanan/{$order->id}");
        $order->refresh();
        $this->assertSame(Order::PAYMENT_UNDER_VERIFICATION, $order->payment_status);
        $this->assertNotSame(Order::PAYMENT_PAID, $order->payment_status);
        $this->assertSame(Order::STATUS_PENDING_CONFIRMATION, $order->status);
    }

    public function test_admin_can_update_order_lifecycle_from_processing_to_shipped_to_completed(): void
    {
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'status' => Order::STATUS_PROCESSING,
            'payment_status' => Order::PAYMENT_PAID,
        ]);

        // Dikirim (shipped)
        $shipResponse = $this->actingAs($this->admin, 'admin')->put("/admin/pesanan/{$order->id}/status", [
            'status' => Order::STATUS_SHIPPED,
        ]);
        $shipResponse->assertRedirect("/admin/pesanan/{$order->id}");
        $order->refresh();
        $this->assertEquals(Order::STATUS_SHIPPED, $order->status);

        // Selesai (completed)
        $completeResponse = $this->actingAs($this->admin, 'admin')->put("/admin/pesanan/{$order->id}/status", [
            'status' => Order::STATUS_COMPLETED,
        ]);
        $completeResponse->assertRedirect("/admin/pesanan/{$order->id}");
        $order->refresh();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }

    public function test_admin_cannot_ship_unpaid_order(): void
    {
        $order = $this->createOrder([
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'shipping_cost' => 10000,
            'total_amount' => 60000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->put("/admin/pesanan/{$order->id}/status", [
            'status' => Order::STATUS_SHIPPED,
        ]);

        $response->assertSessionHasErrors('status');
    }

    public function test_admin_cancelling_unpaid_order_marks_payment_as_unpaid(): void
    {
        $order = $this->createOrder([
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'shipping_cost' => 10000,
            'total_amount' => 60000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post(route('admin.orders.cancel', $order), [
            'reason' => 'Pembatalan diminta sebelum pesanan diproses.',
        ]);

        $response->assertRedirect("/admin/pesanan/{$order->id}");
        $order->refresh();
        $this->assertSame(Order::STATUS_CANCELLED, $order->status);
        $this->assertSame(Order::PAYMENT_UNPAID, $order->payment_status);
        $this->assertDatabaseHas('order_cancellation_requests', [
            'order_id' => $order->id,
            'requested_by_user_id' => $this->admin->id,
            'status' => 'approved',
        ]);
    }

    public function test_admin_cannot_cancel_a_legacy_order_with_unreconciled_stock(): void
    {
        $category = Category::create([
            'name' => 'Bakso Rekonsiliasi',
            'slug' => 'bakso-rekonsiliasi',
            'description' => 'Kategori untuk pengujian rekonsiliasi.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Legacy',
            'slug' => 'bakso-legacy',
            'description' => 'Produk order lama.',
            'price' => 25000,
            'stock' => 4,
            'is_available' => true,
        ]);
        $order = $this->createOrder([
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
        ]);
        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 25000,
            'quantity' => 2,
            'subtotal' => 50000,
        ]);
        OrderStockReservation::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'state' => 'legacy_unreconciled',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.orders.cancel', $order), [
                'reason' => 'Menguji perlindungan stok historis.',
            ]);

        $response->assertSessionHasErrors('decision');
        $this->assertSame(Order::STATUS_PENDING_CONFIRMATION, $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock);

        $customerResponse = $this->actingAs($this->customer, 'customer')
            ->post(route('orders.cancel', $order), [
                'reason' => 'Menguji perlindungan legacy untuk pelanggan.',
            ]);

        $customerResponse->assertSessionHasErrors('reason');
        $this->assertSame(Order::STATUS_PENDING_CONFIRMATION, $order->fresh()->status);
        $this->assertSame(4, $product->fresh()->stock);
        $this->assertDatabaseCount('order_cancellation_requests', 0);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_payment_verification_consumes_a_reservation_without_changing_stock_again(): void
    {
        $category = Category::create([
            'name' => 'Bakso Pembayaran',
            'slug' => 'bakso-pembayaran',
            'description' => 'Kategori untuk pengujian pembayaran.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Reservasi',
            'slug' => 'bakso-reservasi',
            'description' => 'Produk dengan stok yang telah ditahan.',
            'price' => 25000,
            'stock' => 3,
            'is_available' => true,
        ]);
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'total_approved_at' => now()->subMinutes(30),
            'payment_instructions_issued_at' => now()->subMinutes(20),
            'payment_due_at' => now()->addHours(23),
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
        ]);
        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 25000,
            'quantity' => 2,
            'subtotal' => 50000,
        ]);
        OrderStockReservation::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'state' => 'reserved',
            'reserved_at' => now()->subHour(),
        ]);

        $verificationResponse = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.update-payment-status', $order), [
                'payment_status' => Order::PAYMENT_UNDER_VERIFICATION,
                'verification_note' => 'Mutasi transaksi sedang dicocokkan.',
            ]);

        $verificationResponse->assertRedirect(route('admin.orders.show', $order));

        $mismatchResponse = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.update-payment-status', $order), [
                'payment_status' => Order::PAYMENT_PAID,
                'actual_amount' => 60001,
                'verification_note' => 'Nominal dari mutasi tidak sama.',
            ]);

        $mismatchResponse->assertSessionHasErrors('actual_amount');
        $this->assertSame(Order::PAYMENT_UNDER_VERIFICATION, $order->fresh()->payment_status);
        $this->assertDatabaseHas('order_stock_reservations', [
            'order_item_id' => $item->id,
            'state' => 'reserved',
        ]);
        $this->assertDatabaseCount('product_stock_movements', 0);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.update-payment-status', $order), [
                'payment_status' => Order::PAYMENT_PAID,
                'actual_amount' => 60000,
                'transaction_reference' => 'TRX-BAKSO-001',
                'verification_note' => 'Dana aktual cocok dengan nominal tagihan.',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame(Order::PAYMENT_PAID, $order->fresh()->payment_status);
        $this->assertDatabaseHas('order_stock_reservations', [
            'order_item_id' => $item->id,
            'state' => 'consumed',
        ]);
        $this->assertDatabaseCount('product_stock_movements', 1);

        $retryResponse = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.update-payment-status', $order), [
                'payment_status' => Order::PAYMENT_PAID,
                'actual_amount' => 60000,
                'verification_note' => 'Percobaan verifikasi ulang.',
            ]);

        $retryResponse->assertSessionHasErrors('payment_status');
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseCount('product_stock_movements', 1);
    }

    public function test_repeated_admin_cancellation_releases_reserved_stock_only_once(): void
    {
        $category = Category::create([
            'name' => 'Bakso Pembatalan',
            'slug' => 'bakso-pembatalan',
            'description' => 'Kategori untuk pengujian pelepasan stok.',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Dibatalkan',
            'slug' => 'bakso-dibatalkan',
            'description' => 'Produk dengan stok reservasi.',
            'price' => 25000,
            'stock' => 3,
            'is_available' => true,
        ]);
        $order = $this->createOrder([
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
        ]);
        $item = $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 25000,
            'quantity' => 2,
            'subtotal' => 50000,
        ]);
        OrderStockReservation::create([
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'state' => 'reserved',
            'reserved_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.orders.cancel', $order), [
                'reason' => 'Pesanan dibatalkan sebelum diproses.',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseCount('product_stock_movements', 1);

        $retryResponse = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.orders.cancel', $order), [
                'reason' => 'Percobaan pembatalan ulang.',
            ]);

        $retryResponse->assertSessionHasErrors('reason');
        $this->assertSame(5, $product->fresh()->stock);
        $this->assertDatabaseCount('product_stock_movements', 1);
    }

    public function test_admin_dashboard_renders_with_metrics_and_recent_orders(): void
    {
        $order = $this->createOrder();

        $response = $this->actingAs($this->admin, 'admin')->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Operasional');
        $response->assertSee($order->order_number);
        $response->assertSee('href="'.route('home').'"', false);
        $response->assertSee('target="_blank"', false);
    }

    public function test_audit_history_is_searchable_and_admin_only(): void
    {
        $order = $this->createOrder([
            'order_number' => 'BP-AUDIT-001',
        ]);
        $order->recordEvent('payment_verified', $this->admin, newValues: [
            'payment_status' => Order::PAYMENT_PAID,
        ]);

        $customerResponse = $this->actingAs($this->customer, 'customer')
            ->get(route('admin.audit.index'));
        $customerResponse->assertRedirect(route('admin.login'));

        $adminResponse = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.audit.index', ['search' => 'BP-AUDIT-001']));

        $adminResponse->assertOk();
        $adminResponse->assertSee('BP-AUDIT-001');
        $adminResponse->assertSee('Payment Verified');
    }

    public function test_admin_can_filter_orders_by_search_and_status(): void
    {
        $order1 = $this->createOrder([
            'order_number' => 'BP-SEARCH-111',
            'recipient_name' => 'Siti Nurhaliza',
            'status' => Order::STATUS_PENDING_CONFIRMATION,
        ]);

        $order2 = $this->createOrder([
            'order_number' => 'BP-SEARCH-222',
            'recipient_name' => 'Joko Widodo',
            'status' => Order::STATUS_PROCESSING,
            'payment_status' => Order::PAYMENT_PAID,
        ]);

        // Filter by keyword search
        $searchResponse = $this->actingAs($this->admin, 'admin')->get('/admin/pesanan?search=Nurhaliza');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('BP-SEARCH-111');
        $searchResponse->assertDontSee('BP-SEARCH-222');

        // Filter by status
        $statusResponse = $this->actingAs($this->admin, 'admin')->get('/admin/pesanan?status=processing');
        $statusResponse->assertStatus(200);
        $statusResponse->assertSee('BP-SEARCH-222');
        $statusResponse->assertDontSee('BP-SEARCH-111');
    }

    public function test_admin_can_moderate_a_rating_and_the_action_is_audited(): void
    {
        $order = $this->createOrder([
            'status' => Order::STATUS_COMPLETED,
            'payment_status' => Order::PAYMENT_PAID,
        ]);
        $rating = OrderRating::factory()->create([
            'order_id' => $order->id,
            'customer_user_id' => $this->customer->id,
            'rating' => 4,
            'original_comment' => 'Rasanya enak dan pengiriman cepat.',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.ratings.moderate', $rating), [
                'decision' => 'approve',
                'edited_comment' => 'Rasanya enak dan pengiriman cepat.',
                'moderation_note' => 'Disetujui tanpa perubahan makna.',
            ]);

        $response->assertRedirect(route('admin.ratings.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('order_ratings', [
            'id' => $rating->id,
            'moderation_status' => 'approved',
            'original_comment' => 'Rasanya enak dan pengiriman cepat.',
            'edited_comment' => 'Rasanya enak dan pengiriman cepat.',
            'moderated_by_user_id' => $this->admin->id,
        ]);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'actor_user_id' => $this->admin->id,
            'event_type' => 'rating_moderated',
        ]);
    }

    public function test_rating_moderation_queue_is_admin_only(): void
    {
        $order = $this->createOrder([
            'status' => Order::STATUS_COMPLETED,
            'payment_status' => Order::PAYMENT_PAID,
        ]);
        $rating = OrderRating::factory()->create([
            'order_id' => $order->id,
            'customer_user_id' => $this->customer->id,
            'original_comment' => 'Perlu ditinjau admin.',
        ]);

        $customerResponse = $this->actingAs($this->customer, 'customer')->get(route('admin.ratings.index'));
        $customerResponse->assertRedirect(route('admin.login'));

        $adminResponse = $this->actingAs($this->admin, 'admin')->get(route('admin.ratings.index'));
        $adminResponse->assertOk();
        $adminResponse->assertSee($order->order_number);
        $adminResponse->assertSee('Perlu ditinjau admin.');
        $this->assertModelExists($rating);
    }

    public function test_admin_payment_instructions_start_a_24_hour_deadline_when_issued(): void
    {
        $this->freezeTime();
        $issuedAt = now();
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'total_approved_at' => $issuedAt->copy()->subMinutes(10),
            'created_at' => $issuedAt->copy()->subDays(2),
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION,
        ]);
        $qrisImage = 'testing-qris-'.$order->id.'.png';
        $qrisPath = public_path($qrisImage);
        File::put($qrisPath, 'temporary test fixture; not a payment QR code');

        try {
            config([
                'bakso_pesona.whatsapp_number' => '08123456789',
                'bakso_pesona.qris.image' => $qrisImage,
                'bakso_pesona.qris.merchant_name' => 'Bakso Pesona Uji',
            ]);

            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.orders.issue-payment-instructions', $order), [
                    'confirmed_sent' => 'yes',
                ]);

            $response->assertRedirect(route('admin.orders.show', $order));
            $order->refresh();
            $this->assertSame($issuedAt->format('Y-m-d H:i:s'), $order->payment_instructions_issued_at->format('Y-m-d H:i:s'));
            $this->assertSame($issuedAt->copy()->addHours(24)->format('Y-m-d H:i:s'), $order->payment_due_at->format('Y-m-d H:i:s'));
            $this->assertSame(Order::PAYMENT_AWAITING_PAYMENT, $order->payment_status);
            $this->assertDatabaseHas('order_events', [
                'order_id' => $order->id,
                'actor_user_id' => $this->admin->id,
                'event_type' => 'payment_instructions_sent',
            ]);

            $firstDeadline = $order->payment_due_at;
            $missingReasonResponse = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.orders.issue-payment-instructions', $order), [
                    'confirmed_sent' => 'yes',
                ]);

            $missingReasonResponse->assertSessionHasErrors('resend_reason');
            $this->assertSame($firstDeadline->format('Y-m-d H:i:s'), $order->fresh()->payment_due_at->format('Y-m-d H:i:s'));

            $this->travel(1)->minutes();
            $resendResponse = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.orders.issue-payment-instructions', $order), [
                    'confirmed_sent' => 'yes',
                    'resend_reason' => 'Pelanggan meminta instruksi dikirim ulang.',
                ]);

            $resendResponse->assertRedirect(route('admin.orders.show', $order));
            $this->assertSame(now()->addHours(24)->format('Y-m-d H:i:s'), $order->fresh()->payment_due_at->format('Y-m-d H:i:s'));
            $this->assertDatabaseHas('order_events', [
                'order_id' => $order->id,
                'actor_user_id' => $this->admin->id,
                'event_type' => 'payment_instructions_resent',
            ]);
        } finally {
            File::delete($qrisPath);
        }
    }

    public function test_opening_the_whatsapp_instruction_page_does_not_start_the_deadline(): void
    {
        $this->freezeTime();
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.show', $order));

        $response->assertOk();
        $this->assertNull($order->fresh()->payment_instructions_issued_at);
        $this->assertNull($order->fresh()->payment_due_at);
    }

    public function test_payment_instructions_cannot_be_recorded_without_official_qris_configuration(): void
    {
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
        ]);

        config([
            'bakso_pesona.whatsapp_number' => null,
            'bakso_pesona.qris.image' => null,
            'bakso_pesona.qris.merchant_name' => null,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.issue-payment-instructions', $order), [
                'confirmed_sent' => 'yes',
            ]);

        $response->assertSessionHasErrors('confirmed_sent');
        $this->assertNull($order->fresh()->payment_instructions_issued_at);
        $this->assertNull($order->fresh()->payment_due_at);
        $this->assertDatabaseMissing('order_events', [
            'order_id' => $order->id,
            'event_type' => 'payment_instructions_sent',
        ]);
    }

    public function test_admin_can_manually_reconcile_a_late_matching_payment(): void
    {
        $this->freezeTime();
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'payment_instructions_issued_at' => now()->subHours(25),
            'payment_due_at' => now()->subHour(),
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'payment_review_required' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.resolve-payment-review', $order), [
                'result' => 'received',
                'actual_amount' => 60000,
                'transaction_reference' => 'LATE-TRX-001',
                'review_note' => 'Dana ditemukan pada mutasi setelah tenggat.',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame(Order::PAYMENT_PAID, $order->fresh()->payment_status);
        $this->assertFalse($order->fresh()->payment_review_required);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'actor_user_id' => $this->admin->id,
            'event_type' => 'expired_payment_review_received',
        ]);
    }

    public function test_late_payment_with_a_mismatched_amount_is_not_marked_paid(): void
    {
        $this->freezeTime();
        $order = $this->createOrder([
            'shipping_cost' => 10000,
            'total_amount' => 60000,
            'payment_instructions_issued_at' => now()->subHours(25),
            'payment_due_at' => now()->subHour(),
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'payment_review_required' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.orders.resolve-payment-review', $order), [
                'result' => 'mismatch',
                'actual_amount' => 59000,
                'transaction_reference' => 'LATE-TRX-002',
                'review_note' => 'Nominal mutasi berbeda dari total tagihan.',
            ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame(Order::PAYMENT_REJECTED, $order->fresh()->payment_status);
        $this->assertFalse($order->fresh()->payment_review_required);
        $this->assertNull($order->fresh()->payment_due_at);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'actor_user_id' => $this->admin->id,
            'event_type' => 'late_payment_amount_mismatch',
        ]);
    }

    public function test_payment_instructions_cannot_be_recorded_for_an_invalid_customer_whatsapp_number(): void
    {
        $order = $this->createOrder([
            'phone' => '0812',
            'shipping_cost' => 10000,
            'total_amount' => 60000,
        ]);
        $qrisImage = 'testing-qris-'.$order->id.'.png';
        $qrisPath = public_path($qrisImage);
        File::put($qrisPath, 'temporary test fixture; not a payment QR code');

        try {
            config([
                'bakso_pesona.whatsapp_number' => '08123456789',
                'bakso_pesona.qris.image' => $qrisImage,
                'bakso_pesona.qris.merchant_name' => 'Bakso Pesona Uji',
            ]);

            $response = $this->actingAs($this->admin, 'admin')
                ->put(route('admin.orders.issue-payment-instructions', $order), [
                    'confirmed_sent' => 'yes',
                ]);

            $response->assertSessionHasErrors('confirmed_sent');
            $this->assertNull($order->fresh()->payment_instructions_issued_at);
            $this->assertNull($order->fresh()->payment_due_at);
        } finally {
            File::delete($qrisPath);
        }
    }

    public function test_expired_payment_deadlines_require_manual_review_without_releasing_stock(): void
    {
        $this->travelTo(now()->setTime(12, 0));
        $expiredOrder = $this->createOrder([
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'payment_due_at' => now()->subMinute(),
        ]);
        $expiredVerificationOrder = $this->createOrder([
            'payment_status' => Order::PAYMENT_UNDER_VERIFICATION,
            'payment_due_at' => now()->subMinute(),
        ]);
        $notYetDueOrder = $this->createOrder([
            'payment_status' => Order::PAYMENT_UNDER_VERIFICATION,
            'payment_due_at' => now()->addMinute(),
        ]);
        $paidOrder = $this->createOrder([
            'payment_status' => Order::PAYMENT_PAID,
            'payment_due_at' => now()->subMinute(),
        ]);

        $exitCode = Artisan::call('app:mark-expired-payment-reviews');
        $secondExitCode = Artisan::call('app:mark-expired-payment-reviews');

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, $secondExitCode);
        $this->assertDatabaseHas('orders', [
            'id' => $expiredOrder->id,
            'payment_review_required' => true,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $expiredVerificationOrder->id,
            'payment_review_required' => true,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $notYetDueOrder->id,
            'payment_review_required' => false,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $paidOrder->id,
            'payment_review_required' => false,
        ]);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $expiredOrder->id,
            'event_type' => 'payment_deadline_review_required',
        ]);
        $this->assertDatabaseCount('order_events', 2);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_guest_can_view_admin_login_page(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Admin Portal');
        $response->assertSee('Bakso Pesona');
    }

    public function test_admin_can_login_via_admin_login_page(): void
    {
        $adminUser = User::factory()->create([
            'email' => 'realadmin@baksopesona.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'realadmin@baksopesona.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($adminUser, 'admin');
    }

    public function test_customer_session_is_not_replaced_by_admin_login_flow(): void
    {
        $response = $this->actingAs($this->customer, 'customer')->get('/admin/login');

        $response->assertOk();
        $this->assertAuthenticatedAs($this->customer, 'customer');

        $loginResponse = $this->post('/admin/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->customer, 'customer');
        $this->assertAuthenticatedAs($this->admin, 'admin');
    }

    public function test_admin_can_open_customer_login_without_losing_admin_session(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/login');

        $response->assertOk();
        $response->assertSee('action="'.route('login.store').'"', false);
        $this->assertAuthenticatedAs($this->admin, 'admin');
    }

    public function test_customer_cannot_login_via_admin_login_page(): void
    {
        User::factory()->create([
            'email' => 'regularcustomer@gmail.com',
            'password' => bcrypt('secret123'),
            'role' => 'customer',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'regularcustomer@gmail.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('admin');
    }

    public function test_invalid_credentials_rejected_on_admin_login(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'fake@baksopesona.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest('admin');
    }

    public function test_admin_can_logout_from_admin_panel(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest('admin');

        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_seeder_preserves_existing_admin_accounts(): void
    {
        config([
            'bakso_pesona.admin_seed.email' => 'configured-admin@baksopesona.test',
            'bakso_pesona.admin_seed.password' => 'configured-safe-password',
        ]);

        $existingAdmin = User::factory()->create([
            'email' => 'existing-admin@baksopesona.test',
            'role' => 'admin',
        ]);

        $this->seed(AdminUserSeeder::class);

        $this->assertModelExists($existingAdmin);
        $this->assertDatabaseHas('users', [
            'email' => 'configured-admin@baksopesona.test',
            'role' => 'admin',
        ]);
    }
}
