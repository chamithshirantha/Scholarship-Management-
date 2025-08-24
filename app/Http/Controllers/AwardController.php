<?php

namespace App\Http\Controllers;

use App\Services\AwardService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AwardController extends Controller
{
    protected $awardService;

    public function __construct(AwardService $awardService)
    {
        $this->awardService = $awardService;
    }

    public function myAwards(Request $request): JsonResponse
    {
        try {
            $awards = $this->awardService->getUserAwards($request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Awards retrieved successfully',
                'data' => $awards
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving awards: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve awards',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function disbursements($awardId): JsonResponse
    {
        try {
            $disbursements = $this->awardService->getAwardDisbursements($awardId);

            // Check if user owns this award
            $award = $this->awardService->getAwardById($awardId);
            if (!$award || $award->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this award disbursements'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Disbursements retrieved successfully',
                'data' => $disbursements
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving disbursements: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve disbursements',
                'error' => 'Server error'
            ], 500);
        }
    }
}
