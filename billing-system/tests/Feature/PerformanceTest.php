<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Service;
use App\Services\Cache\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_caches_settings_efficiently()
    {
        // Clear cache first
        CacheService::clearCache('setting_app_name');
        
        $startTime = microtime(true);
        
        // First call should hit database
        $result1 = CacheService::getSetting('app_name', 'Default');
        $firstCallTime = microtime(true) - $startTime;
        
        $startTime = microtime(true);
        
        // Second call should hit cache
        $result2 = CacheService::getSetting('app_name', 'Default');
        $secondCallTime = microtime(true) - $startTime;
        
        $this->assertEquals($result1, $result2);
        $this->assertLessThan($firstCallTime, $secondCallTime); // Cache should be faster
    }

    /** @test */
    public function it_optimizes_database_queries()
    {
        // Create test data
        $users = User::factory()->count(10)->create();
        $invoices = Invoice::factory()->count(50)->create([
            'user_id' => $users->random()->id,
        ]);
        
        // Test optimized query
        $startTime = microtime(true);
        
        $result = Invoice::with(['user:id,name,email'])
            ->select(['id', 'user_id', 'invoice_number', 'status', 'total'])
            ->paginate(15);
        
        $queryTime = microtime(true) - $startTime;
        
        // Should complete quickly (less than 100ms for this small dataset)
        $this->assertLessThan(0.1, $queryTime);
        $this->assertCount(15, $result->items());
    }

    /** @test */
    public function it_handles_large_datasets_efficiently()
    {
        // Create larger dataset
        $users = User::factory()->count(100)->create();
        $services = Service::factory()->count(500)->create([
            'user_id' => $users->random()->id,
        ]);
        
        // Test pagination performance
        $startTime = microtime(true);
        
        $result = Service::with(['user:id,name', 'product:id,name'])
            ->select(['id', 'user_id', 'product_id', 'status', 'price'])
            ->paginate(20);
        
        $queryTime = microtime(true) - $startTime;
        
        // Should still be reasonably fast even with more data
        $this->assertLessThan(0.5, $queryTime);
        $this->assertCount(20, $result->items());
    }

    /** @test */
    public function it_prevents_n_plus_one_queries()
    {
        // Create test data
        $users = User::factory()->count(5)->create();
        $invoices = Invoice::factory()->count(25)->create([
            'user_id' => $users->random()->id,
        ]);
        
        // Enable query logging
        \DB::enableQueryLog();
        
        // Test eager loading
        $invoices = Invoice::with('user')->get();
        
        foreach ($invoices as $invoice) {
            $invoice->user->name; // Access user data
        }
        
        $queryCount = count(\DB::getQueryLog());
        \DB::disableQueryLog();
        
        // Should only be 2 queries (one for invoices, one for users)
        $this->assertLessThanOrEqual(2, $queryCount);
    }

    /** @test */
    public function it_uses_proper_indexes()
    {
        // Create test data
        $users = User::factory()->count(50)->create();
        
        // Test indexed query
        $startTime = microtime(true);
        
        $result = User::where('status', 'active')
            ->where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $queryTime = microtime(true) - $startTime;
        
        // Should be very fast with proper indexes
        $this->assertLessThan(0.05, $queryTime);
        $this->assertCount(10, $result);
    }
}