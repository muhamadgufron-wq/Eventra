<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function summary(): array
    {
        $now = now();
        $sixMonthsAgo = $now->copy()->subMonths(6)->startOfMonth();

        // Calculate Monthly Revenue: Payments + DP from invoices created this month
        $monthlyPayments = (float) Payment::whereMonth('payment_date', $now->month)
            ->whereYear('payment_date', $now->year)
            ->sum('amount');

        $monthlyDP = (float) Invoice::whereMonth('invoice_date', $now->month)
            ->whereYear('invoice_date', $now->year)
            ->sum('dp_amount');

        return [
            'monthly_revenue' => $monthlyPayments + $monthlyDP,

            'unpaid_invoices' => Invoice::where('status', '!=', 'paid')->count(),

            'total_receivable' => (float) Invoice::where('status', '!=', 'paid')
                ->sum('remaining_amount'),

            'new_clients_this_month' => Client::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),

            'cashflow' => $this->getCashflowData($sixMonthsAgo),

            'latest_invoices' => Invoice::with('client')
                ->where('status', '!=', 'paid')
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($i) => [
                    'id' => $i->id,
                    'invoice_number' => $i->invoice_number,
                    'bride_name' => $i->client->bride_name,
                    'groom_name' => $i->client->groom_name,
                    'status' => $i->status,
                    'due_date' => $i->due_date ?? $i->invoice_date->addDays(7)->format('Y-m-d'),
                    'remaining_amount' => $i->remaining_amount,
                ]),

            'latest_payments' => Payment::with('invoice.client')
                ->latest('payment_date')
                ->latest('id')
                ->take(5)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'amount' => $p->amount,
                    'payment_date' => $p->payment_date->format('M d, Y'),
                    'method' => $p->method,
                    'client_name' => $p->invoice->client->bride_name . ' & ' . $p->invoice->client->groom_name,
                    'invoice_number' => $p->invoice->invoice_number,
                ]),
        ];
    }

    /**
     * Get cashflow data for the last 6 months using safe query builder.
     * Compatible with PostgreSQL, MySQL, and SQLite.
     */
    private function getCashflowData($startDate): array
    {
        $driver = DB::connection()->getDriverName();

        // Build payment subquery
        $paymentSubquery = DB::table('payments')
            ->selectRaw($this->getDateFormatExpression($driver, 'payment_date') . ' as month, SUM(amount) as total')
            ->where('payment_date', '>=', $startDate)
            ->groupByRaw($this->getDateFormatExpression($driver, 'payment_date'));

        // Build DP subquery
        $dpSubquery = DB::table('invoices')
            ->selectRaw($this->getDateFormatExpression($driver, 'invoice_date') . ' as month, SUM(dp_amount) as total')
            ->where('invoice_date', '>=', $startDate)
            ->where('dp_amount', '>', 0)
            ->groupByRaw($this->getDateFormatExpression($driver, 'invoice_date'));

        // Union both subqueries
        $combined = DB::connection()
            ->table(DB::raw("({$paymentSubquery->toSql()} UNION ALL {$dpSubquery->toSql()}) as combined"))
            ->mergeBindings($paymentSubquery)
            ->mergeBindings($dpSubquery)
            ->selectRaw('month, SUM(total) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format the result
        return $combined->map(fn($item) => [
            'month' => $item->month,
            'total' => (float) $item->total,
        ])->toArray();
    }

    /**
     * Get date format expression based on database driver.
     */
    private function getDateFormatExpression(string $driver, string $column): string
    {
        return match ($driver) {
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'mysql' => "DATE_FORMAT({$column}, '%Y-%m')",
            'sqlite' => "STRFTIME('%Y-%m', {$column})",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
