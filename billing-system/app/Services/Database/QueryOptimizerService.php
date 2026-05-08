<?php

namespace App\Services\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryOptimizerService
{
    /**
     * Optimize common queries with proper indexing hints
     */
    public static function optimizeUserQuery(Builder $query): Builder
    {
        return $query->select(['id', 'name', 'email', 'first_name', 'last_name', 'is_admin', 'status', 'created_at'])
                   ->withCount(['invoices', 'services', 'tickets']);
    }

    /**
     * Optimize invoice queries with eager loading
     */
    public static function optimizeInvoiceQuery(Builder $query): Builder
    {
        return $query->select([
                'id', 'user_id', 'invoice_number', 'status', 'subtotal', 
                'discount', 'tax', 'total', 'amount_paid', 'balance',
                'invoice_date', 'due_date', 'paid_date', 'created_at'
            ])
            ->with(['user:id,name,email,first_name,last_name'])
            ->withCount(['items', 'payments']);
    }

    /**
     * Optimize order queries with eager loading
     */
    public static function optimizeOrderQuery(Builder $query): Builder
    {
        return $query->select([
                'id', 'user_id', 'order_number', 'status', 'total', 
                'currency', 'created_at', 'updated_at'
            ])
            ->with(['user:id,name,email', 'items.product:id,name,price'])
            ->withCount(['items']);
    }

    /**
     * Optimize service queries with eager loading
     */
    public static function optimizeServiceQuery(Builder $query): Builder
    {
        return $query->select([
                'id', 'user_id', 'product_id', 'status', 'price', 
                'billing_cycle', 'next_billing_date', 'created_at'
            ])
            ->with(['user:id,name,email', 'product:id,name,type'])
            ->withCount(['invoices']);
    }

    /**
     * Optimize ticket queries with eager loading
     */
    public static function optimizeTicketQuery(Builder $query): Builder
    {
        return $query->select([
                'id', 'user_id', 'department_id', 'subject', 'status', 
                'priority', 'created_at', 'updated_at'
            ])
            ->with(['user:id,name,email', 'department:id,name'])
            ->withCount(['replies', 'attachments'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Log slow queries for monitoring
     */
    public static function logSlowQuery(callable $callback, string $queryName = 'query', float $threshold = 100.0)
    {
        $startTime = microtime(true);
        
        DB::enableQueryLog();
        
        $result = $callback();
        
        $executionTime = (microtime(true) - $startTime) * 1000;
        
        if ($executionTime > $threshold) {
            $queries = DB::getQueryLog();
            Log::warning("Slow query detected: {$queryName}", [
                'execution_time_ms' => $executionTime,
                'query_count' => count($queries),
                'queries' => $queries,
            ]);
        }
        
        DB::disableQueryLog();
        
        return $result;
    }

    /**
     * Get database statistics for monitoring
     */
    public static function getDatabaseStats(): array
    {
        return [
            'total_queries' => DB::getQueryCount(),
            'slow_queries' => self::getSlowQueryCount(),
            'connection_time' => DB::select('SELECT CONNECTION_ID() as connection_id')[0]->connection_id,
        ];
    }

    /**
     * Get slow query count (this would need to be implemented based on your database)
     */
    private static function getSlowQueryCount(): int
    {
        try {
            // This is a placeholder - implement based on your database system
            // For MySQL: SELECT COUNT(*) FROM mysql.slow_log WHERE start_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Batch update for better performance
     */
    public static function batchUpdate(string $table, array $data, array $where): int
    {
        return DB::table($table)
            ->where($where)
            ->update($data);
    }

    /**
     * Bulk insert for better performance
     */
    public static function bulkInsert(string $table, array $data): bool
    {
        try {
            DB::table($table)->insert($data);
            return true;
        } catch (\Exception $e) {
            Log::error("Bulk insert failed for table {$table}: " . $e->getMessage());
            return false;
        }
    }
}