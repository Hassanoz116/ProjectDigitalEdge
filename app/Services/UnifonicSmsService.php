<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnifonicSmsService
{
    protected $appSid;
    protected $senderId;
    protected $baseUrl = 'https://el.cloud.unifonic.com/rest';

    public function __construct()
    {
        $this->appSid = config('services.unifonic.app_sid');
        $this->senderId = config('services.unifonic.sender_id');
    }

    /**
     * Send SMS via Unifonic
     *
     * @param string $to Phone number
     * @param string $message Message content
     * @return bool Success status
     */
    public function sendSms($to, $message)
    {
        try {
            if (!$this->appSid) {
                Log::error('Unifonic not configured properly');
                return false;
            }

            // Remove + from phone number if exists
            $to = ltrim($to, '+');

            Log::info("Attempting to send SMS via Unifonic to: {$to}");

            $response = Http::post("{$this->baseUrl}/SMS/Messages", [
                'AppSid' => $this->appSid,
                'SenderID' => $this->senderId,
                'Recipient' => $to,
                'Body' => $message,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                if ($result['success'] === true) {
                    Log::info("SMS sent successfully via Unifonic. Message ID: {$result['data']['MessageID']}");
                    return true;
                } else {
                    Log::error("Unifonic API error: " . $result['message']);
                    return false;
                }
            } else {
                Log::error("Unifonic HTTP error: " . $response->status());
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Failed to send SMS via Unifonic to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via Unifonic
     *
     * @param string $phoneNumber
     * @param string $code
     * @param string $locale
     * @return bool
     */
    public function sendOtp($phoneNumber, $code, $locale = 'ar')
    {
        $message = $this->getOtpMessage($code, $locale);
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Get OTP message in Arabic (Unifonic is mainly for Arabic regions)
     *
     * @param string $code
     * @param string $locale
     * @return string
     */
    private function getOtpMessage($code, $locale = 'ar')
    {
        $messages = [
            'ar' => "رمز التحقق الخاص بك في Digital Edge هو: {$code}\nلا تشارك هذا الرمز مع أحد. صالح لمدة 15 دقيقة.",
            'en' => "Your Digital Edge verification code is: {$code}\nDo not share this code. Valid for 15 minutes."
        ];

        return $messages[$locale] ?? $messages['ar'];
    }

    /**
     * Check if Unifonic is configured
     *
     * @return bool
     */
    public function isConfigured()
    {
        return !empty($this->appSid) && !empty($this->senderId);
    }

    /**
     * Get account balance
     *
     * @return array|null
     */
    public function getBalance()
    {
        try {
            if (!$this->appSid) {
                return null;
            }

            $response = Http::post("{$this->baseUrl}/Account/GetBalance", [
                'AppSid' => $this->appSid,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Failed to get Unifonic balance: " . $e->getMessage());
            return null;
        }
    }
}
