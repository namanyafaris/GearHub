<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed the product categories.
     */
    public function run(): void
    {
        $categories = [
            'Mouse Gaming' => 'mouse-gaming',
            'Keyboard Gaming' => 'keyboard-gaming',
            'Headset Gaming' => 'headset-gaming',
            'Mousepad Gaming' => 'mousepad-gaming',
            'Controller Gaming' => 'controller-gaming',
            'Webcam Gaming' => 'webcam-gaming',
        ];

        foreach ($categories as $name => $slug) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $name . ' untuk kebutuhan gaming gear.',
                    'image' => 'categories/' . Str::slug($name) . '.jpg',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
