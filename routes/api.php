<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MLController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GoalController;
use App\Http\Controllers\API\CareerGoalController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\SkillController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\CareerPathController;
use App\Http\Controllers\API\AdminController;

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

    // Career Goals routes
    Route::apiResource('career-goals', CareerGoalController::class);
    Route::put('career-goals/{careerGoal}/progress', [CareerGoalController::class, 'updateProgress']);
    Route::get('career-goals-statistics', [CareerGoalController::class, 'statistics']);

    // Skills routes
    Route::get('/skills', [SkillController::class, 'index']);
    Route::get('/user/skills', [SkillController::class, 'userSkills']);
    Route::post('/user/skills/{skill}', [SkillController::class, 'updateUserSkill']);

    // Career Paths routes
    Route::get('/career-paths', [CareerPathController::class, 'index']);
    Route::get('/user/career-paths', [CareerPathController::class, 'userPaths']);
    Route::post('/user/career-paths/{careerPath}/join', [CareerPathController::class, 'join']);
    Route::delete('/user/career-paths/{careerPath}/leave', [CareerPathController::class, 'leave']);

    // Courses routes
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/recommendations/courses', [CourseController::class, 'recommended']);
    Route::match(['put', 'post'], '/courses/{course}/progress', [CourseController::class, 'updateProgress']);
    Route::post('/courses/{course}/rate', [CourseController::class, 'rate']);

    // ML routes
    Route::get('/ml/recommendations', [MLController::class, 'getRecommendations']);
    Route::get('/ml/career-prediction', [MLController::class, 'predictCareerPath']);
    Route::post('/ml/skill-gaps', [MLController::class, 'analyzeSkillGaps']);

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/users/{user}', [AdminController::class, 'showUser']);
        Route::put('/users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser']);

        Route::get('/courses', [AdminController::class, 'courses']);
        Route::post('/courses', [AdminController::class, 'createCourse']);
        Route::put('/courses/{course}', [AdminController::class, 'updateCourse']);
        Route::delete('/courses/{course}', [AdminController::class, 'deleteCourse']);

        Route::get('/skills', [AdminController::class, 'skills']);
        Route::post('/skills', [AdminController::class, 'createSkill']);
        Route::put('/skills/{skill}', [AdminController::class, 'updateSkill']);
        Route::delete('/skills/{skill}', [AdminController::class, 'deleteSkill']);

        Route::get('/career-paths', [AdminController::class, 'careerPaths']);
        Route::post('/career-paths', [AdminController::class, 'createCareerPath']);
        Route::put('/career-paths/{path}', [AdminController::class, 'updateCareerPath']);
        Route::delete('/career-paths/{path}', [AdminController::class, 'deleteCareerPath']);

        Route::get('/statistics', [AdminController::class, 'statistics']);
    });
});
