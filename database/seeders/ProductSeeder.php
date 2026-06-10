<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Seed 120 products across all categories.
     */
    public function run(): void
    {
        // Create dummy placeholder image if doesn't exist
        $this->createPlaceholderImage();

        $categories = Category::query()->get()->keyBy('slug');

        $catalog = [
            'mouse-gaming' => ['method' => 'mouseGaming', 'count' => 20],
            'keyboard-gaming' => ['method' => 'keyboardGaming', 'count' => 20],
            'headset-gaming' => ['method' => 'headsetGaming', 'count' => 20],
            'mousepad-gaming' => ['method' => 'mousepadGaming', 'count' => 20],
            'controller-gaming' => ['method' => 'controllerGaming', 'count' => 20],
            'webcam-gaming' => ['method' => 'webcamGaming', 'count' => 20],
        ];

        foreach ($catalog as $slug => $config) {
            $category = $categories->get($slug);

            if (! $category) {
                continue;
            }

            Product::factory()
                ->count($config['count'])
                ->{$config['method']}()
                ->create([
                    'category_id' => $category->id,
                ])
                ->each(function (Product $product) {
                    $this->copyPlaceholderImage($product);
                });
        }
    }

    /**
     * Create a placeholder image file.
     */
    private function createPlaceholderImage(): void
    {
        $path = storage_path('app/public/products');

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        // Create simple placeholder using GD (fallback: copy existing or skip)
        if (!file_exists($path . '/placeholder.jpg')) {
            // Create 400x300 gaming-themed placeholder image
            $image = imagecreatetruecolor(400, 300);

            // Gaming colors: dark background with neon accent
            $darkBg = imagecolorallocate($image, 20, 20, 30);
            $neonOrange = imagecolorallocate($image, 255, 107, 0);
            $neonPurple = imagecolorallocate($image, 168, 85, 247);
            $textColor = imagecolorallocate($image, 255, 255, 255);

            // Fill background
            imagefilledrectangle($image, 0, 0, 400, 300, $darkBg);

            // Add neon borders
            imagerectangle($image, 5, 5, 395, 295, $neonOrange);
            imagerectangle($image, 10, 10, 390, 290, $neonPurple);

            // Draw geometric gaming pattern (diagonal lines)
            for ($i = 0; $i < 400; $i += 40) {
                imageline($image, $i, 0, $i + 300, 300, imagecolorallocate($image, 100, 100, 120));
            }

            // Add text
            imagestring($image, 5, 140, 140, 'GAMING GEAR', $textColor);

            // Save image
            imagejpeg($image, $path . '/placeholder.jpg', 85);
            imagedestroy($image);
        }
    }

    /**
     * Copy placeholder image for product.
     */
    private function copyPlaceholderImage(Product $product): void
    {
        try {
            $path = storage_path('app/public/products');
            $placeholder = $path . '/placeholder.jpg';

            if (file_exists($placeholder)) {
                // Create unique filename per product
                $filename = 'products/' . $product->id . '-' . Str::slug($product->name) . '.jpg';
                $dest = $path . '/' . basename($filename);

                copy($placeholder, $dest);
                $product->update(['image' => $filename]);
            }
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}
