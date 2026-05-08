<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        if ($request->authenticate()) {
            $request->session()->regenerate();
            
            // Update last login
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);
            
            // Log successful login
            Log::info("User logged in: {$user->email}");
            
            // Redirect based on user role
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('client.dashboard');
            }
        }

        // Log failed login attempt
        Log::warning("Failed login attempt for: {$request->input('email')}");

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Log logout
        if ($user) {
            Log::info("User logged out: {$user->email}");
        }

        return redirect('/');
    }
}