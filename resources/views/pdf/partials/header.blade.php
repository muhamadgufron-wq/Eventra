<table class="header">
    <tr>
        <td class="brand-cell">
            <table class="brand-lockup">
                <tr>
                    <td class="brand-mark">{{ strtoupper(substr($company['name'], 0, 1)) }}</td>
                    <td class="brand-copy">
                        <div class="brand-name">{{ $company['name'] }}</div>
                        <div class="brand-tagline">{{ $company['tagline'] }}</div>
                    </td>
                </tr>
            </table>
            <div class="company-meta">
                {{ $company['address'] }}<br>
                {{ $company['phone'] }} &nbsp;|&nbsp; {{ $company['email'] }}
            </div>
        </td>
        <td class="header-doc">
            <div class="doc-title">{{ $documentTitle }}</div>
            <div class="doc-number">{{ $documentNumber }}</div>
            @if (!empty($statusLabel))
                <span class="status status-{{ $statusClass }}">{{ $statusLabel }}</span>
            @endif
            @if (!empty($isFinal))
                <div class="paid-mark">LUNAS</div>
            @endif
        </td>
    </tr>
</table>
