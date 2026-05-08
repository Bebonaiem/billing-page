<?php

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Client\Dashboard as ClientDashboard;
use App\Livewire\Checkout\Checkout;
use App\Livewire\Cart\Cart;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Cart\CartService;
use App\Services\Billing\InvoiceService;
use App\Services\Payment\PayPalGateway;
use App\Services\Payment\StripeGateway;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/order', function () {
    $products = Product::query()
        ->visible()
        ->with(['category', 'configOptions.values'])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    return view('order', [
        'products' => $products,
    ]);
})->name('order');

Route::post('/cart/add/{product}', function (Product $product, CartService $cartService) {
    if (!$product->is_visible || !$product->hasStock()) {
        abort(404);
    }

    $product->loadMissing('configOptions.values');

    $selectedOptions = request()->input('config_options', []);
    $selectedOptions = is_array($selectedOptions) ? $selectedOptions : [];
    $normalizedOptions = [];

    foreach ($product->configOptions as $option) {
        $valueId = $selectedOptions[$option->id] ?? null;

        if ($option->is_required && empty($valueId)) {
            return redirect()->route('order')
                ->with('error', "Please select {$option->name} for {$product->name}.");
        }

        if (!empty($valueId)) {
            $value = $option->values->firstWhere('id', (int) $valueId);
            if (!$value) {
                abort(422);
            }

            $normalizedOptions[$option->id] = (int) $value->id;
        }
    }

    $cartService->addItem($product, $normalizedOptions);

    return redirect()->route('checkout')->with('success', "{$product->name} added to your cart.");
})->name('cart.add');

Route::get('/cart', function () {
    return view('cart');
})->name('cart');

Route::get('/checkout', \App\Livewire\Checkout\Checkout::class)->name('checkout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('login.submit');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest', 'throttle:3,60', 'throttle:10,1']);

// Password reset routes
Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware(['guest', 'throttle:3,60'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| Client Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', ClientDashboard::class)->name('dashboard');
    
    Route::get('/services', \App\Livewire\Client\Services::class)->name('services');
    
    Route::get('/invoices', \App\Livewire\Client\Invoices::class)->name('invoices');

    Route::get('/invoices/{invoice}/print', function (Invoice $invoice) {
        abort_unless($invoice->user_id === Auth::id(), 403);

        $invoice->loadMissing(['user', 'items', 'payments', 'order']);

        app(InvoiceService::class)->generatePdf($invoice);

        return view('client.invoice-print', [
            'invoice' => $invoice,
        ]);
    })->name('invoices.print');

    Route::get('/invoices/{invoice}/pdf', function (Invoice $invoice) {
        abort_unless($invoice->user_id === Auth::id(), 403);

        $path = app(InvoiceService::class)->generatePdf($invoice);

        return response()->download($path, basename($path), [
            'Content-Type' => 'application/pdf',
        ]);
    })->name('invoices.pdf');
    
    Route::get('/tickets', \App\Livewire\Client\Tickets::class)->name('tickets');
    
    Route::get('/tickets/create', function () {
        return view('client.tickets.create');
    })->name('tickets.create');
    
    Route::post('/tickets', function () {
        request()->validate([
            'subject' => 'required|string|max:255',
            'department' => 'required|string|in:billing,technical,sales,general',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'message' => 'required|string|min:10',
        ]);

        // Create ticket logic would go here
        // For now, redirect back with success message
        return redirect()->route('client.tickets')->with('success', 'Ticket created successfully!');
    })->name('tickets.store');
    
    Route::get('/tickets/{ticket}', function ($ticketId) {
        $ticket = \App\Models\Ticket::where('user_id', Auth::id())->with(['replies'])->findOrFail($ticketId);
        return view('client.tickets.show', ['ticket' => $ticket]);
    })->name('tickets.show');
    
    Route::post('/tickets/{ticket}/reply', function ($ticket) {
        request()->validate([
            'message' => 'required|string|min:10',
        ]);

        // Reply logic would go here
        // For now, redirect back with success message
        return back()->with('success', 'Reply sent successfully!');
    })->name('tickets.reply');
    
    Route::get('/profile', function () {
        return view('client.profile');
    })->name('profile');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    
    Route::get('/orders', \App\Livewire\Admin\Orders::class)->name('orders.index');
    
    Route::get('/services', \App\Livewire\Admin\Services::class)->name('services.index');
    
    Route::get('/invoices', \App\Livewire\Admin\Invoices::class)->name('invoices.index');
    
    Route::get('/products', \App\Livewire\Admin\Products::class)->name('products.index');
    
    Route::get('/tickets', \App\Livewire\Admin\Tickets::class)->name('tickets.index');
    
    Route::get('/tickets/{ticket}', function ($ticketId) {
        $ticket = \App\Models\Ticket::with(['user', 'replies'])->findOrFail($ticketId);
        return view('admin.tickets.show', ['ticket' => $ticket]);
    })->name('tickets.show');
    
    Route::get('/users', \App\Livewire\Admin\Users::class)->name('users.index');
    
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
    
    Route::get('/extensions', \App\Livewire\Admin\Extensions::class)->name('extensions.index');
});

/*
|--------------------------------------------------------------------------
| Payment Callback Routes
|--------------------------------------------------------------------------
*/

Route::post('/payment/paypal/success', function () {
    $gateway = PaymentGateway::where('driver', 'paypal')->first();

    if (!$gateway) {
        return redirect()->route('client.dashboard')->with('error', 'PayPal gateway is not configured.');
    }

    // Validate required fields
    $validated = request()->validate([
        'payment_id' => 'required|string',
        'PayerID' => 'required|string',
        'invoice_id' => 'required|integer|exists:invoices,id'
    ]);

    try {
        // Get invoice to verify amount
        $invoice = \App\Models\Invoice::findOrFail($validated['invoice_id']);
        
        // Process payment with validation
        $paypalGateway = new PayPalGateway();
        $paypalGateway->processCallback($validated);

        return redirect()->route('client.invoices')->with('success', 'Payment completed successfully!');
    } catch (\Exception $e) {
        \Log::error('PayPal payment processing error: ' . $e->getMessage());
        return redirect()->route('client.invoices')->with('error', 'Payment processing failed. Please contact support.');
    }
})->name('payment.paypal.success');

Route::get('/payment/paypal/cancel', function () {
    return redirect()->route('checkout')->with('error', 'Payment was cancelled.');
})->name('payment.paypal.cancel');

Route::post('/payment/stripe/webhook', function () {
    $payload = request()->getContent();
    $sigHeader = request()->header('Stripe-Signature', '');

    $gateway = new StripeGateway();
    $gateway->handleWebhook($payload, $sigHeader);

    return response()->json(['received' => true]);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])->name('payment.stripe.webhook');
