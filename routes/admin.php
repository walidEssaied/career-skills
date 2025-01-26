<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\CareerPathController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\MLInsightController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // Skills Management
    Route::resource('skills', SkillController::class);
    Route::post('skills/import', [SkillController::class, 'import'])->name('skills.import');
    Route::get('skills/export', [SkillController::class, 'export'])->name('skills.export');

    // Career Paths Management
    Route::resource('career-paths', CareerPathController::class);
    Route::post('career-paths/{careerPath}/skills', [CareerPathController::class, 'updateSkills'])
        ->name('career-paths.skills.update');
    Route::post('career-paths/{careerPath}/courses', [CareerPathController::class, 'updateCourses'])
        ->name('career-paths.courses.update');

    // Courses Management
    Route::resource('courses', CourseController::class);
    Route::post('courses/import', [CourseController::class, 'import'])->name('courses.import');
    Route::get('courses/export', [CourseController::class, 'export'])->name('courses.export');
    Route::post('courses/{course}/skills', [CourseController::class, 'updateSkills'])
        ->name('courses.skills.update');

    // User Management
    Route::resource('users', UserController::class);
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');

    // User Skills, Goals, and Courses Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('{user}/skills', [UserController::class, 'showSkills'])->name('skills.index');
        Route::post('{user}/skills', [UserController::class, 'addSkill'])->name('skills.store');
        Route::delete('{user}/skills/{skill}', [UserController::class, 'removeSkill'])->name('skills.destroy');
        
        Route::get('{user}/goals', [UserController::class, 'showGoals'])->name('goals.index');
        Route::post('{user}/goals', [UserController::class, 'addGoal'])->name('goals.store');
        Route::delete('{user}/goals/{goal}', [UserController::class, 'removeGoal'])->name('goals.destroy');
        
        Route::get('{user}/courses', [UserController::class, 'showCourses'])->name('courses.index');
        Route::post('{user}/courses', [UserController::class, 'addCourse'])->name('courses.store');
        Route::delete('{user}/courses/{course}', [UserController::class, 'removeCourse'])->name('courses.destroy');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/user-growth', [ReportController::class, 'userGrowth'])->name('user-growth');
        Route::get('/skill-trends', [ReportController::class, 'skillTrends'])->name('skill-trends');
        Route::get('/course-analytics', [ReportController::class, 'courseAnalytics'])->name('course-analytics');
        Route::get('/career-path-insights', [ReportController::class, 'careerPathInsights'])->name('career-path-insights');
    });

    // ML Insights
    Route::get('/ml-insights', [MLInsightController::class, 'index'])->name('ml-insights');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/update', [SettingController::class, 'update'])->name('update');
        Route::post('/cache-clear', [SettingController::class, 'clearCache'])->name('cache.clear');
    });
});
