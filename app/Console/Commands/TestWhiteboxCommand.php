<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Product;
use App\Services\CollaborativeFilteringService;
use Illuminate\Console\Command;

class TestWhiteboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:whitebox {userId : ID of the target buyer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run White-Box testing for Collaborative Filtering algorithm (CLI version)';

    public function __construct(private readonly CollaborativeFilteringService $cfService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = (int) $this->argument('userId');
        $user = User::find($userId);

        if (!$user || !$user->isBuyer()) {
            $this->error("Buyer dengan ID {$userId} tidak ditemukan.");
            return Command::FAILURE;
        }

        $this->info("==========================================================");
        $this->info(" WHITE-BOX TESTING: COLLABORATIVE FILTERING (USER ID: {$userId}) ");
        $this->info("==========================================================\n");

        // 1. Matrix
        $this->warn(">>> STEP 1: MEMBANGUN USER-ITEM MATRIX");
        $matrix = $this->cfService->buildUserItemMatrix();
        
        if (!isset($matrix[$userId])) {
            $this->error("User belum memiliki interaksi. Menggunakan Fallback.");
            return Command::SUCCESS;
        }

        $targetVector = $matrix[$userId];
        $this->info("Vektor Preferensi Target User ({$user->name}):");
        $this->table(['Product ID', 'Accumulated Weight'], $this->formatVector($targetVector));
        $this->line("");

        // 2 & 3. Cosine Similarity & Similar Users
        $this->warn(">>> STEP 2 & 3: MENGHITUNG COSINE SIMILARITY & TOP SIMILAR USERS");
        $similarUsers = $this->cfService->getSimilarUsers($userId, $matrix, 5);

        if (empty($similarUsers)) {
            $this->error("Tidak ada similar user yang ditemukan (Similarity = 0).");
            return Command::SUCCESS;
        }

        $simTable = [];
        foreach ($similarUsers as $simUserId => $score) {
            $simUser = User::find($simUserId);
            $simTable[] = [
                'User ID' => $simUserId,
                'Name' => $simUser ? $simUser->name : 'Unknown',
                'Cosine Similarity' => number_format($score, 4)
            ];
        }
        $this->table(['User ID', 'Name', 'Cosine Similarity Score'], $simTable);
        $this->line("");

        // Detail 1 contoh perhitungan jika ada
        $firstSimUserId = array_key_first($similarUsers);
        $firstSimScore = $similarUsers[$firstSimUserId];
        $simVector = $matrix[$firstSimUserId];
        
        $this->info("Contoh Perhitungan Detail (Target ID {$userId} vs Similar ID {$firstSimUserId}):");
        $dotProduct = 0;
        foreach ($targetVector as $pId => $w) {
            if (isset($simVector[$pId])) $dotProduct += $w * $simVector[$pId];
        }
        $magA = sqrt(array_sum(array_map(fn($w) => $w * $w, $targetVector)));
        $magB = sqrt(array_sum(array_map(fn($w) => $w * $w, $simVector)));
        
        $this->line("Dot Product (A . B) = {$dotProduct}");
        $this->line("Magnitude A (||A||) = " . number_format($magA, 4));
        $this->line("Magnitude B (||B||) = " . number_format($magB, 4));
        $this->line("Similarity = Dot / (MagA * MagB) = " . number_format($firstSimScore, 4));
        $this->line("");

        // 4. Generate Rekomendasi (Weighted Score)
        $this->warn(">>> STEP 4: MENGHITUNG WEIGHTED SCORE PRODUK REKOMENDASI");
        $productScores = [];
        foreach ($similarUsers as $simUserId => $similarityScore) {
            foreach ($matrix[$simUserId] as $productId => $weight) {
                if (isset($targetVector[$productId])) continue; // Skip already interacted

                $weightedScore = $weight * $similarityScore;
                if (isset($productScores[$productId])) {
                    $productScores[$productId] += $weightedScore;
                } else {
                    $productScores[$productId] = $weightedScore;
                }
            }
        }
        
        arsort($productScores);
        $recTable = [];
        foreach ($productScores as $pId => $score) {
            $product = Product::find($pId);
            $recTable[] = [
                'Product ID' => $pId,
                'Product Name' => $product ? $product->name : 'Unknown',
                'Weighted Score' => number_format($score, 4)
            ];
        }
        
        if (empty($recTable)) {
            $this->error("Similar users tidak memiliki produk unik untuk direkomendasikan.");
        } else {
            $this->table(['Product ID', 'Product Name', 'Total Weighted Score'], $recTable);
        }
        $this->line("");

        // 5. Final output
        $this->warn(">>> STEP 5: FINAL TOP 8 RECOMMENDATIONS");
        $finalRecs = $this->cfService->getRecommendations($userId);
        $finalTable = [];
        foreach ($finalRecs as $idx => $prod) {
            $finalTable[] = [
                'Rank' => $idx + 1,
                'Product ID' => $prod->id,
                'Product Name' => $prod->name,
                'Category' => $prod->category->name ?? 'N/A'
            ];
        }
        $this->table(['Rank', 'Product ID', 'Product Name', 'Category'], $finalTable);
        $this->line("");
        $this->info("White-Box testing selesai secara sukses!");

        return Command::SUCCESS;
    }

    private function formatVector(array $vector): array
    {
        $formatted = [];
        foreach ($vector as $productId => $weight) {
            $formatted[] = [
                'Product ID' => $productId,
                'Weight' => $weight
            ];
        }
        return $formatted;
    }
}
