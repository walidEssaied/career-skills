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
    // User Profile and Dashboard
    Route::get('/profile', [UserProfileController::class, 'profile'])->name('profile');
    
    // User Skills Management
    Route::prefix('users/{user}')->name('users.')->group(function () {
        Route::get('skills', [UserProfileController::class, 'showSkills'])->name('skills.index');
        Route::post('skills', [UserProfileController::class, 'addSkill'])->name('skills.store');
        Route::delete('skills/{skill}', [UserProfileController::class, 'removeSkill'])->name('skills.destroy');
        
        Route::get('goals', [UserProfileController::class, 'showGoals'])->name('goals.index');
        Route::post('goals', [UserProfileController::class, 'addGoal'])->name('goals.store');
        Route::delete('goals/{goal}', [UserProfileController::class, 'removeGoal'])->name('goals.destroy');
        
        Route::get('courses', [UserProfileController::class, 'showCourses'])->name('courses.index');
        Route::post('courses', [UserProfileController::class, 'addCourse'])->name('courses.store');
        Route::delete('courses/{course}', [UserProfileController::class, 'removeCourse'])->name('courses.destroy');
    });
});

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // User Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
        Route::post('/users/import', [AdminUserController::class, 'import'])->name('users.import');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');

        // User Skills Management
        Route::prefix('users/{user}')->name('users.')->group(function () {
            Route::get('skills', [AdminUserController::class, 'showSkills'])->name('skills.index');
            Route::post('skills', [AdminUserController::class, 'addSkill'])->name('skills.store');
            Route::delete('skills/{skill}', [AdminUserController::class, 'removeSkill'])->name('skills.destroy');
            
            Route::get('goals', [AdminUserController::class, 'showGoals'])->name('goals.index');
            Route::post('goals', [AdminUserController::class, 'addGoal'])->name('goals.store');
            Route::delete('goals/{goal}', [AdminUserController::class, 'removeGoal'])->name('goals.destroy');
            
            Route::get('courses', [AdminUserController::class, 'showCourses'])->name('courses.index');
            Route::post('courses', [AdminUserController::class, 'addCourse'])->name('courses.store');
            Route::delete('courses/{course}', [AdminUserController::class, 'removeCourse'])->name('courses.destroy');
        });

        // Basic User Routes
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Career Paths
        Route::resource('career-paths', CareerPathController::class);
        Route::post('career-paths/import', [CareerPathController::class, 'import'])->name('career-paths.import');
        Route::get('career-paths/export', [CareerPathController::class, 'export'])->name('career-paths.export');

        // Skills Management
        Route::resource('skills', SkillController::class);
        Route::post('skills/import', [SkillController::class, 'import'])->name('skills.import');
        Route::get('skills/export', [SkillController::class, 'export'])->name('skills.export');

        // Courses Management
        Route::resource('courses', CourseController::class);
        Route::post('courses/import', [CourseController::class, 'import'])->name('courses.import');
        Route::get('courses/export', [CourseController::class, 'export'])->name('courses.export');
    });
});

Auth::routes();
