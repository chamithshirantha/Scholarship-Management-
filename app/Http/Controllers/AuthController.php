<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register($request->validated());

            return response()->json([
                'message' => 'User registered successfully',
                'user' => $result['user'],
                'token' => $result['token'],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (Exception $e) {
            Log::error('Exception occurred while registering user: ' . $e->getMessage());

            return response()->json([
                'message' => 'Registration failed. Please try again later.',
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'message' => 'Login successful',
                'user' => $result['user'],
                'token' => $result['token'],
                'role' => $result['role'],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid credentials',
                'errors' => $e->errors(),
            ], 401);

        } catch (Exception $e) {
            Log::error('Exception occurred while logging in: ' . $e->getMessage());

            return response()->json([
                'message' => 'Login failed. Please try again later.',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'message' => 'Logged out successfully'
            ]);

        } catch (Exception $e) {
            Log::error('Exception occurred while logging out: ' . $e->getMessage());

            return response()->json([
                'message' => 'Logout failed. Please try again.',
            ], 500);
        }
    }

    public function profile(Request $request): JsonResponse
    {
        try {

            if (!$request->user()) {
                return response()->json([
                    'message' => 'Unauthenticated. Please login first.',
                ], 401);
            }

            $profile = $this->authService->getUserProfile($request->user());

            return response()->json([
                'message' => 'Profile retrieved successfully',
                'user' => $profile,
            ]);

        } catch (Exception $e) {
            Log::error('Exception occurred while fetching profile: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to retrieve profile. Please try again.',
            ], 500);
        }
    }
}
