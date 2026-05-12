<template>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-4 px-2">
            <router-link :to="{ name: 'payments' }"
                         class="p-2 rounded-xl hover:bg-surface-100 text-surface-400 hover:text-surface-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
            </router-link>
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">{{ isEdit ? 'Edit Pembayaran' : 'Catat Pembayaran Baru' }}</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">
                    {{ isEdit ? 'Ubah informasi pembayaran' : 'Input pembayaran dari invoice' }}
                </p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-[2rem] border border-surface-100 shadow-xl shadow-surface-950/5 p-8 sm:p-10">
            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Invoice Selection -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-surface-500 ml-1">
                        Invoice <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select v-model="form.invoice_id" required
                                :disabled="isEdit"
                                class="block w-full px-4 py-3.5 bg-surface-50 border border-surface-200 rounded-xl text-surface-900 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all disabled:bg-surface-100 disabled:cursor-not-allowed"
                                :class="{ 'border-red-300 bg-red-50': errors.invoice_id }">
                            <option value="">-- Pilih Invoice --</option>
                            <option v-for="inv in unpaidInvoices" :key="inv.id" :value="inv.id">
                                {{ inv.invoice_number }} - {{ inv.client_name }} (Sisa: {{ formatCurrency(inv.remaining_amount) }})
                            </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </div>
                    <p v-if="errors.invoice_id" class="text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        {{ errors.invoice_id }}
                    </p>
                </div>

                <!-- Amount -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-surface-500 ml-1">
                        Jumlah Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <CurrencyInput v-model="form.amount" placeholder="0" required
                                   input-class="block w-full py-3.5 bg-surface-50 border border-surface-200 rounded-xl text-surface-900 text-sm font-bold focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all"
                                   :class="{ 'border-red-300 bg-red-50': errors.amount }" />
                    <p v-if="errors.amount" class="text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        {{ errors.amount }}
                    </p>
                    <!-- Remaining Amount Info -->
                    <div v-if="selectedInvoice" class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-100 rounded-xl">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-xs font-bold text-amber-800">
                            Sisa setelah pembayaran ini: <span class="font-black">{{ formatCurrency(Math.max(0, selectedInvoice.remaining_amount - form.amount)) }}</span>
                        </p>
                    </div>
                </div>

                <!-- Payment Date -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-surface-500 ml-1">
                        Tanggal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <input v-model="form.payment_date" type="date" required
                           class="block w-full px-4 py-3.5 bg-surface-50 border border-surface-200 rounded-xl text-surface-900 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all"
                           :class="{ 'border-red-300 bg-red-50': errors.payment_date }" />
                    <p v-if="errors.payment_date" class="text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        {{ errors.payment_date }}
                    </p>
                </div>

                <!-- Payment Method -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-surface-500 ml-1">
                        Metode Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <button type="button" @click="form.method = 'cash'"
                                :class="[
                                    'p-4 rounded-xl border-2 font-bold text-sm transition-all active:scale-95',
                                    form.method === 'cash'
                                        ? 'bg-primary-50 border-primary-500 text-primary-700'
                                        : 'bg-surface-50 border-surface-200 text-surface-500 hover:border-surface-300'
                                ]">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                <span>Tunai</span>
                            </div>
                        </button>
                        <button type="button" @click="form.method = 'transfer'"
                                :class="[
                                    'p-4 rounded-xl border-2 font-bold text-sm transition-all active:scale-95',
                                    form.method === 'transfer'
                                        ? 'bg-primary-50 border-primary-500 text-primary-700'
                                        : 'bg-surface-50 border-surface-200 text-surface-500 hover:border-surface-300'
                                ]">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                </svg>
                                <span>Transfer</span>
                            </div>
                        </button>
                        <button type="button" @click="form.method = 'qris'"
                                :class="[
                                    'p-4 rounded-xl border-2 font-bold text-sm transition-all active:scale-95',
                                    form.method === 'qris'
                                        ? 'bg-primary-50 border-primary-500 text-primary-700'
                                        : 'bg-surface-50 border-surface-200 text-surface-500 hover:border-surface-300'
                                ]">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75Z" />
                                </svg>
                                <span>QRIS</span>
                            </div>
                        </button>
                        <button type="button" @click="form.method = 'other'"
                                :class="[
                                    'p-4 rounded-xl border-2 font-bold text-sm transition-all active:scale-95',
                                    form.method === 'other'
                                        ? 'bg-primary-50 border-primary-500 text-primary-700'
                                        : 'bg-surface-50 border-surface-200 text-surface-500 hover:border-surface-300'
                                ]">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                                </svg>
                                <span>Lainnya</span>
                            </div>
                        </button>
                    </div>
                    <p v-if="errors.method" class="text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        {{ errors.method }}
                    </p>
                </div>

                <!-- Notes -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-surface-500 ml-1">
                        Catatan (Opsional)
                    </label>
                    <textarea v-model="form.notes" rows="3"
                              class="block w-full px-4 py-3.5 bg-surface-50 border border-surface-200 rounded-xl text-surface-900 text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all resize-none"
                              placeholder="Tambahkan catatan untuk pembayaran ini..."></textarea>
                    <p v-if="errors.notes" class="text-xs font-bold text-red-500 ml-1 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        {{ errors.notes }}
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-surface-100">
                    <router-link :to="{ name: 'payments' }"
                                 class="px-6 py-3 text-sm font-bold text-surface-600 bg-surface-100 rounded-xl hover:bg-surface-200 transition-colors">
                        Batal
                    </router-link>
                    <button type="submit" :disabled="loading"
                            class="px-8 py-3 bg-primary-800 text-white rounded-xl text-sm font-bold hover:bg-primary-700 disabled:bg-primary-400 transition-all shadow-xl shadow-primary-950/20 active:scale-95 flex items-center gap-2">
                        <svg v-if="loading" class="animate-spin h-5 w-5 text-white/50" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        {{ isEdit ? 'Simpan Perubahan' : 'Catat Pembayaran' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { usePaymentStore } from '@/stores/payment'
import CurrencyInput from '@/components/ui/CurrencyInput.vue'
import { formatCurrency, formatDate } from '@/utils/format'
import paymentService from '@/services/paymentService'
import invoiceService from '@/services/invoiceService'

const router = useRouter()
const route = useRoute()
const paymentStore = usePaymentStore()

const isEdit = computed(() => !!route.params.id)
const loading = ref(false)
const unpaidInvoices = ref([])
const errors = reactive({})

const form = reactive({
    invoice_id: '',
    amount: 0,
    payment_date: new Date().toISOString().split('T')[0],
    method: 'cash',
    notes: '',
})

const selectedInvoice = computed(() => {
    return unpaidInvoices.value.find(inv => inv.id === form.invoice_id)
})

// Load unpaid invoices for dropdown
const loadUnpaidInvoices = async () => {
    try {
        const { data } = await invoiceService.getAll({ status: 'unpaid', per_page: 100 })
        unpaidInvoices.value = data.data.map(inv => ({
            id: inv.id,
            invoice_number: inv.invoice_number,
            client_name: `${inv.client?.bride_name} & ${inv.client?.groom_name}`,
            remaining_amount: parseFloat(inv.remaining_amount),
        }))
    } catch (error) {
        console.error('Failed to load invoices:', error)
    }
}

// Load payment data if editing
const loadPayment = async () => {
    if (!isEdit.value) return

    try {
        const { data } = await paymentService.get(route.params.id)
        form.invoice_id = data.invoice_id
        form.amount = parseFloat(data.amount)
        form.payment_date = data.payment_date
        form.method = data.method
        form.notes = data.notes || ''
    } catch (error) {
        console.error('Failed to load payment:', error)
        router.push({ name: 'payments' })
    }
}

const handleSubmit = async () => {
    errors.invoice_id = null
    errors.amount = null
    errors.payment_date = null
    errors.method = null
    errors.notes = null

    // Validation
    if (!form.invoice_id) errors.invoice_id = 'Invoice harus dipilih'
    if (!form.amount || form.amount <= 0) errors.amount = 'Jumlah pembayaran harus lebih dari 0'
    if (!form.payment_date) errors.payment_date = 'Tanggal pembayaran harus diisi'
    if (!form.method) errors.method = 'Metode pembayaran harus dipilih'

    if (Object.keys(errors).some(k => errors[k])) return

    loading.value = true
    try {
        if (isEdit.value) {
            await paymentService.update(route.params.id, form)
        } else {
            await paymentStore.createPayment(form)
        }
        router.push({ name: 'payments' })
    } catch (error) {
        console.error('Failed to save payment:', error)
        if (error.response?.data?.errors) {
            Object.assign(errors, error.response.data.errors)
        }
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    await loadUnpaidInvoices()
    await loadPayment()
})
</script>
