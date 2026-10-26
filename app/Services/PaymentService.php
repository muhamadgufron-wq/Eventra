<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($data['invoice_id']);

            $this->ensureAmountFitsInvoice($invoice, (float) $data['amount']);

            $payment = Payment::create($data);

            // Recalculate invoice totals and status
            $this->invoiceService->recalculate($invoice);

            return $payment->fresh(['invoice']);
        });
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id);

            if (array_key_exists('invoice_id', $data) && (int) $data['invoice_id'] !== (int) $invoice->id) {
                throw ValidationException::withMessages([
                    'invoice_id' => 'Invoice pada pembayaran tidak dapat diubah.',
                ]);
            }

            // The current payment is already included in the invoice's remaining amount,
            // so it can be reused when validating the new amount.
            $available = (float) $invoice->remaining_amount + (float) $payment->amount;
            $this->ensureAmountFitsInvoice($invoice, (float) ($data['amount'] ?? $payment->amount), $available);

            $payment->update($data);

            // Recalculate invoice after payment update
            $this->invoiceService->recalculate($invoice);

            return $payment->fresh(['invoice']);
        });
    }

    public function deletePayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id);

            $payment->delete();

            // Recalculate after payment removal
            $this->invoiceService->recalculate($invoice);
        });
    }

    private function ensureAmountFitsInvoice(Invoice $invoice, float $amount, ?float $available = null): void
    {
        $available ??= (float) $invoice->remaining_amount;

        if ($amount > $available) {
            throw ValidationException::withMessages([
                'amount' => 'Jumlah pembayaran melebihi sisa tagihan invoice.',
            ]);
        }
    }
}
