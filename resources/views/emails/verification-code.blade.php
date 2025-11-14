<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $isResend ? 'رمز التحقق الجديد' : 'رمز التحقق' }} - Digital Edge</title>
    <style>
        body {
            font-family: {{ app()->getLocale() == 'ar' ? "'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" : "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif" }};
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
            display: block;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .code-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .code-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            margin: 20px 0;
            color: #495057;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 14px;
            color: #6c757d;
        }
        .footer .company {
            font-weight: 600;
            color: #007bff;
            margin-bottom: 5px;
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #007bff, transparent);
            margin: 30px 0;
            border: none;
        }
        .security-note {
            font-size: 12px;
            color: #868e96;
            font-style: italic;
            margin-top: 20px;
        }
        
        /* RTL Support */
        [dir="rtl"] .code {
            letter-spacing: 4px;
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <span class="icon">🔐</span>
            <h1>{{ $isResend ? 'رمز التحقق الجديد' : 'رمز التحقق' }}</h1>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">
                {{ $isResend ? 'New Verification Code' : 'Verification Code' }}
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Arabic Section -->
            <div class="greeting">
                مرحباً <strong>{{ $user->name }}</strong>،
            </div>

            <div class="message">
                {{ $isResend ? 'تم إنشاء رمز تحقق جديد لحسابك.' : 'شكراً لك على التسجيل في منصة Digital Edge.' }}
                <br>
                {{ $isResend ? 'يرجى استخدام الرمز التالي لتفعيل حسابك:' : 'يرجى استخدام رمز التحقق التالي لتفعيل حسابك:' }}
            </div>

            <!-- Verification Code -->
            <div class="code-container">
                <div class="code-label">رمز التحقق / Verification Code</div>
                <div class="code">{{ $verificationCode }}</div>
            </div>

            <div class="warning">
                <strong>⚠️ تنبيه مهم:</strong>
                <ul style="margin: 10px 0; padding-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 20px;">
                    <li>هذا الرمز صالح لمدة <strong>15 دقيقة</strong> فقط</li>
                    <li>لا تشارك هذا الرمز مع أي شخص آخر</li>
                    <li>إذا لم تقم بطلب هذا الرمز، يرجى تجاهل هذه الرسالة</li>
                </ul>
            </div>

            <hr class="divider">

            <!-- English Section -->
            <div class="greeting">
                Hello <strong>{{ $user->name }}</strong>,
            </div>

            <div class="message">
                {{ $isResend ? 'A new verification code has been generated for your account.' : 'Thank you for registering with Digital Edge platform.' }}
                <br>
                Please use the verification code above to activate your account.
            </div>

            <div class="warning">
                <strong>⚠️ Important Notice:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This code is valid for <strong>15 minutes</strong> only</li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this code, please ignore this message</li>
                </ul>
            </div>

            <div class="security-note">
                تم إرسال هذا البريد الإلكتروني في {{ now()->format('Y-m-d H:i:s') }} (UTC+3)
                <br>
                This email was sent on {{ now()->format('Y-m-d H:i:s') }} (UTC+3)
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="company">Digital Edge Platform</div>
            <div>منصة رقمية متطورة لإدارة الأعمال</div>
            <div>Advanced Digital Platform for Business Management</div>
            <div style="margin-top: 10px; font-size: 12px;">
                © {{ date('Y') }} Digital Edge. جميع الحقوق محفوظة - All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
