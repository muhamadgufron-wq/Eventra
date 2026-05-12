<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $payments = Payment::with('invoice')
            ->when($request->search, fn ($q, $s) => $q->whereHas('invoice', fn ($q) =>
                $q->where('invoice_number', 'ilike', "%{$s}%")
            ))
            ->latest('payment_date')
            ->paginate($request->input('per_page', 15));

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->recordPayment($request->validated());

        return (new PaymentResource($payment->load('invoice')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    {
        $payment->update($request->validated());

        // Recalculate invoice after payment update
        $this->paymentService->recalculateInvoice($payment->invoice);

        return new PaymentResource($payment->load('invoice'));
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $this->paymentService->deletePayment($payment);

        return response()->json(['message' => 'Pembayaran berhasil dihapus.']);
    }
}
