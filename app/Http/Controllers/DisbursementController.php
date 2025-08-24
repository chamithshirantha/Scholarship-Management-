<?php

namespace App\Http\Controllers;

use App\Services\DisbursementService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisbursementController extends Controller
{
    protected $disbursementService;

    public function __construct(DisbursementService $disbursementService, )
    {
        $this->disbursementService = $disbursementService;
    }

    

    public function show($id): JsonResponse
    {
        try {
            $disbursement = $this->disbursementService->getDisbursementDetails($id);

            if (!$disbursement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disbursement not found'
                ], 404);
            }

            // Check if user owns this disbursement
            if ($disbursement->award->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this disbursement'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Disbursement retrieved successfully',
                'data' => $disbursement
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving disbursement: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve disbursement',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function uploadReceipt(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'receipt_file' => 'required|file|mimes:jpeg,png,pdf|max:2048'
            ]);

            // Implementation for file upload would go here
            // This would typically involve storing the file and updating the disbursement record

            return response()->json([
                'success' => true,
                'message' => 'Receipt uploaded successfully',
                'data' => [
                    'disbursement_id' => $id,
                    'file_name' => $request->file('receipt_file')->getClientOriginalName()
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error uploading receipt: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload receipt',
                'error' => 'Server error'
            ], 500);
        }
    }

    
}
