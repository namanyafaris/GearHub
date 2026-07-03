<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\UserInteraction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds for White-Box Testing purposes.
     * 
     * Skenario 20 User Uji (Test User):
     * - Group A (5 User): Hanya berinteraksi dengan Mouse (User 1 - 5)
     * - Group B (5 User): Hanya berinteraksi dengan Keyboard (User 6 - 10)
     * - Group C (5 User): Berinteraksi dengan Mouse dan Keyboard (User 11 - 15)
     * - Group D (5 User): Cold Start / Tanpa interaksi (User 16 - 20)
     */
    public function run(): void
    {
        // 1. Ambil sampel produk yang spesifik (5 Mouse, 5 Keyboard)
        $mice = Product::whereHas('category', fn($q) => $q->where('slug', 'mouse-gaming'))
            ->limit(5)->get();
        $keyboards = Product::whereHas('category', fn($q) => $q->where('slug', 'keyboard-gaming'))
            ->limit(5)->get();

        if ($mice->count() < 5 || $keyboards->count() < 5) {
            $this->command->warn('TestSeeder butuh minimal 5 Mouse dan 5 Keyboard untuk berjalan dengan baik.');
            return;
        }

        $now = Carbon::now();

        // 2. Buat 20 User Uji
        for ($i = 1; $i <= 20; $i++) {
            $user = User::firstOrCreate(
                ['email' => "testuser{$i}@demo.com"],
                [
                    'name' => "Test User {$i}",
                    'role' => 'buyer',
                    'password' => Hash::make('password'),
                ]
            );

            // Bersihkan interaksi lama jika ada (idempotent)
            UserInteraction::where('user_id', $user->id)->delete();

            // Group A: User 1-5 (Mouse Only)
            if ($i >= 1 && $i <= 5) {
                // User 1 berinteraksi dengan 5 mouse, User 2 dengan 4, dst untuk variasi weight
                $interactCount = 6 - $i; 
                foreach ($mice->take($interactCount) as $mouse) {
                    UserInteraction::create([
                        'user_id' => $user->id,
                        'product_id' => $mouse->id,
                        'interaction_type' => 'purchase', // bobot = 3
                        'weight' => 3.0,
                        'created_at' => $now,
                    ]);
                }
            }
            
            // Group B: User 6-10 (Keyboard Only)
            elseif ($i >= 6 && $i <= 10) {
                $interactCount = 11 - $i;
                foreach ($keyboards->take($interactCount) as $keyboard) {
                    UserInteraction::create([
                        'user_id' => $user->id,
                        'product_id' => $keyboard->id,
                        'interaction_type' => 'cart', // bobot = 2
                        'weight' => 2.0,
                        'created_at' => $now,
                    ]);
                }
            }

            // Group C: User 11-15 (Mouse + Keyboard) -> Menghubungkan A dan B
            elseif ($i >= 11 && $i <= 15) {
                // 2 Mouse
                foreach ($mice->take(2) as $mouse) {
                    UserInteraction::create([
                        'user_id' => $user->id,
                        'product_id' => $mouse->id,
                        'interaction_type' => 'view', // bobot = 1
                        'weight' => 1.0,
                        'created_at' => $now,
                    ]);
                }
                // 3 Keyboard
                foreach ($keyboards->take(3) as $keyboard) {
                    UserInteraction::create([
                        'user_id' => $user->id,
                        'product_id' => $keyboard->id,
                        'interaction_type' => 'view', // bobot = 1
                        'weight' => 1.0,
                        'created_at' => $now,
                    ]);
                }
            }

            // Group D: User 16-20 (Cold Start)
            // Sengaja tidak diberi interaksi
        }

        $this->command->info('TestSeeder berhasil membuat 20 test users dengan pola interaksi spesifik.');
    }
}
