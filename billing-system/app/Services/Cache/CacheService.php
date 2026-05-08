<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use App\Models\User;
use App\Models\Product;
use App\Models\Currency;

class CacheService
{
    /**
     * Cache settings for performance
     */
    public static function getSetting(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", now()->addHours(6), function () use ($key, $default) {
            return Setting::getValue($key, $default);
        });
    }

    /**
     * Cache user data for performance
     */
    public static function getUser(int $userId)
    {
        return Cache::remember("user_{$userId}", now()->addMinutes(30), function () use ($userId) {
            return User::select('id', 'name', 'email', 'first_name', 'last_name', 'is_admin', 'status')
                       ->find($userId);
        });
    }

    /**
     * Cache active products for performance
     */
    public static function getActiveProducts()
    {
        return Cache::remember('active_products', now()->addHours(2), function () {
            return Product::where('is_visible', true)
                         ->with(['category:id,name', 'configOptions.values'])
                         ->orderBy('sort_order')
                         ->orderBy('name')
                         ->get();
        });
    }

    /**
     * Cache currencies for performance
     */
    public static function getCurrencies()
    {
        return Cache::remember('currencies', now()->addDays(1), function () {
            return Currency::where('active', true)->get();
        });
    }

    /**
     * Cache dashboard statistics
     */
    public static function getDashboardStats()
    {
        return Cache::remember('dashboard_stats', now()->addMinutes(15), function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'total_revenue' => DB::table('payments')
                    ->where('status', 'completed')
                    ->sum('amount'),
                'pending_orders' => DB::table('orders')
                    ->where('status', 'pending')
                    ->count(),
                'overdue_invoices' => DB::table('invoices')
                    ->where('status', 'unpaid')
                    ->whereDate('due_date', '<', now())
                    ->count(),
            ];
        });
    }

    /**
     * Clear cache for a specific key or pattern
     */
    public static function clearCache(string $key = null)
    {
        if ($key) {
            Cache::forget($key);
        } else {
            Cache::flush();
        }
    }

    /**
     * Clear user-specific cache
     */
    public static function clearUserCache(int $userId)
    {
        Cache::forget("user_{$userId}");
    }

    /**
     * Clear settings cache
     */
    public static function clearSettingsCache()
    {
        $keys = Cache::getRedis()->keys('setting_*');
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear product cache
     */
    public static function clearProductCache()
    {
        Cache::forget('active_products');
    }

    /**
     * Clear dashboard cache
     */
    public static function clearDashboardCache()
    {
        Cache::forget('dashboard_stats');
    }
}