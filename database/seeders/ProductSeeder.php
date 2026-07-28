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
                ]);
        }
    }
}
