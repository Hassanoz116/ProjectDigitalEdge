{{ $isResend ? 'New Verification Code' : 'Verification Code' }} - Digital Edge

Hello {{ $user->name }},

{{ $isResend ? 'A new verification code has been generated for your account.' : 'Thank you for registering with Digital Edge platform.' }}

Your verification code is: {{ $verificationCode }}

⚠️ Important Notice:
- This code is valid for 15 minutes only
- Do not share this code with anyone
- If you didn't request this code, please ignore this message

Thank you,
Digital Edge Team

This email was sent on: {{ now()->format('Y-m-d H:i:s') }} (UTC+3)

© {{ date('Y') }} Digital Edge. جميع الحقوق محفوظة - All rights reserved.
