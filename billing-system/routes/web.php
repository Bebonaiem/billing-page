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

Route::get('/checkout', \App\Livewire\Checkout\Checkout::class)->name('checkout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, request()->boolean('remember'))) {
        request()->session()->regenerate();
        return redirect()->intended(route('client.dashboard'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->middleware('guest');

Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

Route::post('/register', function () {
    $validated = request()->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
    ]);

    Auth::login($user);

    return redirect()->route('client.dashboard');
})->middleware('guest');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Client Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
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
    
    Route::get('/profile', function () {
        return view('client.profile');
    })->name('profile');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    
    Route::get('/orders', \App\Livewire\Admin\Orders::class)->name('orders.index');
    
    Route::get('/services', \App\Livewire\Admin\Services::class)->name('services.index');
    
    Route::get('/invoices', \App\Livewire\Admin\Invoices::class)->name('invoices.index');
    
    Route::get('/products', \App\Livewire\Admin\Products::class)->name('products.index');
    
    Route::get('/tickets', \App\Livewire\Admin\Tickets::class)->name('tickets.index');
    
    Route::get('/users', \App\Livewire\Admin\Users::class)->name('users.index');
    
    Route::get('/settings', \App\Livewire\Admin\Settings::class)->name('settings');
    
    Route::get('/extensions', \App\Livewire\Admin\Extensions::class)->name('extensions.index');
});

/*
|--------------------------------------------------------------------------
| Payment Callback Routes
|--------------------------------------------------------------------------
*/

Route::get('/payment/paypal/success', function () {
    $gateway = PaymentGateway::where('driver', 'paypal')->first();

    if (!$gateway) {
        return redirect()->route('client.dashboard')->with('error', 'PayPal gateway is not configured.');
    }

    try {
        (new PayPalGateway())->processCallback(request()->all());

        return redirect()->route('client.invoices')->with('success', 'Payment completed successfully!');
    } catch (\Exception $e) {
        return redirect()->route('client.invoices')->with('error', 'Payment capture failed: ' . $e->getMessage());
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
