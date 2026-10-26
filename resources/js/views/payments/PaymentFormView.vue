<template>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center gap-4 px-2">
            <Button as-child variant="ghost" size="icon" class="rounded-xl text-surface-400 hover:text-surface-600" aria-label="Kembali ke daftar pembayaran">
                <RouterLink :to="{ name: 'payments' }">
                    <ArrowLeftIcon />
                </RouterLink>
            </Button>
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">{{ isEdit ? 'Edit Pembayaran' : 'Catat Pembayaran Baru' }}</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">
                    {{ isEdit ? 'Ubah informasi pembayaran' : 'Input pembayaran dari invoice' }}
                </p>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="pageLoading" class="flex items-center justify-center gap-3 rounded-[2rem] border border-surface-100 bg-white p-12 text-sm font-medium text-surface-500 shadow-xl shadow-surface-950/5">
            <Loader2Icon class="size-5 animate-spin text-primary-600" />
            Memuat data pembayaran...
        </div>

        <!-- Load Error State -->
        <div v-else-if="loadError" class="rounded-[2rem] border border-destructive/20 bg-white p-8 shadow-xl shadow-surface-950/5">
            <div class="flex items-start gap-4">
                <div class="rounded-2xl bg-destructive/10 p-3 text-destructive">
                    <AlertCircleIcon class="size-6" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-surface-900">Gagal memuat pembayaran</h2>
                    <p class="mt-1 text-sm text-surface-500">{{ loadError }}</p>
                </div>
            </div>
            <Button as-child variant="outline" class="mt-6 h-11 rounded-xl px-5 text-sm font-bold">
                <RouterLink :to="{ name: 'payments' }">Kembali ke daftar pembayaran</RouterLink>
            </Button>
        </div>

        <!-- Form Card -->
        <div v-else class="bg-white rounded-[2rem] border border-surface-100 shadow-xl shadow-surface-950/5 p-8 sm:p-10">
            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Invoice Selection -->
                <div class="space-y-2">
                    <Label for="payment-invoice" class="block text-xs font-bold text-surface-500 ml-1">
                        Invoice <span class="text-destructive">*</span>
                    </Label>
                    <Select v-model="invoiceId" :disabled="isEdit">
                        <SelectTrigger
                            id="payment-invoice"
                            class="h-12 w-full rounded-xl border-surface-200 bg-surface-50 text-sm font-semibold disabled:bg-surface-100"
                            :aria-invalid="Boolean(fieldError('invoice_id'))">
                            <SelectValue placeholder="Pilih invoice..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="inv in openInvoices" :key="inv.id" :value="String(inv.id)">
                                {{ inv.invoice_number }} - {{ inv.client_name }} (Sisa: {{ formatCurrency(inv.remaining_amount) }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="fieldError('invoice_id')" class="text-xs font-medium text-destructive ml-1">
                        {{ fieldError('invoice_id') }}
                    </p>
                </div>

                <!-- Amount -->
                <div class="space-y-2">
                    <Label for="payment-amount" class="block text-xs font-bold text-surface-500 ml-1">
                        Jumlah Pembayaran <span class="text-destructive">*</span>
                    </Label>
                    <CurrencyInput
                        id="payment-amount"
                        v-model="form.amount"
                        placeholder="0"
                        required
                        :input-class="amountInputClass" />
                    <p v-if="fieldError('amount')" class="text-xs font-medium text-destructive ml-1">
                        {{ fieldError('amount') }}
                    </p>

                    <!-- Remaining Amount Info -->
                    <div v-if="selectedInvoice" class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-100 rounded-xl">
                        <AlertTriangleIcon class="size-4 text-amber-600 shrink-0" />
                        <p class="text-xs font-bold text-amber-800">
                            Sisa setelah pembayaran ini:
                            <span class="font-black">{{ formatCurrency(remainingAfterPayment) }}</span>
                        </p>
                    </div>
                </div>

                <!-- Payment Date -->
                <div class="space-y-2">
                    <Label for="payment-date" class="block text-xs font-bold text-surface-500 ml-1">
                        Tanggal Pembayaran <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="payment-date"
                        v-model="form.payment_date"
                        type="date"
                        required
                        :aria-invalid="Boolean(fieldError('payment_date'))"
                        class="h-12 rounded-xl border-surface-200 bg-surface-50 text-sm font-semibold" />
                    <p v-if="fieldError('payment_date')" class="text-xs font-medium text-destructive ml-1">
                        {{ fieldError('payment_date') }}
                    </p>
                </div>

                <!-- Payment Method -->
                <div class="space-y-2">
                    <Label class="block text-xs font-bold text-surface-500 ml-1">
                        Metode Pembayaran <span class="text-destructive">*</span>
                    </Label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <Button
                            v-for="method in paymentMethods"
                            :key="method.value"
                            type="button"
                            variant="outline"
                            :aria-pressed="form.method === method.value"
                            :class="[
                                'flex h-auto flex-col gap-2 rounded-xl border-2 p-4 text-sm font-bold',
                                form.method === method.value
                                    ? 'border-primary-500 bg-primary-50 text-primary-700 hover:bg-primary-50'
                                    : 'border-surface-200 bg-surface-50 text-surface-500 hover:border-surface-300'
                            ]"
                            @click="form.method = method.value">
                            <component :is="method.icon" class="size-6" />
                            <span>{{ method.label }}</span>
                        </Button>
                    </div>
                    <p v-if="fieldError('method')" class="text-xs font-medium text-destructive ml-1">
                        {{ fieldError('method') }}
                    </p>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <Label for="payment-notes" class="block text-xs font-bold text-surface-500 ml-1">
                        Catatan (Opsional)
                    </Label>
                    <Textarea
                        id="payment-notes"
                        v-model="form.notes"
                        rows="3"
                        placeholder="Tambahkan catatan untuk pembayaran ini..."
                        :aria-invalid="Boolean(fieldError('notes'))"
                        class="min-h-24 resize-none rounded-xl border-surface-200 bg-surface-50 px-4 py-3 text-sm font-semibold" />
                    <p v-if="fieldError('notes')" class="text-xs font-medium text-destructive ml-1">
                        {{ fieldError('notes') }}
                    </p>
                </div>

                <!-- General Error -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0">
                    <div v-if="error" role="alert" class="flex items-start gap-3 rounded-2xl border border-destructive/20 bg-destructive/10 p-4 text-sm font-medium text-destructive">
                        <AlertCircleIcon class="mt-0.5 size-5 shrink-0" />
                        <span>{{ error }}</span>
                    </div>
                </Transition>

                <!-- Submit Button -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-6 border-t border-surface-100">
                    <Button as-child variant="outline" class="h-12 w-full rounded-xl px-6 text-sm font-bold sm:w-auto">
                        <RouterLink :to="{ name: 'payments' }">Batal</RouterLink>
                    </Button>
                    <Button
                        type="submit"
                        :disabled="submitting"
                        class="h-12 w-full gap-2 rounded-xl px-8 text-sm font-bold shadow-xl shadow-primary-900/20 sm:w-auto">
                        <Loader2Icon v-if="submitting" class="size-4 animate-spin" />
                        <CheckIcon v-else class="size-4" />
                        {{ submitting ? 'Memproses...' : (isEdit ? 'Simpan Perubahan' : 'Catat Pembayaran') }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import {
    AlertCircleIcon,
    AlertTriangleIcon,
    ArrowLeftIcon,
    ArrowLeftRightIcon,
    BanknoteIcon,
    CheckIcon,
    Loader2Icon,
    QrCodeIcon,
    SparklesIcon,
} from '@lucide/vue'
import { toast } from 'vue-sonner'
import { usePaymentStore } from '@/stores/payment'
import paymentService from '@/services/paymentService'
import invoiceService from '@/services/invoiceService'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import CurrencyInput from '@/components/ui/CurrencyInput.vue'
import { formatCurrency } from '@/utils/format'

const router = useRouter()
const route = useRoute()
const paymentStore = usePaymentStore()

const isEdit = computed(() => !!route.params.id)
const pageLoading = ref(true)
const submitting = ref(false)
const loadError = ref('')
const error = ref('')
const errors = ref({})
const openInvoices = ref([])
const originalAmount = ref(0)

const paymentMethods = [
    { value: 'cash', label: 'Tunai', icon: BanknoteIcon },
    { value: 'transfer', label: 'Transfer', icon: ArrowLeftRightIcon },
    { value: 'qris', label: 'QRIS', icon: QrCodeIcon },
    { value: 'other', label: 'Lainnya', icon: SparklesIcon },
]

const form = reactive({
    invoice_id: '',
    amount: 0,
    payment_date: new Date().toISOString().split('T')[0],
    method: 'cash',
    notes: '',
})

const invoiceId = computed({
    get: () => form.invoice_id === '' || form.invoice_id === null ? '' : String(form.invoice_id),
    set: (value) => {
        form.invoice_id = value === '' ? '' : Number(value)
    },
})

const selectedInvoice = computed(() => {
    return openInvoices.value.find((inv) => inv.id === form.invoice_id)
})

// While editing, the current payment is already included in the invoice's remaining amount.
const availableAmount = computed(() => {
    if (!selectedInvoice.value) return 0

    return selectedInvoice.value.remaining_amount + (isEdit.value ? originalAmount.value : 0)
})

const remainingAfterPayment = computed(() => {
    return Math.max(0, availableAmount.value - Number(form.amount || 0))
})

const normalizeInvoice = (inv) => ({
    id: inv.id,
    invoice_number: inv.invoice_number,
    client_name: `${inv.client?.bride_name ?? '-'} & ${inv.client?.groom_name ?? '-'}`,
    remaining_amount: parseFloat(inv.remaining_amount) || 0,
})

const fieldError = (key) => {
    const value = errors.value[key]
    return Array.isArray(value) ? (value[0] ?? '') : (value ?? '')
}

const amountInputClass = computed(() => [
    'h-12 w-full rounded-xl border border-surface-200 bg-surface-50 text-sm font-bold text-surface-900 shadow-xs transition-colors',
    'focus-visible:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500/10 focus-visible:bg-white',
    fieldError('amount') ? 'border-destructive focus-visible:ring-destructive/20' : '',
].join(' '))

watch(form, () => {
    if (Object.keys(errors.value).length > 0) {
        errors.value = {}
    }
    if (error.value) {
        error.value = ''
    }
}, { deep: true })

const loadOpenInvoices = async () => {
    const { data } = await invoiceService.getAll({ status: 'unpaid,partial', per_page: 100 })
    openInvoices.value = (data.data ?? []).map(normalizeInvoice)
}

const loadPayment = async () => {
    if (!isEdit.value) return

    const { data } = await paymentService.get(route.params.id)
    form.invoice_id = data.invoice_id
    form.amount = parseFloat(data.amount)
    originalAmount.value = parseFloat(data.amount)
    form.payment_date = data.payment_date
    form.method = data.method
    form.notes = data.notes || ''

    // A paid invoice is excluded from the open invoice list, but it must still
    // be available in the select when editing the payment that settled it.
    if (data.invoice && !openInvoices.value.some((inv) => inv.id === data.invoice.id)) {
        openInvoices.value.push(normalizeInvoice(data.invoice))
    }
}

onMounted(async () => {
    pageLoading.value = true
    loadError.value = ''

    try {
        await loadOpenInvoices()
        await loadPayment()
    } catch (err) {
        loadError.value = err.response?.data?.message || 'Gagal mengambil data pembayaran.'
        toast.error(loadError.value)
    } finally {
        pageLoading.value = false
    }
})

const handleSubmit = async () => {
    errors.value = {}
    error.value = ''

    if (!form.invoice_id) {
        errors.value.invoice_id = 'Invoice harus dipilih.'
    }
    if (!form.amount || Number(form.amount) <= 0) {
        errors.value.amount = 'Jumlah pembayaran harus lebih dari 0.'
    } else if (selectedInvoice.value && Number(form.amount) > availableAmount.value) {
        errors.value.amount = `Jumlah pembayaran melebihi sisa tagihan (${formatCurrency(availableAmount.value)}).`
    }
    if (!form.payment_date) {
        errors.value.payment_date = 'Tanggal pembayaran harus diisi.'
    }
    if (!form.method) {
        errors.value.method = 'Metode pembayaran harus dipilih.'
    }

    if (Object.keys(errors.value).length > 0) return

    submitting.value = true

    const payload = {
        invoice_id: Number(form.invoice_id),
        amount: Number(form.amount),
        payment_date: form.payment_date,
        method: form.method,
        notes: form.notes.trim(),
    }

    try {
        if (isEdit.value) {
            await paymentService.update(route.params.id, payload)
        } else {
            await paymentStore.createPayment(payload)
        }

        toast.success(isEdit.value ? 'Pembayaran Diperbarui' : 'Pembayaran Dicatat', {
            description: isEdit.value
                ? 'Perubahan pembayaran berhasil disimpan.'
                : 'Pembayaran baru berhasil dicatat.',
        })

        router.push({ name: 'payments' })
    } catch (err) {
        const data = err.response?.data

        if (data?.errors) {
            errors.value = data.errors
            toast.error('Periksa kembali data pembayaran.')
        } else {
            error.value = data?.message || 'Gagal menyimpan pembayaran.'
            toast.error(error.value)
        }
    } finally {
        submitting.value = false
    }
}
</script>
