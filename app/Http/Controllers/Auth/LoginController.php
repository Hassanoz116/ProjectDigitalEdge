<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use Carbon\Carbon;
use App\Helpers\ActivityLogger;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Determine if login is email or phone
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        // Find user
        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'بيانات الدخول غير صحيحة. / Invalid login credentials.',
            ])->withInput();
        }

        // Check if account is active
        if (!$user->is_active) {
            // Generate new verification code if expired
            if (!$user->verification_code || $user->verification_code_expires_at < now()) {
                $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $user->update([
                    'verification_code' => $verificationCode,
                    'verification_code_expires_at' => Carbon::now()->addMinutes(15),
                ]);

                // Send verification email if user has email
                if ($user->email) {
                    try {
                        Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
                        \Log::info('Verification email sent to inactive user: ' . $user->email);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send verification email: ' . $e->getMessage());
                    }
                }
            }

            // Redirect to verification page with appropriate message
            $identifier = $user->email ?? $user->phone;
            $contactMethod = $user->email ? 'البريد الإلكتروني' : 'رقم الهاتف';
            
            return redirect()->route('verify.show', ['identifier' => $identifier])
                ->with('warning', "حسابك غير مفعل. يرجى التحقق من {$contactMethod} لتفعيل حسابك.")
                ->with('user_id', $user->id);
        }

        // Login successful
        Auth::login($user);
        
        // Log activity
        ActivityLogger::log('user_login', "User logged in: {$user->name}", $user);

        // Redirect based on role
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->intended('/dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            ActivityLogger::log('user_logout', "User logged out: {$user->name}", $user);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Resend verification code for inactive user
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        // Find user by email or phone
        $user = User::where(function($query) use ($request) {
            $query->where('email', $request->identifier)
                  ->orWhere('phone', $request->identifier);
        })->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'المستخدم غير موجود.']);
        }

        if ($user->is_active) {
            return redirect()->route('login')
                ->with('success', 'الحساب مفعل بالفعل. يمكنك تسجيل الدخول.');
        }

        // Generate new verification code
        $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Send verification code
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode, true));
                \Log::info('Verification code resent to: ' . $user->email);
                
                return back()->with('success', 'تم إرسال رمز التحقق الجديد إلى بريدك الإلكتروني.');
            } catch (\Exception $e) {
                \Log::error('Failed to resend verification email: ' . $e->getMessage());
                return back()->withErrors(['email' => 'حدث خطأ في إرسال البريد الإلكتروني.']);
            }
        }

        // If no email, show message about phone verification (to be implemented)
        return back()->with('info', 'سيتم إرسال رمز التحقق عبر الرسائل النصية قريباً.');
    }
}
