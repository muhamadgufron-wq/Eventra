<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function createWithItems(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $this->ensureDpFitsInvoice(
                total: $this->itemsTotal($data['items']),
                dp: (float) ($data['dp_amount'] ?? 0),
            );

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
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);

            $this->ensureDpFitsInvoice(
                total: $this->itemsTotal($data['items']),
                dp: (float) ($data['dp_amount'] ?? 0),
                alreadyPaid: (float) $invoice->payments()->sum('amount'),
            );

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

    private function itemsTotal(array $items): float
    {
        return array_reduce(
            $items,
            fn (float $total, array $item) => $total + ((float) $item['quantity'] * (float) $item['unit_price']),
            0.0,
        );
    }

    private function ensureDpFitsInvoice(float $total, float $dp, float $alreadyPaid = 0): void
    {
        if ($dp + $alreadyPaid > $total) {
            throw ValidationException::withMessages([
                'dp_amount' => $alreadyPaid > 0
                    ? 'DP dan pembayaran yang sudah tercatat tidak boleh melebihi total invoice.'
                    : 'DP tidak boleh melebihi total invoice.',
            ]);
        }
    }

    /**
     * Generate unique invoice number with database locking to prevent race conditions.
     * Format: INV-YYYYMMDD-0001
     */
    private function generateNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'INV-' . now()->format('Ymd');
            
            // Lock the invoices table for update to prevent race conditions
            $last = Invoice::where('invoice_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByDesc('invoice_number')
                ->first();

            $seq = $last ? ((int) substr($last->invoice_number, -4)) + 1 : 1;

            return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
        });
    }
}
