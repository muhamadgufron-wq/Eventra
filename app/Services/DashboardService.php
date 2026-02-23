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

            'cashflow' => DB::table(DB::raw("(
                    SELECT TO_CHAR(payment_date, 'YYYY-MM') as month, SUM(amount) as total
                    FROM payments
                    WHERE payment_date >= '" . $now->copy()->subMonths(6)->startOfMonth()->format('Y-m-d') . "'
                    GROUP BY month
                    
                    UNION ALL
                    
                    SELECT TO_CHAR(invoice_date, 'YYYY-MM') as month, SUM(dp_amount) as total
                    FROM invoices
                    WHERE invoice_date >= '" . $now->copy()->subMonths(6)->startOfMonth()->format('Y-m-d') . "'
                    AND dp_amount > 0
                    GROUP BY month
                ) as combined"))
                ->selectRaw("month, SUM(total) as total")
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->toArray(),

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
                    'due_date' => $i->invoice_date->addDays(7)->format('Y-m-d'), // Simplified due date logic
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
}
