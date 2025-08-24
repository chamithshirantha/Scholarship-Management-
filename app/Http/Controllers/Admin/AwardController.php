<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AwardRequest;
use App\Models\Application;
use App\Models\Award;
use App\Models\CostCategory;
use App\Models\Disbursement;
use App\Services\AwardService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AwardController extends Controller
{
    public function store(AwardRequest $request, $applicationId): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Find the application
            $application = Application::with(['user', 'scholarship'])->findOrFail($applicationId);

            // Check if application is approved
            if ($application->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create award for non-approved application'
                ], 422);
            }

            // Check if award already exists for this application
            if ($application->award()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Award already exists for this application'
                ], 422);
            }

            $validated = $request->validated();

            // Create the award
            $award = Award::create([
                'application_id' => $applicationId,
                'user_id' => $application->user_id,
                'scholarship_id' => $application->scholarship_id,
                'total_amount' => $validated['total_amount'],
                'disbursed_amount' => 0,
                'status' => 'active',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'terms_and_conditions' => $validated['terms_and_conditions']
            ]);

            // Create initial disbursement records if disbursement schedule is provided
            if (isset($validated['disbursement_schedule'])) {
                $this->createDisbursementSchedule($award, $validated['disbursement_schedule']);
            }

            // Update application status to awarded
            $application->update(['status' => 'awarded']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Award created successfully',
                'data' => $award->load(['user', 'scholarship', 'disbursements'])
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating award: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create award',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function createSchedule(Request $request, $awardId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $award = Award::findOrFail($awardId);

            $request->validate([
                'disbursements' => 'required|array|min:1',
                'disbursements.*.cost_category_id' => 'required|exists:cost_categories,id',
                'disbursements.*.amount' => 'required|numeric|min:0',
                'disbursements.*.scheduled_date' => 'required|date|after:today'
            ]);

            $disbursements = $request->disbursements;

            foreach ($disbursements as $disbursementData) {
                Disbursement::create([
                    'award_id' => $awardId,
                    'cost_category_id' => $disbursementData['cost_category_id'],
                    'amount' => $disbursementData['amount'],
                    'status' => 'scheduled',
                    'scheduled_date' => $disbursementData['scheduled_date'],
                    'processed_by' => auth()->id()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Disbursement schedule created successfully',
                'data' => $award->load('disbursements.costCategory')
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Award not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating disbursement schedule: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create disbursement schedule',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function show($awardId): JsonResponse
    {
        try {
            $award = Award::with(['user', 'scholarship', 'disbursements.costCategory', 'disbursements.receipt'])
                        ->findOrFail($awardId);

            return response()->json([
                'success' => true,
                'message' => 'Award retrieved successfully',
                'data' => $award
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Award not found'
            ], 404);

        } catch (Exception $e) {
            Log::error('Error retrieving award: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve award',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Award::with(['user', 'scholarship']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by scholarship
            if ($request->has('scholarship_id')) {
                $query->where('scholarship_id', $request->scholarship_id);
            }

            // Filter by user
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            $awards = $query->orderBy('created_at', 'desc')->paginate(20);

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

    public function update(AwardRequest $request, $awardId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $award = Award::findOrFail($awardId);
            $validated = $request->validated();

            $award->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Award updated successfully',
                'data' => $award->load(['user', 'scholarship'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Award not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating award: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update award',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function destroy($awardId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $award = Award::findOrFail($awardId);

            // Check if there are any disbursements
            if ($award->disbursements()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete award with existing disbursements'
                ], 422);
            }

            $award->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Award deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Award not found'
            ], 404);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting award: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete award',
                'error' => 'Server error'
            ], 500);
        }
    }

    /**
     * Create disbursement schedule for an award
     */
    private function createDisbursementSchedule(Award $award, array $schedule): void
    {
        foreach ($schedule as $disbursement) {
            Disbursement::create([
                'award_id' => $award->id,
                'cost_category_id' => $disbursement['cost_category_id'],
                'amount' => $disbursement['amount'],
                'status' => 'scheduled',
                'scheduled_date' => $disbursement['scheduled_date'],
                'processed_by' => auth()->id()
            ]);
        }
    }

    /**
     * Get cost categories for dropdown
     */
    public function getCostCategories(): JsonResponse
    {
        try {
            $categories = CostCategory::where('is_active', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Cost categories retrieved successfully',
                'data' => $categories
            ]);

        } catch (Exception $e) {
            Log::error('Error retrieving cost categories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cost categories',
                'error' => 'Server error'
            ], 500);
        }
    }
}
