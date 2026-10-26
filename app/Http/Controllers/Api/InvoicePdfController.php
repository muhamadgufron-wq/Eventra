<?php

namespace App\Http\Controllers\Api;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoicePdfService;
use Illuminate\Http\Response;

class InvoicePdfController extends Controller
{
    public function __construct(private InvoicePdfService $pdfService) {}

    public function invoice(Invoice $invoice): Response
    {
        return $this->pdfService->invoice($invoice);
    }

    public function paidInvoice(Invoice $invoice): Response
    {
        abort_unless(
            $invoice->status === InvoiceStatus::PAID,
            422,
            'Invoice Lunas hanya tersedia setelah invoice berstatus lunas.'
        );

        return $this->pdfService->invoice($invoice, true);
    }

    public function dpReceipt(Invoice $invoice): Response
    {
        abort_unless(
            (float) $invoice->dp_amount > 0,
            422,
            'Invoice ini tidak memiliki uang muka (DP).'
        );

        return $this->pdfService->dpReceipt($invoice);
    }

    public function paymentReceipt(Payment $payment): Response
    {
        return $this->pdfService->paymentReceipt($payment);
    }
}
