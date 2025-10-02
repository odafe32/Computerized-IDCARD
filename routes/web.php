<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminNotificationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentNotificationController;
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

// ID Card Verification Route (Public - no authentication required)
Route::get('/verify/id-card/{cardNumber}', function($cardNumber) {
    $request = \App\Models\IdCardRequest::where('card_number', $cardNumber)->first();

    if (!$request) {
        return response()->json(['error' => 'Invalid card number'], 404);
    }

    return response()->json([
        'valid' => true,
        'student_name' => $request->user->name,
        'matric_no' => $request->user->matric_no,
        'department' => $request->user->department,
        'card_number' => $request->card_number,
        'issued_date' => $request->printed_at->format('Y-m-d'),
        'university' => 'Lexa University',
        'status' => $request->status
    ]);
})->name('verify.id-card');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'showDashboard'])->name('dashboard');

        // Admin Profile Management
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminController::class, 'showProfile'])->name('show');
            Route::get('/edit', [AdminController::class, 'editProfile'])->name('edit');
            Route::put('/', [AdminController::class, 'updateProfile'])->name('update');
            Route::post('/photo', [AdminController::class, 'updatePhoto'])->name('photo');
        });

        // Password Management
        Route::get('/change-password', [AdminController::class, 'showChangePassword'])->name('password.change');
        Route::put('/change-password', [AdminController::class, 'updatePassword'])->name('password.update');

        // User Management Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'listUsers'])->name('index');
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('destroy');

            // Additional user actions
            Route::put('/{user}/activate', [AdminController::class, 'activateUser'])->name('activate');
            Route::put('/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('deactivate');
            Route::put('/{user}/suspend', [AdminController::class, 'suspendUser'])->name('suspend');
            Route::post('/{user}/reset-password', [AdminController::class, 'resetUserPassword'])->name('reset-password');
        });

// ID Card Management Routes
Route::prefix('id-cards')->name('id-cards.')->group(function () {
    Route::get('/', [AdminController::class, 'listIdCards'])->name('index');
    Route::get('/pending', [AdminController::class, 'listPendingIdCards'])->name('pending');
    Route::get('/approved', [AdminController::class, 'listApprovedIdCards'])->name('approved');
    Route::get('/rejected', [AdminController::class, 'listRejectedIdCards'])->name('rejected');

    Route::get('/{idCard}', [AdminController::class, 'showIdCard'])->name('show');
    Route::put('/{idCard}/approve', [AdminController::class, 'approveIdCard'])->name('approve');
    Route::put('/{idCard}/reject', [AdminController::class, 'rejectIdCard'])->name('reject');
    Route::put('/{idCard}/ready', [AdminController::class, 'markIdCardReady'])->name('ready');
    Route::put('/{idCard}/printed', [AdminController::class, 'markIdCardPrinted'])->name('printed');
    Route::put('/{idCard}/collected', [AdminController::class, 'markIdCardCollected'])->name('collected');

    // ID Card Generation and Download
    Route::post('/{idCard}/generate', [AdminController::class, 'generateIdCard'])->name('generate');
    Route::get('/{idCard}/download', [AdminController::class, 'downloadIdCard'])->name('download');
    Route::get('/{idCard}/preview', [AdminController::class, 'previewIdCard'])->name('preview');

    // Bulk actions - Fixed route names
    Route::post('/bulk/approve', [AdminController::class, 'bulkApproveIdCards'])->name('bulk.approve');
    Route::post('/bulk/reject', [AdminController::class, 'bulkRejectIdCards'])->name('bulk.reject');
    Route::post('/bulk/ready', [AdminController::class, 'bulkMarkReady'])->name('bulk.ready');
    // Add to admin.id-cards group in routes/web.php
Route::get('/{idCard}/download-qr', [AdminController::class, 'downloadQRCode'])->name('download-qr');
Route::post('/bulk/download', [AdminController::class, 'bulkDownloadIdCards'])->name('bulk.download');
});

        // Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [AdminController::class, 'showReports'])->name('index');
            Route::get('/users', [AdminController::class, 'usersReport'])->name('users');
            Route::get('/id-cards', [AdminController::class, 'idCardsReport'])->name('id-cards');
            Route::get('/activity', [AdminController::class, 'activityReport'])->name('activity');
            Route::get('/statistics', [AdminController::class, 'statisticsReport'])->name('statistics');

            // Export reports
            Route::get('/users/export', [AdminController::class, 'exportUsersReport'])->name('users.export');
            Route::get('/id-cards/export', [AdminController::class, 'exportIdCardsReport'])->name('id-cards.export');
            Route::get('/activity/export', [AdminController::class, 'exportActivityReport'])->name('activity.export');
        });

        // Notifications Management
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [AdminController::class, 'listNotifications'])->name('index');
            Route::post('/send', [AdminController::class, 'sendNotification'])->name('send');
            Route::post('/broadcast', [AdminController::class, 'broadcastNotification'])->name('broadcast');
            Route::delete('/{notification}', [AdminController::class, 'deleteNotification'])->name('delete');
        });

        // System Settings Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [AdminController::class, 'showSettings'])->name('index');
            Route::put('/', [AdminController::class, 'updateSettings'])->name('update');
            Route::get('/system', [AdminController::class, 'showSystemSettings'])->name('system');
            Route::put('/system', [AdminController::class, 'updateSystemSettings'])->name('system.update');
            Route::get('/email', [AdminController::class, 'showEmailSettings'])->name('email');
            Route::put('/email', [AdminController::class, 'updateEmailSettings'])->name('email.update');
            Route::get('/backup', [AdminController::class, 'showBackupSettings'])->name('backup');
            Route::post('/backup/create', [AdminController::class, 'createBackup'])->name('backup.create');
            Route::get('/backup/download/{backup}', [AdminController::class, 'downloadBackup'])->name('backup.download');
        });
        // Add these routes inside the admin middleware group in routes/web.php

// Admin Notification Routes
Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
    Route::post('/{notification}/read', [AdminNotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::delete('/{notification}', [AdminNotificationController::class, 'delete'])->name('delete');
    Route::post('/bulk-delete', [AdminNotificationController::class, 'bulkDelete'])->name('bulk-delete');
    Route::post('/clear-read', [AdminNotificationController::class, 'clearRead'])->name('clear-read');
    Route::post('/send-to-all', [AdminNotificationController::class, 'sendToAllAdmins'])->name('send-to-all');
});

// API endpoints for admin notifications
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/notifications', [AdminNotificationController::class, 'getNotifications'])->name('notifications');
    Route::get('/notification-stats', [AdminNotificationController::class, 'getStats'])->name('notification-stats');
});

        // Activity Logs
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [AdminController::class, 'showLogs'])->name('index');
            Route::get('/system', [AdminController::class, 'showSystemLogs'])->name('system');
            Route::get('/user-activity', [AdminController::class, 'showUserActivityLogs'])->name('user-activity');
            Route::get('/admin-activity', [AdminController::class, 'showAdminActivityLogs'])->name('admin-activity');
            Route::delete('/clear', [AdminController::class, 'clearLogs'])->name('clear');
        });

        // API endpoints for admin dashboard
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/dashboard-stats', [AdminController::class, 'getDashboardStats'])->name('dashboard-stats');
            Route::get('/recent-activities', [AdminController::class, 'getRecentActivities'])->name('recent-activities');
            Route::get('/chart-data', [AdminController::class, 'getChartData'])->name('chart-data');
            Route::get('/users/search', [AdminController::class, 'searchUsers'])->name('users.search');
            Route::get('/id-cards/stats', [AdminController::class, 'getIdCardStats'])->name('id-cards.stats');
        });
    });

    // Student Routes
    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'showDashboard'])->name('dashboard');

        // Profile Routes
        Route::get('/profile', [StudentController::class, 'showProfile'])->name('profile');
        Route::get('/profile/edit', [StudentController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/photo', [StudentController::class, 'updatePhoto'])->name('profile.photo');

        // ID Card Request Routes
        // Add this route inside the student middleware group
Route::delete('/id-card/cancel/{idCard}', [StudentController::class, 'cancelIdCardRequest'])->name('id-card.cancel');
        Route::get('/id-card/show', [StudentController::class, 'showIdCard'])->name('id-card.show');
        // Add this download route - it was missing!
    Route::get('/id-card/download/{idCard?}', [StudentController::class, 'downloadIdCard'])->name('id-card.download');
        Route::get('/id-card/request', [StudentController::class, 'showIdCardRequest'])->name('id-card.request');
        Route::post('/id-card/request', [StudentController::class, 'submitIdCardRequest'])->name('id-card.submit');
        Route::get('/id-card/status', [StudentController::class, 'showIdCardStatus'])->name('id-card.status');
        Route::get('/id-card/download', [StudentController::class, 'downloadIdCard'])->name('id-card.download');

        // Password Change Routes
        Route::get('/change-password', [StudentController::class, 'showChangePassword'])->name('password.change');
        Route::put('/change-password', [StudentController::class, 'updatePassword'])->name('password.update');

        // Notification Routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            // AJAX route for getting notifications
            Route::get('/', [StudentNotificationController::class, 'getNotifications'])->name('get');

            // Show all notifications page
            Route::get('/all', [StudentNotificationController::class, 'showAll'])->name('all');

            // Mark individual notification as read
            Route::post('/{notification}/read', [StudentNotificationController::class, 'markAsRead'])->name('mark-read');

            // Mark all notifications as read
            Route::post('/mark-all-read', [StudentNotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        });
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

    // Additional API routes for notifications (alternative endpoints)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [StudentNotificationController::class, 'getNotifications']);
        Route::post('/{notification}/read', [StudentNotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [StudentNotificationController::class, 'markAllAsRead']);
    });
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
