<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserInteraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserInteractionSeeder extends Seeder
{
    /**
     * Seed buyer accounts, interactions, and delivered purchase history.
     */
    public function run(): void
    {
        $products = Product::query()->activeProducts()->get();

        if ($products->isEmpty()) {
            return;
        }

        $buyers = $this->createDummyBuyers();
        $purchasingBuyers = $buyers->take(8);

        foreach ($buyers as $index => $buyer) {
            $interactionCount = random_int(10, 20);
            $selectedProducts = $this->pickUniqueProducts($products, $interactionCount);

            $purchaseCount = $index < 8 ? random_int(1, 3) : 0;
            $cartCount = random_int(1, 3);
            $viewCount = $interactionCount - $purchaseCount - $cartCount;

            if ($viewCount < 0) {
                $cartCount = max(0, $cartCount + $viewCount);
                $viewCount = 0;
            }

            $types = array_merge(
                array_fill(0, $viewCount, 'view'),
                array_fill(0, $cartCount, 'cart'),
                array_fill(0, $purchaseCount, 'purchase')
            );

            shuffle($types);

            foreach ($types as $position => $type) {
                $product = $selectedProducts[$position];
                $createdAt = Carbon::now()->subDays(random_int(1, 60))->subMinutes(random_int(0, 1440));

                UserInteraction::create([
                    'user_id' => $buyer->id,
                    'product_id' => $product->id,
                    'interaction_type' => $type,
                    'weight' => $this->weightFor($type),
                    'created_at' => $createdAt,
                ]);

                if ($type === 'cart') {
                    Cart::create([
                        'buyer_id' => $buyer->id,
                        'product_id' => $product->id,
                        'quantity' => random_int(1, 3),
                    ]);
                }

                if ($type === 'purchase') {
                    $this->createDeliveredOrder($buyer, $product, $createdAt);
                }
            }
        }
    }

    /**
     * Create 15 buyer accounts with Indonesian names.
     *
     * @return Collection<int, User>
     */
    protected function createDummyBuyers(): Collection
    {
        $buyers = [
            'Andi Pratama',
            'Budi Santoso',
            'Dimas Saputra',
            'Rizky Ramadhan',
            'Fajar Nugroho',
            'Aldi Maulana',
            'Putra Hidayat',
            'Reza Purnama',
            'Nanda Wijaya',
            'Bayu Kurniawan',
            'Taufik Hidayat',
            'Cahya Anugrah',
            'Ilham Syahputra',
            'Arif Rinaldi',
            'Wahyu Prabowo',
        ];

        return collect($buyers)->values()->map(function (string $name, int $index) {
            return User::query()->firstOrCreate(
                ['email' => 'buyer' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '@gaminggear.com'],
                [
                    'name' => $name,
                    'role' => 'buyer',
                    'password' => Hash::make('password'),
                ]
            );
        });
    }

    /**
     * Pick a unique set of products for a buyer.
     *
     * @param  Collection<int, Product>  $products
     * @return array<int, Product>
     */
    protected function pickUniqueProducts(Collection $products, int $count): array
    {
        return $products
            ->shuffle()
            ->take($count)
            ->values()
            ->all();
    }

    /**
     * Return the interaction weight based on type.
     */
    protected function weightFor(string $type): float
    {
        return match ($type) {
            'view' => 1.0,
            'cart' => 2.0,
            'purchase' => 3.0,
            default => 1.0,
        };
    }

    /**
     * Create a delivered order for a purchased product.
     */
    protected function createDeliveredOrder(User $buyer, Product $product, Carbon $createdAt): void
    {
        $quantity = random_int(1, 2);
        $orderTotal = $product->price * $quantity;

        $order = Order::create([
            'buyer_id' => $buyer->id,
            'total_price' => $orderTotal,
            'status' => 'delivered',
            'shipping_name' => $buyer->name,
            'shipping_phone' => '08' . random_int(1000000000, 9999999999),
            'shipping_address' => 'Jl. Melati No. ' . random_int(1, 99) . ', Jakarta',
            'payment_method' => random_int(0, 1) === 0 ? 'transfer' : 'cod',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
        ]);
    }
}
