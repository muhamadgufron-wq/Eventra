<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $invoice->invoice_number }}</title>
    @include('pdf.partials.styles')
</head>
<body>
    @php
        $formatMoney = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $formatDate = fn ($date) => $date
            ? \Illuminate\Support\Carbon::parse($date)->locale('id')->translatedFormat('d F Y')
            : '-';
    @endphp

    @include('pdf.partials.header', [
        'documentTitle' => 'KWITANSI',
        'documentNumber' => $receiptNumber,
        'statusLabel' => null,
        'statusClass' => null,
        'isFinal' => false,
    ])

    <div class="receipt-sheet">
        <div class="receipt-intro">Telah diterima pembayaran dari:</div>
        <div class="receipt-client">
            {{ $invoice->client?->bride_name ?? '-' }} &amp; {{ $invoice->client?->groom_name ?? '-' }}
        </div>

        <table class="receipt-detail">
            <tr>
                <td class="label">Nomor Invoice</td>
                <td class="value">{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pembayaran</td>
                <td class="value">{{ $formatDate($paidAt) }}</td>
            </tr>
            @unless ($isDp)
                <tr>
                    <td class="label">Metode Pembayaran</td>
                    <td class="value">{{ $method }}</td>
                </tr>
            @endunless
            <tr>
                <td class="label">Jenis Pembayaran</td>
                <td class="value">{{ $isDp ? 'Uang Muka (DP)' : 'Pembayaran Invoice' }}</td>
            </tr>
        </table>

        <div class="receipt-amount">
            <div class="receipt-amount-label">Jumlah Diterima</div>
            <div class="receipt-amount-value">{{ $formatMoney($amount) }}</div>
        </div>

        @if ($notes)
            <div class="receipt-note">
                <strong>Catatan:</strong> {{ $notes }}
            </div>
        @endif

        <div class="receipt-note">
            Kwitansi ini merupakan bukti pembayaran yang sah untuk invoice
            <strong>{{ $invoice->invoice_number }}</strong>.
        </div>
    </div>

    <table class="bottom">
        <tr>
            <td class="notes-box">
                <div class="notes-label">Terima Kasih</div>
                <div class="notes-text">
                    Pembayaran Anda telah kami terima dan catat. Terima kasih atas kepercayaan Anda kepada {{ $company['name'] }}.
                </div>
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
