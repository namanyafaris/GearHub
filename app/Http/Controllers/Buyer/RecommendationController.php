<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\CollaborativeFilteringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly CollaborativeFilteringService $cfService
    ) {}

    /**
     * GET /recommendations
     *
     * Endpoint JSON untuk testing & debugging algoritma Collaborative Filtering.
     * Mengembalikan detail lengkap proses rekomendasi:
     * - user_id, method yang digunakan, similar_users, dan daftar produk.
     *
     * Digunakan sebagai bukti white-box testing pada Bab IV skripsi.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Guest: tidak ada data interaksi, tampilkan best-seller info
        if (!$user) {
            $recommendations = $this->cfService->getFallbackRecommendations(0);

            return response()->json([
                'user_id'         => null,
                'method'          => 'fallback_bestseller',
                'reason'          => 'User belum login (guest)',
                'similar_users'   => [],
                'recommendations' => $recommendations->map(fn($p) => [
                    'id'       => $p->id,
                    'name'     => $p->name,
                    'price'    => $p->price,
                    'format_price' => $p->format_price,
                    'category' => $p->category?->name,
                    'image'    => $p->image,
                ]),
            ]);
        }

        $userId = $user->id;

        // Step 1: Bangun matrix
        $matrix = $this->cfService->buildUserItemMatrix();

        // Step 2: Cek apakah user ada di matrix (cold start check)
        if (!isset($matrix[$userId])) {
            $recommendations = $this->cfService->getFallbackRecommendations($userId, $matrix);

            return response()->json([
                'user_id'         => $userId,
                'method'          => 'fallback_bestseller',
                'reason'          => 'User baru — belum ada data interaksi (cold start)',
                'similar_users'   => [],
                'recommendations' => $recommendations->map(fn($p) => [
                    'id'       => $p->id,
                    'name'     => $p->name,
                    'price'    => $p->price,
                    'format_price' => $p->format_price,
                    'category' => $p->category?->name,
                    'image'    => $p->image,
                ]),
            ]);
        }

        // Step 3: Cari similar users
        $similarUsers = $this->cfService->getSimilarUsers($userId, $matrix, topN: 5);

        // Step 4: Generate rekomendasi
        $recommendations = $this->cfService->getRecommendations($userId);

        // Tentukan method yang digunakan
        $method = !empty($similarUsers)
            ? 'collaborative_filtering'
            : 'fallback_category';

        $reason = match ($method) {
            'collaborative_filtering' => 'Rekomendasi berdasarkan Top-' . count($similarUsers) . ' similar users',
            'fallback_category'       => 'Tidak ditemukan similar users — fallback ke kategori favorit',
            default                   => 'Unknown',
        };

        return response()->json([
            'user_id'         => $userId,
            'method'          => $method,
            'reason'          => $reason,
            'similar_users'   => collect($similarUsers)->map(fn($score, $id) => [
                'user_id'          => $id,
                'similarity_score' => round($score, 6),
            ])->values(),
            'recommendations' => $recommendations->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->name,
                'price'    => $p->price,
                'format_price' => $p->format_price,
                'category' => $p->category?->name,
                'image'    => $p->image,
            ]),
        ]);
    }
}
