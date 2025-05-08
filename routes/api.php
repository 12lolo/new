<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\PostController;

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

//// Welcome endpoint with minimal dependencies for testing
Route::get('/welcome', function () {
    return response()->json([
        'success' => true,
        'message' => 'Welcome to the API!',
        'current_time' => now()->toString()
    ]);
});

// More detailed welcome endpoint that checks database connection
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API v1 Routes
Route::prefix('v1')->group(function () {
    Route::apiResource('posts', PostController::class);

    // Welcome endpoint in v1 namespace
    Route::get('/welcome', function () {
        return response()->json([
            'success' => true,
            'message' => 'Welcome to API v1!',
            'current_time' => now()->toString(),
            'date' => now()->toDateString(),
            'time' => now()->toTimeString()
        ]);
    });
});

// Simple "ping" endpoint for basic connectivity testing
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
