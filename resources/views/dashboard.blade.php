<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? 'لوحة التحكم' : 'Dashboard' }} - Digital Edge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" }};
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .welcome-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px 0;
        }
        .feature-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-digital-tachograph me-2"></i>
                Digital Edge
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>{{ app()->getLocale() == 'ar' ? 'الملف الشخصي' : 'Profile' }}</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>{{ app()->getLocale() == 'ar' ? 'الإعدادات' : 'Settings' }}</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i>{{ app()->getLocale() == 'ar' ? 'تسجيل الخروج' : 'Logout' }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-3">
                        {{ app()->getLocale() == 'ar' ? 'مرحباً بك، ' : 'Welcome, ' }}
                        <span class="text-primary">{{ Auth::user()->name }}</span>! 👋
                    </h1>
                    <p class="lead text-muted">
                        {{ app()->getLocale() == 'ar' ? 'حسابك مفعل ويمكنك الآن الوصول إلى جميع الميزات.' : 'Your account is activated and you can now access all features.' }}
                    </p>
                    <div class="d-flex align-items-center mt-3">
                        <span class="badge bg-success me-2">
                            <i class="fas fa-check-circle me-1"></i>
                            {{ app()->getLocale() == 'ar' ? 'حساب مفعل' : 'Account Verified' }}
                        </span>
                        <small class="text-muted">
                            {{ app()->getLocale() == 'ar' ? 'تم التفعيل في:' : 'Verified on:' }} 
                            {{ Auth::user()->email_verified_at ? Auth::user()->email_verified_at->format('Y-m-d') : 'N/A' }}
                        </small>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <i class="fas fa-user-check text-success" style="font-size: 5rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="row">
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-box-open text-primary feature-icon"></i>
                    <h5>{{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}</h5>
                    <p class="text-muted">{{ app()->getLocale() == 'ar' ? 'استعرض المنتجات المخصصة لك' : 'Browse your assigned products' }}</p>
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        {{ app()->getLocale() == 'ar' ? 'عرض المنتجات' : 'View Products' }}
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-user-edit text-info feature-icon"></i>
                    <h5>{{ app()->getLocale() == 'ar' ? 'الملف الشخصي' : 'Profile' }}</h5>
                    <p class="text-muted">{{ app()->getLocale() == 'ar' ? 'تحديث معلوماتك الشخصية' : 'Update your personal information' }}</p>
                    <a href="#" class="btn btn-outline-info btn-sm">
                        {{ app()->getLocale() == 'ar' ? 'تحديث الملف' : 'Update Profile' }}
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center">
                    <i class="fas fa-bell text-warning feature-icon"></i>
                    <h5>{{ app()->getLocale() == 'ar' ? 'الإشعارات' : 'Notifications' }}</h5>
                    <p class="text-muted">{{ app()->getLocale() == 'ar' ? 'تحقق من الإشعارات الجديدة' : 'Check your latest notifications' }}</p>
                    <a href="#" class="btn btn-outline-warning btn-sm">
                        {{ app()->getLocale() == 'ar' ? 'عرض الإشعارات' : 'View Notifications' }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ app()->getLocale() == 'ar' ? 'معلومات الحساب' : 'Account Information' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>{{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني:' : 'Email:' }}</strong><br>
                                <span class="text-muted">{{ Auth::user()->email ?? 'N/A' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>{{ app()->getLocale() == 'ar' ? 'رقم الهاتف:' : 'Phone:' }}</strong><br>
                                <span class="text-muted">{{ Auth::user()->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>{{ app()->getLocale() == 'ar' ? 'تاريخ التسجيل:' : 'Member Since:' }}</strong><br>
                                <span class="text-muted">{{ Auth::user()->created_at->format('Y-m-d') }}</span>
                            </div>
                            <div class="col-sm-6">
                                <strong>{{ app()->getLocale() == 'ar' ? 'الدور:' : 'Role:' }}</strong><br>
                                <span class="badge bg-primary">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            {{ app()->getLocale() == 'ar' ? 'إحصائيات سريعة' : 'Quick Stats' }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h4 class="text-primary">0</h4>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'المنتجات' : 'Products' }}</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-success">1</h4>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'الحساب' : 'Account' }}</small>
                            </div>
                            <div class="col-4">
                                <h4 class="text-info">0</h4>
                                <small class="text-muted">{{ app()->getLocale() == 'ar' ? 'الإشعارات' : 'Notifications' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
