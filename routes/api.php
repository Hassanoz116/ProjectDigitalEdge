<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes (No Authentication Required)
// Route::prefix('auth')->group(function () {
//     Route::post('/register', [AuthController::class, 'register'])->name('api.register');
//     Route::post('/verify', [AuthController::class, 'verify'])->name('api.verify');
//     Route::post('/login', [AuthController::class, 'login'])->name('api.login');
//     Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.forgot_password');
//     Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.reset_password');
// });

// Protected Routes (Require Authentication)
Route::middleware(['auth:sanctum', 'throttle:20,1'])->group(function () {
    
    // Auth Routes
    // Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    // Route::get('/user-info', [AuthController::class, 'getUserInfo'])->name('api.get_user_info');
    Route::put('/update-user-info', [UserController::class, 'updateUserInfo'])->name('api.update_user_info');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('api.change_password');
    
    // Products Routes (All Users)
    Route::get('/products', [ProductController::class, 'index'])->name('api.get_products');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('api.get_product');
    Route::get('/user-products', [ProductController::class, 'getUserProducts'])->name('api.get_user_products');
    
    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('api.get_users');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('api.get_user');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('api.update_user');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('api.delete_user');
        Route::post('/users/{id}/change-password', [UserController::class, 'adminChangePassword'])->name('api.admin_change_password');
        Route::post('/users/{id}/send-email', [UserController::class, 'sendEmail'])->name('api.send_email');
        Route::get('/users/{id}/products', [UserController::class, 'getUserProducts'])->name('api.get_user_products_list');
        Route::get('/export-users', [UserController::class, 'exportUsers'])->name('api.export_users');
        
        // Product Management
        Route::post('/products', [ProductController::class, 'store'])->name('api.create_product');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('api.update_product');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('api.delete_product');
        Route::post('/assign-product', [ProductController::class, 'assignProduct'])->name('api.assign_product');
        Route::post('/unassign-product', [ProductController::class, 'unassignProduct'])->name('api.unassign_product');
        Route::get('/export-products', [ProductController::class, 'exportProducts'])->name('api.export_products');
        
        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('api.get_activity_logs');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('api.get_notifications');
        Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('api.mark_notification_read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('api.mark_all_notifications_read');
    });
});
