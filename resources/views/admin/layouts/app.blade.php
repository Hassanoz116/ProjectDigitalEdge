<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Digital Edge') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @if(app()->getLocale() == 'ar')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    @endif

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Figtree', sans-serif" }};
            background-color: #f8f9fa;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            color: #fff;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .main-content {
            padding: 20px;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        
        .stats-card {
            border-left: 4px solid #007bff;
        }
        
        .stats-card .card-body {
            padding: 1.5rem;
        }
        
        .stats-card .stats-icon {
            font-size: 2rem;
            color: #007bff;
        }
        
        .stats-card .stats-number {
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .stats-card .stats-text {
            color: #6c757d;
        }
        
        /* RTL Support */
        [dir="rtl"] .sidebar {
            right: 0;
            left: auto;
        }
        
        [dir="rtl"] .stats-card {
            border-right: 4px solid #007bff;
            border-left: none;
        }
        
        [dir="rtl"] .dropdown-menu {
            text-align: right;
        }
        
        [dir="rtl"] .btn-group {
            direction: ltr;
        }
        
        [dir="rtl"] .me-2,
        [dir="rtl"] .me-3 {
            margin-left: 0.5rem !important;
            margin-right: 0 !important;
        }
        
        [dir="rtl"] .ms-2,
        [dir="rtl"] .ms-3 {
            margin-right: 0.5rem !important;
            margin-left: 0 !important;
        }
        
        /* Arabic Font Improvements */
        [dir="rtl"] {
            text-align: right;
        }
        
        [dir="rtl"] .table {
            text-align: right;
        }
        
        [dir="rtl"] .form-label {
            text-align: right;
        }
        
        [dir="rtl"] .notification-item {
            text-align: right;
        }
        
        /* Header Buttons Styling */
        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
            margin-right: 0.5rem;
            margin-left: 0.5rem;

            
        }
        
        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }
        
        .dropdown-item.active {
            background-color: #007bff;
            color: white;
        }
        
        #notificationsDropdown,
        #languageDropdown,
        #userDropdown {
            white-space: nowrap;
        }
    </style>
    
    @yield('styles')
</head>
<body dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-4">{{ config('app.name', 'Digital Edge') }}</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                {{ __('admin.dashboard') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="fas fa-users me-2"></i>
                                {{ __('admin.users') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                <i class="fas fa-box me-2"></i>
                                {{ __('admin.products') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.gallery.index') }}" class="nav-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}">
                                <i class="fas fa-images me-2"></i>
                                {{ __('admin.gallery') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.activity-logs.index') }}" class="nav-link {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                                <i class="fas fa-history me-2"></i>
                                {{ __('admin.activity_logs') }}
                            </a>
                        </li>
                    </ul>
                    <hr>
                </div>
            </div>
            
            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">@yield('title')</h1>
                    <div class="d-flex align-items-center">
                        <!-- Notifications Dropdown -->
                        <div class="dropdown me-3">
                            <button class="btn btn-link position-relative p-0" type="button" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-lg text-dark"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationsDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <li class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span><strong>Notifications</strong></span>
                                    <a href="#" class="text-decoration-none small" id="mark-all-read">Mark all as read</a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <div id="notifications-list">
                                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                        <li>
                                            <a class="dropdown-item notification-item {{ $notification->read_at ? '' : 'bg-light' }}" href="#" data-notification-id="{{ $notification->id }}">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-box text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <p class="mb-1 small">{{ $notification->data['message'] ?? 'New notification' }}</p>
                                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="dropdown-item text-center text-muted">No new notifications</li>
                                    @endforelse
                                </div>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <li><hr class="dropdown-divider"></li>
                                <li class="text-center">
                                    <a class="dropdown-item small" href="#">View all notifications</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                        
                        <!-- Language Switcher -->
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-language"></i>
                                {{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('admin.language', 'en') }}">
                                        <i class="fas fa-check me-2" style="{{ app()->getLocale() == 'en' ? '' : 'visibility: hidden;' }}"></i>
                                        English
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('admin.language', 'ar') }}">
                                        <i class="fas fa-check me-2" style="{{ app()->getLocale() == 'ar' ? '' : 'visibility: hidden;' }}"></i>
                                        العربية
                                    </a>
                                </li>
                            </ul>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>
                                        {{ __('admin.logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                        
                        @yield('actions')
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Real-time Notifications via AJAX Polling -->
    <script>
        console.log('🔄 Initializing AJAX-based notification system...');
        
        @auth
        const userId = {{ auth()->id() }};
        let lastNotificationId = null;
        let isPollingActive = true;
        
        // Initialize last notification ID from server
        fetch('/admin/notifications/api?limit=1', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json())
        .then(data => {
            if (data.data && data.data.length > 0) {
                lastNotificationId = data.data[0].id;
                console.log('📋 Initial last notification ID:', lastNotificationId);
            }
        }).catch(err => {
            console.log('📋 No existing notifications or API error');
        });
        
        // Check for new notifications every 3 seconds
        function checkForNewNotifications() {
            if (!isPollingActive) return;
            
            fetch('/admin/notifications/api?limit=5', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.data && data.data.length > 0) {
                    const latestNotification = data.data[0]; // Most recent notification
                    
                    // Check if this is truly a new notification
                    if (!lastNotificationId || latestNotification.id !== lastNotificationId) {
                        console.log('🆕 New notification found:', latestNotification.data.title || latestNotification.data.message);
                        
                        // Only process the newest notification
                        handleNewNotification(latestNotification);
                        
                        // Show browser notification
                        if (Notification.permission === "granted") {
                            new Notification("إشعار جديد / New Notification", {
                                body: latestNotification.data.title || latestNotification.data.message || "لديك إشعار جديد",
                                icon: '/favicon.ico'
                            });
                        }
                        
                        // Update last notification ID to prevent duplicates
                        lastNotificationId = latestNotification.id;
                        console.log('🔄 Updated last notification ID:', lastNotificationId);
                        
                        console.log('✅ New notification processed via AJAX!');
                    }
                    
                    // Always update the count to reflect current state
                    document.getElementById('notification-count').textContent = data.unread_count || data.count;
                }
            })
            .catch(error => {
                console.error('❌ AJAX polling error:', error);
            });
        }
        
        // Start polling every 3 seconds
        setInterval(checkForNewNotifications, 3000);
        console.log('✅ AJAX polling started (every 3 seconds)');
        @endauth
        
        // Function to handle new notifications (available globally)
        function handleNewNotification(notification) {
            // Don't manually increment count - it's handled by API response
            console.log('🔧 Processing notification for UI:', notification.data.title || notification.data.message);
            
            // Determine icon based on notification type
            let icon = 'fas fa-bell';
            let iconColor = 'text-primary';
            
            // Extract notification data properly first
            const notificationData = notification.data || {};
            const notificationType = notificationData.type || notification.type || 'info';
            
            if (notificationType === 'success') {
                icon = 'fas fa-check-circle';
                iconColor = 'text-success';
            } else if (notificationType === 'warning') {
                icon = 'fas fa-exclamation-triangle';
                iconColor = 'text-warning';
            } else if (notificationType === 'error') {
                icon = 'fas fa-times-circle';
                iconColor = 'text-danger';
            } else if (notificationData.message && notificationData.message.includes('product')) {
                icon = 'fas fa-box';
                iconColor = 'text-primary';
            }
            
            // Extract title and message
            const title = notificationData.title || notification.title || 'إشعار جديد';
            const message = notificationData.message || notification.message || 'لديك إشعار جديد';
            
            // Add notification to list
            let notificationsList = document.getElementById('notifications-list');
            let notificationHtml = `
                <li>
                    <a class="dropdown-item notification-item bg-light" href="#" data-notification-id="${notification.id}">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="${icon} ${iconColor}"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1 small"><strong>${title}</strong></p>
                                <p class="mb-1 small text-muted">${message}</p>
                                <small class="text-muted">الآن / Just now</small>
                            </div>
                        </div>
                    </a>
                </li>
            `;
            
            // Remove "No notifications" message if exists
            let noNotifications = notificationsList.querySelector('.text-center.text-muted');
            if (noNotifications && noNotifications.textContent.includes('No new notifications')) {
                noNotifications.parentElement.remove();
            }
            
            notificationsList.insertAdjacentHTML('afterbegin', notificationHtml);
            
            // Show browser notification
            if (Notification.permission === "granted") {
                new Notification(notification.data.title || "Digital Edge", {
                    body: notification.data.message || 'You have a new notification',
                    icon: '/favicon.ico',
                    tag: 'digital-edge-notification'
                });
            }
            
            // Add visual feedback (badge animation) to notification count
            const notificationBadge = document.getElementById('notification-count');
            if (notificationBadge) {
                notificationBadge.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    notificationBadge.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            }
        }
        
        // Request notification permission on page load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
        
        
        // Function to add notification to UI
        function addNotificationToUI(notification) {
            let notificationsList = document.getElementById('notifications-list');
            
            // Check if notifications list exists
            if (!notificationsList) {
                console.error('❌ notifications-list element not found!');
                return;
            }
            
            // Determine icon based on notification type
            let icon = 'fas fa-bell';
            let iconColor = 'text-primary';
            
            if (notification.data && notification.data.type) {
                switch(notification.data.type) {
                    case 'success':
                        icon = 'fas fa-check-circle';
                        iconColor = 'text-success';
                        break;
                    case 'warning':
                        icon = 'fas fa-exclamation-triangle';
                        iconColor = 'text-warning';
                        break;
                    case 'error':
                        icon = 'fas fa-times-circle';
                        iconColor = 'text-danger';
                        break;
                }
            }
            
            let notificationHtml = `
                <li>
                    <a class="dropdown-item notification-item bg-light" href="#" data-notification-id="${notification.id}">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <i class="${icon} ${iconColor}"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1 small">${notification.data.title || notification.data.message || 'New notification'}</p>
                                <small class="text-muted">${new Date(notification.created_at).toLocaleString()}</small>
                            </div>
                        </div>
                    </a>
                </li>
            `;
            
            // Remove "No notifications" message if exists
            let noNotifications = notificationsList.querySelector('.text-center.text-muted');
            if (noNotifications && noNotifications.textContent.includes('No new notifications')) {
                noNotifications.parentElement.remove();
            }
            
            notificationsList.insertAdjacentHTML('afterbegin', notificationHtml);
        }
        
        // Mark all as read
        document.getElementById('mark-all-read')?.addEventListener('click', function(e) {
            e.preventDefault();
            
            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('notification-count').textContent = '0';
                    document.querySelectorAll('.notification-item').forEach(item => {
                        item.classList.remove('bg-light');
                    });
                }
            });
        });
        
        // Mark single notification as read
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                let notificationId = this.dataset.notificationId;
                
                fetch(`/admin/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.remove('bg-light');
                        let count = parseInt(document.getElementById('notification-count').textContent);
                        if (count > 0) {
                            document.getElementById('notification-count').textContent = count - 1;
                        }
                    }
                });
            });
        });
        
        // Request notification permission
        if (Notification.permission === "default") {
            Notification.requestPermission();
        }
    </script>
    
    @yield('scripts')
</body>
</html>
