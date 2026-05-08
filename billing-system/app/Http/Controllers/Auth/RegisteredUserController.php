<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request)
    {
        $user = $request->createUser();
        
        Auth::login($user);
        
        // Log successful registration
        Log::info("New user registered: {$user->email}");
        
        return redirect()->route('client.dashboard')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }
}