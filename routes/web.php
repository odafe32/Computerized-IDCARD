<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

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

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes (Guest only)
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'showDashboard'])->name('dashboard');

        // User Management Routes
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');

        // ID Card Management Routes
        Route::get('/id-cards', [AdminController::class, 'listIdCards'])->name('id-cards.index');
        Route::get('/id-cards/{idCard}', [AdminController::class, 'showIdCard'])->name('id-cards.show');
        Route::put('/id-cards/{idCard}/approve', [AdminController::class, 'approveIdCard'])->name('id-cards.approve');
        Route::put('/id-cards/{idCard}/reject', [AdminController::class, 'rejectIdCard'])->name('id-cards.reject');
        Route::get('/id-cards/{idCard}/generate', [AdminController::class, 'generateIdCard'])->name('id-cards.generate');


    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'showDashboard'])->name('dashboard');

        // Profile Routes
        Route::get('/profile', [StudentController::class, 'showProfile'])->name('profile.show');
        Route::get('/profile/edit', [StudentController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [StudentController::class, 'updatePhoto'])->name('profile.photo');

        // ID Card Request Routes
        Route::get('/id-card/request', [StudentController::class, 'showIdCardRequest'])->name('id-card.request');
        Route::post('/id-card/request', [StudentController::class, 'submitIdCardRequest'])->name('id-card.submit');
        Route::get('/id-card/status', [StudentController::class, 'showIdCardStatus'])->name('id-card.status');
        Route::get('/id-card/download', [StudentController::class, 'downloadIdCard'])->name('id-card.download');

        // Change Password Routes
        Route::get('/change-password', [StudentController::class, 'showChangePassword'])->name('password.change');
        Route::put('/change-password', [StudentController::class, 'updatePassword'])->name('password.update');
    });
});

// API Routes for AJAX requests
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/user', function () {
        return response()->json(auth()->user());
    });

    Route::post('/check-matric', function () {
        $matricNo = request('matric_no');
        $exists = \App\Models\User::where('matric_no', $matricNo)->exists();
        return response()->json(['exists' => $exists]);
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
