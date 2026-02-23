<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;

class PaymentService
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function recordPayment(array $data): Payment
    {
        $payment = Payment::create($data);

        // Recalculate invoice totals and status
        $this->invoiceService->recalculate($payment->invoice);

        return $payment->fresh(['invoice']);
    }

    public function deletePayment(Payment $payment): void
    {
        $invoice = $payment->invoice;
        $payment->delete();

        // Recalculate after payment removal
        $this->invoiceService->recalculate($invoice);
    }
}
