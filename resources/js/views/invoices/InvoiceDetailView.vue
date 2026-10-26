<template>
    <!-- Loading -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-32 space-y-4">
        <Loader2Icon class="size-12 animate-spin text-primary-600" />
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-surface-400">Menyusun Dokumen...</p>
    </div>

    <!-- Load Error -->
    <div v-else-if="loadError" class="max-w-5xl mx-auto">
        <div class="rounded-3xl border border-destructive/20 bg-white p-8 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="rounded-2xl bg-destructive/10 p-3 text-destructive">
                    <AlertCircleIcon class="size-6" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-surface-900">Gagal memuat invoice</h2>
                    <p class="mt-1 text-sm text-surface-500">{{ loadError }}</p>
                </div>
            </div>
            <Button as-child variant="outline" class="mt-6 h-11 rounded-xl px-5 text-sm font-bold">
                <RouterLink :to="{ name: 'invoices' }">Kembali ke daftar invoice</RouterLink>
            </Button>
        </div>
    </div>

    <!-- Invoice Master Document -->
    <div v-else-if="invoice" class="max-w-5xl mx-auto space-y-8">
        <div class="bg-white rounded-3xl border border-surface-200 shadow-xl shadow-surface-950/5 relative overflow-hidden">
            <!-- Professional Header Section -->
            <div class="p-8 sm:p-12 border-b border-surface-100 bg-surface-50/30 relative overflow-hidden">
                <!-- Branding Background Accent -->
                <div class="absolute top-0 right-0 w-80 h-80 bg-primary-50 rounded-full blur-[100px] -mr-40 -mt-40 opacity-40"></div>

                <div class="relative z-10 flex flex-col md:flex-row justify-between gap-10">
                    <!-- Left: Company Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-primary-900 flex items-center justify-center text-white shadow-lg shadow-primary-900/10">
                                <FileTextIcon class="size-8" />
                            </div>
                            <div>
                                <h2 class="text-2xl font-serif font-bold text-surface-900 tracking-tight">Eventra</h2>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-700">SaaS Wedding Organizer</p>
                            </div>
                        </div>

                        <div class="space-y-1 text-xs font-medium text-surface-500 max-w-xs">
                            <p>Jl. Melati No. 123, Jakarta Selatan</p>
                            <p>contact@eventra.id | (021) 555-0123</p>
                        </div>
                    </div>

                    <!-- Right: Invoice Info -->
                    <div class="md:text-right space-y-4">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-surface-400">Nomor Invoice</span>
                            <h1 class="text-3xl font-bold text-surface-950 mt-1">{{ invoice.invoice_number }}</h1>
                        </div>
                        <div class="flex md:justify-end items-center gap-4">
                            <div class="text-right">
                                <p class="text-[9px] font-black uppercase tracking-widest text-surface-400">Tanggal Terbit</p>
                                <p class="text-xs font-bold text-surface-900">{{ formatDate(invoice.invoice_date) }}</p>
                            </div>
                            <div class="w-px h-8 bg-surface-200"></div>
                            <div class="text-right">
                                <p class="text-[9px] font-black uppercase tracking-widest text-surface-400">Status Bayar</p>
                                <Badge :variant="statusVariant(invoice.status)" class="mt-1 h-6 rounded-full border px-3 text-[9px] font-black uppercase tracking-widest">
                                    {{ statusLabel(invoice.status) }}
                                </Badge>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Client & Summary Section -->
            <div class="p-8 sm:p-12 grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-surface-400 flex items-center gap-2 mb-6">
                        <span class="w-1 h-3 bg-primary-600 rounded-full"></span>
                        Ditujukan Kepada
                    </h4>
                    <div class="space-y-4">
                        <h3 class="text-2xl font-serif font-bold text-surface-900 leading-tight">
                            {{ invoice.client?.bride_name }} <br class="hidden sm:block"> & {{ invoice.client?.groom_name }}
                        </h3>
                        <div class="space-y-2.5">
                            <p class="flex items-center gap-3 text-xs font-bold text-surface-500 uppercase tracking-wide">
                                <SmartphoneIcon class="size-4 text-primary-600" />
                                {{ invoice.client?.phone || '-' }}
                            </p>
                            <p class="flex items-center gap-3 text-xs font-bold text-surface-500 uppercase tracking-wide">
                                <MapPinIcon class="size-4 text-primary-600" />
                                {{ invoice.client?.event_location || '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-50 p-8 rounded-3xl border border-surface-100 flex flex-col justify-center">
                    <div class="space-y-6">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-surface-400 uppercase tracking-widest">Total Penagihan</span>
                            <span class="text-3xl font-serif font-bold text-surface-900 mt-1">{{ formatCurrency(invoice.total) }}</span>
                        </div>

                        <div class="pt-5 border-t border-surface-200/50">
                            <div class="flex justify-between items-end mb-3">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-surface-400 uppercase tracking-[0.2em]">Status Pelunasan</span>
                                    <span class="text-[10px] font-bold mt-1 tracking-tight">
                                        {{ Math.floor((1 - invoice.remaining_amount / invoice.total) * 100) }}% Terbayar
                                    </span>
                                </div>
                                <div class="flex flex-col items-end text-right">
                                    <span class="text-[8px] font-black text-red-400 uppercase tracking-[0.2em]">Sisa Tagihan</span>
                                    <span class="text-sm font-bold mt-1" :class="invoice.remaining_amount > 0 ? 'text-red-600' : 'text-emerald-600'">
                                        {{ formatCurrency(invoice.remaining_amount) }}
                                    </span>
                                </div>
                            </div>

                            <div class="w-full bg-surface-200 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full transition-all duration-1000 shadow-[0_0_8px_rgba(16,185,129,0.3)]"
                                     :style="{ width: `${(1 - invoice.remaining_amount / invoice.total) * 100}%` }"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table Section -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px]">
                    <thead>
                        <tr class="bg-surface-50 border-y border-surface-100">
                            <th class="px-12 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em] text-surface-600">Rincian Layanan</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em] text-surface-600">Qty</th>
                            <th class="px-6 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-surface-600">Harga Unit</th>
                            <th class="px-12 py-4 text-right text-[10px] font-black uppercase tracking-[0.2em] text-surface-600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        <tr v-for="item in invoice.items" :key="item.id" class="group hover:bg-primary-50/20 transition-all duration-300">
                            <td class="px-12 py-6">
                                <p class="text-sm font-bold text-surface-900">{{ item.description }}</p>
                            </td>
                            <td class="px-6 py-6 text-center">
                                <span class="text-xs font-bold text-surface-500">{{ item.quantity }}</span>
                            </td>
                            <td class="px-6 py-6 text-right text-sm font-medium text-surface-500">{{ formatCurrency(item.unit_price) }}</td>
                            <td class="px-12 py-6 text-right text-sm font-bold text-surface-900 tracking-tight">{{ formatCurrency(item.subtotal) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-50/50">
                            <td colspan="3" class="px-12 py-8 text-right">
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-surface-400">Total Keseluruhan</span>
                            </td>
                            <td class="px-12 py-8 text-right">
                                <div class="flex flex-col items-end">
                                    <span class="text-2xl font-serif font-bold text-primary-900 tracking-tight">{{ formatCurrency(invoice.total) }}</span>
                                    <span class="text-[8px] font-black text-emerald-600 uppercase tracking-widest mt-1">Tax Included (Excl.)</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Details & Payment History -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Payment Log -->
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white border border-surface-200 rounded-3xl overflow-hidden shadow-sm">
                    <div class="px-8 py-5 border-b border-surface-100 flex items-center justify-between bg-surface-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <WalletIcon class="size-4" />
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-surface-600">Riwayat Pembayaran</h4>
                        </div>
                        <Button
                            v-if="invoice.status !== 'paid'"
                            type="button"
                            class="h-9 gap-2 rounded-xl bg-emerald-600 px-4 text-[10px] font-black uppercase tracking-widest text-white shadow-md shadow-emerald-700/10 hover:bg-emerald-700"
                            @click="openPaymentModal">
                            <PlusIcon class="size-4" />
                            Tambah Dana
                        </Button>
                    </div>

                    <div class="overflow-x-auto">
                        <table v-if="invoice.payments?.length || (invoice.dp_amount > 0)" class="w-full min-w-[640px]">
                            <thead class="bg-surface-50/30">
                                <tr class="border-b border-surface-100">
                                    <th class="px-8 py-3 text-left text-[9px] font-black uppercase tracking-widest text-surface-400">Tanggal</th>
                                    <th class="px-8 py-3 text-left text-[9px] font-black uppercase tracking-widest text-surface-400">Metode</th>
                                    <th class="px-8 py-3 text-right text-[9px] font-black uppercase tracking-widest text-surface-400">Jumlah</th>
                                    <th class="px-4 py-3 text-right text-[9px] font-black uppercase tracking-widest text-surface-400">Kwitansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100">
                                <!-- Prepend DP (Down Payment) -->
                                <tr v-if="invoice.dp_amount > 0" class="group hover:bg-emerald-50/10 transition-colors bg-emerald-50/5">
                                    <td class="px-8 py-4 text-xs font-bold text-surface-900">{{ formatDate(invoice.invoice_date) }}</td>
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-2">
                                            <Badge variant="success" class="h-5 px-2 text-[9px] font-black uppercase tracking-tighter">Uang Muka</Badge>
                                            <span class="text-[10px] font-bold text-surface-400">#DP</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-right text-xs font-bold text-emerald-600">{{ formatCurrency(invoice.dp_amount) }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-surface-500 hover:text-primary-600"
                                            aria-label="Cetak kwitansi DP"
                                            :disabled="Boolean(pdfLoading)"
                                            @click="printDpReceipt">
                                            <Loader2Icon v-if="pdfLoading === 'dp'" class="size-4 animate-spin" />
                                            <PrinterIcon v-else class="size-4" />
                                        </Button>
                                    </td>
                                </tr>

                                <tr v-for="payment in invoice.payments" :key="payment.id" class="group hover:bg-emerald-50/10 transition-colors">
                                    <td class="px-8 py-4 text-xs font-bold text-surface-900">{{ formatDate(payment.payment_date) }}</td>
                                    <td class="px-8 py-4">
                                        <Badge variant="secondary" class="h-5 px-2 text-[9px] font-bold uppercase tracking-tighter">{{ payment.method }}</Badge>
                                    </td>
                                    <td class="px-8 py-4 text-right text-xs font-bold text-emerald-600">{{ formatCurrency(payment.amount) }}</td>
                                    <td class="px-4 py-4 text-right">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-surface-500 hover:text-primary-600"
                                            :aria-label="`Cetak kwitansi pembayaran ${formatCurrency(payment.amount)}`"
                                            :disabled="Boolean(pdfLoading)"
                                            @click="printPaymentReceipt(payment)">
                                            <Loader2Icon v-if="pdfLoading === 'payment-' + payment.id" class="size-4 animate-spin" />
                                            <PrinterIcon v-else class="size-4" />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="py-12 text-center text-surface-300 font-bold uppercase tracking-widest text-[10px]">
                            Menunggu pembayaran pertama...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Notes & PDF -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white border border-surface-200 rounded-3xl p-8 space-y-6">
                    <div>
                        <h5 class="text-[10px] font-black uppercase tracking-[0.2em] text-surface-400 mb-4">Catatan Dokumen</h5>
                        <p class="text-xs font-medium text-surface-600 leading-relaxed italic">
                            " {{ invoice.notes || 'Sesuai kesepakatan awal layanan.' }} "
                        </p>
                    </div>

                    <div class="space-y-3">
                        <Button
                            type="button"
                            class="h-12 w-full gap-2 rounded-2xl bg-primary-900 text-[10px] font-black uppercase tracking-[0.2em] text-accent-400 shadow-xl shadow-primary-900/20 hover:bg-primary-800"
                            :disabled="Boolean(pdfLoading)"
                            @click="printInvoice">
                            <Loader2Icon v-if="pdfLoading === 'invoice'" class="size-4 animate-spin" />
                            <PrinterIcon v-else class="size-4" />
                            {{ pdfLoading === 'invoice' ? 'Menyiapkan...' : 'Cetak Invoice PDF' }}
                        </Button>

                        <Button
                            v-if="invoice.status === 'paid'"
                            type="button"
                            variant="outline"
                            class="h-12 w-full gap-2 rounded-2xl border-primary-200 text-[10px] font-black uppercase tracking-[0.2em] text-primary-700 hover:bg-primary-50"
                            :disabled="Boolean(pdfLoading)"
                            @click="printPaidInvoice">
                            <Loader2Icon v-if="pdfLoading === 'paid'" class="size-4 animate-spin" />
                            <PrinterIcon v-else class="size-4" />
                            {{ pdfLoading === 'paid' ? 'Menyiapkan...' : 'Cetak Invoice Lunas' }}
                        </Button>

                        <p class="text-[10px] font-medium leading-relaxed text-surface-400">
                            Invoice dapat dicetak kapan saja. Invoice Lunas tersedia setelah pembayaran selesai.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Not Found -->
    <div v-else class="max-w-5xl mx-auto">
        <div class="rounded-3xl border border-surface-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm font-bold text-surface-500">Invoice tidak ditemukan.</p>
            <Button as-child variant="outline" class="mt-6 h-11 rounded-xl px-5 text-sm font-bold">
                <RouterLink :to="{ name: 'invoices' }">Kembali ke daftar invoice</RouterLink>
            </Button>
        </div>
    </div>

    <!-- Payment Modal -->
    <Dialog v-if="invoice" v-model:open="showPaymentModal">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle class="text-xl font-bold">Rekam Pembayaran Baru</DialogTitle>
                <DialogDescription class="sr-only">Form untuk mencatat pembayaran invoice.</DialogDescription>
            </DialogHeader>

            <form id="paymentForm" class="space-y-4" @submit.prevent="handleAddPayment">
                <div class="space-y-2">
                    <Label for="payment-amount" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                        Jumlah Bayar (IDR)
                    </Label>
                    <CurrencyInput
                        id="payment-amount"
                        v-model="paymentForm.amount"
                        required
                        input-class="h-10 w-full rounded-lg border border-surface-200 bg-surface-50 text-sm font-bold focus:outline-none focus:ring-3 focus:ring-primary-500/10 focus:border-primary-500" />
                    <p class="text-[10px] font-medium text-surface-400">
                        Sisa tagihan saat ini: <strong>{{ formatCurrency(invoice.remaining_amount) }}</strong>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="payment-date" class="text-[10px] font-black uppercase tracking-widest text-surface-500">Tanggal</Label>
                        <Input id="payment-date" v-model="paymentForm.payment_date" type="date" required class="h-10" />
                    </div>
                    <div class="space-y-2">
                        <Label for="payment-method" class="text-[10px] font-black uppercase tracking-widest text-surface-500">Metode</Label>
                        <Select v-model="paymentForm.method">
                            <SelectTrigger id="payment-method" class="h-10 w-full">
                                <SelectValue placeholder="Pilih metode" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="transfer">Transfer Bank</SelectItem>
                                <SelectItem value="cash">Tunai (Cash)</SelectItem>
                                <SelectItem value="qris">QRIS / Digital</SelectItem>
                                <SelectItem value="other">Lainnya</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="payment-notes" class="text-[10px] font-black uppercase tracking-widest text-surface-500">Catatan Internal</Label>
                    <Input
                        id="payment-notes"
                        v-model="paymentForm.notes"
                        type="text"
                        placeholder="Misal: Bukti transfer terlampir via WA"
                        class="h-10" />
                </div>

                <p v-if="paymentError" role="alert" class="rounded-lg border border-destructive/20 bg-destructive/10 p-3 text-xs font-medium text-destructive">
                    {{ paymentError }}
                </p>
            </form>

            <DialogFooter>
                <Button type="button" variant="ghost" class="h-10 px-4" :disabled="paymentSubmitting" @click="showPaymentModal = false">
                    Batal
                </Button>
                <Button type="submit" form="paymentForm" class="h-10 px-6" :disabled="paymentSubmitting">
                    <Loader2Icon v-if="paymentSubmitting" class="size-4 animate-spin" />
                    {{ paymentSubmitting ? 'Menyimpan...' : 'Konfirmasi Bayar' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import {
    AlertCircleIcon,
    FileTextIcon,
    Loader2Icon,
    MapPinIcon,
    PlusIcon,
    PrinterIcon,
    SmartphoneIcon,
    WalletIcon,
} from '@lucide/vue'
import { toast } from 'vue-sonner'
import { useInvoiceStore } from '@/stores/invoice'
import { usePaymentStore } from '@/stores/payment'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import CurrencyInput from '@/components/ui/CurrencyInput.vue'
import pdfService from '@/services/pdfService'
import { formatCurrency, formatDate } from '@/utils/format'
import { openPdfRequest, pdfErrorMessage } from '@/utils/pdf'

const route = useRoute()
const invoiceStore = useInvoiceStore()
const paymentStore = usePaymentStore()
const showPaymentModal = ref(false)
const paymentSubmitting = ref(false)
const loading = ref(true)
const loadError = ref('')
const paymentError = ref('')
const pdfLoading = ref('')

const invoice = computed(() => invoiceStore.currentInvoice)

const today = () => new Date().toISOString().split('T')[0]

const paymentForm = reactive({
    amount: 0,
    payment_date: today(),
    method: 'transfer',
    notes: '',
})

const loadInvoice = async () => {
    loading.value = true
    loadError.value = ''

    try {
        await invoiceStore.fetchInvoice(route.params.id)
    } catch (err) {
        loadError.value = err.response?.data?.message || 'Gagal mengambil data invoice.'
        toast.error(loadError.value)
    } finally {
        loading.value = false
    }
}

onMounted(loadInvoice)
watch(() => route.params.id, loadInvoice)

const generatePdf = async (key, request, fallbackFilename, errorMessage) => {
    if (pdfLoading.value) return

    pdfLoading.value = key

    try {
        await openPdfRequest(request, fallbackFilename)
    } catch (err) {
        toast.error(await pdfErrorMessage(err, errorMessage))
    } finally {
        pdfLoading.value = ''
    }
}

const printInvoice = () => generatePdf(
    'invoice',
    () => pdfService.invoice(invoice.value.id),
    `Invoice-${invoice.value.invoice_number}.pdf`,
    'Gagal membuat PDF invoice.',
)

const printPaidInvoice = () => generatePdf(
    'paid',
    () => pdfService.paidInvoice(invoice.value.id),
    `Invoice-Lunas-${invoice.value.invoice_number}.pdf`,
    'Gagal membuat Invoice Lunas.',
)

const printDpReceipt = () => generatePdf(
    'dp',
    () => pdfService.dpReceipt(invoice.value.id),
    `Kwitansi-DP-${invoice.value.invoice_number}.pdf`,
    'Gagal membuat kwitansi DP.',
)

const printPaymentReceipt = (payment) => generatePdf(
    `payment-${payment.id}`,
    () => pdfService.paymentReceipt(payment.id),
    `Kwitansi-${invoice.value.invoice_number}-${payment.id}.pdf`,
    'Gagal membuat kwitansi pembayaran.',
)

const openPaymentModal = () => {
    paymentError.value = ''
    showPaymentModal.value = true
}

const resetPaymentForm = () => {
    Object.assign(paymentForm, {
        amount: 0,
        payment_date: today(),
        method: 'transfer',
        notes: '',
    })
}

const handleAddPayment = async () => {
    const amount = Number(paymentForm.amount)

    if (!amount || amount <= 0) {
        paymentError.value = 'Jumlah pembayaran harus lebih dari 0.'
        return
    }

    paymentSubmitting.value = true
    paymentError.value = ''

    try {
        await paymentStore.createPayment({
            invoice_id: invoice.value.id,
            amount,
            payment_date: paymentForm.payment_date,
            method: paymentForm.method,
            notes: paymentForm.notes.trim(),
        })

        showPaymentModal.value = false
        toast.success('Pembayaran Dicatat', {
            description: `${formatCurrency(amount)} berhasil ditambahkan ke invoice.`,
        })
        resetPaymentForm()

        try {
            await invoiceStore.fetchInvoice(route.params.id)
        } catch {
            toast.error('Pembayaran tersimpan, tetapi riwayat invoice gagal dimuat ulang.')
        }
    } catch (err) {
        const data = err.response?.data
        paymentError.value = data?.errors
            ? Object.values(data.errors)[0]?.[0]
            : data?.message || 'Gagal mencatat pembayaran.'
        toast.error(paymentError.value)
    } finally {
        paymentSubmitting.value = false
    }
}

const statusVariant = (s) => ({ unpaid: 'danger', partial: 'warning', paid: 'success' }[s] ?? 'default')
const statusLabel = (s) => ({ unpaid: 'Belum Bayar', partial: 'Sebagian', paid: 'Lunas' }[s] ?? s)
</script>
