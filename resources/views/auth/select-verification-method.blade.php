<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ app()->getLocale() == 'ar' ? 'اختر طريقة التحقق' : 'Select Verification Method' }} - Digital Edge</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .verification-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            margin: 20px auto;
        }
        .verification-header {
            background: linear-gradient(135deg, #424242ff 0%, #4b65a2ff 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .verification-body {
            padding: 40px 30px;
        }
        .method-card {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid #e9ecef;
            border-radius: 15px;
            padding: 30px 20px;
            margin: 15px 0;
            text-align: center;
            background: #f8f9fa;
        }
        .method-card:hover {
            border-color: #007bff;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,123,255,0.2);
            background: #ffffff;
        }
        .method-card.selected {
            border-color: #28a745;
            background: #f8fff9;
            box-shadow: 0 10px 30px rgba(40,167,69,0.3);
        }
        .method-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .method-card:hover .method-icon {
            transform: scale(1.1);
        }
        .method-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .method-description {
            color: #6c757d;
            margin-bottom: 20px;
        }
        .contact-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid #2196f3;
        }
        .btn-send {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 15px 30px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40,167,69,0.4);
        }
        .btn-send:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .success-icon {
            color: #28a745;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-card">
            <div class="verification-header">
                <div class="mb-3">
                    <i class="fas fa-shield-check fa-3x"></i>
                </div>
                <h2 class="mb-0">
                    {{ app()->getLocale() == 'ar' ? 'اختر طريقة التحقق' : 'Choose Verification Method' }}
                </h2>
                <p class="mb-0 mt-3 opacity-75">
                    {{ app()->getLocale() == 'ar' ? 'تم إنشاء حسابك بنجاح! الآن اختر الطريقة المفضلة لتلقي رمز التحقق' : 'Your account has been created successfully! Now choose your preferred method to receive the verification code' }}
                </p>
            </div>
            
            <div class="verification-body">
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

                @if (session('info'))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.method.send') }}" id="verificationForm">
                    @csrf
                    
                    <div class="row">
                        @if($user->email)
                        <div class="col-md-6">
                            <div class="method-card" data-method="email" onclick="selectMethod('email')">
                                <i class="fas fa-envelope method-icon text-primary"></i>
                                <div class="method-title">
                                    {{ app()->getLocale() == 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                                </div>
                                <div class="method-description">
                                    {{ app()->getLocale() == 'ar' ? 'سيتم إرسال رمز التحقق إلى بريدك الإلكتروني' : 'Verification code will be sent to your email' }}
                                </div>
                                <div class="contact-info">
                                    <i class="fas fa-envelope me-2"></i>
                                    <strong>{{ $user->email }}</strong>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="radio" name="verification_method" 
                                           id="method_email" value="email">
                                    <label class="form-check-label fw-bold" for="method_email">
                                        {{ app()->getLocale() == 'ar' ? 'اختر هذه الطريقة' : 'Select this method' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($user->phone)
                        <div class="col-md-6">
                            <div class="method-card" data-method="phone" onclick="selectMethod('phone')">
                                <i class="fas fa-mobile-alt method-icon text-success"></i>
                                <div class="method-title">
                                    {{ app()->getLocale() == 'ar' ? 'رقم الهاتف' : 'Phone Number' }}
                                </div>
                                <div class="method-description">
                                    {{ app()->getLocale() == 'ar' ? 'سيتم إرسال رمز التحقق عبر الرسائل النصية' : 'Verification code will be sent via SMS' }}
                                </div>
                                <div class="contact-info">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    <strong>{{ $user->phone }}</strong>
                                </div>
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="radio" name="verification_method" 
                                           id="method_phone" value="phone">
                                    <label class="form-check-label fw-bold" for="method_phone">
                                        {{ app()->getLocale() == 'ar' ? 'اختر هذه الطريقة' : 'Select this method' }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @error('verification_method')
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                        </div>
                    @enderror

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-send btn-lg" id="sendBtn" disabled>
                            <i class="fas fa-paper-plane {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}"></i>
                            {{ app()->getLocale() == 'ar' ? 'إرسال رمز التحقق' : 'Send Verification Code' }}
                        </button>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">
                            {{ app()->getLocale() == 'ar' ? 'تريد تغيير معلومات الحساب؟' : 'Want to change account information?' }}
                            <a href="{{ route('register') }}" class="text-decoration-none">
                                {{ app()->getLocale() == 'ar' ? 'العودة للتسجيل' : 'Back to Registration' }}
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectMethod(method) {
            // Remove selected class from all cards
            document.querySelectorAll('.method-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            document.querySelector(`.method-card[data-method="${method}"]`).classList.add('selected');
            
            // Check the radio button
            document.getElementById(`method_${method}`).checked = true;
            
            // Enable send button
            document.getElementById('sendBtn').disabled = false;
        }

        // Handle radio button changes
        document.querySelectorAll('input[name="verification_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    selectMethod(this.value);
                }
            });
        });

        // Form submission handling
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="verification_method"]:checked');
            if (!selectedMethod) {
                e.preventDefault();
                alert('{{ app()->getLocale() == 'ar' ? 'يرجى اختيار طريقة التحقق' : 'Please select a verification method' }}');
                return false;
            }
            
            // Disable button to prevent double submission
            const sendBtn = document.getElementById('sendBtn');
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ app()->getLocale() == 'ar' ? 'جاري الإرسال...' : 'Sending...' }}';
        });
    </script>
</body>
</html>
