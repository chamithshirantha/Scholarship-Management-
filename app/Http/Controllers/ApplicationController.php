<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
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

    public function store(ApplicationRequest $request): JsonResponse
    {
        try {
            $application = $this->applicationService->createApplication(
                $request->user()->id,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => $application
            ], 201);

        } catch (Exception $e) {
            Log::error('Error creating application: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function myApplications(Request $request): JsonResponse
    {
        try {
            $applications = $this->applicationService->getUserApplications($request->user()->id);

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

            // Check if user owns this application
            if ($application->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this application'
                ], 403);
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

    public function logs($id): JsonResponse
    {
        try {
            $logs = $this->applicationService->getApplicationLogs($id);

            // Check if user owns this application
            $application = $this->applicationService->getApplicationById($id);
            if ($application->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this application logs'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Application logs retrieved successfully',
                'data' => $logs
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving application logs: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve application logs',
                'error' => 'Server error'
            ], 500);
        }
    }
}
