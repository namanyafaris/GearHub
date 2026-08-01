<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Blackbox Testing - CRUD Produk (Admin)
 *
 * Menguji fungsionalitas CRUD produk dari perspektif blackbox:
 * input → proses → output, tanpa melihat internal kode.
 */
class AdminProductCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake storage agar tidak upload file sungguhan
        Storage::fake();

        // Buat user admin dan kategori untuk keperluan test
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->category = Category::create([
            'name' => 'Mouse Gaming',
            'slug' => 'mouse-gaming',
        ]);
    }

    // =====================================================================
    // INDEX (READ ALL)
    // =====================================================================

    /** @test */
    public function admin_can_view_product_list_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Produk');
    }

    /** @test */
    public function guest_cannot_access_product_list(): void
    {
        $response = $this->get(route('admin.products.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function buyer_cannot_access_admin_product_list(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer)->get(route('admin.products.index'));

        $response->assertForbidden();
    }

    // =====================================================================
    // CREATE
    // =====================================================================

    /** @test */
    public function admin_can_view_create_product_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_store_product_without_image(): void
    {
        $data = [
            'name' => 'Rexus Daxa M84 Pro',
            'category_id' => $this->category->id,
            'description' => 'Mouse gaming premium dengan sensor terbaik.',
            'price' => 350000,
            'stock' => 50,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $data);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'name' => 'Rexus Daxa M84 Pro',
            'category_id' => $this->category->id,
            'price' => 350000,
            'stock' => 50,
        ]);
    }

    /** @test */
    public function admin_can_store_product_with_image(): void
    {
        $image = UploadedFile::fake()->image('mouse.jpg', 400, 300);

        $data = [
            'name' => 'Logitech G102',
            'category_id' => $this->category->id,
            'description' => 'Mouse gaming entry level terbaik.',
            'price' => 250000,
            'stock' => 100,
            'is_active' => true,
            'image' => $image,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $data);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Logitech G102']);

        // Pastikan file gambar tersimpan di disk
        $product = Product::where('name', 'Logitech G102')->first();
        $this->assertNotNull($product->image);
        Storage::assertExists($product->image);
    }

    /** @test */
    public function store_product_fails_without_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), []);

        $response->assertSessionHasErrors(['name', 'category_id', 'price', 'stock']);
        $this->assertDatabaseCount('products', 0);
    }

    /** @test */
    public function store_product_rejects_invalid_image(): void
    {
        $data = [
            'name' => 'Test Product',
            'category_id' => $this->category->id,
            'price' => 100000,
            'stock' => 10,
            'image' => UploadedFile::fake()->create('document.pdf', 1024),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), $data);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('products', 0);
    }

    // =====================================================================
    // UPDATE
    // =====================================================================

    /** @test */
    public function admin_can_view_edit_product_form(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    /** @test */
    public function admin_can_update_product_data(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id, 'price' => 200000]);

        $data = [
            'name' => 'Updated Product Name',
            'category_id' => $this->category->id,
            'description' => 'Deskripsi baru.',
            'price' => 500000,
            'stock' => 75,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $data);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 500000,
            'stock' => 75,
        ]);
    }

    /** @test */
    public function admin_can_update_product_image(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $newImage = UploadedFile::fake()->image('new_mouse.jpg', 400, 300);

        $data = [
            'name' => $product->name,
            'category_id' => $this->category->id,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => $newImage,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $data);

        $response->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertNotNull($product->image);
        Storage::assertExists($product->image);
    }

    // =====================================================================
    // DELETE
    // =====================================================================

    /** @test */
    public function admin_can_delete_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
