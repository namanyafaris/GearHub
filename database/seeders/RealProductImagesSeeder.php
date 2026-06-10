<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RealProductImagesSeeder extends Seeder
{
	/**
	 * Category-to-image file mapping.
	 * Files should be placed in storage/app/public/products/
	 */
	private array $categoryImages = [
		'mouse-gaming' => 'mouse.jpg',
		'keyboard-gaming' => 'keyboard.jpg',
		'headset-gaming' => 'headset.jpg',
		'mousepad-gaming' => 'mousepad.jpg',
		'controller-gaming' => 'controller.jpg',
		'webcam-gaming' => 'webcam.jpg',
	];

	/**
	 * Seed products with real images by category.
	 */
	public function run(): void
	{
		$sourcePath = storage_path('app/public/products');

		// Check if source images exist
		$missingFiles = [];
		foreach ($this->categoryImages as $category => $filename) {
			if (!file_exists($sourcePath . '/' . $filename)) {
				$missingFiles[] = $filename;
			}
		}

		if (!empty($missingFiles)) {
			$this->command->error('⚠️  Missing image files in storage/app/public/products/:');
			foreach ($missingFiles as $file) {
				$this->command->error("   - $file");
			}
			$this->command->info("\n📥 Please download these files from Unsplash/Pexels and place them in storage/app/public/products/\n");
			return;
		}

		$this->command->info('✅ Found all source images. Starting seeding...');

		// Process each category
		foreach ($this->categoryImages as $categorySlug => $sourceFile) {
			$this->seedCategoryImages($categorySlug, $sourceFile, $sourcePath);
		}

		$this->command->info("\n✅ All product images seeded successfully!");
	}

	/**
	 * Seed images for a specific category.
	 */
	private function seedCategoryImages(string $categorySlug, string $sourceFile, string $sourcePath): void
	{
		$sourceFilePath = $sourcePath . '/' . $sourceFile;
		$products = Product::whereHas('category', function ($query) use ($categorySlug) {
			$query->where('slug', $categorySlug);
		})->get();

		if ($products->isEmpty()) {
			$this->command->warn("No products found for category: $categorySlug");
			return;
		}

		$imageContent = file_get_contents($sourceFilePath);
		$this->command->line("Processing $categorySlug: {$products->count()} products");

		// Assign images to all products in this category
		foreach ($products as $index => $product) {
			// Generate unique filename
			$filename = 'products/' . $product->id . '-' . Str::slug($product->name) . '.jpg';
			$fullPath = $sourcePath . '/' . basename($filename);

			// Copy image to new filename
			file_put_contents($fullPath, $imageContent);

			// Update product with image path
			$product->update(['image' => $filename]);

			// Progress indicator
			if (($index + 1) % 5 === 0) {
				$this->command->getOutput()->write('.');
			}
		}

		$this->command->line(" Done!");
	}
}
