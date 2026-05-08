<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class EnsureTempDir
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure temp directory exists
        $tempDir = storage_path('temp');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // Ensure uploads directory exists
        $uploadsDir = storage_path('uploads');
        if (!File::isDirectory($uploadsDir)) {
            File::makeDirectory($uploadsDir, 0755, true);
        }

        return $next($request);
    }
}
