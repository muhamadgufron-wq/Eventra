<template>
    <div v-if="invoice" class="max-w-5xl mx-auto space-y-8">
        <!-- Invoice Master Document -->
        <div class="bg-white rounded-3xl border border-surface-200 shadow-xl shadow-surface-950/5 relative overflow-hidden">
            <!-- Professional Header Section -->
            <div class="p-8 sm:p-12 border-b border-surface-100 bg-surface-50/30 relative overflow-hidden">
                <!-- Branding Background Accent -->
                <div class="absolute top-0 right-0 w-80 h-80 bg-primary-50 rounded-full blur-[100px] -mr-40 -mt-40 opacity-40"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row justify-between gap-10">
                    <!-- Left: WO Company Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-primary-900 flex items-center justify-center text-white shadow-lg shadow-primary-900/10">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-serif font-bold text-surface-900 tracking-tight">WO Manager</h2>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-700">Premium Event Organizer</p>
                            </div>
                        </div>

                        <div class="space-y-1 text-xs font-medium text-surface-500 max-w-xs">
                            <p>Jl. Melati No. 123, Jakarta Selatan</p>
                            <p>contact@womanager.com | (021) 555-0123</p>
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
                                <Badge :variant="statusVariant(invoice.status)" class="mt-0.5 text-[9px] font-black uppercase tracking-widest px-3 py-1 rounded-full border">
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
                                <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                                {{ invoice.client?.phone || '-' }}
                            </p>
                            <p class="flex items-center gap-3 text-xs font-bold text-surface-500 uppercase tracking-wide">
                                <svg class="w-4 h-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
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
            <div class="">
                <table class="w-full">
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
                                    <span class="text-2xl font-serif font-bold text-primary-950 tracking-tight">{{ formatCurrency(invoice.total) }}</span>
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
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75m0 5.25v.75m0 5.25v.75m11.25-13.5h.75m-12 11.25h.75m11.25-6H12m1.5 9h.75m0-12H13.5m0 0a4.5 4.5 0 1 0-9 0 4.5 4.5 0 0 0 9 0Z" />
                                </svg>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-surface-600">Riwayat Pembayaran</h4>
                        </div>
                        <button v-if="invoice.status !== 'paid'" @click="showPaymentModal = true"
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all transform active:scale-95 shadow-md shadow-emerald-700/10">
                            Tambah Dana
                        </button>
                    </div>

                    <div class="p-0">
                        <table v-if="invoice.payments?.length || (invoice.dp_amount > 0)" class="w-full">
                            <thead class="bg-surface-50/30">
                                <tr class="border-b border-surface-100">
                                    <th class="px-8 py-3 text-left text-[9px] font-black uppercase tracking-widest text-surface-400">Tanggal</th>
                                    <th class="px-8 py-3 text-left text-[9px] font-black uppercase tracking-widest text-surface-400">Metode</th>
                                    <th class="px-8 py-3 text-right text-[9px] font-black uppercase tracking-widest text-surface-400">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100">
                                <!-- Prepend DP (Down Payment) -->
                                <tr v-if="invoice.dp_amount > 0" class="group hover:bg-emerald-50/10 transition-colors bg-emerald-50/5">
                                    <td class="px-8 py-4 text-xs font-bold text-surface-900">{{ formatDate(invoice.invoice_date) }}</td>
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-emerald-100 rounded text-[9px] font-black text-emerald-600 uppercase tracking-tighter">Uang Muka</span>
                                            <span class="text-[10px] font-bold text-surface-400">#DP</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-right text-xs font-bold text-emerald-600">{{ formatCurrency(invoice.dp_amount) }}</td>
                                </tr>

                                <tr v-for="payment in invoice.payments" :key="payment.id" class="group hover:bg-emerald-50/10 transition-colors">
                                    <td class="px-8 py-4 text-xs font-bold text-surface-900">{{ formatDate(payment.payment_date) }}</td>
                                    <td class="px-8 py-4">
                                        <span class="px-2 py-0.5 bg-surface-100 rounded text-[9px] font-bold text-surface-500 uppercase tracking-tighter">{{ payment.method }}</span>
                                    </td>
                                    <td class="px-8 py-4 text-right text-xs font-bold text-emerald-600">{{ formatCurrency(payment.amount) }}</td>
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
                        <button class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-primary-950 text-accent-400 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] hover:bg-primary-900 transition-all shadow-xl shadow-primary-950/20 active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231a1.125 1.125 0 0 1-1.122-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-14.326 0C3.768 7.441 3 8.375 3 9.456V15.75a2.25 2.25 0 0 0 2.25 2.25h1.091M9 13.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                            </svg>
                            Cetak Invoice PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <Modal :show="showPaymentModal" title="Rekam Pembayaran Baru" @close="showPaymentModal = false">
            <form @submit.prevent="handleAddPayment" class="p-2 space-y-5">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Jumlah Bayar (IDR)</label>
                    <CurrencyInput v-model="paymentForm.amount" required
                                   input-class="w-full pl-10 pr-4 py-3 bg-surface-50 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all font-bold" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Tanggal</label>
                        <input v-model="paymentForm.payment_date" type="date" required
                               class="w-full px-4 py-3 bg-surface-50 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Metode</label>
                        <select v-model="paymentForm.method"
                                class="w-full px-4 py-3 bg-surface-50 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-bold">
                            <option value="transfer">Transfer Bank</option>
                            <option value="cash">Tunai (Cash)</option>
                            <option value="qris">QRIS / Digital</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Catatan Internal</label>
                    <input v-model="paymentForm.notes" type="text" placeholder="Misal: Bukti transfer terlampir via WA"
                           class="w-full px-4 py-3 bg-surface-50 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                </div>
            </form>
            <template #footer>
                <div class="flex items-center justify-end gap-3 w-full">
                    <button @click="showPaymentModal = false"
                            class="px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-surface-500 hover:bg-surface-100 rounded-xl transition-all">
                        Batal
                    </button>
                    <button @click="handleAddPayment" :disabled="paymentSubmitting"
                            class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-700/20 active:scale-95 disabled:opacity-50">
                        {{ paymentSubmitting ? 'Menyimpan...' : 'Konfirmasi Bayar' }}
                    </button>
                </div>
            </template>
        </Modal>
    </div>

    <!-- Loading -->
    <div v-else class="flex flex-col items-center justify-center py-32 space-y-4">
        <div class="w-12 h-12 border-4 border-primary-100 border-t-primary-600 rounded-full animate-spin"></div>
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-surface-400">Menyusun Dokumen...</p>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useInvoiceStore } from '@/stores/invoice'
import { usePaymentStore } from '@/stores/payment'
import Badge from '@/components/ui/Badge.vue'
import Modal from '@/components/ui/Modal.vue'
import CurrencyInput from '@/components/ui/CurrencyInput.vue'
import { formatCurrency, formatDate } from '@/utils/format'

const route = useRoute()
const invoiceStore = useInvoiceStore()
const paymentStore = usePaymentStore()
const showPaymentModal = ref(false)
const paymentSubmitting = ref(false)

const invoice = computed(() => invoiceStore.currentInvoice)

const paymentForm = reactive({
    amount: 0,
    payment_date: new Date().toISOString().split('T')[0],
    method: 'transfer',
    notes: '',
})

onMounted(() => {
    invoiceStore.fetchInvoice(route.params.id)
})

const handleAddPayment = async () => {
    if (paymentForm.amount <= 0) return
    paymentSubmitting.value = true
    try {
        await paymentStore.createPayment({
            invoice_id: invoice.value.id,
            ...paymentForm,
        })
        showPaymentModal.value = false
        // Refresh invoice to get updated status and payments
        await invoiceStore.fetchInvoice(route.params.id)
        // Reset form
        Object.assign(paymentForm, { amount: 0, payment_date: new Date().toISOString().split('T')[0], method: 'transfer', notes: '' })
    } finally {
        paymentSubmitting.value = false
    }
}

const statusVariant = (s) => ({ unpaid: 'danger', partial: 'warning', paid: 'success' }[s] ?? 'default')
const statusLabel = (s) => ({ unpaid: 'Belum Bayar', partial: 'Sebagian', paid: 'Lunas' }[s] ?? s)
</script>
