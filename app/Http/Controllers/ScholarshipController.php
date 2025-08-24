<?php

namespace App\Http\Controllers;

use App\Services\ScholarshipService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ScholarshipController extends Controller
{
    protected $scholarshipService;

    public function __construct(ScholarshipService $scholarshipService)
    {
        $this->scholarshipService = $scholarshipService;
    }

    public function index(): JsonResponse
    {
        try {
            $scholarships = $this->scholarshipService->getActiveScholarships();

            return response()->json([
                'success' => true,
                'message' => 'Scholarships retrieved successfully',
                'data' => $scholarships
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving scholarships: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve scholarships',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $scholarship = $this->scholarshipService->getScholarshipById($id);

            if (!$scholarship) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scholarship not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Scholarship retrieved successfully',
                'data' => $scholarship
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving scholarship: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve scholarship',
                'error' => 'Server error'
            ], 500);
        }
    }
}
