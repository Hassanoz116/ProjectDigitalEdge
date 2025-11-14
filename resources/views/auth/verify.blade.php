<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('تحقق من الحساب') }} - Digital Edge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', sans-serif" : "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" }};
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .verify-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            margin: 20px auto;
        }
        .verify-header {
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .verify-body {
            padding: 40px;
        }
        .code-input {
            font-size: 2rem;
            text-align: center;
            letter-spacing: {{ app()->getLocale() == 'ar' ? '0.5rem' : '1rem' }};
            font-weight: bold;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .code-input:focus {
            border-color: #4b65a2ff;
            box-shadow: 0 0 0 0.2rem rgba(75, 101, 162, 0.25);
        }
        .btn-verify {
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(75, 101, 162, 0.4);
        }
        .resend-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        .contact-method {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #2196f3;
        }
        [dir="rtl"] .contact-method {
            border-left: none;
            border-right: 4px solid #2196f3;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verify-card">
            <div class="verify-header">
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h2 class="mb-0">
                    {{ app()->getLocale() == 'ar' ? 'تحقق من حسابك' : 'Verify Your Account' }}
                </h2>
                <p class="mb-0 mt-2">
                    @if(app()->getLocale() == 'ar')
                        أدخل الرمز المكون من 4 أرقام المرسل إلى {{ strpos($identifier, '@') !== false ? 'بريدك الإلكتروني' : 'رقم هاتفك' }}
                    @else
                        Enter the 4-digit code sent to your {{ strpos($identifier, '@') !== false ? 'email' : 'phone' }}
                    @endif
                </p>
            </div>
            <div class="verify-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="warning-box">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    </div>
                @endif

                @if (session('sms_info'))
                    <div class="alert alert-info">
                        <i class="fas fa-mobile-alt me-2"></i>{{ session('sms_info') }}
                    </div>
                @endif

                @if (session('sms_error'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('sms_error') }}
                    </div>
                @endif

                @if (session('email_error'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('email_error') }}
                    </div>
                @endif

                <!-- Contact Method Info -->
                <div class="contact-method">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-{{ strpos($identifier, '@') !== false ? 'envelope' : 'phone' }} me-3 text-primary"></i>
                        <div>
                            <strong>
                                {{ app()->getLocale() == 'ar' ? 'طريقة التواصل:' : 'Contact Method:' }}
                            </strong>
                            <br>
                            <span class="text-muted">{{ $identifier }}</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('verify.submit') }}">
                    @csrf
                    <input type="hidden" name="email_or_phone" value="{{ $identifier }}">

                    <div class="mb-4">
                        <label for="code" class="form-label text-center d-block">
                            {{ app()->getLocale() == 'ar' ? 'رمز التحقق' : 'Verification Code' }}
                        </label>
                        <input type="text" 
                               class="form-control code-input @error('code') is-invalid @enderror" 
                               id="code" 
                               name="code" 
                               maxlength="4" 
                               pattern="\d{4}"
                               placeholder="____"
                               required 
                               autofocus>
                        @error('code')
                            <div class="invalid-feedback text-center">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-verify w-100">
                        <i class="fas fa-check-circle {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                        {{ app()->getLocale() == 'ar' ? 'تحقق من الحساب' : 'Verify Account' }}
                    </button>
                </form>

                <!-- Resend Section -->
                <div class="resend-section">
                    <p class="text-muted mb-3">
                        {{ app()->getLocale() == 'ar' ? 'لم تستلم الرمز؟' : "Didn't receive the code?" }}
                    </p>
                    
                    <form method="POST" action="{{ route('login.resend') }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="identifier" value="{{ $identifier }}">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-redo {{ app()->getLocale() == 'ar' ? 'ms-1' : 'me-1' }}"></i>
                            {{ app()->getLocale() == 'ar' ? 'إعادة إرسال الرمز' : 'Resend Code' }}
                        </button>
                    </form>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}" class="text-decoration-none">
                        <i class="fas fa-arrow-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }} {{ app()->getLocale() == 'ar' ? 'ms-1' : 'me-1' }}"></i>
                        {{ app()->getLocale() == 'ar' ? 'العودة لتسجيل الدخول' : 'Back to Login' }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus and format code input
        document.getElementById('code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
