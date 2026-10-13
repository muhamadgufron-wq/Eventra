<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class CheckDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menampilkan ringkasan data testing (User, Invoice, Payment)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== Test Data Summary ===\n");

        $this->line("<comment>Users:</comment>");
        User::all()->each(function ($u) {
            $role = is_object($u->role) ? $u->role->value : $u->role;
            $this->line("  - {$u->email} ({$role})");
        });
        $this->newLine();

        $this->line("<comment>Invoices:</comment>");
        Invoice::with('client')->get()->each(function ($inv) {
            $status = is_object($inv->status) ? $inv->status->value : $inv->status;
            $remaining = number_format($inv->remaining_amount, 0, ',', '.');
            $clientName = $inv->client ? "{$inv->client->bride_name} & {$inv->client->groom_name}" : 'Tanpa Klien';
            $this->line("  - {$inv->invoice_number} | {$clientName} | Status: {$status} | Sisa: Rp {$remaining}");
        });
        $this->newLine();

        $this->line("<comment>Payments:</comment>");
        Payment::with('invoice')->get()->each(function ($pay) {
            $amount = number_format($pay->amount, 0, ',', '.');
            $invNum = $pay->invoice?->invoice_number ?? '-';
            $method = is_object($pay->method) ? $pay->method->value : $pay->method;
            $this->line("  - Rp {$amount} | {$invNum} | {$method}");
        });
        $this->newLine();

        $this->info("✅ Database status checked successfully!");

        return Command::SUCCESS;
    }
}
