<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    
    // Authentication
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // User profile
        Route::get('/user/profile', [AuthController::class, 'profile']);
        Route::put('/user/profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        
        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders/{id}/activate', [OrderController::class, 'activate']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);
        
        // Services
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/services/{id}', [ServiceController::class, 'show']);
        Route::post('/services/{id}/upgrade', [ServiceController::class, 'upgrade']);
        Route::post('/services/{id}/cancel', [ServiceController::class, 'cancel']);
        
        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
        Route::post('/invoices/{id}/pay', [InvoiceController::class, 'pay']);
        
        // Tickets
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::get('/tickets/{id}', [TicketController::class, 'show']);
        Route::post('/tickets/{id}/reply', [TicketController::class, 'reply']);
        Route::post('/tickets/{id}/close', [TicketController::class, 'close']);
    });
});

// Admin API routes (admin middleware required)
Route::prefix('admin/v1')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Products
    Route::apiResource('products', ProductController::class);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'adminIndex']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    
    // Services
    Route::get('/services', [ServiceController::class, 'adminIndex']);
    Route::put('/services/{id}/status', [ServiceController::class, 'updateStatus']);
    
    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'adminIndex']);
    Route::post('/invoices/{id}/mark-paid', [InvoiceController::class, 'markPaid']);
    
    // Users
    Route::get('/users', [AuthController::class, 'users']);
    Route::put('/users/{id}/status', [AuthController::class, 'updateUserStatus']);
    
    // Tickets
    Route::get('/tickets', [TicketController::class, 'adminIndex']);
    Route::post('/tickets/{id}/reply', [TicketController::class, 'adminReply']);
});
