<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Models\CostCategory;
use App\Models\Scholarship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function setBudget(BudgetRequest $request, $scholarshipId): JsonResponse
    {
        try {
            $scholarship = Scholarship::findOrFail($scholarshipId);
            
            // Validate that categories exist
            $categoryIds = array_column($request->categories, 'cost_category_id');
            $existingCategories = CostCategory::whereIn('id', $categoryIds)->count();
            
            if ($existingCategories !== count($request->categories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more cost categories do not exist'
                ], 422);
            }
            
            // Process each budget category
            foreach ($request->categories as $categoryData) {
                Budget::updateOrCreate(
                    [
                        'scholarship_id' => $scholarshipId,
                        'cost_category_id' => $categoryData['cost_category_id']
                    ],
                    [
                        'allocated_amount' => $categoryData['amount'],
                        'utilized_amount' => 0 // Reset utilized amount when updating allocation
                    ]
                );
            }
            
            // Get updated budget with categories
            $budget = Budget::with('costCategory')
                ->where('scholarship_id', $scholarshipId)
                ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Budget set successfully',
                'data' => [
                    'scholarship' => $scholarship,
                    'budget' => $budget,
                    'total_allocated' => $budget->sum('allocated_amount'),
                    'total_utilized' => $budget->sum('utilized_amount')
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to set budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * View budget summary for a scholarship
     */
    public function getBudget($scholarshipId): JsonResponse
    {
        try {
            $scholarship = Scholarship::findOrFail($scholarshipId);
            
            $budget = Budget::with('costCategory')
                ->where('scholarship_id', $scholarshipId)
                ->get();
            
            if ($budget->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No budget set for this scholarship'
                ], 404);
            }
            
            $totalAllocated = $budget->sum('allocated_amount');
            $totalUtilized = $budget->sum('utilized_amount');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'scholarship' => $scholarship,
                    'budget' => $budget,
                    'summary' => [
                        'total_allocated' => $totalAllocated,
                        'total_utilized' => $totalUtilized,
                        'total_remaining' => $totalAllocated - $totalUtilized
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve budget',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
