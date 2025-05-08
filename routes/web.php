<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomObjectController;
use App\Http\Controllers\ItemController;

Route::get('/', function () {
    return view('welcome');
});

// Add a direct web route that mimics the API welcome functionality for testing
Route::get('/web-api-test', function () {
    $connected = true;
    $message = 'Welcome to the Web API test! Database connection successful.';

    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $connected = false;
        $message = 'Welcome to the Web API test! Database connection failed: ' . $e->getMessage();
    }

    return response()->json([
        'success' => $connected,
        'message' => $message,
        'current_time' => now()->toString(),
        'date' => now()->toDateString(),
        'time' => now()->toTimeString()
    ]);
});

// TEMPORARY WORKAROUND - Duplicate API routes in web.php
// These will be removed once the API routing issue is resolved

// Main API endpoints in web routes (temporary)
Route::prefix('api')->group(function () {
    // Welcome endpoint - minimal version
    Route::get('/welcome', function () {
        return response()->json([
            'success' => true,
            'message' => 'Welcome to the API (via web routes)!',
            'current_time' => now()->toString()
        ]);
    });

    // Status endpoint with DB check
    Route::get('/status', function () {
        $connected = true;
        $message = 'Welcome to the API! Database connection successful.';

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $connected = false;
            $message = 'Welcome to the API! Database connection failed: ' . $e->getMessage();
        }

        return response()->json([
            'success' => $connected,
            'message' => $message,
            'current_time' => now()->toString(),
            'date' => now()->toDateString(),
            'time' => now()->toTimeString()
        ]);
    });

    // Simple ping endpoint
    Route::get('/ping', function () {
        return response()->json(['message' => 'pong']);
    });

    // API v1 Routes
    Route::prefix('v1')->group(function () {
        // Posts resource routes
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/{post}', [PostController::class, 'show']);
        Route::put('/posts/{post}', [PostController::class, 'update']);
        Route::delete('/posts/{post}', [PostController::class, 'destroy']);

        // Welcome endpoint
        Route::get('/welcome', function () {
            return response()->json([
                'success' => true,
                'message' => 'Welcome to API v1 (via web routes)!',
                'current_time' => now()->toString(),
                'date' => now()->toDateString(),
                'time' => now()->toTimeString()
            ]);
        });
    });

    // Escape Room API Routes
    Route::prefix('room1')->group(function () {
        Route::get('/look', [RoomController::class, 'look']);
        Route::get('/{object}/look', [RoomObjectController::class, 'look']);
        Route::post('/{object}/{action}', [RoomObjectController::class, 'interact']);
    });

    Route::prefix('room2')->group(function () {
        Route::post('/open', [RoomController::class, 'open']);
    });
});

// Diagnostic route to test API connectivity (only for development)
Route::get('/test-api', function () {
    return response()->json([
        'message' => 'API test route working',
        'routes' => [
            'api_welcome' => url('/api/welcome'),
            'api_v1_welcome' => url('/api/v1/welcome')
        ],
        'time' => now()->toString()
    ]);
});

// Direct implementation of the API welcome endpoint in web routes
// This helps us test if there's an issue with the API routes specifically
Route::get('/direct-api-welcome', function () {
    $connected = true;
    $message = 'Welcome to the Direct API! Database connection successful.';

    try {
        DB::connection()->getPdo();
    } catch (\Exception $e) {
        $connected = false;
        $message = 'Welcome to the Direct API! Database connection failed: ' . $e->getMessage();
    }

    return response()->json([
        'success' => $connected,
        'message' => $message,
        'current_time' => now()->toString(),
        'date' => now()->toDateString(),
        'time' => now()->toTimeString()
    ]);
});

// Route to clear cache for troubleshooting (only use in development)
Route::get('/clear-cache', function () {
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');

    return 'Cache cleared successfully.';
});

// Fallback route to catch all missing routes and log details
Route::fallback(function () {
    $path = request()->path();
    $method = request()->method();
    $message = "404 Not Found: {$method} /{$path}";

    Log::error($message);

    return response()->json([
        'success' => false,
        'message' => $message,
        'debug_info' => [
            'request_path' => $path,
            'request_method' => $method,
            'full_url' => request()->fullUrl(),
            'user_agent' => request()->header('User-Agent')
        ]
    ], 404);
});
