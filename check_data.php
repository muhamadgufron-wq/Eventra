<?php

use App\Models\User;
use App\Models\Invoice;
use App\Models\Payment;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Data Summary ===" . PHP_EOL . PHP_EOL;

echo "Users:" . PHP_EOL;
User::all()->each(fn($u) => print("  - {$u->email} ({$u->role})" . PHP_EOL));
echo PHP_EOL;

echo "Invoices:" . PHP_EOL;
Invoice::with('client')->get()->each(function($inv) {
    print("  - {$inv->invoice_number} | {$inv->client->bride_name} & {$inv->client->groom_name} | Status: {$inv->status->value} | Remaining: Rp " . number_format($inv->remaining_amount, 0, ',', '.') . PHP_EOL);
});
echo PHP_EOL;

echo "Payments:" . PHP_EOL;
Payment::with('invoice')->get()->each(function($pay) {
    print("  - Rp " . number_format($pay->amount, 0, ',', '.') . " | {$pay->invoice->invoice_number} | {$pay->method}" . PHP_EOL);
});
echo PHP_EOL;

echo "✅ Database ready for testing!" . PHP_EOL;
