<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\CareerPathController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\UserController as UserProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

// User Routes (Non-Admin)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    
    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [UserProfileController::class, 'show'])->name('show');
        Route::get('/edit', [UserProfileController::class, 'edit'])->name('edit');
        Route::put('/', [UserProfileController::class, 'update'])->name('update');
        
        // Skills Routes
        Route::get('skills', [UserProfileController::class, 'showSkills'])->name('skills.index');
        Route::post('skills', [UserProfileController::class, 'addSkill'])->name('skills.store');
        Route::delete('skills/{skill}', [UserProfileController::class, 'removeSkill'])->name('skills.destroy');
        
        // Goals Routes
        Route::get('goals', [UserProfileController::class, 'showGoals'])->name('goals.index');
        Route::post('goals', [UserProfileController::class, 'addGoal'])->name('goals.store');
        Route::delete('goals/{goal}', [UserProfileController::class, 'removeGoal'])->name('goals.destroy');
        
        // Course Routes
        Route::get('courses', [UserProfileController::class, 'showCourses'])->name('courses.index');
        Route::post('courses', [UserProfileController::class, 'addCourse'])->name('courses.store');
        Route::delete('courses/{course}', [UserProfileController::class, 'removeCourse'])->name('courses.destroy');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Career Paths
    Route::resource('career-paths', CareerPathController::class);
    Route::get('career-paths-export', [CareerPathController::class, 'export'])->name('career-paths.export');
    Route::post('career-paths-import', [CareerPathController::class, 'import'])->name('career-paths.import');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/career-path-insights', [ReportController::class, 'careerPathInsights'])
            ->name('career-path-insights');
        Route::get('/course-analytics', [ReportController::class, 'courseAnalytics'])
            ->name('course-analytics');
    });
});

Auth::routes();
