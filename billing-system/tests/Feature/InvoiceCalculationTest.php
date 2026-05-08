<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Coupon;
use App\Services\Billing\InvoiceCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceCalculationTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceCalculatorService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = app(InvoiceCalculatorService::class);
    }

    /** @test */
    public function it_calculates_invoice_totals_correctly()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'credit' => 0,
            'total' => 0,
            'balance' => 0,
        ]);

        // Add invoice items
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item 1',
            'quantity' => 2,
            'unit_price' => 50.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item 2',
            'quantity' => 1,
            'unit_price' => 25.00,
        ]);

        $result = $this->calculator->calculateInvoiceTotals($invoice);

        $this->assertEquals(125.00, $result['subtotal']); // (2 * 50) + (1 * 25)
        $this->assertEquals(0, $result['discount']);
        $this->assertEquals(0, $result['tax']); // Assuming tax is disabled
        $this->assertEquals(0, $result['credit']);
        $this->assertEquals(125.00, $result['total']);
        $this->assertEquals(125.00, $result['balance']);
    }

    /** @test */
    public function it_applies_percentage_discount_correctly()
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'active' => true,
        ]);

        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'credit' => 0,
            'total' => 0,
            'balance' => 0,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item',
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $result = $this->calculator->calculateInvoiceTotals($invoice);

        $this->assertEquals(100.00, $result['subtotal']);
        $this->assertEquals(10.00, $result['discount']); // 10% of 100
        $this->assertEquals(90.00, $result['total']);
        $this->assertEquals(90.00, $result['balance']);
    }

    /** @test */
    public function it_applies_fixed_discount_correctly()
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'TEST20',
            'type' => 'fixed',
            'value' => 20,
            'active' => true,
        ]);

        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'credit' => 0,
            'total' => 0,
            'balance' => 0,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item',
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $result = $this->calculator->calculateInvoiceTotals($invoice);

        $this->assertEquals(100.00, $result['subtotal']);
        $this->assertEquals(20.00, $result['discount']);
        $this->assertEquals(80.00, $result['total']);
        $this->assertEquals(80.00, $result['balance']);
    }

    /** @test */
    public function it_prevents_negative_totals()
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'TEST150',
            'type' => 'fixed',
            'value' => 150, // More than the subtotal
            'active' => true,
        ]);

        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'credit' => 0,
            'total' => 0,
            'balance' => 0,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item',
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $result = $this->calculator->calculateInvoiceTotals($invoice);

        $this->assertEquals(100.00, $result['subtotal']);
        $this->assertEquals(100.00, $result['discount']); // Capped at subtotal
        $this->assertEquals(0, $result['total']);
        $this->assertEquals(0, $result['balance']);
    }

    /** @test */
    public function it_handles_edge_cases_gracefully()
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'credit' => 0,
            'total' => 0,
            'balance' => 0,
        ]);

        // Test with no items
        $this->expectException(\Exception::class);
        $this->calculator->calculateInvoiceTotals($invoice);
    }

    /** @test */
    public function it_validates_coupon_correctly()
    {
        $user = User::factory()->create();
        
        // Test expired coupon
        $expiredCoupon = Coupon::factory()->create([
            'code' => 'EXPIRED',
            'active' => true,
            'expiry_date' => now()->subDay(),
        ]);

        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'coupon_id' => $expiredCoupon->id,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'credit' => 0,
            'total' => 0,
            'balance' => 0,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item',
            'quantity' => 1,
            'unit_price' => 100.00,
        ]);

        $result = $this->calculator->calculateInvoiceTotals($invoice);

        $this->assertEquals(0, $result['discount']); // Expired coupon should not apply
    }
}