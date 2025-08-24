<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Services\ApplicationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $applications = $this->applicationService->getAllApplications(
                $request->get('status'),
                $request->get('scholarship_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Applications retrieved successfully',
                'data' => $applications
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving applications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve applications',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $application = $this->applicationService->getApplicationById($id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Application retrieved successfully',
                'data' => $application
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving application: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve application',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function review(ReviewRequest $request, $id): JsonResponse
    {
        try {
            $application = $this->applicationService->reviewApplication(
                $id,
                $request->validated()['status'],
                $request->validated()['comments'],
                auth()->user()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Application reviewed successfully',
                'data' => $application
            ]);

        } catch (Exception $e) {
            Log::error('Error reviewing application: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to review application',
                'error' => 'Server error'
            ], 500);
        }
    }
}
