<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomObjectController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PlayerSessionController;
use App\Http\Controllers\PuzzleController;

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

    // Player Session Management Routes
    Route::prefix('player')->group(function () {
        Route::post('/start', [PlayerSessionController::class, 'startGame']);
        Route::get('/status', [PlayerSessionController::class, 'getStatus']);
        Route::post('/inventory/{itemKey}', [PlayerSessionController::class, 'updateInventory']);
        Route::post('/room/{roomKey}', [PlayerSessionController::class, 'updateRoom']);
    });

    // Puzzle routes
    Route::prefix('puzzle')->group(function () {
        Route::post('/combine', [PuzzleController::class, 'combineItems']);
        Route::post('/{roomKey}/{objectKey}/solve', [PuzzleController::class, 'solvePuzzle']);
        Route::get('/read/{itemKey}', [PuzzleController::class, 'readItem']);
    });

    // Room routes (for all available rooms)
    $roomKeys = ['room1', 'room2', 'room3', 'room4', 'room5', 'exit'];
    
    foreach ($roomKeys as $roomKey) {
        Route::prefix($roomKey)->group(function () use ($roomKey) {
            Route::get('/look', [RoomController::class, 'look']);
            if ($roomKey !== 'room1' && $roomKey !== 'exit') {
                Route::post('/open', [RoomController::class, 'open']);
            }
            Route::get('/{object}/look', [RoomObjectController::class, 'look']);
            Route::get('/{object}/{nestedObject}/look', [RoomObjectController::class, 'lookNested']);
            Route::post('/{object}/{nestedObject}/{action}', [RoomObjectController::class, 'interactNested']);
            Route::post('/{object}/{action}', [RoomObjectController::class, 'interact']);
            Route::post('/{object}/try-code', [RoomObjectController::class, 'tryCode']);
        });
    }

    // Add player inventory route
    Route::get('/inventory', function (Request $request) {
        $inventory = $request->session()->get('inventory', []);
        return response()->json([
            'inventory' => $inventory
        ]);
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
