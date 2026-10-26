<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $like = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
        $statuses = collect(explode(',', (string) $request->input('status')))
            ->map(fn ($status) => trim($status))
            ->filter()
            ->values()
            ->all();

        $invoices = Invoice::with('client')
            ->when($statuses, fn ($q) => $q->whereIn('status', $statuses))
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s, $like) {
                $q->where('invoice_number', $like, "%{$s}%")
                  ->orWhereHas('client', fn ($q) => $q->where('bride_name', $like, "%{$s}%")
                      ->orWhere('groom_name', $like, "%{$s}%"));
            }))
            ->latest('invoice_date')
            ->paginate($request->input('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->createWithItems($request->validated());

        return (new InvoiceResource($invoice->load(['client', 'items'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Invoice $invoice): InvoiceResource
    {
        return new InvoiceResource($invoice->load(['client', 'items', 'payments']));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): InvoiceResource
    {
        $invoice = $this->invoiceService->updateWithItems($invoice, $request->validated());

        return new InvoiceResource($invoice->load(['client', 'items', 'payments']));
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        return response()->json(['message' => 'Invoice berhasil dihapus.']);
    }
}
