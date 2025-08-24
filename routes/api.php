<?php

use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\AwardController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\CostCategoryController;
use App\Http\Controllers\Admin\DisbursementController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\ApplicationController as StudentApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AwardController as StudentAwardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ScholarshipController as StudentScholarshipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes - Authentication required
Route::middleware('auth:sanctum')->group(function () {
    
    // Common routes for all authenticated users
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // ==================== STUDENT ROUTES ====================
    Route::middleware('student')->group(function () {
        
        // Scholarships
        Route::get('/scholarships', [StudentScholarshipController::class, 'index']);
        Route::get('/scholarships/{id}', [ScholarshipController::class, 'show']);
        
        // Applications
        Route::post('/applications', [StudentApplicationController::class, 'store']);
        Route::get('/my-applications', [StudentApplicationController::class, 'myApplications']);
        Route::get('/applications/{id}', [StudentApplicationController::class, 'show']);
        Route::get('/applications/{id}/logs', [StudentApplicationController::class, 'logs']);
        
        // Documents
        Route::post('/applications/{id}/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{id}', [DocumentController::class, 'show']);
        Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
        Route::get('/applications/{id}/documents', [DocumentController::class, 'getApplicationDocuments']);
        
        // Awards
        Route::get('/my-awards', [StudentAwardController::class, 'myAwards']);
        Route::get('/awards/{awardId}/disbursements', [StudentAwardController::class, 'disbursements']);
        
        // Disbursements
        Route::get('/disbursements/{id}', [DisbursementController::class, 'show']);
        Route::post('/disbursements/{id}/receipts', [DisbursementController::class, 'uploadReceipt']);
    });

    // ==================== ADMIN ROUTES ====================
    Route::middleware('admin')->prefix('admin')->group(function () {
        
        // Scholarships Management
        Route::post('/scholarships', [ScholarshipController::class, 'store']);
        Route::put('/scholarships/{id}', [ScholarshipController::class, 'update']);
        Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy']);
        
        // Applications Management
        Route::get('/applications', [ApplicationController::class, 'index']);
        Route::get('/applications/{id}', [ApplicationController::class, 'show']);
        Route::post('/applications/{id}/review', [ApplicationController::class, 'review']);
        
        // Cost Categories Management
        Route::post('/cost-categories', [CostCategoryController::class, 'store']);
        Route::get('/cost-categories', [CostCategoryController::class, 'index']);
        
        // Budgets Management
        Route::post('/scholarships/{id}/budgets', [BudgetController::class, 'setBudget']);
        Route::get('/scholarships/{id}/budgets', [BudgetController::class, 'getBudget']);
        
        // Awards Management
        Route::post('/applications/{id}/award', [AwardController::class, 'store']);
        Route::post('/awards/{awardId}/schedules', [AwardController::class, 'createSchedule']);
        
        // Disbursements Management
        Route::post('/disbursements/{id}/pay', [DisbursementController::class, 'markAsPaid']);
        Route::get('/disbursements', [DisbursementController::class, 'index']);
        
        // Receipts Management
        Route::post('/receipts/{id}/verify', [ReceiptController::class, 'verify']);
        
        // Reports
        Route::get('/reports/scholarships/{id}', [ReportController::class, 'scholarshipReport']);
        Route::get('/reports/awards/{awardId}', [ReportController::class, 'awardReport']);
        
        // Documents Management ( access)
        Route::get('/documents/{id}', [DocumentController::class, 'show']);
        Route::get('/documents/{id}/download', [DocumentController::class, 'download']);
        Route::get('/applications/{id}/documents', [DocumentController::class, 'getApplicationDocuments']);
    });

});

