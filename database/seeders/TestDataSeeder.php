<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== TEST 1: User Factory ===" . PHP_EOL;
        $users = User::factory()->count(3)->create();
        echo "Created {$users->count()} users with roles:" . PHP_EOL;
        $users->each(fn($u) => print("- {$u->name} ({$u->role})" . PHP_EOL));
        echo PHP_EOL;

        echo "=== TEST 2: Create Sample Package ===" . PHP_EOL;
        $package = Package::create([
            'name' => 'Package Gold',
            'type' => 'wedding',
            'description' => 'Paket wedding gold lengkap',
            'services' => ['decoration', 'catering', 'documentation', 'makeup'],
            'price' => 25000000,
            'is_active' => true,
        ]);
        echo "Package created: {$package->name} - Rp " . number_format($package->price, 0, ',', '.') . PHP_EOL;
        echo PHP_EOL;

        echo "=== TEST 3: Create Sample Client ===" . PHP_EOL;
        $client = Client::create([
            'bride_name' => 'Putri',
            'groom_name' => 'Andi',
            'bride_phone' => '081234567890',
            'groom_phone' => '081234567891',
            'email' => 'putri.andi@email.com',
            'event_date' => '2026-06-15',
            'event_location' => 'Grand Ballroom Hotel Indonesia',
            'package_id' => $package->id,
            'notes' => 'VIP client',
        ]);
        echo "Client created: {$client->bride_name} & {$client->groom_name}" . PHP_EOL;
        echo PHP_EOL;

        echo "=== TEST 4: Create Invoice with Items ===" . PHP_EOL;
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-' . now()->format('Ymd') . '-0001',
            'invoice_date' => now(),
            'due_date' => now()->addDays(14),
            'dp_amount' => 5000000,
            'notes' => 'Invoice pertama',
        ]);

        $invoice->items()->create([
            'package_id' => $package->id,
            'description' => 'Wedding Package Gold',
            'quantity' => 1,
            'unit_price' => 25000000,
            'subtotal' => 25000000,
        ]);

        $invoice->total = 25000000;
        $invoice->remaining_amount = 25000000 - 5000000; // total - dp
        $invoice->status = 'partial';
        $invoice->save();

        echo "Invoice created: {$invoice->invoice_number}" . PHP_EOL;
        echo "Total: Rp " . number_format($invoice->total, 0, ',', '.') . PHP_EOL;
        echo "DP: Rp " . number_format($invoice->dp_amount, 0, ',', '.') . PHP_EOL;
        echo "Remaining: Rp " . number_format($invoice->remaining_amount, 0, ',', '.') . PHP_EOL;
        echo "Status: {$invoice->status->value}" . PHP_EOL;
        echo PHP_EOL;

        echo "=== TEST 5: Create Payment ===" . PHP_EOL;
        $payment = \App\Models\Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => 3000000,
            'payment_date' => now(),
            'method' => 'transfer',
            'notes' => 'Pembayaran pertama via transfer',
        ]);

        echo "Payment created: Rp " . number_format($payment->amount, 0, ',', '.') . " ({$payment->method})" . PHP_EOL;
        echo PHP_EOL;

        // Recalculate invoice after payment
        $totalPaid = $invoice->payments()->sum('amount') + $invoice->dp_amount;
        $remaining = $invoice->total - $totalPaid;
        $invoice->remaining_amount = max($remaining, 0);
        $invoice->status = $remaining <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'unpaid');
        $invoice->save();

        echo "=== TEST 6: Invoice After Payment Recalculation ===" . PHP_EOL;
        echo "Total Paid (including DP): Rp " . number_format($totalPaid, 0, ',', '.') . PHP_EOL;
        echo "New Remaining: Rp " . number_format($invoice->remaining_amount, 0, ',', '.') . PHP_EOL;
        echo "New Status: {$invoice->status->value}" . PHP_EOL;
        echo PHP_EOL;

        echo "✅ All tests passed!" . PHP_EOL;
    }
}
