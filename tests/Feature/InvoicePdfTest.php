<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoicePdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->owner()->create());
    }

    public function test_invoice_pdf_can_be_generated(): void
    {
        $invoice = $this->createInvoice();

        $response = $this->get("/api/invoices/{$invoice->id}/pdf");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_paid_invoice_pdf_is_only_available_when_paid(): void
    {
        $invoice = $this->createInvoice();

        $this->getJson("/api/invoices/{$invoice->id}/paid-pdf")
            ->assertStatus(422);

        $invoice->update([
            'dp_amount' => 1000,
            'remaining_amount' => 0,
            'status' => InvoiceStatus::PAID,
        ]);

        $this->get("/api/invoices/{$invoice->id}/paid-pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_dp_receipt_requires_a_down_payment(): void
    {
        $invoice = $this->createInvoice();

        $this->getJson("/api/invoices/{$invoice->id}/dp-receipt")
            ->assertStatus(422);

        $invoice->update(['dp_amount' => 250]);

        $this->get("/api/invoices/{$invoice->id}/dp-receipt")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_payment_receipt_can_be_generated(): void
    {
        $invoice = $this->createInvoice();
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => 250,
            'payment_date' => now()->toDateString(),
            'method' => 'transfer',
            'notes' => 'Pembayaran test',
        ]);

        $this->get("/api/payments/{$payment->id}/receipt")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    private function createInvoice(): Invoice
    {
        $client = Client::create([
            'bride_name' => 'Test Bride',
            'groom_name' => 'Test Groom',
        ]);

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-PDF-' . Str::upper(Str::random(8)),
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addWeek()->toDateString(),
            'total' => 1000,
            'dp_amount' => 0,
            'remaining_amount' => 1000,
            'status' => InvoiceStatus::UNPAID,
        ]);

        $invoice->items()->create([
            'description' => 'Paket PDF',
            'quantity' => 1,
            'unit_price' => 1000,
            'subtotal' => 1000,
        ]);

        return $invoice;
    }
}
