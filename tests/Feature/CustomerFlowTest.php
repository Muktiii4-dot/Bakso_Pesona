<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRating;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_home_and_products(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Halus Spesial',
            'slug' => 'bakso-halus-spesial',
            'description' => 'Bakso daging sapi asli pilihan.',
            'price' => 25000,
            'is_available' => true,
        ]);

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Bakso Kuah');
        $homeResponse->assertSee('title="Keranjang belanja"', false);
        $homeResponse->assertSee(route('cart.index'), false);
        $homeResponse->assertDontSee('id="cart-drawer"', false);
        $homeResponse->assertSee('data-testid="home-red-wave"', false);

        $productResponse = $this->get('/produk');
        $productResponse->assertStatus(200);
        $productResponse->assertSee('Bakso Halus Spesial');

        $detailResponse = $this->get("/produk/{$product->slug}");
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Bakso Halus Spesial');
    }

    public function test_cart_page_has_a_link_back_to_home(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Kembali ke Beranda');
        $response->assertSee('href="'.route('home').'"', false);
    }

    public function test_cart_quantity_controls_increment_and_decrement_within_limits(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $minimumProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Minimum',
            'slug' => 'bakso-minimum',
            'description' => 'Bakso untuk menguji batas minimum.',
            'price' => 25000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $maximumProduct = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Stok Terbatas',
            'slug' => 'bakso-stok-terbatas',
            'description' => 'Bakso untuk menguji batas stok.',
            'price' => 25000,
            'stock' => 2,
            'is_available' => true,
        ]);

        $response = $this
            ->withSession([
                'bakso_pesona_cart' => [
                    (string) $minimumProduct->id => 1,
                    (string) $maximumProduct->id => 2,
                ],
            ])
            ->get(route('cart.index'));
        $html = preg_replace('/\s+/', ' ', $response->getContent());

        $response->assertOk();
        $this->assertStringContainsString('name="quantity" value="1" disabled aria-label="Kurangi jumlah Bakso Minimum"', $html);
        $this->assertStringContainsString('name="quantity" value="2" aria-label="Tambah jumlah Bakso Minimum"', $html);
        $this->assertStringContainsString('name="quantity" value="1" aria-label="Kurangi jumlah Bakso Stok Terbatas"', $html);
        $this->assertStringContainsString('name="quantity" value="2" disabled aria-label="Tambah jumlah Bakso Stok Terbatas"', $html);
        $response->assertDontSee('Perbarui');
    }

    public function test_customer_cannot_view_other_customers_order(): void
    {
        $user1 = User::factory()->create(['role' => 'customer']);
        $user2 = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'order_number' => 'BP-USER1-001',
            'user_id' => $user1->id,
            'recipient_name' => 'User 1',
            'phone' => '08123456789',
            'shipping_address' => 'Alamat User 1',
            'subtotal' => 50000,
            'status' => 'pending_confirmation',
            'payment_status' => 'awaiting_confirmation',
        ]);

        // User 2 mencoba membuka pesanan milik User 1 -> wajib 404
        $response = $this->actingAs($user2, 'customer')->get("/pesanan/{$order->id}");
        $response->assertStatus(404);

        // User 1 membuka pesanannya sendiri -> sukses 200
        $ownResponse = $this->actingAs($user1, 'customer')->get("/pesanan/{$order->id}");
        $ownResponse->assertStatus(200);
        $ownResponse->assertSee('BP-USER1-001');
    }

    public function test_admin_account_cannot_use_customer_login_or_customer_pages(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@baksopesona.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]);

        $loginResponse = $this->post(route('login.store'), [
            'email' => 'admin@baksopesona.com',
            'password' => 'secret123',
        ]);

        $loginResponse->assertSessionHasErrors(['email']);
        $this->assertGuest('customer');

        $protectedResponse = $this->actingAs($admin, 'admin')->get(route('checkout.create'));

        $protectedResponse->assertRedirect(route('login'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_guest_can_add_products_to_the_session_cart(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Tamu',
            'slug' => 'bakso-tamu',
            'description' => 'Produk untuk menguji keranjang tamu.',
            'price' => 25000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $response = $this->post(route('cart.store', $product), [
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('bakso_pesona_cart', [
            (string) $product->id => 2,
        ]);
    }

    public function test_admin_cannot_use_cart_or_submit_customer_checkout(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Admin',
            'slug' => 'bakso-admin',
            'description' => 'Produk untuk menguji batas role.',
            'price' => 25000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'admin');

        $this->get(route('cart.index'))->assertStatus(403);
        $this->post(route('cart.store', $product), ['quantity' => 1])->assertStatus(403);
        $this->patch(route('cart.update', $product), ['quantity' => 1])->assertStatus(403);
        $this->delete(route('cart.destroy', $product))->assertStatus(403);

        $this->get(route('checkout.create'))->assertRedirect(route('login'));
        $this->withSession([
            'bakso_pesona_cart' => [(string) $product->id => 1],
        ])->post(route('checkout.store'), [
            'recipient_name' => 'Admin Bakso',
            'phone' => '08123456789',
            'shipping_address' => 'Alamat administrator',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_login_and_logout_keep_customer_routes_protected(): void
    {
        $customer = User::factory()->create([
            'name' => 'Customer Login User',
            'password' => bcrypt('secret123'),
            'role' => 'customer',
        ]);

        $loginResponse = $this->withSession([
            'url.intended' => route('account.profile'),
        ])->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'secret123',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($customer, 'customer');

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee('Customer Login User');

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest('customer');
        $this->get(route('account.profile'))->assertRedirect(route('login'));
    }

    public function test_guest_account_icon_opens_customer_login(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('href="'.route('login').'"', false);
        $response->assertSee('aria-label="Login akun"', false);
    }

    public function test_storefront_does_not_render_an_admin_as_a_customer(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin In Customer Guard',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin, 'customer')->get(route('home'));

        $response->assertOk();
        $response->assertSee('aria-label="Login akun"', false);
        $response->assertDontSee('Admin In Customer Guard');
        $response->assertDontSee('aria-label="Menu akun pengguna"', false);
        $response->assertDontSee('Panel Admin');
        $response->assertDontSee('Keluar dari Panel Admin');
    }

    public function test_admin_account_icon_opens_customer_login_and_valid_login_switches_role(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin Session User',
            'role' => 'admin',
        ]);
        $customer = User::factory()->create([
            'name' => 'Customer Session User',
            'password' => bcrypt('customer-secret'),
            'role' => 'customer',
        ]);

        $adminLoginResponse = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $adminLoginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');

        $loginPageResponse = $this->get(route('login'));

        $loginPageResponse->assertOk();
        $loginPageResponse->assertSee('action="'.route('login.store').'"', false);

        $storefrontResponse = $this->get(route('home'));

        $storefrontResponse->assertOk();
        $storefrontResponse->assertSee('href="'.route('login').'"', false);
        $storefrontResponse->assertSee('aria-label="Login akun"', false);
        $storefrontResponse->assertDontSee('aria-label="Menu akun pengguna"', false);
        $storefrontResponse->assertDontSee('Admin Session User');
        $storefrontResponse->assertSee('title="Keranjang belanja"', false);
        $storefrontResponse->assertSee(route('cart.index'), false);

        $refreshedStorefrontResponse = $this->get(route('home'));
        $refreshedStorefrontResponse->assertSee('aria-label="Login akun"', false);
        $refreshedStorefrontResponse->assertDontSee('Admin Session User');
        $refreshedStorefrontResponse->assertSee('title="Keranjang belanja"', false);

        $loginResponse = $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'customer-secret',
        ]);

        $loginResponse->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($customer, 'customer');
        $this->assertAuthenticatedAs($admin, 'admin');
        $this->get(route('account.profile'))->assertOk();

        $customerStorefrontResponse = $this->get(route('home'));
        $customerStorefrontResponse->assertSee('aria-label="Menu akun pengguna"', false);
        $customerStorefrontResponse->assertSee('Customer Session User');
        $customerStorefrontResponse->assertDontSee('Admin Session User');
        $customerStorefrontResponse->assertDontSee('Panel Admin');

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest('customer');
        $this->assertAuthenticatedAs($admin, 'admin');
        $guestStorefrontResponse = $this->get(route('home'));
        $guestStorefrontResponse->assertSee('aria-label="Login akun"', false);
        $guestStorefrontResponse->assertDontSee('Admin Session User');
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_admin_logout_does_not_end_the_customer_session(): void
    {
        $admin = User::factory()->create([
            'password' => bcrypt('admin-secret'),
            'role' => 'admin',
        ]);
        $customer = User::factory()->create([
            'password' => bcrypt('customer-secret'),
            'role' => 'customer',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'admin-secret',
        ])->assertRedirect(route('admin.dashboard'));

        $this->post(route('login.store'), [
            'email' => $customer->email,
            'password' => 'customer-secret',
        ])->assertRedirect(route('home'));

        $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));

        $this->assertGuest('admin');
        $this->assertAuthenticatedAs($customer, 'customer');
        $this->get(route('account.profile'))->assertOk();
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_session_remains_active_when_customer_login_credentials_are_invalid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create([
            'password' => bcrypt('customer-secret'),
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->from(route('login'))
            ->post(route('login.store'), [
                'email' => $customer->email,
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors([
            'email' => 'Email atau password yang kamu masukkan salah.',
        ]);
        $response->assertRedirect(route('login'));
        $this->assertGuest('customer');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_public_registration_cannot_assign_an_admin_role(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Pendaftar Baru',
            'email' => 'pendaftar@example.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'pendaftar@example.test',
            'role' => 'customer',
        ]);
    }

    public function test_admin_storefront_matches_public_cart_controls_while_backend_stays_protected(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Admin',
            'slug' => 'bakso-admin',
            'description' => 'Produk untuk menguji tampilan katalog admin.',
            'price' => 25000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin, 'admin')->get(route('home'));

        $response->assertOk();
        $response->assertSee('aria-label="Login akun"', false);
        $response->assertSee('title="Keranjang belanja"', false);
        $response->assertSee(route('cart.index'), false);

        $productResponse = $this->get(route('products.show', $product->slug));

        $productResponse->assertOk();
        $productResponse->assertSee(route('cart.store', $product), false);
        $this->get(route('cart.index'))->assertForbidden();
        $this->get(route('account.profile'))->assertRedirect(route('login'));
        $this->get(route('orders.index'))->assertRedirect(route('login'));
    }

    public function test_customer_checkout_uses_current_product_price_and_authenticated_owner(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Checkout',
            'slug' => 'bakso-checkout',
            'description' => 'Produk untuk menguji total pesanan.',
            'price' => 25000,
            'stock' => 5,
            'is_available' => true,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $anotherCustomer = User::factory()->create(['role' => 'customer']);

        $checkoutPage = $this->actingAs($customer, 'customer')
            ->withSession([
                'bakso_pesona_cart' => [(string) $product->id => 2],
            ])
            ->get(route('checkout.create'));
        $checkoutPage->assertOk();
        $checkoutToken = $checkoutPage->viewData('checkoutToken');
        $checkoutData = [
            'checkout_token' => $checkoutToken,
            'recipient_name' => 'Pelanggan',
            'phone' => '08123456789',
            'shipping_address' => 'Alamat pelanggan',
            'user_id' => $anotherCustomer->id,
            'subtotal' => 1,
        ];
        $response = $this->post(route('checkout.store'), $checkoutData);

        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('checkout.success', $order));
        $this->assertSame($customer->id, $order->user_id);
        $this->assertSame('50000.00', $order->subtotal);
        $this->assertSame('0.00', $order->discount_amount);
        $this->assertNull($order->shipping_cost);
        $this->assertNull($order->total_amount);
        $this->assertNull($order->payment_due_at);
        $this->assertSame(Order::PAYMENT_AWAITING_CONFIRMATION, $order->payment_status);
        $this->post(route('checkout.store'), $checkoutData)->assertRedirect(route('checkout.success', $order));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'unit_price' => 25000,
            'quantity' => 2,
            'subtotal' => 50000,
        ]);
    }

    public function test_checkout_rejects_cart_quantity_above_current_stock(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Stok',
            'slug' => 'bakso-stok',
            'description' => 'Produk untuk menguji validasi stok checkout.',
            'price' => 25000,
            'stock' => 2,
            'is_available' => true,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $checkoutPage = $this->actingAs($customer, 'customer')
            ->withSession([
                'bakso_pesona_cart' => [(string) $product->id => 2],
            ])
            ->get(route('checkout.create'));
        $checkoutPage->assertOk();
        $product->update(['stock' => 1]);

        $response = $this->post(route('checkout.store'), [
            'checkout_token' => $checkoutPage->viewData('checkoutToken'),
            'recipient_name' => 'Pelanggan',
            'phone' => '08123456789',
            'shipping_address' => 'Alamat pelanggan',
        ]);

        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_customer_login_attempts_are_rate_limited(): void
    {
        foreach (range(1, 6) as $attempt) {
            $this->post(route('login.store'), [
                'email' => 'not-found@example.test',
                'password' => 'incorrect-password',
            ]);
        }

        $response = $this->post(route('login.store'), [
            'email' => 'not-found@example.test',
            'password' => 'incorrect-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_customer_can_submit_one_rating_for_their_completed_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($customer, 'user')->create([
            'status' => Order::STATUS_COMPLETED,
            'payment_status' => Order::PAYMENT_PAID,
        ]);

        $response = $this->actingAs($customer, 'customer')
            ->post(route('orders.rating.store', $order), [
                'rating' => 5,
                'comment' => 'Baksonya enak dan sampai dengan baik.',
            ]);

        $response->assertRedirect(route('orders.show', $order));
        $this->assertDatabaseHas('order_ratings', [
            'order_id' => $order->id,
            'customer_user_id' => $customer->id,
            'rating' => 5,
            'original_comment' => 'Baksonya enak dan sampai dengan baik.',
            'moderation_status' => 'pending',
        ]);
        $this->assertDatabaseHas('order_events', [
            'order_id' => $order->id,
            'actor_user_id' => $customer->id,
            'event_type' => 'rating_submitted',
        ]);

        $duplicateResponse = $this->post(route('orders.rating.store', $order), [
            'rating' => 4,
        ]);

        $duplicateResponse->assertSessionHasErrors('rating');
        $this->assertDatabaseCount('order_ratings', 1);
    }

    public function test_customer_does_not_need_to_approve_the_admin_calculated_total(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($customer, 'user')->create([
            'subtotal' => 50000,
            'shipping_cost' => 12000,
            'total_amount' => 62000,
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION,
        ]);
        $this->freezeTime();

        $detailResponse = $this->actingAs($customer, 'customer')->get(route('orders.show', $order));
        $detailResponse->assertOk();
        $detailResponse->assertDontSee('Setujui Total Akhir');
        $this->assertNull($order->fresh()->total_approved_at);
        $this->assertNull($order->fresh()->payment_due_at);
        $this->assertDatabaseMissing('order_events', [
            'order_id' => $order->id,
            'event_type' => 'total_approved_by_customer',
        ]);
    }

    public function test_customer_only_sees_qris_instructions_for_qris_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $qrisOrder = Order::factory()->for($customer, 'user')->create([
            'payment_method' => 'qris',
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'payment_instructions_issued_at' => now()->subMinute(),
            'payment_due_at' => now()->addHours(24),
            'subtotal' => 50000,
            'shipping_cost' => 12000,
            'total_amount' => 62000,
        ]);
        $legacyOrder = Order::factory()->for($customer, 'user')->create([
            'payment_method' => 'bank_transfer',
            'payment_status' => Order::PAYMENT_AWAITING_PAYMENT,
            'payment_instructions_issued_at' => now()->subHour(),
            'payment_due_at' => now()->addHours(23),
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total_amount' => 60000,
        ]);

        $qrisResponse = $this->actingAs($customer, 'customer')
            ->get(route('orders.show', $qrisOrder));

        $qrisResponse->assertOk();
        $qrisResponse->assertSee('QRIS resmi belum tersedia.');
        $qrisResponse->assertSee('Rp 62.000');
        $qrisResponse->assertDontSee('Transfer Bank');

        $legacyResponse = $this->get(route('orders.show', $legacyOrder));

        $legacyResponse->assertOk();
        $legacyResponse->assertSee('Instruksi pembayaran pesanan lama perlu dikonfirmasi.');
        $legacyResponse->assertDontSee('Pindai QRIS statis resmi');
        $legacyResponse->assertDontSee('Transfer Bank');
    }

    public function test_customer_cannot_rate_another_customers_completed_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $anotherCustomer = User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($anotherCustomer, 'user')->create([
            'status' => Order::STATUS_COMPLETED,
            'payment_status' => Order::PAYMENT_PAID,
        ]);

        $response = $this->actingAs($customer, 'customer')
            ->post(route('orders.rating.store', $order), [
                'rating' => 5,
            ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('order_ratings', 0);
    }

    public function test_product_page_only_displays_approved_ratings(): void
    {
        $category = Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bakso Rating',
            'slug' => 'bakso-rating',
            'description' => 'Produk untuk menguji ulasan publik.',
            'price' => 25000,
            'stock' => 10,
            'is_available' => true,
        ]);
        $customer = User::factory()->create(['role' => 'customer']);

        foreach ([
            ['status' => 'approved', 'comment' => 'Ulasan yang boleh tampil.', 'rating' => 5],
            ['status' => 'pending', 'comment' => 'Ulasan yang menunggu moderasi.', 'rating' => 3],
            ['status' => 'rejected', 'comment' => 'Ulasan yang ditolak.', 'rating' => 1],
        ] as $review) {
            $order = Order::factory()->for($customer, 'user')->create([
                'status' => Order::STATUS_COMPLETED,
                'payment_status' => Order::PAYMENT_PAID,
            ]);
            OrderItem::factory()->for($order)->create([
                'product_id' => $product->id,
            ]);
            OrderRating::factory()->for($order)->create([
                'customer_user_id' => $customer->id,
                'rating' => $review['rating'],
                'original_comment' => $review['comment'],
                'moderation_status' => $review['status'],
            ]);
        }

        $response = $this->get(route('products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Ulasan yang boleh tampil.');
        $response->assertSee('5.0 / 5');
        $response->assertDontSee('Ulasan yang menunggu moderasi.');
        $response->assertDontSee('Ulasan yang ditolak.');
    }
}
