<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form.
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Delete any existing tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Create a new token
        $token = Str::random(60);
        $hashedToken = Hash::make($token);
        
        // Store the hashed token
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $hashedToken,
            'created_at' => now(),
        ]);

        // In a real application, you would send an email here
        // For now, we'll just show success message
        Log::info("Password reset token generated for {$user->email}: {$token}");
        
        return back()->with('status', 'We have emailed your password reset link!');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm($token)
    {
        // Find the token by checking all unexpired tokens
        $resetTokens = DB::table('password_reset_tokens')
            ->where('created_at', '>', now()->subHours(24))
            ->get();

        $validToken = null;
        foreach ($resetTokens as $tokenRecord) {
            if (Hash::check($token, $tokenRecord->token)) {
                $validToken = $tokenRecord;
                break;
            }
        }

        if (!$validToken) {
            return redirect()->route('password.request')
                ->with('error', 'Invalid or expired password reset token.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $validToken->email
        ]);
    }

    /**
     * Reset the password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find the valid token
        $resetTokens = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('created_at', '>', now()->subHours(24))
            ->get();

        $validToken = null;
        foreach ($resetTokens as $tokenRecord) {
            if (Hash::check($request->token, $tokenRecord->token)) {
                $validToken = $tokenRecord;
                break;
            }
        }

        if (!$validToken) {
            return back()->with('error', 'Invalid or expired password reset token.');
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete all reset tokens for this email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        Log::info("Password reset successfully for {$user->email}");

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully!');
    }
}