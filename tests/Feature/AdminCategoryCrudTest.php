<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Blackbox Testing - CRUD Kategori (Admin)
 *
 * Menguji fungsionalitas CRUD kategori dari perspektif blackbox:
 * input → proses → output, tanpa melihat internal kode.
 */
class AdminCategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // =====================================================================
    // INDEX (READ ALL)
    // =====================================================================

    /** @test */
    public function admin_can_view_category_list_page(): void
    {
        Category::create(['name' => 'Mouse Gaming', 'slug' => 'mouse-gaming']);
        Category::create(['name' => 'Keyboard Gaming', 'slug' => 'keyboard-gaming']);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Mouse Gaming');
        $response->assertSee('Keyboard Gaming');
    }

    /** @test */
    public function guest_cannot_access_category_list(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function buyer_cannot_access_admin_category_list(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);

        $response = $this->actingAs($buyer)->get(route('admin.categories.index'));

        $response->assertForbidden();
    }

    // =====================================================================
    // CREATE
    // =====================================================================

    /** @test */
    public function admin_can_view_create_category_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.categories.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_store_category_without_image(): void
    {
        $data = [
            'name' => 'Headset Gaming',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'name' => 'Headset Gaming',
            'slug' => 'headset-gaming',
        ]);
    }

    /** @test */
    public function admin_can_store_category_with_image(): void
    {
        $image = UploadedFile::fake()->image('headset.jpg', 400, 300);

        $data = [
            'name' => 'Headset Gaming',
            'image' => $image,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Headset Gaming']);

        $category = Category::where('name', 'Headset Gaming')->first();
        $this->assertNotNull($category->image);
        Storage::assertExists($category->image);
    }

    /** @test */
    public function store_category_fails_without_name(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), []);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('categories', 0);
    }

    /** @test */
    public function store_category_rejects_invalid_image(): void
    {
        $data = [
            'name' => 'Test Category',
            'image' => UploadedFile::fake()->create('spreadsheet.xlsx', 500),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), $data);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('categories', 0);
    }

    // =====================================================================
    // UPDATE
    // =====================================================================

    /** @test */
    public function admin_can_view_edit_category_form(): void
    {
        $category = Category::create(['name' => 'Mouse Gaming', 'slug' => 'mouse-gaming']);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.edit', $category));

        $response->assertStatus(200);
        $response->assertSee('Mouse Gaming');
    }

    /** @test */
    public function admin_can_update_category_name(): void
    {
        $category = Category::create(['name' => 'Mouse Gaming', 'slug' => 'mouse-gaming']);

        $data = ['name' => 'Mouse Wireless'];

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Mouse Wireless',
            'slug' => 'mouse-wireless',
        ]);
    }

    /** @test */
    public function admin_can_update_category_image(): void
    {
        $category = Category::create(['name' => 'Mouse Gaming', 'slug' => 'mouse-gaming']);
        $newImage = UploadedFile::fake()->image('new_mouse.jpg', 400, 300);

        $data = [
            'name' => $category->name,
            'image' => $newImage,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), $data);

        $response->assertRedirect(route('admin.categories.index'));
        $category->refresh();
        $this->assertNotNull($category->image);
        Storage::assertExists($category->image);
    }

    // =====================================================================
    // DELETE
    // =====================================================================

    /** @test */
    public function admin_can_delete_category(): void
    {
        $category = Category::create(['name' => 'Mouse Gaming', 'slug' => 'mouse-gaming']);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
