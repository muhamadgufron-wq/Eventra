<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $isFinal ? 'Invoice Lunas' : 'Invoice' }} - {{ $invoice->invoice_number }}</title>
    @include('pdf.partials.styles')
</head>
<body>
    @php
        $formatMoney = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $formatDate = fn ($date) => $date
            ? \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat('d F Y')
            : '-';
        $statusLabel = $isFinal ? 'Lunas' : $invoice->status->label();
        $statusClass = $isFinal ? 'paid' : $invoice->status->value;
        $payments = $invoice->payments->sortBy('payment_date')->values();
        $paymentTotal = (float) $invoice->payments->sum('amount');
    @endphp

    @include('pdf.partials.header', [
        'documentTitle' => $isFinal ? 'INVOICE LUNAS' : 'INVOICE',
        'documentNumber' => '#' . $invoice->invoice_number,
        'statusLabel' => $isFinal ? null : $statusLabel,
        'statusClass' => $statusClass,
        'isFinal' => $isFinal,
    ])

    <table class="info-table">
        <tr>
            <td class="info-panel">
                <div class="kicker">Ditujukan Kepada</div>
                <div class="client-name">
                    {{ $invoice->client?->bride_name ?? '-' }} &amp; {{ $invoice->client?->groom_name ?? '-' }}
                </div>
                <div class="client-contact">
                    {{ $invoice->client?->bride_phone ?: $invoice->client?->groom_phone ?: '-' }}<br>
                    {{ $invoice->client?->event_location ?: 'Lokasi acara belum diisi' }}
                </div>
            </td>
            <td class="info-gap"></td>
            <td class="info-panel">
                <table class="meta-row">
                    <tr>
                        <td class="meta-label">Tanggal Invoice</td>
                        <td class="meta-value">{{ $formatDate($invoice->invoice_date) }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jatuh Tempo</td>
                        <td class="meta-value">{{ $formatDate($invoice->due_date) }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Status</td>
                        <td class="meta-value">{{ $statusLabel }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="item-number">#</th>
                <th>Rincian Layanan</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga Unit</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td class="item-number">{{ $loop->iteration }}</td>
                    <td>
                        <div class="item-description">{{ $item->description }}</div>
                        @if ($item->package)
                            <div class="item-meta">{{ $item->package->name }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $formatMoney($item->unit_price) }}</td>
                    <td class="text-right bold">{{ $formatMoney($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-wrapper">
        <tr>
            <td></td>
            <td>
                <table class="totals">
                    <tr>
                        <td class="total-label">Total</td>
                        <td class="total-value">{{ $formatMoney($invoice->total) }}</td>
                    </tr>
                    @if ((float) $invoice->dp_amount > 0)
                        <tr>
                            <td class="total-label">Uang Muka (DP)</td>
                            <td class="total-value">{{ $formatMoney($invoice->dp_amount) }}</td>
                        </tr>
                    @endif
                    @if ($paymentTotal > 0)
                        <tr>
                            <td class="total-label">Pembayaran</td>
                            <td class="total-value">{{ $formatMoney($paymentTotal) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total">
                        <td>Sisa Tagihan</td>
                        <td class="text-right">{{ $formatMoney($invoice->remaining_amount) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if ($isFinal)
        <div class="section-title">Riwayat Pembayaran</div>
        <table class="payments">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th>Catatan</th>
                    <th class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if ((float) $invoice->dp_amount > 0)
                    <tr>
                        <td>{{ $formatDate($invoice->invoice_date) }}</td>
                        <td>Uang Muka (DP)</td>
                        <td class="muted">Pembayaran awal invoice</td>
                        <td class="amount">{{ $formatMoney($invoice->dp_amount) }}</td>
                    </tr>
                @endif
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $formatDate($payment->payment_date) }}</td>
                        <td>{{ $payment->method->label() }}</td>
                        <td class="muted">{{ $payment->notes ?: '-' }}</td>
                        <td class="amount">{{ $formatMoney($payment->amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="bottom">
        <tr>
            <td class="notes-box">
                <div class="notes-label">Catatan</div>
                <div class="notes-text">
                    {{ $invoice->notes ?: 'Sesuai kesepakatan awal layanan.' }}
                </div>
                @if (! $isFinal)
                    <div class="receipt-note">
                        Dokumen ini merupakan tagihan resmi. Silakan gunakan nomor invoice di atas sebagai referensi pembayaran.
                    </div>
                @endif
            </td>
            <td class="signature-box">
                <div class="muted">Hormat kami,</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $company['name'] }}</div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan otomatis oleh {{ $company['name'] }} pada {{ $formatDate($generatedAt) }}.
    </div>
</body>
</html>
