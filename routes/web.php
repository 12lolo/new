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

    // Escape Room Answers/Walkthrough route
    Route::get('/answers', function () {
        return response()->json([
            'title' => 'Escape Room Walkthrough',
            'description' => 'Complete guide to solving all puzzles in the escape room',
            'walkthrough' => [
                'room1' => [
                    'title' => 'Entrance Room',
                    'steps' => [
                        '1. Look around the room (GET /api/room1/look)',
                        '2. Examine the cabinet (GET /api/room1/kabinet2/look)',
                        '3. Look inside the left cabinet door (GET /api/room1/kabinet2/leftdoor/look)',
                        '4. Take the key (POST /api/room1/kabinet2/leftdoor/take-key)',
                        '5. Read the old letter (GET /api/puzzle/read/oude brief) to find code hint "1-4-7-2"',
                        '6. Check the table (GET /api/room1/tafel/look) and take note',
                        '7. Examine the painting (GET /api/room1/schilderij/look)',
                        '8. Look behind painting (GET /api/room1/schilderij/achter-schilderij/look)',
                        '9. Open safe with code 1472 (POST /api/room1/schilderij/achter-schilderij/kluis/try-code)',
                        '10. Take the laboratory key',
                        '11. Open door to Room 2 (POST /api/room2/open)'
                    ]
                ],
                'room2' => [
                    'title' => 'Mystery Room',
                    'steps' => [
                        '1. Look around the room (GET /api/room2/look)',
                        '2. Check the desk drawers (GET /api/room2/bureau/look)',
                        '3. Open top drawer (GET /api/room2/bureau/bovenste-la/look)',
                        '4. Take small key (POST /api/room2/bureau/bovenste-la/take-small_key)',
                        '5. Unlock middle drawer (POST /api/room2/bureau/middelste-la/unlock)',
                        '6. Take library card (POST /api/room2/bureau/middelste-la/take-library_card)',
                        '7. Check bottom drawer for note (GET /api/room2/bureau/onderste-la/look)',
                        '8. Check bookshelf (GET /api/room2/boekenkast/look)',
                        '9. Take red book (POST /api/room2/boekenkast/take-boek)',
                        '10. Read book to get safe code 1234 (GET /api/puzzle/read/boek)',
                        '11. Open wall safe with code 1234 (POST /api/room2/safe/try-code)',
                        '12. Take exit key',
                        '13. Open laboratory door (POST /api/room3/open)'
                    ]
                ],
                'room3' => [
                    'title' => 'Laboratory',
                    'steps' => [
                        '1. Look around the lab (GET /api/room3/look)',
                        '2. Check workbench (GET /api/room3/werkbank/look)',
                        '3. Check chemical cabinet (GET /api/room3/chemicalienkast/look)',
                        '4. Take sodium chloride (POST /api/room3/chemicalienkast/take-sodium_chloride)',
                        '5. Check computer (GET /api/room3/computer/look)',
                        '6. Unlock computer with password "salt" (POST /api/room3/computer/try-code)',
                        '7. Learn flower sequence RLOS (GET /api/puzzle/read/ancient_manuscript)',
                        '8. Open library door (POST /api/room4/open)'
                    ]
                ],
                'room4' => [
                    'title' => 'Library',
                    'steps' => [
                        '1. Look around the library (GET /api/room4/look)',
                        '2. Check reading desk (GET /api/room4/leeshoek/look)',
                        '3. Take books about roses and lilies',
                        '4. Check history section (GET /api/room4/geschiedenis-sectie/look)',
                        '5. Take books about orchids and sunflowers',
                        '6. Find the oldest book (GET /api/room4/geschiedenis-sectie/oudste-boek/look)',
                        '7. Take garden key (POST /api/room4/geschiedenis-sectie/oudste-boek/take-garden_key)',
                        '8. Solve bookcase puzzle with pattern RLOS (POST /api/room4/geheime-boekenkast/solve)',
                        '9. Take crystal key',
                        '10. Open garden door (POST /api/room5/open)'
                    ]
                ],
                'room5' => [
                    'title' => 'Secret Garden',
                    'steps' => [
                        '1. Look around the garden (GET /api/room5/look)',
                        '2. Check fountain for ancient coin (GET /api/room5/fontein/look)',
                        '3. Take coin (POST /api/room5/fontein/take-ancient_coin)',
                        '4. Check flower bed for special flower (GET /api/room5/bloemperk/look)',
                        '5. Take flower (POST /api/room5/bloemperk/take-special_flower)',
                        '6. Check stone bench for riddle (GET /api/room5/stenen-bank/look)',
                        '7. Unlock crystal pedestal with crystal key (POST /api/room5/kristallen-voetstuk/unlock)',
                        '8. Combine items at pedestal: crystal key, ancient coin, and special flower (POST /api/puzzle/combine)',
                        '9. Take final key',
                        '10. Enter exit door with exit key (POST /api/exit/open)'
                    ]
                ],
                'exit' => [
                    'title' => 'Freedom - Exit Room',
                    'steps' => [
                        '1. Look around the final room (GET /api/exit/look)',
                        '2. Check victory pedestal (GET /api/exit/victory-stand/look)',
                        '3. Read completion certificate (GET /api/puzzle/read/certificate)'
                    ]
                ],
                'special_note' => 'Remember: You need to collect keys and solve puzzles in the correct order to progress through the escape room.'
            ]
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
