<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Debug: Log user info
        \Log::info('AdminMiddleware Check:', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'is_admin_value' => $user->is_admin,
            'is_admin_method' => $user->isAdmin(),
        ]);
        
        // Check if user is admin using both direct property and method
        if (!$user->is_admin && !$user->isAdmin()) {
            abort(403, 'Unauthorized access. User is not an admin.');
        }
        
        return $next($request);
    }
}
