<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MLController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GoalController;

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

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);

    // Goals routes
    Route::apiResource('goals', GoalController::class);

    // ML Routes
    Route::prefix('ml')->group(function () {
        Route::get('/recommendations', [MLController::class, 'getCourseRecommendations']);
        Route::get('/career-prediction', [MLController::class, 'predictCareerPath']);
        Route::post('/skill-gaps', [MLController::class, 'analyzeSkillGaps']);
        Route::post('/update-vectors', [MLController::class, 'updateSkillVectors'])->middleware('admin');
    });
});
