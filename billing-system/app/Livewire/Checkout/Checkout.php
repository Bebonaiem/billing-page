<?php

namespace App\Livewire\Checkout;

use App\Models\PaymentGateway;
use App\Models\ProductConfigOptionValue;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use App\Models\User;
use App\Services\Payment\PaymentGatewayInterface;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Checkout extends Component
{
    protected CartService $cartService;
    protected OrderService $orderService;
    
    public array $cart = [];
    public array $gateways = [];
    public ?int $selectedGateway = null;
    public array $billingInfo = [];
    public bool $termsAccepted = false;
    public string $step = 'review'; // review, payment, complete
    
    public function boot()
    {
        $this->cartService = new CartService();
        $this->orderService = new OrderService();
    }
    
    public function mount()
    {
        $this->cart = $this->hydrateCartItems($this->cartService->getCart());
        
        if ($this->cartService->isEmpty()) {
            return redirect()->route('order');
        }
        
        $this->gateways = PaymentGateway::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
        
        if (!empty($this->gateways)) {
            $this->selectedGateway = $this->gateways[0]['id'];
        }
        
        // Pre-fill billing info from user
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        /** @var User $user */
        $this->billingInfo = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'company' => $user->company,
            'address_line1' => $user->address_line1,
            'address_line2' => $user->address_line2,
            'city' => $user->city,
            'state' => $user->state,
            'postal_code' => $user->postal_code,
            'country' => $user->country,
            'phone' => $user->phone,
        ];
    }

    protected function hydrateCartItems(array $cart): array
    {
        $items = $cart['items'] ?? [];
        if (empty($items)) {
            return $cart;
        }

        $valueIds = [];
        foreach ($items as $item) {
            foreach (($item['config_options'] ?? []) as $valueId) {
                if (!empty($valueId)) {
                    $valueIds[] = (int) $valueId;
                }
            }
        }

        $valueIds = array_unique($valueIds);
        if (empty($valueIds)) {
            return $cart;
        }

        $values = ProductConfigOptionValue::with('configOption')
            ->whereIn('id', $valueIds)
            ->get()
            ->keyBy('id');

        foreach ($items as $itemId => $item) {
            $summary = [];
            foreach (($item['config_options'] ?? []) as $valueId) {
                $value = $values->get((int) $valueId);
                if (!$value) {
                    continue;
                }

                $summary[] = [
                    'option' => $value->configOption?->name ?? 'Option',
                    'value' => $value->label,
                    'price' => (float) $value->price,
                    'price_type' => $value->price_type,
                ];
            }

            $items[$itemId]['config_summary'] = $summary;
        }

        $cart['items'] = $items;

        return $cart;
    }
    
    public function selectGateway(int $gatewayId)
    {
        $this->selectedGateway = $gatewayId;
    }
    
    public function placeOrder()
    {
        if (!$this->termsAccepted) {
            $this->dispatch('notify', type: 'error', message: 'Please accept the terms and conditions.');
            return;
        }
        
        // Update user billing info
        $user = Auth::user();
        if (!$user) {
            $this->dispatch('notify', type: 'error', message: 'You must be logged in to place an order.');
            return;
        }
        /** @var User $user */
        $user->update($this->billingInfo);
        
        try {
            // Create order
            $order = $this->orderService->createFromCart(
                $user->id,
                $this->cart['coupon'] ?? null
            );
            
            // If account credit is selected and sufficient balance
            $gateway = PaymentGateway::find($this->selectedGateway);
            
            if ($gateway && $gateway->driver === 'account_credit') {
                $credit = $user->credit;
                if ($credit && $credit->canAfford($order->total)) {
                    $credit->deductCredit($order->total, "Order #{$order->order_number}");
                    $this->orderService->activateOrder($order);
                    $this->step = 'complete';
                    return;
                } else {
                    $this->dispatch('notify', type: 'error', message: 'Insufficient account credit.');
                    return;
                }
            }
            
            // For other payment methods, generate invoice and redirect to payment
            $invoice = (new \App\Services\Billing\InvoiceService())->generateFromOrder($order);
            
            // Store order/invoice in session for payment processing
            session(['checkout_order_id' => $order->id, 'checkout_invoice_id' => $invoice->id]);
            
            $this->step = 'payment';
            
            // Initialize payment
            $this->initializePayment($invoice, $gateway);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Order failed: ' . $e->getMessage());
        }
    }
    
    protected function initializePayment($invoice, $gateway)
    {
        if (!$gateway) {
            return;
        }
        
        $driver = match($gateway->driver) {
            'stripe' => new \App\Services\Payment\StripeGateway(),
            'paypal' => new \App\Services\Payment\PayPalGateway(),
            default => null,
        };
        
        if ($driver) {
            $result = $driver->initializePayment($invoice);
            
            if ($result['success']) {
                if (isset($result['redirect_url'])) {
                    return redirect()->away($result['redirect_url']);
                }
                
                // For embedded payment (Stripe Elements)
                $this->dispatch('paymentInitialized', $result);
            } else {
                $this->dispatch('notify', type: 'error', message: $result['error'] ?? 'Payment initialization failed');
            }
        }
    }
    
    public function getTotalsProperty(): array
    {
        return $this->cartService->getTotals();
    }
    
    public function render()
    {
        return view('livewire.checkout.checkout')->with('title', 'Checkout');
    }
}
