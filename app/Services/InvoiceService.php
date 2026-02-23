<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function createWithItems(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'client_id'    => $data['client_id'],
                'invoice_number' => $this->generateNumber(),
                'invoice_date' => $data['invoice_date'],
                'due_date'     => $data['due_date'] ?? null,
                'notes'        => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'subtotal'    => $subtotal,
                ]);
            }

            $invoice->dp_amount = $data['dp_amount'] ?? 0;
            $this->recalculate($invoice);

            return $invoice->fresh(['items', 'client']);
        });
    }

    public function updateWithItems(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->update([
                'client_id'    => $data['client_id'],
                'invoice_date' => $data['invoice_date'],
                'due_date'     => $data['due_date'] ?? null,
                'notes'        => $data['notes'] ?? null,
                'dp_amount'    => $data['dp_amount'] ?? 0,
            ]);

            // Sync items: simplest way is to delete and recreate
            $invoice->items()->delete();

            foreach ($data['items'] as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'subtotal'    => $subtotal,
                ]);
            }

            $this->recalculate($invoice);

            return $invoice->fresh(['items', 'client', 'payments']);
        });
    }

    public function recalculate(Invoice $invoice): void
    {
        $total     = $invoice->items()->sum('subtotal');
        $totalPaid = $invoice->payments()->sum('amount') + $invoice->dp_amount;
        $remaining = $total - $totalPaid;

        $invoice->update([
            'total'            => $total,
            'remaining_amount' => max($remaining, 0),
            'status'           => match (true) {
                $remaining <= 0   => 'paid',
                $totalPaid > 0    => 'partial',
                default           => 'unpaid',
            },
        ]);
    }

    private function generateNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $last   = Invoice::where('invoice_number', 'like', "{$prefix}%")
                         ->orderByDesc('invoice_number')
                         ->first();

        $seq = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
