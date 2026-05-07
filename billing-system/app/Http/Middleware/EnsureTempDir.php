<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTempDir
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure temporary directory exists and is writable
        $tempDir = sys_get_temp_dir();
        
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        if (!is_writable($tempDir)) {
            chmod($tempDir, 0755);
        }
        
        // Create Laravel-specific temp directories
        $laravelTempDirs = [
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/testing'),
        ];
        
        foreach ($laravelTempDirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            if (!is_writable($dir)) {
                chmod($dir, 0755);
            }
        }
        
        return $next($request);
    }
}
