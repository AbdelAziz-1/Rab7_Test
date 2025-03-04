<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\OTPController;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{

public function verifyEmail(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->sendError('User not found.');
        }

        //$this->otpController->sendOtp(new Request(['email' => $user->email]));

        return $this->sendResponse([], 'Verification code sent');
    } catch (\Exception $e) {
        
        Log::error("Error verifying email: " . $e->getMessage());

        
        return $this->sendError('Failed to verify email. Please try again later.');
    }
}

public function sendCode(Request $request)
{
    try {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('User not found');
        }

        $verificationCode = VerificationCode::where('code', $request->code)
            ->where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            return $this->sendError('Invalid or expired verification code');
        }

        $user->email_verified_at = now();
        $user->save();

        $verificationCode->delete();

        return $this->sendResponse([], 'Email verified successfully');
    } catch (\Exception $e) {
        // تسجيل الخطأ في ملف السجلات
        Log::error("Error verifying code: " . $e->getMessage());

        // إرجاع رسالة خطأ
        return $this->sendError('Failed to verify code. Please try again later.');
    }
}

protected function sendResponse($data, $message)
{
    return response()->json([
        'success' => true,
        'data' => $data,
        'message' => $message,
    ]);
}

protected function sendError($message)
{
    return response()->json([
        'success' => false,
        'message' => $message,
    ], 404);
}
};
    

