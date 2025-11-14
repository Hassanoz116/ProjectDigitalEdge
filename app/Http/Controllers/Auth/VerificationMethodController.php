<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use App\Services\SmsService;
use App\Events\OtpStatusUpdated;
use Carbon\Carbon;

class VerificationMethodController extends Controller
{
    /**
     * Show verification method selection page
     */
    public function showMethodSelection()
    {
        // Check if user ID is in session
        $userId = session('pending_verification_user_id');
        if (!$userId) {
            return redirect()->route('register')
                ->with('error', 'جلسة التحقق منتهية الصلاحية. يرجى التسجيل مرة أخرى. / Verification session expired. Please register again.');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'المستخدم غير موجود. يرجى التسجيل مرة أخرى. / User not found. Please register again.');
        }

        // Check if user has at least one contact method
        if (!$user->email && !$user->phone) {
            return redirect()->route('register')
                ->with('error', 'لا توجد طريقة اتصال متاحة. يرجى التسجيل مرة أخرى. / No contact method available. Please register again.');
        }

        return view('auth.select-verification-method', compact('user'));
    }

    /**
     * Send verification code via selected method
     */
    public function sendVerificationCode(Request $request)
    {
        // Validate verification method
        $validator = Validator::make($request->all(), [
            'verification_method' => 'required|in:email,phone',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get user from session
        $userId = session('pending_verification_user_id');
        if (!$userId) {
            return redirect()->route('register')
                ->with('error', 'جلسة التحقق منتهية الصلاحية. يرجى التسجيل مرة أخرى. / Verification session expired. Please register again.');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'المستخدم غير موجود. يرجى التسجيل مرة أخرى. / User not found. Please register again.');
        }

        $method = $request->verification_method;

        // Validate that the selected method is available for this user
        if ($method === 'email' && !$user->email) {
            return redirect()->back()
                ->withErrors(['verification_method' => 'البريد الإلكتروني غير متوفر لهذا المستخدم / Email not available for this user']);
        }

        if ($method === 'phone' && !$user->phone) {
            return redirect()->back()
                ->withErrors(['verification_method' => 'رقم الهاتف غير متوفر لهذا المستخدم / Phone number not available for this user']);
        }

        // Generate new verification code
        $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // Update user with new code
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Send verification code
        $sent = false;
        $identifier = null;

        if ($method === 'email') {
            try {
                \Log::info('Sending verification email to: ' . $user->email);
                
                Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
                
                $sent = true;
                $identifier = $user->email;
                \Log::info('Verification email sent successfully to: ' . $user->email);
                
                // Broadcast OTP sent event
                broadcast(new OtpStatusUpdated(
                    $identifier, 
                    'sent', 
                    'تم إرسال رمز التحقق إلى بريدك الإلكتروني / Verification code sent to your email',
                    ['method' => 'email']
                ));
                
            } catch (\Exception $e) {
                \Log::error('Failed to send verification email: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'فشل في إرسال البريد الإلكتروني. يرجى المحاولة مرة أخرى. / Failed to send email. Please try again.');
            }
        } elseif ($method === 'phone') {
            try {
                \Log::info('Attempting to send SMS verification to: ' . $user->phone);
                
                $smsService = new SmsService();
                
                if ($smsService->isConfigured()) {
                    // Send actual SMS
                    $locale = app()->getLocale();
                    $smsSent = $smsService->sendOtp($user->phone, $verificationCode, $locale);
                    
                    if ($smsSent) {
                        $sent = true;
                        $identifier = $user->phone;
                        \Log::info('SMS verification sent successfully to: ' . $user->phone);
                        
                        // Broadcast OTP sent event
                        broadcast(new OtpStatusUpdated(
                            $identifier, 
                            'sent', 
                            'تم إرسال رمز التحقق إلى هاتفك / Verification code sent to your phone',
                            ['method' => 'sms']
                        ));
                    } else {
                        throw new \Exception('SMS service failed to send message');
                    }
                } else {
                    // Fallback: Show code in session for testing
                    \Log::warning('SMS service not configured. Showing code in session for testing.');
                    $sent = true;
                    $identifier = $user->phone;
                    
                    session()->flash('sms_info', 'خدمة الرسائل النصية غير مفعلة حالياً. يمكنك استخدام الرمز: ' . $verificationCode . ' / SMS service not configured. You can use code: ' . $verificationCode);
                }
                
            } catch (\Exception $e) {
                \Log::error('Failed to send SMS verification: ' . $e->getMessage());
                return redirect()->back()
                    ->with('error', 'فشل في إرسال الرسالة النصية. يرجى المحاولة مرة أخرى. / Failed to send SMS. Please try again.');
            }
        }

        if ($sent) {
            // Clear the pending verification session
            session()->forget('pending_verification_user_id');
            
            // Redirect to verification page
            return redirect()->route('verify.show', ['identifier' => $identifier])
                ->with('success', 'تم إرسال رمز التحقق بنجاح! / Verification code sent successfully!');
        }

        return redirect()->back()
            ->with('error', 'حدث خطأ في إرسال رمز التحقق. يرجى المحاولة مرة أخرى. / Error sending verification code. Please try again.');
    }
}
