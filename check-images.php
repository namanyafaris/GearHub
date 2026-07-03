<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use Illuminate\Support\Facades\DB;

$categories = Category::select('id', 'name', 'slug')->orderBy('id')->get();

echo "=== Daftar Kategori ===\n";
foreach ($categories as $cat) {
    echo "  ID:{$cat->id} | slug: {$cat->slug} | {$cat->name}\n";
}
