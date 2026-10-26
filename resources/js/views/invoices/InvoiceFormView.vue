<template>
    <div class="max-w-screen-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 p-6 sm:p-8 shadow-sm">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-surface-50">
                <div class="w-10 h-10 rounded-xl bg-primary-800 text-white flex items-center justify-center shadow-lg shadow-primary-900/10">
                    <FileTextIcon class="size-5" />
                </div>
                <div>
                    <h3 class="text-xl font-bold text-surface-900 leading-tight">
                        {{ isEdit ? 'Perbarui Invoice' : 'Terbitkan Invoice Baru' }}
                    </h3>
                    <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mt-0.5">Dokumen penagihan resmi sistem</p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex items-center justify-center gap-3 rounded-2xl border border-surface-100 bg-surface-50/50 p-12 text-sm font-medium text-surface-500">
                <Loader2Icon class="size-5 animate-spin text-primary-600" />
                Memuat data invoice...
            </div>

            <!-- Load Error State -->
            <div v-else-if="loadError" class="rounded-2xl border border-destructive/20 bg-destructive/5 p-6">
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

            <form v-else @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Client & Dates Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-surface-50/50 rounded-xl border border-surface-100">
                    <div class="space-y-2">
                        <Label for="invoice-client" class="block text-[11px] font-bold text-surface-500 ml-1">Pilih Client</Label>
                        <Select v-model="clientId" @update:model-value="handleClientChange">
                            <SelectTrigger
                                id="invoice-client"
                                class="h-11 w-full rounded-xl border-surface-200 bg-white text-sm font-bold"
                                :aria-invalid="Boolean(fieldError('client_id'))">
                                <SelectValue placeholder="Pilih client..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="c in clients" :key="c.id" :value="String(c.id)">
                                    {{ c.bride_name }} & {{ c.groom_name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="fieldError('client_id')" class="text-xs font-medium text-destructive">
                            {{ fieldError('client_id') }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="invoice-date" class="block text-[11px] font-bold text-surface-500 ml-1">Tanggal Terbit</Label>
                        <Input
                            id="invoice-date"
                            v-model="form.invoice_date"
                            type="date"
                            required
                            :aria-invalid="Boolean(fieldError('invoice_date'))"
                            class="h-11 rounded-xl border-surface-200 bg-white text-sm font-bold" />
                        <p v-if="fieldError('invoice_date')" class="text-xs font-medium text-destructive">
                            {{ fieldError('invoice_date') }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="invoice-due-date" class="block text-[11px] font-bold text-surface-500 ml-1">Jatuh Tempo</Label>
                        <Input
                            id="invoice-due-date"
                            v-model="form.due_date"
                            type="date"
                            :aria-invalid="Boolean(fieldError('due_date'))"
                            class="h-11 rounded-xl border-surface-200 bg-white text-sm font-bold" />
                        <p v-if="fieldError('due_date')" class="text-xs font-medium text-destructive">
                            {{ fieldError('due_date') }}
                        </p>
                    </div>
                </div>

                <!-- Invoice Items Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-primary-700 flex items-center gap-2">
                            <span class="w-1 h-2.5 bg-primary-600 rounded-full"></span>
                            Rincian Item Penagihan
                        </h4>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            class="h-8 gap-1.5 rounded-lg border-primary-200 bg-white px-3 text-[10px] font-black uppercase tracking-wide text-primary-700 hover:bg-primary-50"
                            @click="addItem">
                            <PlusIcon class="size-3.5" />
                            Tambah Item
                        </Button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(item, index) in form.items" :key="index"
                             class="group relative bg-white border border-surface-200 rounded-xl p-3.5 shadow-sm hover:shadow-md hover:border-primary-200 transition-all duration-300">

                            <!-- Remove Button (Top Right) -->
                            <Button
                                v-if="form.items.length > 1"
                                type="button"
                                variant="outline"
                                size="icon-sm"
                                class="absolute -top-2 -right-2 z-10 size-7 rounded-full border-surface-200 bg-white text-surface-400 shadow-sm opacity-100 transition-opacity hover:border-destructive/20 hover:bg-destructive/10 hover:text-destructive sm:opacity-0 sm:group-hover:opacity-100 sm:group-focus-within:opacity-100"
                                aria-label="Hapus item"
                                @click="removeItem(index)">
                                <XIcon />
                            </Button>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                                <!-- Service Description -->
                                <div class="lg:col-span-6 space-y-1.5">
                                    <Label :for="`item-description-${index}`" class="block text-[9px] font-black uppercase tracking-widest text-surface-400 ml-1">Item</Label>
                                    <Input
                                        :id="`item-description-${index}`"
                                        v-model="item.description"
                                        type="text"
                                        placeholder="Masukkan nama paket atau rincian layanan..."
                                        required
                                        :aria-invalid="Boolean(fieldError(`items.${index}.description`))"
                                        class="h-10 rounded-lg border-surface-100 bg-surface-50 px-3.5 text-sm font-semibold focus-visible:bg-white" />
                                    <p v-if="fieldError(`items.${index}.description`)" class="text-xs font-medium text-destructive">
                                        {{ fieldError(`items.${index}.description`) }}
                                    </p>
                                </div>

                                <!-- Qty -->
                                <div class="lg:col-span-1 space-y-1.5">
                                    <Label :for="`item-quantity-${index}`" class="block text-[9px] font-black uppercase tracking-widest text-surface-400 text-center">Qty</Label>
                                    <Input
                                        :id="`item-quantity-${index}`"
                                        v-model="item.quantity"
                                        type="number"
                                        min="1"
                                        required
                                        :aria-invalid="Boolean(fieldError(`items.${index}.quantity`))"
                                        class="h-10 rounded-lg border-surface-100 bg-surface-50 px-1 text-center text-sm font-bold focus-visible:bg-white" />
                                    <p v-if="fieldError(`items.${index}.quantity`)" class="text-xs font-medium text-destructive text-center">
                                        {{ fieldError(`items.${index}.quantity`) }}
                                    </p>
                                </div>

                                <!-- Unit Price -->
                                <div class="lg:col-span-3 space-y-1.5">
                                    <Label :for="`item-price-${index}`" class="block text-[9px] font-black uppercase tracking-widest text-surface-400 ml-1">Harga Satuan</Label>
                                    <CurrencyInput
                                        :id="`item-price-${index}`"
                                        v-model="item.unit_price"
                                        required
                                        :input-class="itemPriceInputClass(index)" />
                                    <p v-if="fieldError(`items.${index}.unit_price`)" class="text-xs font-medium text-destructive">
                                        {{ fieldError(`items.${index}.unit_price`) }}
                                    </p>
                                </div>

                                <!-- Subtotal (ReadOnly) -->
                                <div class="lg:col-span-2 space-y-1.5 text-right">
                                    <span class="block text-[9px] font-black uppercase tracking-widest text-surface-400 mr-1">Subtotal</span>
                                    <div class="px-1 py-2 text-sm font-serif font-black text-surface-900 overflow-hidden text-ellipsis whitespace-nowrap">
                                        {{ formatNumber(item.quantity * item.unit_price) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="form.items.length === 0" class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-surface-200 rounded-3xl bg-surface-50/50">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-surface-100 mb-4">
                                <PlusIcon class="size-8 text-surface-300" />
                            </div>
                            <p class="text-sm font-bold text-surface-400 uppercase tracking-widest">Belum ada item rincian</p>
                            <Button
                                type="button"
                                variant="link"
                                class="mt-2 h-auto p-0 text-xs font-black text-primary-600 hover:text-primary-700"
                                @click="addItem">
                                Klik untuk tambah item pertama
                            </Button>
                        </div>
                    </div>
                    <p v-if="fieldError('items')" class="text-xs font-medium text-destructive">
                        {{ fieldError('items') }}
                    </p>
                </div>

                <!-- Bottom Section: Summary & Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start pt-6 border-t border-surface-50">
                    <div class="space-y-2">
                        <Label for="invoice-notes" class="block text-[11px] font-bold text-surface-500 ml-1">Keterangan Tambahan</Label>
                        <Textarea
                            id="invoice-notes"
                            v-model="form.notes"
                            rows="4"
                            placeholder="Metode pembayaran, instruksi khusus, dll..."
                            :aria-invalid="Boolean(fieldError('notes'))"
                            class="min-h-28 resize-none rounded-xl border-surface-100 bg-surface-50 px-4 py-3 text-sm font-medium focus-visible:bg-white" />
                        <p v-if="fieldError('notes')" class="text-xs font-medium text-destructive">
                            {{ fieldError('notes') }}
                        </p>
                    </div>

                    <div class="bg-surface-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                        <div class="space-y-3 relative z-10">
                            <div class="flex justify-between items-center text-emerald-100/40 uppercase tracking-widest text-[9px] font-black">
                                <span>Subtotal Keseluruhan</span>
                                <span class="font-bold">{{ formatCurrency(grandTotal) }}</span>
                            </div>
                            <div class="flex justify-between items-center gap-4 py-1">
                                <Label for="invoice-dp" class="text-[10px] font-black uppercase tracking-widest text-accent-400">Upfront / DP (IDR)</Label>
                                <CurrencyInput
                                    id="invoice-dp"
                                    v-model="form.dp_amount"
                                    prefix=""
                                    input-class="h-9 w-32 rounded-lg border border-white/20 bg-white/10 px-3 text-right text-xs font-bold text-accent-400 focus:outline-none focus:ring-2 focus:ring-accent-500/50" />
                            </div>
                            <p v-if="fieldError('dp_amount')" class="text-right text-xs font-medium text-red-300">
                                {{ fieldError('dp_amount') }}
                            </p>
                            <div class="h-px bg-white/10 my-2"></div>
                            <div class="flex justify-between items-end">
                                <div class="flex flex-col">
                                    <span class="text-[9px] uppercase tracking-[0.2em] font-black text-surface-400 leading-none">Sisa Tagihan</span>
                                    <span class="text-2xl font-bold text-accent-400 mt-1">{{ formatCurrency(grandTotal - (form.dp_amount || 0)) }}</span>
                                </div>
                                <span class="px-2 py-0.5 bg-accent-500/20 text-accent-400 rounded-md text-[8px] font-black uppercase tracking-widest border border-accent-500/30">
                                    Official Doc
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0">
                    <div v-if="error" role="alert" class="flex items-start gap-3 rounded-2xl border border-destructive/20 bg-destructive/10 p-4 text-sm font-medium text-destructive">
                        <AlertCircleIcon class="mt-0.5 size-5 shrink-0" />
                        <span>{{ error }}</span>
                    </div>
                </Transition>

                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 pt-4 border-t border-surface-50">
                    <Button as-child variant="outline" class="h-12 w-full rounded-xl px-6 text-sm font-bold sm:w-auto">
                        <RouterLink :to="{ name: 'invoices' }">Batal</RouterLink>
                    </Button>
                    <Button
                        type="submit"
                        :disabled="submitting"
                        class="h-12 w-full rounded-xl px-12 text-sm font-bold shadow-xl shadow-primary-900/20 sm:w-auto">
                        <Loader2Icon v-if="submitting" class="size-4 animate-spin" />
                        <SaveIcon v-else class="size-4" />
                        {{ submitting ? 'Memproses...' : (isEdit ? 'Simpan Perubahan' : 'Terbitkan Invoice') }}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import {
    AlertCircleIcon,
    FileTextIcon,
    Loader2Icon,
    PlusIcon,
    SaveIcon,
    XIcon,
} from '@lucide/vue'
import { toast } from 'vue-sonner'
import { useInvoiceStore } from '@/stores/invoice'
import clientService from '@/services/clientService'
import packageService from '@/services/packageService'
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
import { formatCurrency, formatNumber } from '@/utils/format'

const props = defineProps({
    id: { type: [String, Number], default: null }
})

const router = useRouter()
const invoiceStore = useInvoiceStore()
const submitting = ref(false)
const loading = ref(false)
const loadError = ref('')
const error = ref('')
const errors = ref({})
const clients = ref([])
let packageRequestId = 0

const isEdit = computed(() => !!props.id)

const createBlankItem = () => ({
    package_id: null,
    description: '',
    quantity: 1,
    unit_price: 0,
})

const form = reactive({
    client_id: '',
    invoice_date: new Date().toISOString().split('T')[0],
    due_date: '',
    dp_amount: 0,
    notes: '',
    items: [createBlankItem()],
})

const clientId = computed({
    get: () => form.client_id === '' || form.client_id === null ? '' : String(form.client_id),
    set: (value) => {
        form.client_id = value
    },
})

const isBlankItem = (item) =>
    !item.package_id &&
    !String(item.description ?? '').trim() &&
    Number(item.unit_price) === 0

const handleClientChange = async (value) => {
    if (isEdit.value) return

    const client = clients.value.find((item) => String(item.id) === String(value))
    const packageId = client?.package_id
    const requestId = ++packageRequestId

    if (!packageId) {
        if (form.items.length > 0 && form.items.every((item) => item.package_id)) {
            form.items = [createBlankItem()]
        }

        return
    }

    try {
        const { data } = await packageService.getById(packageId)
        if (requestId !== packageRequestId) return

        const pkg = data.data
        const canReplace = form.items.every((item) => item.package_id || isBlankItem(item))

        if (!canReplace) return

        form.items = [{
            package_id: pkg.id,
            description: pkg.name,
            quantity: 1,
            unit_price: Number(pkg.price) || 0,
        }]
        errors.value = {}
    } catch (err) {
        if (requestId !== packageRequestId) return
        toast.error(err.response?.data?.message || 'Gagal memuat paket client.')
    }
}

const grandTotal = computed(() =>
    form.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0)
)

const fieldError = (key) => errors.value[key]?.[0] ?? ''

const itemPriceInputClass = (index) => [
    'h-10 w-full rounded-lg border border-surface-100 bg-surface-50 text-sm font-bold text-surface-900 shadow-xs transition-colors',
    'focus-visible:border-primary-500 focus-visible:ring-3 focus-visible:ring-primary-500/10 focus-visible:bg-white',
    fieldError(`items.${index}.unit_price`) ? 'border-destructive focus-visible:ring-destructive/20' : '',
].join(' ')

onMounted(async () => {
    loading.value = true

    try {
        const { data } = await clientService.getAll({ per_page: 100 })
        clients.value = data.data ?? []

        if (isEdit.value) {
            await invoiceStore.fetchInvoice(props.id)
            const inv = invoiceStore.currentInvoice

            if (!inv) {
                throw new Error('Invoice tidak ditemukan.')
            }

            Object.assign(form, {
                client_id: inv.client_id ?? '',
                invoice_date: inv.invoice_date ?? '',
                due_date: inv.due_date ?? '',
                dp_amount: Number(inv.dp_amount) || 0,
                notes: inv.notes ?? '',
                items: (inv.items ?? []).map((item) => ({
                    package_id: item.package_id ?? null,
                    description: item.description ?? '',
                    quantity: Number(item.quantity) || 1,
                    unit_price: Number(item.unit_price) || 0,
                })),
            })
        }
    } catch (err) {
        loadError.value = err.response?.data?.message || err.message || 'Gagal mengambil data invoice.'
        toast.error(loadError.value)
    } finally {
        loading.value = false
    }
})

const addItem = () => {
    form.items.push(createBlankItem())
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

const handleSubmit = async () => {
    submitting.value = true
    error.value = ''
    errors.value = {}

    const payload = {
        client_id: form.client_id === '' || form.client_id === null ? null : Number(form.client_id),
        invoice_date: form.invoice_date,
        due_date: form.due_date || null,
        dp_amount: Number(form.dp_amount) || 0,
        notes: form.notes.trim(),
        items: form.items.map((item) => ({
            package_id: item.package_id ?? null,
            description: (item.description ?? '').trim(),
            quantity: Number(item.quantity),
            unit_price: Number(item.unit_price),
        })),
    }

    try {
        if (isEdit.value) {
            await invoiceStore.updateInvoice(props.id, payload)
        } else {
            await invoiceStore.createInvoice(payload)
        }

        toast.success(isEdit.value ? 'Invoice Diperbarui' : 'Invoice Diterbitkan', {
            description: isEdit.value
                ? 'Perubahan invoice berhasil disimpan.'
                : 'Invoice baru berhasil diterbitkan.',
        })

        router.push({ name: 'invoices' })
    } catch (err) {
        const data = err.response?.data

        if (data?.errors) {
            errors.value = data.errors
            toast.error('Periksa kembali data invoice.')
        } else {
            error.value = data?.message || 'Gagal menyimpan invoice.'
            toast.error(error.value)
        }
    } finally {
        submitting.value = false
    }
}
</script>
