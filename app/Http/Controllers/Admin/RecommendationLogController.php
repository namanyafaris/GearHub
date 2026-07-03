<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Services\CollaborativeFilteringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RecommendationLogController extends Controller
{
    public function __construct(private readonly CollaborativeFilteringService $cfService) {}

    /**
     * Tampilkan daftar buyer untuk dipilih.
     */
    public function index(): View
    {
        $buyers = User::where('role', 'buyer')->get();
        return view('admin.recommendations-log.index', compact('buyers'));
    }

    /**
     * Tampilkan proses White-Box (kalkulasi CF) untuk buyer tertentu.
     */
    public function show(Request $request, User $user)
    {
        abort_if($user->role !== 'buyer', 404);

        $matrix = $this->cfService->buildUserItemMatrix();
        $targetVector = $matrix[$user->id] ?? [];
        
        $similarUsers = $this->cfService->getSimilarUsers($user->id, $matrix, 5);
        
        $productScores = [];
        foreach ($similarUsers as $simUserId => $similarityScore) {
            $simUserProducts = $matrix[$simUserId] ?? [];
            foreach ($simUserProducts as $productId => $weight) {
                if (isset($targetVector[$productId])) continue;

                $weightedScore = $weight * $similarityScore;
                if (isset($productScores[$productId])) {
                    $productScores[$productId] += $weightedScore;
                } else {
                    $productScores[$productId] = $weightedScore;
                }
            }
        }
        arsort($productScores);
        
        $recommendations = $this->cfService->getRecommendations($user->id);

        // Ambil info nama similar user
        $similarUsersDetails = [];
        foreach ($similarUsers as $id => $score) {
            $simUser = User::find($id);
            $similarUsersDetails[] = [
                'id' => $id,
                'name' => $simUser ? $simUser->name : 'Unknown',
                'score' => $score
            ];
        }

        // Ambil info nama produk score
        $productScoresDetails = [];
        foreach ($productScores as $pId => $score) {
            $prod = Product::find($pId);
            if ($prod) {
                $productScoresDetails[] = [
                    'id' => $pId,
                    'name' => $prod->name,
                    'score' => $score
                ];
            }
        }

        $data = [
            'user' => $user,
            'targetVector' => $targetVector,
            'similarUsersDetails' => $similarUsersDetails,
            'productScoresDetails' => $productScoresDetails,
            'recommendations' => $recommendations,
        ];

        if ($request->has('export') && $request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.recommendations-log.pdf', $data);
            return $pdf->download('recommendation_log_' . $user->id . '.pdf');
        }

        return view('admin.recommendations-log.show', $data);
    }
}
