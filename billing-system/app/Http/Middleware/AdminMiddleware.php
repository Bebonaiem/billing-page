<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        // Check if user is admin (modify this logic based on your user model)
        if ($user->email !== 'admin@example.com' && !($user->is_admin ?? false)) {
            abort(403, 'Unauthorized access.');
        }
        
        return $next($request);
    }
}
