<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use App\Helpers\ActivityLogger;
use App\Events\OtpStatusUpdated;
use App\Events\NotificationSent;
use Carbon\Carbon;

class VerifyController extends Controller
{
    /**
     * Show verification form
     */
    public function showVerificationForm($identifier)
    {
        return view('auth.verify', compact('identifier'));
    }

    /**
     * Handle verification
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email_or_phone' => 'required|string',
            'code' => 'required|string|size:4',
        ]);

        $user = User::where(function ($query) use ($request) {
            $query->where('email', $request->email_or_phone)
                  ->orWhere('phone', $request->email_or_phone);
        })->first();

        if (!$user) {
            return redirect()->back()
                ->withErrors(['code' => 'User not found'])
                ->withInput();
        }

        if ($user->is_active) {
            return redirect()->route('login')
                ->with('success', 'Account already verified. Please login.');
        }

        if ($user->verification_code !== $request->code) {
            return redirect()->back()
                ->withErrors(['code' => 'Invalid verification code'])
                ->withInput();
        }

        if (Carbon::now()->greaterThan($user->verification_code_expires_at)) {
            return redirect()->back()
                ->withErrors(['code' => 'Verification code has expired. Please request a new one.'])
                ->withInput();
        }

        $user->update([
            'is_active' => true,
            'email_verified_at' => Carbon::now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        // Log activity
        ActivityLogger::log('account_verified', "Account verified: {$user->name}", $user);

        // Broadcast OTP verification success
        broadcast(new OtpStatusUpdated(
            $user->email ?? $user->phone,
            'verified',
            'تم التحقق بنجاح! / Verification successful!',
            ['user_id' => $user->id]
        ));

        // Send notification to admin about successful verification
        $adminUser = User::role('admin')->first();
        if ($adminUser) {
            $adminUser->notify(new \App\Notifications\CustomNotification(
                'تم تفعيل حساب!',
                "تم تفعيل حساب المستخدم: {$user->name} بنجاح / User account activated: {$user->name}",
                'success',
                ['user_id' => $user->id, 'action' => 'user_verified']
            ));
        }

        // Send welcome notification
        broadcast(new NotificationSent($user, [
            'title' => app()->getLocale() == 'ar' ? 'مرحباً بك!' : 'Welcome!',
            'message' => app()->getLocale() == 'ar' ? 'تم تفعيل حسابك بنجاح. يمكنك الآن تسجيل الدخول.' : 'Your account has been activated successfully. You can now login.',
            'data' => ['type' => 'account_verified']
        ], 'success'));

        return redirect()->route('login')
            ->with('success', 'Account verified successfully! You can now login.');
    }

    /**
     * Resend verification code
     */
    public function resendCode($identifier)
    {
        $user = User::where(function ($query) use ($identifier) {
            $query->where('email', $identifier)
                  ->orWhere('phone', $identifier);
        })->first();

        if (!$user) {
            return redirect()->route('register')
                ->withErrors(['email' => 'User not found']);
        }

        if ($user->is_active) {
            return redirect()->route('login')
                ->with('success', 'Account already verified. Please login.');
        }

        // Generate new code
        $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Send new code
        if ($user->email) {
            try {
                \Log::info('Resending verification code to: ' . $user->email);
                
                Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode, true));
                
                \Log::info('New verification code sent successfully to: ' . $user->email);
                
            } catch (\Exception $e) {
                \Log::error('Failed to resend verification email to ' . $user->email . ': ' . $e->getMessage());
                return redirect()->route('verify.show', ['identifier' => $identifier])
                    ->withErrors(['email' => 'حدث خطأ في إرسال الرمز الجديد. يرجى المحاولة مرة أخرى.']);
            }
        }

        return redirect()->route('verify.show', ['identifier' => $identifier])
            ->with('success', 'New verification code sent!');
    }
}
