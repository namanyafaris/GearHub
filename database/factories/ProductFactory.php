<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected string $searchTerm = 'gaming gear';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'category_id' => 1,
            'name' => Str::title($name),
            'slug' => Str::slug($name . '-' . fake()->unique()->bothify('####')),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(100000, 1000000),
            'stock' => fake()->numberBetween(20, 100),
            'image' => 'gaming gear',
            'is_active' => true,
        ];
    }

    /**
     * Create mouse gaming products.
     */
    public function mouseGaming(): static
    {
        $this->searchTerm = 'gaming mouse';
        return $this->state(fn() => $this->categoryState(
            [
                'Rexus Daxa M84 Pro RGB',
                'Fantech X15 Phantom',
                'Logitech G102 Lightsync',
                'Razer DeathAdder Essential',
                'SteelSeries Rival 3',
                'Viper Mini SE',
            ],
            150000,
            1500000
        ));
    }

    /**
     * Create keyboard gaming products.
     */
    public function keyboardGaming(): static
    {
        $this->searchTerm = 'gaming keyboard';
        return $this->state(fn() => $this->categoryState(
            [
                'Fantech Atom MK876 TKL',
                'Rexus Legionare MX9.1',
                'Royal Kludge RK84',
                'Logitech G413 SE',
                'Keychron K6 Pro',
                'Redragon K552 Kumara',
            ],
            200000,
            2500000
        ));
    }

    /**
     * Create headset gaming products.
     */
    public function headsetGaming(): static
    {
        $this->searchTerm = 'gaming headset';
        return $this->state(fn() => $this->categoryState(
            [
                'Rexus Vonix F55',
                'Fantech HG20 Fusion',
                'Logitech G231 Prodigy',
                'HyperX Cloud Stinger 2',
                'Razer BlackShark V2 X',
                'Sades Spirit Wolf',
            ],
            150000,
            1800000
        ));
    }

    /**
     * Create mousepad gaming products.
     */
    public function mousepadGaming(): static
    {
        $this->searchTerm = 'gaming mousepad';
        return $this->state(fn() => $this->categoryState(
            [
                'Fantech Sven MP25',
                'Rexus Musepad RXM-103',
                'HyperX Fury S Pro',
                'SteelSeries QcK Medium',
                'Razer Gigantus V2',
                'Logitech G240 Cloth',
            ],
            50000,
            400000
        ));
    }

    /**
     * Create controller gaming products.
     */
    public function controllerGaming(): static
    {
        $this->searchTerm = 'gaming controller';
        return $this->state(fn() => $this->categoryState(
            [
                'Fantech Shooter GP11',
                'Rexus Gladius RX-G1',
                '8BitDo SN30 Pro',
                'Xbox Wireless Controller',
                'DualShock 4 V2',
                'GameSir T4 Pro',
            ],
            200000,
            1200000
        ));
    }

    /**
     * Create webcam gaming products.
     */
    public function webcamGaming(): static
    {
        $this->searchTerm = 'gaming webcam';
        return $this->state(fn() => $this->categoryState(
            [
                'Fantech Luminous C30',
                'Rexus Daxa C1',
                'Logitech C270 HD',
                'AverMedia PW310P',
                'Razer Kiyo X',
                'Brio 100 HD',
            ],
            300000,
            2000000
        ));
    }

    /**
     * Build a category-specific product state.
     *
     * @param  array<int, string>  $names
     * @return array<string, mixed>
     */
    protected function categoryState(array $names, int $minPrice, int $maxPrice): array
    {
        $baseName = fake()->randomElement($names);
        $suffixes = ['', ' Pro', ' RGB', ' SE', ' Max', ' V2', ' Lite'];
        $name = $baseName . fake()->randomElement($suffixes);

        $idNames = [
            'gaming mouse' => 'mouse',
            'gaming keyboard' => 'keyboard',
            'gaming headset' => 'headset',
            'gaming mousepad' => 'mousepad',
            'gaming controller' => 'controller',
            'gaming webcam' => 'webcam',
        ];
        
        $imageName = $idNames[$this->searchTerm] ?? 'placeholder';
        
        $templates = [
            "Tingkatkan pengalaman gaming Anda dengan perlengkapan berkualitas tinggi ini. Didesain secara ergonomis untuk kenyamanan maksimal selama sesi bermain panjang. Dilengkapi dengan fitur unggulan yang memastikan respon cepat dan presisi tinggi.",
            "Lengkapi setup gaming-mu dengan perlengkapan terbaru ini! Material premium menjamin keawetan produk, sementara desainnya yang stylish membuat meja kerjamu terlihat semakin profesional. Cocok untuk gamer kasual maupun atlet e-sports.",
            "Performa tanpa kompromi! Perlengkapan ini dirancang khusus untuk memenangkan setiap pertandingan. Dengan latensi super rendah dan kustomisasi fitur yang lengkap, Anda memegang kendali penuh di tangan Anda.",
            "Jangan biarkan perlengkapan standar menghambat potensimu. Perlengkapan ini hadir dengan spesifikasi kelas atas: durabilitas tinggi, pencahayaan RGB yang memukau, dan akurasi sempurna untuk menunjang performa gaming kompetitif.",
            "Solusi terbaik untuk kebutuhan gaming harianmu. Perlengkapan ini menawarkan kombinasi sempurna antara harga yang masuk akal dan spesifikasi mumpuni. Dibuat dengan build-quality yang kokoh dan desain yang sangat nyaman digunakan."
        ];

        return [
            'name' => $name,
            'slug' => Str::slug($name . '-' . fake()->unique()->bothify('####')),
            'description' => fake()->randomElement($templates),
            'price' => fake()->numberBetween($minPrice, $maxPrice),
            'stock' => fake()->numberBetween(20, 100),
            'image' => 'products/' . $imageName . '.jpg',
            'is_active' => true,
        ];
    }
}
