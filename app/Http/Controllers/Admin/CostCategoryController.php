<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CostCategoryRequest;
use App\Models\CostCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CostCategoryController extends Controller
{
    public function store(CostCategoryRequest $request): JsonResponse
    {
        try {
            $category = CostCategory::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Cost category created successfully',
                'data' => $category
            ], 201);

        } catch (Exception $e) {
            Log::error('Error creating cost category: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create cost category',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $categories = CostCategory::where('is_active', true)->get();

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
