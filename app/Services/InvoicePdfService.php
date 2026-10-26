<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoicePdfService
{
    public function invoice(Invoice $invoice, bool $final = false): Response
    {
        $invoice->loadMissing(['client', 'items.package', 'payments']);

        return $this->render('pdf.invoice', [
            'invoice' => $invoice,
            'company' => config('company'),
            'isFinal' => $final,
            'generatedAt' => now(),
        ], $this->invoiceFilename($invoice, $final));
    }

    public function paymentReceipt(Payment $payment): Response
    {
        $payment->loadMissing('invoice.client');

        return $this->render('pdf.receipt', [
            'invoice' => $payment->invoice,
            'company' => config('company'),
            'receiptNumber' => $this->receiptNumber($payment->invoice, $payment),
            'amount' => (float) $payment->amount,
            'paidAt' => $payment->payment_date,
            'method' => $payment->method->label(),
            'notes' => $payment->notes,
            'isDp' => false,
            'generatedAt' => now(),
        ], 'Kwitansi-' . $this->safeName($payment->invoice->invoice_number) . '-' . $payment->id . '.pdf');
    }

    public function dpReceipt(Invoice $invoice): Response
    {
        $invoice->loadMissing('client');

        return $this->render('pdf.receipt', [
            'invoice' => $invoice,
            'company' => config('company'),
            'receiptNumber' => $this->receiptNumber($invoice, null),
            'amount' => (float) $invoice->dp_amount,
            'paidAt' => $invoice->invoice_date,
            'method' => 'Uang Muka (DP)',
            'notes' => $invoice->notes,
            'isDp' => true,
            'generatedAt' => now(),
        ], 'Kwitansi-DP-' . $this->safeName($invoice->invoice_number) . '.pdf');
    }

    private function render(string $view, array $data, string $filename): Response
    {
        return Pdf::loadView($view, $data)
            ->setPaper('a4')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
            ])
            ->stream($filename);
    }

    private function invoiceFilename(Invoice $invoice, bool $final): string
    {
        return ($final ? 'Invoice-Lunas-' : 'Invoice-') . $this->safeName($invoice->invoice_number) . '.pdf';
    }

    private function receiptNumber(Invoice $invoice, ?Payment $payment): string
    {
        $suffix = $payment ? str_pad((string) $payment->id, 4, '0', STR_PAD_LEFT) : 'DP';

        return 'KW-' . $invoice->invoice_number . '-' . $suffix;
    }

    private function safeName(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_-]+/', '-', $value);
    }
}
