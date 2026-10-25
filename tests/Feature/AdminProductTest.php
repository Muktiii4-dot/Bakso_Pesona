<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use RefreshDatabase;

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Bakso Kuah',
            'slug' => 'bakso-kuah',
            'description' => 'Bakso hangat berkuah gurih',
        ]);
    }

    private function createProduct(Category $category, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Bakso Test',
            'slug' => 'bakso-test',
            'description' => 'Produk test.',
            'price' => 25000,
            'stock' => 10,
            'is_available' => true,
        ], $attributes));
    }

    public function test_admin_creation_records_initial_stock_movement(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = $this->createCategory();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'name' => 'Bakso Stok Awal',
            'category_id' => $category->id,
            'price' => 25000,
            'stock' => 12,
            'description' => 'Stok awal produk.',
            'is_available' => true,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $product = Product::query()->where('slug', 'bakso-stok-awal')->firstOrFail();
        $this->assertDatabaseHas('product_stock_movements', [
            'product_id' => $product->id,
            'actor_user_id' => $admin->id,
            'movement_type' => 'initial_stock',
            'direction' => 1,
            'quantity' => 12,
        ]);
    }

    public function test_admin_stock_adjustment_requires_a_reason_and_does_not_mutate_stock_without_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = $this->createCategory();
        $product = $this->createProduct($category);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'category_id' => $category->id,
            'price' => 25000,
            'stock' => 15,
            'description' => $product->description,
            'is_available' => true,
        ]);

        $response->assertSessionHasErrors('stock_change_reason');
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertDatabaseCount('product_stock_movements', 0);
    }

    public function test_admin_stock_adjustment_records_actor_quantity_and_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = $this->createCategory();
        $product = $this->createProduct($category);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'category_id' => $category->id,
            'price' => 27500,
            'stock' => 14,
            'stock_change_reason' => 'Stok fisik hasil penghitungan ulang.',
            'description' => $product->description,
            'is_available' => true,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSame(14, $product->fresh()->stock);
        $this->assertSame('27500.00', $product->fresh()->price);
        $this->assertDatabaseHas('product_stock_movements', [
            'product_id' => $product->id,
            'actor_user_id' => $admin->id,
            'movement_type' => 'admin_adjustment',
            'direction' => 1,
            'quantity' => 4,
            'metadata->stock_before' => 10,
            'metadata->stock_after' => 14,
            'metadata->reason' => 'Stok fisik hasil penghitungan ulang.',
        ]);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'category_id' => $category->id,
            'price' => 27500,
            'stock' => 8,
            'stock_change_reason' => 'Stok rusak dipisahkan.',
            'description' => $product->description,
            'is_available' => true,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSame(8, $product->fresh()->stock);
        $this->assertDatabaseHas('product_stock_movements', [
            'product_id' => $product->id,
            'actor_user_id' => $admin->id,
            'movement_type' => 'admin_adjustment',
            'direction' => -1,
            'quantity' => 6,
            'metadata->stock_before' => 14,
            'metadata->stock_after' => 8,
            'metadata->reason' => 'Stok rusak dipisahkan.',
        ]);
    }

    public function test_admin_cannot_save_a_fractional_rupiah_price(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = $this->createCategory();
        $product = $this->createProduct($category);

        $response = $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'name' => $product->name,
            'category_id' => $category->id,
            'price' => '25000.50',
            'stock' => 10,
            'description' => $product->description,
            'is_available' => true,
        ]);

        $response->assertSessionHasErrors('price');
        $this->assertSame('25000.00', $product->fresh()->price);
    }

    public function test_admin_cannot_delete_a_product_while_stock_remains(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = $this->createCategory();
        $product = $this->createProduct($category, ['stock' => 7]);

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertModelExists($product);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertFalse($product->fresh()->is_available);
    }

    public function test_dashboard_low_stock_link_filters_to_available_products_with_five_or_fewer_units(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = $this->createCategory();
        $lowStockProduct = $this->createProduct($category, [
            'name' => 'Bakso Stok Rendah',
            'slug' => 'bakso-stok-rendah',
            'stock' => 5,
        ]);
        $this->createProduct($category, [
            'name' => 'Bakso Stok Cukup',
            'slug' => 'bakso-stok-cukup',
            'stock' => 6,
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.products.index', ['low_stock' => 1]));

        $response->assertOk();
        $response->assertSee($lowStockProduct->name);
        $response->assertDontSee('Bakso Stok Cukup');
        $response->assertSee('Menampilkan produk aktif dengan stok 5 atau kurang.');
    }
}
