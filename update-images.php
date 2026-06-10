<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$products = DB::table('products')->get();

foreach ($products as $product) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $product->name));
    $files = glob('storage/app/public/products/' . $slug . '-*.jpg');

    if (!empty($files)) {
        $localPath = 'products/' . basename($files[0]);
        DB::table('products')->where('id', $product->id)->update(['image' => $localPath]);
        echo "✓ Product {$product->id}: {$localPath}\n";
    } else {
        echo "✗ Product {$product->id} ({$product->name}): No image found\n";
    }
}

echo "\nDone!\n";
