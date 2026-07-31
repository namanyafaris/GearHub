<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Models\UserInteraction;
use Illuminate\Support\Collection;

/**
 * CollaborativeFilteringService
 *
 * Implementasi User-based Collaborative Filtering dengan Cosine Similarity
 * untuk sistem rekomendasi produk pada e-commerce gaming gear.
 *
 * Algoritma:
 * 1. Bangun User-Item Matrix dari user_interactions
 * 2. Hitung Cosine Similarity antar user
 * 3. Ambil Top-5 Similar Users
 * 4. Generate rekomendasi dari produk yang disukai similar users
 * 5. Fallback untuk cold start problem
 *
 * @author Skripsi S1 - E-commerce Gaming Gear
 * @version 1.0
 */
class CollaborativeFilteringService
{
    /**
     * LANGKAH 1: USER-ITEM MATRIX
     *
     * Membangun matrix 2 dimensi yang merepresentasikan interaksi user terhadap produk.
     *
     * Struktur:
     * - Baris: user_id
     * - Kolom: product_id
     * - Nilai cell: akumulasi weight dari semua interaksi
     *   (view=1.0, cart=2.0, purchase=3.0)
     *
     * Contoh matrix 3x4:
     *     prod1  prod2  prod3  prod4
     * u1:   3      1      0      2
     * u2:   1      0      3      0
     * u3:   2      2      1      3
     *
     * @return array User-Item Matrix [user_id => [product_id => weight]]
     */
    public function buildUserItemMatrix(): array
    {
        // Ambil semua interaksi user dengan eager loading
        $interactions = UserInteraction::with(['user', 'product'])
            ->select('user_id', 'product_id', 'weight')
            ->get();

        // Initialize matrix kosong
        $matrix = [];

        // Populate matrix dengan weight dari setiap interaksi
        foreach ($interactions as $interaction) {
            $userId = $interaction->user_id;
            $productId = $interaction->product_id;
            $weight = $interaction->weight;

            // Initialize user row jika belum ada
            if (!isset($matrix[$userId])) {
                $matrix[$userId] = [];
            }

            // Akumulasi weight jika produk sudah pernah diinteraksi user
            if (isset($matrix[$userId][$productId])) {
                $matrix[$userId][$productId] += $weight;
            } else {
                $matrix[$userId][$productId] = $weight;
            }
        }

        return $matrix;
    }

    /**
     * LANGKAH 2: COSINE SIMILARITY
     *
     * Menghitung kesamaan/kemiripan antara dua user berdasarkan preferensi produk mereka.
     *
     * Rumus Cosine Similarity:
     * similarity(A, B) = (A · B) / (||A|| × ||B||)
     *
     * Dimana:
     * - A · B = dot product (sum of A[i] * B[i])
     * - ||A|| = magnitude/norm vector A (sqrt of sum of A[i]²)
     * - ||B|| = magnitude/norm vector B (sqrt of sum of B[i]²)
     *
     * Range: 0 hingga 1
     * - 1 = user sangat mirip (preferensi sama)
     * - 0 = user berbeda sama sekali (tidak ada overlap produk)
     *
     * @param array $vectorA User A's preference vector [product_id => weight]
     * @param array $vectorB User B's preference vector [product_id => weight]
     * @return float Similarity score (0.0 - 1.0)
     */
    public function calculateCosineSimilarity(array $vectorA, array $vectorB): float
    {
        // Edge case: jika salah satu vector kosong
        if (empty($vectorA) || empty($vectorB)) {
            return 0.0;
        }

        // Step 1: Hitung dot product (A · B)
        $dotProduct = 0.0;
        foreach ($vectorA as $productId => $weightA) {
            if (isset($vectorB[$productId])) {
                $dotProduct += $weightA * $vectorB[$productId];
            }
        }

        // Jika tidak ada common product, similarity = 0
        if ($dotProduct == 0) {
            return 0.0;
        }

        // Step 2: Hitung magnitude vector A (||A||)
        $magnitudeA = 0.0;
        foreach ($vectorA as $weight) {
            $magnitudeA += $weight * $weight;
        }
        $magnitudeA = sqrt($magnitudeA);

        // Step 3: Hitung magnitude vector B (||B||)
        $magnitudeB = 0.0;
        foreach ($vectorB as $weight) {
            $magnitudeB += $weight * $weight;
        }
        $magnitudeB = sqrt($magnitudeB);

        // Edge case: jika magnitude = 0
        if ($magnitudeA == 0 || $magnitudeB == 0) {
            return 0.0;
        }

        // Step 4: Hitung cosine similarity
        return $dotProduct / ($magnitudeA * $magnitudeB);
    }

    /**
     * LANGKAH 3: TOP-5 SIMILAR USERS
     *
     * Mencari user-user yang paling mirip dengan target user berdasarkan preferensi produk.
     *
     * Proses:
     * 1. Hitung similarity antara target user dan semua user lain
     * 2. Urutkan dari score tertinggi ke terendah
     * 3. Exclude target user sendiri
     * 4. Ambil top-N user (default 5)
     *
     * @param int $userId Target user ID
     * @param array $matrix User-Item Matrix
     * @param int $topN Jumlah similar users yang ingin diambil (default: 5)
     * @return array Similar users [user_id => similarity_score]
     */
    public function getSimilarUsers(int $userId, array $matrix, int $topN = 5): array
    {
        // Edge case: user tidak ditemukan di matrix
        if (!isset($matrix[$userId])) {
            return [];
        }

        $targetUserVector = $matrix[$userId];
        $similarities = [];

        // Hitung similarity terhadap semua user lain
        foreach ($matrix as $otherId => $otherVector) {
            // Skip user itu sendiri
            if ($otherId === $userId) {
                continue;
            }

            // Hitung cosine similarity
            $similarity = $this->calculateCosineSimilarity(
                $targetUserVector,
                $otherVector
            );

            // Hanya simpan jika ada kesamaan
            if ($similarity > 0) {
                $similarities[$otherId] = $similarity;
            }
        }

        // Sort by similarity score (tertinggi ke terendah)
        arsort($similarities);

        // Ambil top-N
        return array_slice($similarities, 0, $topN, preserve_keys: true);
    }

    /**
     * LANGKAH 4: GENERATE REKOMENDASI
     *
     * Menghasilkan list produk yang direkomendasikan untuk user berdasarkan
     * preferensi dari top similar users.
     *
     * Strategi:
     * 1. Kumpulkan semua produk yang pernah diinteraksi top similar users
     * 2. Exclude produk yang sudah pernah diinteraksi target user
     * 3. Bobot setiap produk dengan similarity score dari similar users
     * 4. Urutkan produk berdasarkan total bobot (tertinggi = prioritas utama)
     * 5. Kembalikan maksimal 8 produk terbaik
     *
     * @param int $userId Target user ID
     * @param array $matrix User-Item Matrix
     * @param array $similarUsers Top similar users [user_id => similarity_score]
     * @return Collection Produk yang direkomendasikan
     */
    private function generateRecommendations(
        int $userId,
        array $matrix,
        array $similarUsers
    ): Collection {
        // Edge case: tidak ada similar users
        if (empty($similarUsers)) {
            return Collection::make([]);
        }

        // Step 1: Ambil produk yang sudah diinteraksi user aktif
        $userInteractedProducts = $matrix[$userId] ?? [];

        // Step 2: Hitung weighted score untuk setiap produk dari similar users
        $productScores = [];

        foreach ($similarUsers as $similarUserId => $similarityScore) {
            $similarUserProducts = $matrix[$similarUserId] ?? [];

            foreach ($similarUserProducts as $productId => $weight) {
                // Skip jika user sudah pernah interaksi produk ini
                if (isset($userInteractedProducts[$productId])) {
                    continue;
                }

                // Bobot produk = weight dari similar user × similarity score
                $weightedScore = $weight * $similarityScore;

                if (isset($productScores[$productId])) {
                    $productScores[$productId] += $weightedScore;
                } else {
                    $productScores[$productId] = $weightedScore;
                }
            }
        }

        // Step 3: Sort berdasarkan score (tertinggi ke terendah)
        arsort($productScores);

        // Step 4: Ambil top 8 produk
        $topProductIds = array_slice(
            array_keys($productScores),
            0,
            8
        );

        // Step 5: Fetch produk dari database
        return Product::whereIn('id', $topProductIds)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($product) use ($topProductIds) {
                return array_search($product->id, $topProductIds);
            })
            ->values();
    }

    /**
     * FALLBACK: COLD START PROBLEM
     *
     * Menangani kasus-kasus khusus ketika collaborative filtering
     * tidak dapat menghasilkan rekomendasi:
     *
     * 1. User Baru (belum ada interaksi):
     *    → Tampilkan best-seller products (most popular)
     *
     * 2. Similar Users Tidak Ditemukan:
     *    → Tampilkan produk terlaris dari kategori favorit user
     *
     * @param int $userId User ID
     * @param array $matrix User-Item Matrix
     * @return Collection Produk fallback
     */
    public function getFallbackRecommendations(int $userId, array $matrix = null): Collection
    {
        // Build matrix jika tidak diberikan
        if (is_null($matrix)) {
            $matrix = $this->buildUserItemMatrix();
        }

        // Case 1: User baru (tidak ada di matrix)
        if (!isset($matrix[$userId])) {
            // Ambil 8 produk dengan total weight tertinggi di user_interactions
            $popularProducts = Product::whereHas('userInteractions')
                ->where('is_active', true)
                ->withCount(['userInteractions as total_weight' => function ($q) {
                    $q->selectRaw('sum(weight) as total_weight');
                }])
                ->orderByDesc('total_weight')
                ->limit(8)
                ->get();

            // Absolute Fallback: Jika database interaksi masih KOSONG SAMA SEKALI (Hari pertama rilis)
            if ($popularProducts->isEmpty()) {
                return Product::where('is_active', true)->inRandomOrder()->limit(8)->get();
            }

            return $popularProducts;
        }

        // Case 2: User sudah ada tapi tidak ada similar users
        // Ambil produk terlaris dari kategori favorit

        // Cari kategori yang paling sering diinteraksi user
        $userProducts = array_keys($matrix[$userId]);

        $favoriteCategory = Product::whereIn('id', $userProducts)
            ->selectRaw('category_id, count(*) as interaction_count')
            ->groupBy('category_id')
            ->orderByDesc('interaction_count')
            ->first();

        if (!$favoriteCategory) {
            // Fallback: best-seller dari semua kategori
            $popularProducts = Product::whereHas('userInteractions')
                ->where('is_active', true)
                ->withCount(['userInteractions as total_weight' => function ($q) {
                    $q->selectRaw('sum(weight)');
                }])
                ->orderByDesc('total_weight')
                ->limit(8)
                ->get();

            if ($popularProducts->isEmpty()) {
                return Product::where('is_active', true)->inRandomOrder()->limit(8)->get();
            }

            return $popularProducts;
        }

        // Ambil best-seller dari favorite category
        return Product::where('category_id', $favoriteCategory->category_id)
            ->where('is_active', true)
            ->whereNotIn('id', $userProducts)
            ->withCount(['userInteractions as total_weight' => function ($q) {
                $q->selectRaw('sum(weight)');
            }])
            ->orderByDesc('total_weight')
            ->limit(8)
            ->get();
    }

    /**
     * PUBLIC API: GET RECOMMENDATIONS
     *
     * Main method untuk mendapatkan rekomendasi produk untuk user.
     *
     * Flow:
     * 1. Bangun user-item matrix
     * 2. Ambil top-5 similar users
     * 3. Generate rekomendasi dari similar users
     * 4. Jika tidak ada rekomendasi, gunakan fallback
     * 5. Return max 8 produk
     *
     * @param int $userId User ID
     * @return Collection Produk rekomendasi (max 8 items)
     */
    public function getRecommendations(int $userId): Collection
    {
        // Step 1: Bangun matrix
        $matrix = $this->buildUserItemMatrix();

        // Step 2: Cari top-5 similar users
        $similarUsers = $this->getSimilarUsers($userId, $matrix, topN: 5);

        // Step 3: Generate rekomendasi
        $recommendations = $this->generateRecommendations(
            $userId,
            $matrix,
            $similarUsers
        );

        // Step 4: Fallback jika tidak ada rekomendasi
        if ($recommendations->isEmpty()) {
            $recommendations = $this->getFallbackRecommendations($userId, $matrix);
        }

        // Step 5: Return max 8 produk
        return $recommendations->take(8);
    }

    /**
     * HELPER: Check apakah user termasuk "new user" (cold start)
     *
     * @param int $userId User ID
     * @return bool True jika user belum ada interaksi
     */
    public function isNewUser(int $userId): bool
    {
        return !UserInteraction::where('user_id', $userId)->exists();
    }
}
