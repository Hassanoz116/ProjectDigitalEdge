<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyController;
use App\Http\Controllers\Auth\VerificationMethodController;

Route::get('/', function () {
    return view('welcome');
});

// Broadcasting Auth Route
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Verification Method Selection Routes
Route::get('/verification/method', [VerificationMethodController::class, 'showMethodSelection'])->name('verification.method.select');
Route::post('/verification/method/send', [VerificationMethodController::class, 'sendVerificationCode'])->name('verification.method.send');

// Verification Routes
Route::get('/verify/{identifier}', [VerifyController::class, 'showVerificationForm'])->name('verify.show');
Route::post('/verify', [VerifyController::class, 'verify'])->name('verify.submit');
Route::get('/resend/{identifier}', [VerifyController::class, 'resendCode'])->name('resend.code');
Route::post('/login/resend', [LoginController::class, 'resendVerification'])->name('login.resend');

// User Dashboard (Protected Routes)
Route::middleware(['auth', 'check.active'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Helper Routes
Route::get('/get-cities', function () {
    $countryId = request('country_id');
    $cities = \App\Models\City::where('country_id', $countryId)
        ->select('id', 'name_en', 'name_ar', 'country_id')
        ->get();
    return response()->json($cities);
})->name('admin.get-cities');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.active', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');
    Route::resource('users', UserController::class);
    Route::post('users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::post('users/{id}/send-email', [UserController::class, 'sendEmail'])->name('users.send-email');
    Route::get('users/{id}/products', [UserController::class, 'products'])->name('users.products');
    
    // Products
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('products', ProductController::class);
    
    // Gallery
    Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
    Route::post('gallery/upload', [GalleryController::class, 'upload'])->name('gallery.upload');
    Route::delete('gallery/{id}', [GalleryController::class, 'destroy'])->name('gallery.destroy');
    
    // Activity Logs
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Notifications
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.mark-read');
    
    // AJAX API for notifications (web-based authentication)
    Route::get('/notifications/api', function() {
        $limit = request('limit', 5);
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toISOString(),
                ];
            }),
            'count' => $notifications->count(),
            'unread_count' => $user->unreadNotifications()->count()
        ]);
    })->name('admin.notifications.api');
    
    Route::post('notifications/mark-all-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.mark-all-read');
    
    Route::post('notifications/{id}/mark-read', function($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    })->name('notifications.mark-read');
    
    // Language
    Route::get('language/{locale}', [LanguageController::class, 'changeLanguage'])->name('language');
});


// Test Pusher Notification Route (Remove in production)
Route::get('/test-pusher-notification', function() {
    $user = \App\Models\User::first();
    if (!$user) {
        return "No users found. Please register first.";
    }
    
    // Test NotificationSent event
    broadcast(new \App\Events\NotificationSent($user, [
        'title' => 'اختبار Pusher',
        'message' => 'هذا اختبار للإشعارات المباشرة عبر Pusher! / This is a test for real-time notifications via Pusher!',
        'data' => ['test' => true]
    ], 'success'));
    
    return "✅ Pusher notification sent to user: {$user->name}. Check the admin dashboard!";
})->name('test.pusher');

// Direct Pusher Test (No Queue)
Route::get('/test-pusher-direct', function() {
    $user = \App\Models\User::first();
    if (!$user) {
        return "No users found. Please register first.";
    }
    
    // Send directly without queue
    event(new \App\Events\NotificationSent($user, [
        'title' => 'اختبار مباشر',
        'message' => 'هذا اختبار مباشر بدون Queue! / Direct test without Queue!',
        'data' => ['direct' => true]
    ], 'info'));
    
    return "✅ Direct Pusher notification sent to user: {$user->name}. Check the admin dashboard!";
})->name('test.pusher.direct');

// Real Laravel Notification Test (Saves to Database + Pusher)
Route::get('/test-real-notification', function() {
    $user = \App\Models\User::first();
    if (!$user) {
        return "No users found. Please register first.";
    }
    
    // Send real Laravel notification (saves to DB + broadcasts)
    $user->notify(new \App\Notifications\CustomNotification(
        'إشعار حقيقي!',
        'هذا إشعار حقيقي يُحفظ في قاعدة البيانات ويُرسل عبر Pusher! / This is a real notification saved to database and sent via Pusher!',
        'success',
        ['test' => true, 'timestamp' => now()->toISOString()]
    ));
    
    return "✅ Real Laravel notification sent to user: {$user->name}. Check the admin dashboard - it should appear in the dropdown!";
})->name('test.real.notification');

// Direct Notification Test (No Queue - Immediate)
Route::get('/test-direct-notification', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    // Send notification directly (no queue)
    $notification = new \App\Notifications\CustomNotification(
        'اختبار مباشر!',
        'هذا إشعار مباشر بدون Queue - يجب أن يظهر فوراً! / Direct notification without Queue - should appear immediately!',
        'warning',
        ['direct' => true, 'timestamp' => now()->toISOString()]
    );
    
    // Send directly without queue
    $adminUser->notifyNow($notification);
    
    return "✅ Direct notification sent to admin: {$adminUser->name}. Should appear immediately in dashboard!";
})->name('test.direct.notification');


// Debug Notifications Status
Route::get('/debug-notifications', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    $notifications = $adminUser->notifications()->latest()->take(5)->get();
    
    return [
        'admin_user' => $adminUser->name,
        'total_notifications' => $adminUser->notifications()->count(),
        'unread_notifications' => $adminUser->unreadNotifications()->count(),
        'latest_5' => $notifications->map(function($n) {
            return [
                'title' => $n->data['title'] ?? 'No title',
                'message' => $n->data['message'] ?? 'No message',
                'type' => $n->data['type'] ?? 'No type', 
                'created_at' => $n->created_at->diffForHumans()
            ];
        })
    ];
})->name('debug.notifications');

// Test Pusher Configuration
Route::get('/test-pusher-config', function() {
    return [
        'pusher_settings' => [
            'BROADCAST_CONNECTION' => config('broadcasting.default'),
            'PUSHER_APP_ID' => config('broadcasting.connections.pusher.app_id'),
            'PUSHER_APP_KEY' => config('broadcasting.connections.pusher.key'),
            'PUSHER_APP_SECRET' => substr(config('broadcasting.connections.pusher.secret'), 0, 5) . '***',
            'PUSHER_APP_CLUSTER' => config('broadcasting.connections.pusher.options.cluster'),
            'PUSHER_ENCRYPTED' => config('broadcasting.connections.pusher.options.encrypted'),
        ],
        'env_values' => [
            'BROADCAST_CONNECTION' => env('BROADCAST_CONNECTION'),
            'PUSHER_APP_ID' => env('PUSHER_APP_ID'),
            'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
            'PUSHER_APP_SECRET' => substr(env('PUSHER_APP_SECRET'), 0, 5) . '***',
            'PUSHER_APP_CLUSTER' => env('PUSHER_APP_CLUSTER'),
        ],
        'test_url' => 'https://ws-' . env('PUSHER_APP_CLUSTER') . '.pusher-channels.com/app/' . env('PUSHER_APP_KEY'),
        'status' => 'Configuration loaded'
    ];
})->name('test.pusher.config');

// Simple Connection Test
Route::get('/test-simple-pusher', function() {
    try {
        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'), 
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true
            ]
        );
        
        // Test connection
        $response = $pusher->get('/channels');
        
        return [
            'pusher_connection' => 'SUCCESS',
            'channels_info' => $response,
            'message' => 'Pusher is working correctly!'
        ];
    } catch (\Exception $e) {
        return [
            'pusher_connection' => 'FAILED',
            'error' => $e->getMessage(),
            'message' => 'Check your Pusher credentials'
        ];
    }
})->name('test.simple.pusher');

// Debug Broadcasting Step by Step  
Route::get('/debug-broadcast', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    $steps = [];
    
    // Step 1: Test Pusher Connection
    try {
        $pusherOptions = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,  // Keep TLS but disable verification
            'encrypted' => true,
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
            ]
        ];
        
        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'), 
            env('PUSHER_APP_ID'),
            $pusherOptions
        );
        $steps['pusher_connection'] = 'SUCCESS';
    } catch (\Exception $e) {
        $steps['pusher_connection'] = 'FAILED: ' . $e->getMessage();
    }
    
    // Step 2: Test Direct Broadcast
    try {
        $directData = [
            'title' => 'تجريب مباشر',
            'message' => 'إشعار تجريبي مباشر',
            'type' => 'info',
            'id' => 'test-' . uniqid(),
            'created_at' => now()->toISOString()
        ];
        
        $result = $pusher->trigger('notifications.' . $adminUser->id, 'notification', $directData);
        $steps['direct_broadcast'] = 'SUCCESS - Check Console Now!';
        $steps['direct_data'] = $directData;
    } catch (\Exception $e) {
        $steps['direct_broadcast'] = 'FAILED: ' . $e->getMessage();
    }
    
    // Step 3: Test Laravel Notification
    try {
        $notification = new \App\Notifications\CustomNotification(
            'تجريب Laravel',
            'إشعار عبر Laravel Notifications',
            'success',
            ['debug' => true]
        );
        
        $adminUser->notify($notification);
        $steps['laravel_notification'] = 'SUCCESS - Check Console in 2-3 seconds';
    } catch (\Exception $e) {
        $steps['laravel_notification'] = 'FAILED: ' . $e->getMessage();
    }
    
    return [
        'user' => $adminUser->name . ' (ID: ' . $adminUser->id . ')',
        'channel' => 'notifications.' . $adminUser->id,
        'steps' => $steps,
        'message' => 'Check your browser console for real-time results!'
    ];
})->name('debug.broadcast');

// Ultra Simple Test
Route::get('/simple-pusher-test', function() {
    try {
        // Use default Pusher configuration
        $pusher = new \Pusher\Pusher(
            '855c1cf47eca434603dc',     // PUSHER_APP_KEY
            '2837aa91471326ac5174',     // PUSHER_APP_SECRET  
            '2076951',                  // PUSHER_APP_ID
            [
                'cluster' => 'us2',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ]
        );
        
        $data = [
            'title' => 'اختبار بسيط ✅',
            'message' => 'إشعار تجريبي بسيط / Simple test notification',
            'type' => 'success'
        ];
        
        $result = $pusher->trigger('notifications.1', 'notification', $data);
        
        return [
            'status' => 'SUCCESS ✅',
            'message' => 'Simple broadcast sent to notifications.1',
            'data' => $data,
            'result' => $result
        ];
        
    } catch (\Exception $e) {
        return [
            'status' => 'ERROR ❌',
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ];
    }
})->name('simple.pusher.test');

// Ultimate SSL Fix Test
Route::get('/pusher-no-ssl', function() {
    try {
        // Create custom HTTP client context
        $httpClient = new \GuzzleHttp\Client([
            'verify' => false,
            'curl' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_CAINFO => null,
                CURLOPT_CAPATH => null,
            ]
        ]);
        
        // Use Pusher with custom HTTP client
        $pusher = new \Pusher\Pusher(
            '855c1cf47eca434603dc',
            '2837aa91471326ac5174', 
            '2076951',
            [
                'cluster' => 'us2',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                ]
            ],
            $httpClient
        );
        
        $data = [
            'title' => 'SSL تم تعطيله! 🔓',
            'message' => 'إشعار بدون SSL تماماً',
            'type' => 'warning'
        ];
        
        $result = $pusher->trigger('notifications.1', 'notification', $data);
        
        return [
            'status' => 'SUCCESS - NO SSL! ✅',
            'message' => 'Notification sent without SSL verification',
            'data' => $data
        ];
        
    } catch (\Exception $e) {
        return [
            'status' => 'STILL FAILED ❌',
            'error' => $e->getMessage(),
            'suggestion' => 'Try the PHP script: php test-direct-pusher.php'
        ];
    }
})->name('pusher.no.ssl');

// Database Only + Manual Trigger
Route::get('/manual-notification', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    // Save to database only (no broadcasting)
    $adminUser->notify(new \App\Notifications\CustomNotification(
        'إشعار يدوي 📝',
        'تم حفظه في قاعدة البيانات فقط - بدون Pusher',
        'info',
        ['manual' => true, 'timestamp' => now()->toISOString()]
    ));
    
    $notificationCount = $adminUser->unreadNotifications()->count();
    
    return [
        'status' => 'SUCCESS ✅',
        'message' => 'Notification saved to database',
        'count' => $notificationCount,
        'javascript' => "
            // Update notification count manually
            document.getElementById('notification-count').textContent = {$notificationCount};
            
            // Show success message
            alert('✅ تم إنشاء إشعار جديد! عدد الإشعارات: {$notificationCount}');
            
            // Refresh the notifications dropdown
            window.location.reload();
        "
    ];
})->name('manual.notification');

// Test AJAX Notifications (No Pusher Required)
Route::get('/test-ajax-notification', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    // Create notification (database only)
    $adminUser->notify(new \App\Notifications\CustomNotification(
        'إشعار AJAX فوري! ⚡',
        'يظهر خلال 3 ثوانٍ بدون Pusher! / Shows in 3 seconds without Pusher!',
        'success',
        ['ajax_test' => true, 'timestamp' => now()->toISOString()]
    ));
    
    return [
        'status' => 'SUCCESS ✅',
        'message' => 'AJAX notification created! Will appear in 3 seconds.',
        'system' => 'AJAX Polling (No Pusher)',
        'user' => $adminUser->name,
        'instruction' => 'Watch the notification dropdown in your admin panel!'
    ];
})->name('test.ajax.notification');

// Test Instant Pusher Broadcasting (No Queue)
Route::get('/test-pusher-instant', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    // Send notification INSTANTLY (ShouldBroadcastNow - no queue)
    $adminUser->notify(new \App\Notifications\CustomNotification(
        'إشعار فوري! 🚀',
        'هذا إشعار فوري عبر Pusher بدون Queue! / Instant Pusher notification without Queue!',
        'success',
        ['instant' => true, 'timestamp' => now()->toISOString()]
    ));
    
    return "🚀 INSTANT notification sent! Should appear in 1-2 seconds!";
})->name('test.pusher.instant');

// Test Direct Pusher Broadcast  
Route::get('/test-pusher-direct', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    try {
        // Direct Pusher broadcast without Laravel Notifications
        $data = [
            'title' => 'بث مباشر! 📡',
            'message' => 'إشعار مباشر عبر Pusher API / Direct Pusher broadcast',
            'type' => 'success',
            'id' => uniqid(),
            'created_at' => now()->toISOString()
        ];
        
        // Direct Pusher API call with SSL disabled
        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,  // Keep TLS but disable verification
            'encrypted' => true,
            'curl_options' => [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_TIMEOUT => 30,
            ]
        ];
        
        $pusher = new \Pusher\Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'), 
            env('PUSHER_APP_ID'),
            $options
        );
        
        $result = $pusher->trigger('notifications.' . $adminUser->id, 'notification', $data);
        
        return [
            'status' => 'SUCCESS',
            'message' => '📡 DIRECT Pusher broadcast sent! Should appear IMMEDIATELY!',
            'channel' => 'notifications.' . $adminUser->id,
            'data' => $data,
            'pusher_result' => $result
        ];
        
    } catch (\Exception $e) {
        return [
            'status' => 'ERROR',
            'message' => 'Pusher broadcast failed',
            'error' => $e->getMessage()
        ];
    }
})->name('test.pusher.direct');

// Test Notification without Broadcasting (Direct to DB)
Route::get('/test-db-only', function() {
    $adminUser = \App\Models\User::role('admin')->first();
    if (!$adminUser) {
        return "No admin user found.";
    }
    
    // Send notification directly (no broadcasting)
    $adminUser->notifyNow(new \App\Notifications\CustomNotification(
        'قاعدة البيانات فقط!',
        'إشعار يُحفظ في قاعدة البيانات فقط بدون Pusher / DB only notification without Pusher',
        'warning',
        ['db_only' => true, 'timestamp' => now()->toISOString()]
    ));
    
    return "✅ Notification saved to database only (no Broadcasting). Check polling!";
})->name('test.db.only');

// Test Laravel Notification Route (Remove in production)
Route::get('/test-laravel-notification', function() {
    $user = \App\Models\User::first();
    $product = \App\Models\Product::first();
    
    if (!$user || !$product) {
        return "User or Product not found. Please create them first.";
    }
    
    // Send Laravel notification
    $user->notify(new \App\Notifications\ProductAssignedNotification($product));
    
    return "✅ Laravel notification sent to user: {$user->name} for product: {$product->title_en}";
})->name('test.laravel.notification');

// Test Inactive User Route (Remove in production)
Route::get('/test-inactive-user', function() {
    $user = \App\Models\User::first();
    if ($user) {
        $user->update(['is_active' => 0]);
        return "User {$user->name} is now inactive. Try logging in with their credentials.";
    }
    return "No users found. Please register first.";
})->name('test.inactive');

// Test Active User Route (Remove in production)
Route::get('/test-active-user', function() {
    $user = \App\Models\User::first();
    if ($user) {
        $user->update(['is_active' => 1, 'email_verified_at' => now()]);
        return "User {$user->name} is now active. They can login normally.";
    }
    return "No users found. Please register first.";
})->name('test.active');

// Test Notification Route (Remove in production)
Route::get('/test-notification', function() {
    $user = \App\Models\User::find(1); // Admin user
    $product = \App\Models\Product::first();
    
    if (!$user) {
        return 'User not found. Please run: php artisan db:seed';
    }
    
    if (!$product) {
        return 'Product not found. Please create a product first.';
    }
    
    $user->notify(new \App\Notifications\ProductAssignedNotification($product));
    
    return '
        <div style="font-family: Arial; padding: 20px; max-width: 600px; margin: 50px auto; border: 2px solid #4CAF50; border-radius: 10px;">
            <h2 style="color: #4CAF50;">✅ Notification Sent Successfully!</h2>
            <p><strong>To:</strong> ' . $user->name . ' (' . $user->email . ')</p>
            <p><strong>Product:</strong> ' . $product->title_en . '</p>
            <hr>
            <h3>Next Steps:</h3>
            <ol>
                <li>Login with: <code>' . $user->email . '</code></li>
                <li>Go to: <a href="' . url('/admin') . '">Admin Dashboard</a></li>
                <li>Look at the bell icon 🔔 in the header</li>
                <li>You should see notification count!</li>
            </ol>
            <p style="color: #666; font-size: 12px; margin-top: 30px;">
                Note: Remove this route in production by deleting the /test-notification route from routes/web.php
            </p>
        </div>
    ';
})->name('test.notification');
