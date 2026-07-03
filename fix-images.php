<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "=== Memperbarui Path Gambar Produk (1 Gambar per Kategori) ===\n\n";

$categories = Category::all();

$categoryImages = [
    'mouse-gaming' => 'products/mouse.jpg',
    'keyboard-gaming' => 'products/keyboard.jpg',
    'headset-gaming' => 'products/headset.jpg',
    'mousepad-gaming' => 'products/mousepad.jpg',
    'controller-gaming' => 'products/controller.jpg',
    'webcam-gaming' => 'products/webcam.jpg',
];

$totalUpdated = 0;

foreach ($categories as $cat) {
    if (isset($categoryImages[$cat->slug])) {
        $imagePath = $categoryImages[$cat->slug];
        
        $updated = Product::where('category_id', $cat->id)->update(['image' => $imagePath]);
        
        echo "✓ Kategori '{$cat->name}': Diperbarui {$updated} produk menjadi '{$imagePath}'\n";
        $totalUpdated += $updated;
    } else {
        echo "✗ Kategori '{$cat->name}': Tidak ada mapping gambar default\n";
    }
}

echo "\nSelesai! Total produk diperbarui: {$totalUpdated}\n";
