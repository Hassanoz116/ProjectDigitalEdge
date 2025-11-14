<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ActivityLogger;
use Carbon\Carbon;
use App\Mail\VerificationCodeMail;

class RegisterController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        $countries = Country::all();
        return view('auth.register', compact('countries'));
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20|unique:users',
            'phone_code' => 'nullable|string|max:10',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
                'confirmed'
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate that at least email or phone is provided
        if (!$request->email && !$request->phone) {
            return redirect()->back()
                ->withErrors(['email' => 'يجب إدخال البريد الإلكتروني أو رقم الهاتف على الأقل / Either email or phone number is required'])
                ->withInput();
        }

        // Generate 4-digit verification code
        $verificationCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

        // Combine phone code with phone number
        $fullPhone = null;
        if ($request->phone && $request->phone_code) {
            $fullPhone = $request->phone_code . $request->phone;
        } elseif ($request->phone) {
            $fullPhone = $request->phone;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $fullPhone,
            'country_id' => $request->country_id,
            'city_id' => $request->city_id,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(15),
            'is_active' => false,
        ]);

        // Assign default role
        $user->assignRole('user');

        // Send welcome notification to admin
        $adminUser = User::role('admin')->first();
        if ($adminUser) {
            $adminUser->notify(new \App\Notifications\CustomNotification(
                'مستخدم جديد!',
                "تم تسجيل مستخدم جديد: {$user->name} / New user registered: {$user->name}",
                'info',
                ['user_id' => $user->id, 'action' => 'user_registered']
            ));
        }

        // Log activity
        ActivityLogger::log('user_registered', "User registered: {$user->name}", $user);

        // Store user ID in session for verification method selection
        session(['pending_verification_user_id' => $user->id]);

        // Redirect to verification method selection page
        return redirect()->route('verification.method.select')
            ->with('success', 'تم إنشاء الحساب بنجاح! الآن اختر طريقة التحقق المفضلة لديك. / Account created successfully! Now choose your preferred verification method.');
    }
}
