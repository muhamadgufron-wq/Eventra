<style>
@page {
    margin: 28px 32px 34px;
}

body {
    margin: 0;
    color: #27272a;
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 9pt;
    line-height: 1.45;
}

table {
    width: 100%;
    border-collapse: collapse;
}

td,
th {
    vertical-align: top;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.muted {
    color: #71717a;
}

.small {
    font-size: 7.5pt;
}

.bold {
    font-weight: bold;
}

.header {
    margin-bottom: 22px;
    border-bottom: 3px solid #065f46;
    padding-bottom: 16px;
}

.brand-cell {
    width: 62%;
}

.brand-lockup {
    width: auto;
}

.brand-mark {
    width: 38px;
    height: 38px;
    background: #065f46;
    color: #ffffff;
    font-size: 20pt;
    font-weight: bold;
    line-height: 38px;
    text-align: center;
}

.brand-copy {
    padding-left: 10px;
}

.brand-name {
    color: #064e3b;
    font-size: 19pt;
    font-weight: bold;
    line-height: 1.1;
}

.brand-tagline {
    margin-top: 2px;
    color: #b45309;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.company-meta {
    margin-top: 8px;
    color: #71717a;
    font-size: 7.5pt;
    line-height: 1.6;
}

.header-doc {
    width: 38%;
    text-align: right;
}

.doc-title {
    color: #065f46;
    font-size: 20pt;
    font-weight: bold;
    letter-spacing: 1px;
}

.doc-number {
    margin-top: 2px;
    color: #3f3f46;
    font-size: 9pt;
    font-weight: bold;
}

.status {
    display: inline-block;
    margin-top: 8px;
    padding: 3px 9px;
    border: 1px solid #d4d4d8;
    color: #52525b;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.status-unpaid {
    border-color: #fecaca;
    background: #fef2f2;
    color: #b91c1c;
}

.status-partial {
    border-color: #fde68a;
    background: #fffbeb;
    color: #b45309;
}

.status-paid {
    border-color: #a7f3d0;
    background: #ecfdf5;
    color: #047857;
}

.paid-mark {
    display: inline-block;
    margin-top: 8px;
    padding: 4px 12px;
    border: 2px solid #059669;
    color: #059669;
    font-size: 11pt;
    font-weight: bold;
    letter-spacing: 2px;
}

.info-table {
    margin-bottom: 22px;
}

.info-panel {
    padding: 14px 16px;
    border: 1px solid #e4e4e7;
    background: #fafafa;
}

.info-gap {
    width: 14px;
}

.kicker {
    margin-bottom: 6px;
    color: #71717a;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.client-name {
    color: #18181b;
    font-size: 13pt;
    font-weight: bold;
    line-height: 1.35;
}

.client-contact {
    margin-top: 5px;
    color: #52525b;
    font-size: 8pt;
    line-height: 1.5;
}

.meta-row {
    color: #52525b;
    font-size: 8pt;
}

.meta-row td {
    padding: 3px 0;
}

.meta-row .meta-label {
    color: #71717a;
}

.meta-row .meta-value {
    color: #18181b;
    font-weight: bold;
    text-align: right;
}

.section-title {
    margin: 22px 0 8px;
    color: #065f46;
    font-size: 8pt;
    font-weight: bold;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.items {
    margin-top: 4px;
}

.items th {
    padding: 9px 10px;
    background: #065f46;
    color: #ffffff;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 0.8px;
    text-align: left;
    text-transform: uppercase;
}

.items td {
    padding: 9px 10px;
    border-bottom: 1px solid #e4e4e7;
    font-size: 8.5pt;
}

.items tr {
    page-break-inside: avoid;
}

.items .item-description {
    color: #18181b;
    font-weight: bold;
}

.items .item-meta {
    margin-top: 2px;
    color: #a1a1aa;
    font-size: 7pt;
}

.items .item-number {
    color: #71717a;
    width: 24px;
}

.totals-wrapper {
    margin-top: 12px;
}

.totals {
    width: 46%;
    margin-left: 54%;
}

.totals td {
    padding: 5px 10px;
    font-size: 8.5pt;
}

.totals .total-label {
    color: #71717a;
}

.totals .total-value {
    color: #18181b;
    font-weight: bold;
    text-align: right;
}

.totals .grand-total td {
    padding: 9px 10px;
    border-top: 2px solid #065f46;
    background: #ecfdf5;
    color: #064e3b;
    font-size: 10pt;
    font-weight: bold;
}

.payments {
    margin-top: 4px;
}

.payments th {
    padding: 6px 8px;
    border-bottom: 1px solid #d4d4d8;
    color: #71717a;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 0.6px;
    text-align: left;
    text-transform: uppercase;
}

.payments td {
    padding: 7px 8px;
    border-bottom: 1px solid #f4f4f5;
    font-size: 8pt;
}

.payments .amount {
    color: #047857;
    font-weight: bold;
    text-align: right;
}

.bottom {
    margin-top: 24px;
}

.notes-box {
    width: 58%;
    padding-right: 24px;
}

.notes-label {
    margin-bottom: 6px;
    color: #71717a;
    font-size: 7pt;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.notes-text {
    padding: 10px 12px;
    border-left: 3px solid #d4af37;
    background: #fffbeb;
    color: #52525b;
    font-size: 8pt;
    line-height: 1.55;
}

.signature-box {
    width: 42%;
    text-align: center;
}

.signature-space {
    height: 48px;
}

.signature-name {
    display: inline-block;
    min-width: 150px;
    padding-top: 6px;
    border-top: 1px solid #a1a1aa;
    color: #18181b;
    font-size: 8pt;
    font-weight: bold;
}

.footer {
    margin-top: 26px;
    padding-top: 9px;
    border-top: 1px solid #e4e4e7;
    color: #a1a1aa;
    font-size: 6.8pt;
    text-align: center;
}

.receipt-sheet {
    margin-top: 12px;
    padding: 24px;
    border: 1px solid #e4e4e7;
    background: #fafafa;
}

.receipt-intro {
    margin-bottom: 4px;
    color: #71717a;
    font-size: 8.5pt;
}

.receipt-client {
    margin-bottom: 20px;
    color: #18181b;
    font-size: 16pt;
    font-weight: bold;
}

.receipt-detail td {
    padding: 5px 0;
    border-bottom: 1px solid #f4f4f5;
    font-size: 8.5pt;
}

.receipt-detail .label {
    width: 38%;
    color: #71717a;
}

.receipt-detail .value {
    color: #18181b;
    font-weight: bold;
}

.receipt-amount {
    margin-top: 22px;
    padding: 18px;
    border: 2px solid #065f46;
    background: #ecfdf5;
    text-align: center;
}

.receipt-amount-label {
    color: #047857;
    font-size: 7.5pt;
    font-weight: bold;
    letter-spacing: 1.4px;
    text-transform: uppercase;
}

.receipt-amount-value {
    margin-top: 4px;
    color: #064e3b;
    font-size: 21pt;
    font-weight: bold;
}

.receipt-note {
    margin-top: 16px;
    color: #52525b;
    font-size: 8pt;
    line-height: 1.5;
}
</style>
