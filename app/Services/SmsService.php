<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $twilio;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.from');

        if ($sid && $token) {
            $this->twilio = new Client($sid, $token);
        }
    }

    /**
     * Send SMS message
     *
     * @param string $to Phone number with country code
     * @param string $message Message content
     * @return bool Success status
     */
    public function sendSms($to, $message)
    {
        try {
            if (!$this->twilio) {
                Log::error('Twilio not configured properly');
                return false;
            }

            // Ensure phone number starts with +
            if (!str_starts_with($to, '+')) {
                $to = '+' . $to;
            }

            Log::info("Attempting to send SMS to: {$to}");

            $result = $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message
            ]);

            Log::info("SMS sent successfully. SID: {$result->sid}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send SMS to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via SMS
     *
     * @param string $phoneNumber
     * @param string $code
     * @param string $locale
     * @return bool
     */
    public function sendOtp($phoneNumber, $code, $locale = 'en')
    {
        $message = $this->getOtpMessage($code, $locale);
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Get OTP message in different languages
     *
     * @param string $code
     * @param string $locale
     * @return string
     */
    private function getOtpMessage($code, $locale = 'en')
    {
        $messages = [
            'ar' => "رمز التحقق الخاص بك في Digital Edge هو: {$code}\nلا تشارك هذا الرمز مع أحد.\nصالح لمدة 15 دقيقة.",
            'en' => "Your Digital Edge verification code is: {$code}\nDo not share this code with anyone.\nValid for 15 minutes."
        ];

        return $messages[$locale] ?? $messages['en'];
    }

    /**
     * Check if SMS service is configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return $this->twilio !== null;
    }

    /**
     * Get available SMS providers for fallback
     *
     * @return array
     */
    public static function getAvailableProviders()
    {
        return [
            'twilio' => [
                'name' => 'Twilio',
                'description' => 'Global SMS service with high reliability',
                'website' => 'https://www.twilio.com/',
                'pricing' => 'Pay per message (~$0.0075 per SMS)'
            ],
            'aws_sns' => [
                'name' => 'AWS SNS',
                'description' => 'Amazon Simple Notification Service',
                'website' => 'https://aws.amazon.com/sns/',
                'pricing' => 'Pay per message (~$0.0075 per SMS)'
            ],
            'vonage' => [
                'name' => 'Vonage (Nexmo)',
                'description' => 'Global communications platform',
                'website' => 'https://www.vonage.com/',
                'pricing' => 'Pay per message (~$0.0072 per SMS)'
            ],
            'unifonic' => [
                'name' => 'Unifonic',
                'description' => 'Saudi Arabia SMS service',
                'website' => 'https://www.unifonic.com/',
                'pricing' => 'Local rates for Saudi Arabia'
            ],
            'taqnyat' => [
                'name' => 'Taqnyat',
                'description' => 'Saudi Arabia SMS service',
                'website' => 'https://taqnyat.sa/',
                'pricing' => 'Local rates for Saudi Arabia'
            ]
        ];
    }
}
