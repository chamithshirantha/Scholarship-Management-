<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScholarshipRequest;
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

    public function store(ScholarshipRequest $request): JsonResponse
    {
        try {
            $scholarship = $this->scholarshipService->createScholarship($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Scholarship created successfully',
                'data' => $scholarship
            ], 201);

        } catch (Exception $e) {
            Log::error('Error creating scholarship: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create scholarship',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function update(ScholarshipRequest $request, $id): JsonResponse
    {
        try {
            $updated = $this->scholarshipService->updateScholarship($id, $request->validated());

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scholarship not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Scholarship updated successfully',
                'data' => $this->scholarshipService->getScholarshipById($id)
            ]);

        } catch (Exception $e) {
            Log::error('Error updating scholarship: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update scholarship',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deleted = $this->scholarshipService->deleteScholarship($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scholarship not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Scholarship deleted successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Error deleting scholarship: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete scholarship',
                'error' => 'Server error'
            ], 500);
        }
    }
}
