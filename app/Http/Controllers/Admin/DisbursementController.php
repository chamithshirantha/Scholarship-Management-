<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DisbursementService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisbursementController extends Controller
{
    protected $disbursementService;

    public function __construct(DisbursementService $disbursementService)
    {
        $this->disbursementService = $disbursementService;
    }

    

    public function markAsPaid(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'payment_details' => 'required|array',
                'payment_details.transaction_id' => 'required|string',
                'payment_details.payment_method' => 'required|string'
            ]);

            $updated = $this->disbursementService->markAsPaid($id, $request->payment_details);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Disbursement not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Disbursement marked as paid successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Error marking disbursement as paid: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update disbursement status',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $status = $request->get('status');
            
            if ($status) {
                $disbursements = $this->disbursementService->getDisbursementsByStatus($status);
            } else {
                $disbursements = $this->disbursementService->getAllDisbursements();
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
