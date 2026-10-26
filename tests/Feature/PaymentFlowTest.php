<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private int $invoiceSequence = 0;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->owner()->create());
    }

    public function test_payment_detail_can_be_loaded(): void
    {
        $invoice = $this->createInvoice();
        $payment = $this->recordPayment($invoice, 250);

        $this->getJson("/api/payments/{$payment->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $payment->id)
            ->assertJsonPath('data.invoice.id', $invoice->id)
            ->assertJsonPath('data.invoice.client.id', $invoice->client_id);
    }

    public function test_invoice_list_accepts_multiple_statuses(): void
    {
        $this->createInvoice(status: InvoiceStatus::UNPAID->value);
        $this->createInvoice(dp: 200, status: InvoiceStatus::PARTIAL->value);
        $this->createInvoice(dp: 1000, status: InvoiceStatus::PAID->value);

        $response = $this->getJson('/api/invoices?status=unpaid,partial')->assertOk();

        $statuses = collect($response->json('data'))->pluck('status')->sort()->values()->all();

        $this->assertSame(['partial', 'unpaid'], $statuses);
    }

    public function test_payment_creation_and_deletion_recalculate_the_invoice(): void
    {
        $invoice = $this->createInvoice();

        $response = $this->postJson('/api/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 500,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
        ])->assertCreated();

        $invoice->refresh();
        $this->assertEquals(500, (float) $invoice->remaining_amount);
        $this->assertSame(InvoiceStatus::PARTIAL, $invoice->status);

        $this->deleteJson("/api/payments/{$response->json('data.id')}")->assertOk();

        $invoice->refresh();
        $this->assertEquals(1000, (float) $invoice->remaining_amount);
        $this->assertSame(InvoiceStatus::UNPAID, $invoice->status);
    }

    public function test_payment_cannot_exceed_remaining_balance(): void
    {
        $invoice = $this->createInvoice();

        $this->postJson('/api/payments', [
            'invoice_id' => $invoice->id,
            'amount' => 1001,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_update_uses_the_existing_amount_when_validating(): void
    {
        $invoice = $this->createInvoice();
        $payment = $this->recordPayment($invoice, 400);

        $this->putJson("/api/payments/{$payment->id}", [
            'invoice_id' => $invoice->id,
            'amount' => 1000,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
        ])->assertOk();

        $invoice->refresh();
        $this->assertEquals(0, (float) $invoice->remaining_amount);
        $this->assertSame(InvoiceStatus::PAID, $invoice->status);

        $this->putJson("/api/payments/{$payment->id}", [
            'invoice_id' => $invoice->id,
            'amount' => 1001,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_payment_invoice_cannot_be_changed(): void
    {
        $invoice = $this->createInvoice();
        $otherInvoice = $this->createInvoice();
        $payment = $this->recordPayment($invoice, 250);

        $this->putJson("/api/payments/{$payment->id}", [
            'invoice_id' => $otherInvoice->id,
            'amount' => 250,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('invoice_id');

        $this->assertSame($invoice->id, $payment->fresh()->invoice_id);
    }

    public function test_invoice_dp_cannot_exceed_items_total(): void
    {
        $client = $this->createClient();

        $this->postJson('/api/invoices', [
            'client_id' => $client->id,
            'invoice_date' => now()->toDateString(),
            'dp_amount' => 1001,
            'items' => [
                ['description' => 'Paket Test', 'quantity' => 1, 'unit_price' => 1000],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('dp_amount');

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_invoice_update_cannot_reduce_total_below_recorded_payments(): void
    {
        $invoice = $this->createInvoice();
        $this->recordPayment($invoice, 400);

        $this->putJson("/api/invoices/{$invoice->id}", [
            'client_id' => $invoice->client_id,
            'invoice_date' => now()->toDateString(),
            'dp_amount' => 0,
            'items' => [
                ['description' => 'Paket Test', 'quantity' => 1, 'unit_price' => 300],
            ],
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('dp_amount');

        $invoice->refresh();
        $this->assertEquals(1000, (float) $invoice->total);
    }

    private function createClient(): Client
    {
        return Client::create([
            'bride_name' => 'Test Bride',
            'groom_name' => 'Test Groom',
        ]);
    }

    private function createInvoice(float $total = 1000, float $dp = 0, string $status = 'unpaid'): Invoice
    {
        $client = $this->createClient();
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-TEST-' . ++$this->invoiceSequence . '-' . Str::upper(Str::random(6)),
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addWeek()->toDateString(),
            'total' => $total,
            'dp_amount' => $dp,
            'remaining_amount' => max(0, $total - $dp),
            'status' => $status,
        ]);

        $invoice->items()->create([
            'description' => 'Paket Test',
            'quantity' => 1,
            'unit_price' => $total,
            'subtotal' => $total,
        ]);

        return $invoice->load('client');
    }

    private function recordPayment(Invoice $invoice, float $amount): Payment
    {
        return app(PaymentService::class)->recordPayment([
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
        ]);
    }
}
