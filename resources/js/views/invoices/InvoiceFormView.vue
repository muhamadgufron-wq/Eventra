<template>
    <div class="max-w-screen-2xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 p-6 sm:p-8 shadow-sm">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-surface-50">
                <div class="w-10 h-10 rounded-xl bg-primary-800 text-white flex items-center justify-center shadow-lg shadow-primary-900/10">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-surface-900 leading-tight">
                        {{ isEdit ? 'Perbarui Invoice' : 'Terbitkan Invoice Baru' }}
                    </h3>
                    <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mt-0.5">Dokumen penagihan resmi sistem</p>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Client & Dates Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-surface-50/50 rounded-xl border border-surface-100">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-surface-500 ml-1">Pilih Client</label>
                        <select v-model="form.client_id" required
                                class="w-full px-3 py-2 bg-white border border-surface-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-bold appearance-none cursor-pointer">
                            <option value="">Cari Client...</option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">
                                {{ c.bride_name }} & {{ c.groom_name }}
                            </option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-surface-500 ml-1">Tanggal Terbit</label>
                        <input v-model="form.invoice_date" type="date" required
                               class="w-full px-3 py-2 bg-white border border-surface-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-bold" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-surface-500 ml-1">Jatuh Tempo</label>
                        <input v-model="form.due_date" type="date"
                               class="w-full px-3 py-2 bg-white border border-surface-200 rounded-lg focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-bold" />
                    </div>
                </div>

                <!-- Invoice Items Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between px-1">
                        <h4 class="text-[9px] font-black uppercase tracking-[0.2em] text-primary-700 flex items-center gap-2">
                            <span class="w-1 h-2.5 bg-primary-600 rounded-full"></span>
                            Rincian Item Penagihan
                        </h4>
                        <button type="button" @click="addItem"
                                class="flex items-center gap-1 px-2.5 py-1.25 border border-primary-200 text-primary-700 bg-white rounded-lg text-[10px] font-black hover:bg-primary-50 transition-all transform active:scale-95 shadow-sm">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            TAMBAH ITEM
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(item, index) in form.items" :key="index" 
                             class="group relative bg-white border border-surface-200 rounded-xl p-3.5 shadow-sm hover:shadow-md hover:border-primary-200 transition-all duration-300">
                            
                            <!-- Remove Button (Top Right) -->
                            <button v-if="form.items.length > 1" type="button" @click="removeItem(index)"
                                    class="absolute -top-1.5 -right-1.5 w-7 h-7 flex items-center justify-center bg-white border border-surface-200 text-surface-400 hover:text-red-500 hover:border-red-100 hover:bg-red-50 rounded-full shadow-sm transition-all opacity-0 group-hover:opacity-100 z-10">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                                <!-- Service Description -->
                                <div class="lg:col-span-6 space-y-1">
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-surface-400 ml-1">Item</label>
                                    <input v-model="item.description" type="text" placeholder="Masukkan nama paket atau rincian layanan..." required
                                           class="w-full px-3.5 py-2 bg-surface-50 border border-surface-100 rounded-lg text-sm font-semibold focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-500 transition-all" />
                                </div>

                                <!-- Qty -->
                                <div class="lg:col-span-1 space-y-1">
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-surface-400 text-center">Qty</label>
                                    <input v-model.number="item.quantity" type="number" min="1" required
                                           class="w-full px-1 py-2 bg-surface-50 border border-surface-100 rounded-lg text-sm font-bold text-center focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-500 transition-all" />
                                </div>

                                <!-- Unit Price -->
                                <div class="lg:col-span-3 space-y-1">
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-surface-400 ml-1">Harga Satuan</label>
                                    <CurrencyInput v-model="item.unit_price" required 
                                                   input-class="w-full pl-9 pr-3 py-2 bg-surface-50 border border-surface-100 rounded-lg focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-500 transition-all text-sm font-bold" />
                                </div>

                                <!-- Subtotal (ReadOnly) -->
                                <div class="lg:col-span-2 space-y-1 text-right">
                                    <label class="block text-[9px] font-black uppercase tracking-widest text-surface-400 mr-1">Subtotal</label>
                                    <div class="px-1 py-2 text-sm font-serif font-black text-surface-900 overflow-hidden text-ellipsis whitespace-nowrap">
                                        {{ formatNumber(item.quantity * item.unit_price) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State Illustration/Manual (optional, but good for context) -->
                        <div v-if="form.items.length === 0" class="py-12 flex flex-col items-center justify-center border-2 border-dashed border-surface-200 rounded-3xl bg-surface-50/50">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-surface-100 mb-4">
                                <svg class="w-8 h-8 text-surface-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-surface-400 uppercase tracking-widest">Belum ada item rincian</p>
                            <button @click="addItem" class="mt-4 text-xs font-black text-primary-600 hover:text-primary-700 transition-colors">
                                KLIK UNTUK TAMBAH ITEM PERTAMA
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section: Summary & Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start pt-6 border-t border-surface-50">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-surface-500 ml-1">Keterangan Tambahan</label>
                        <textarea v-model="form.notes" rows="4" placeholder="Metode pembayaran, instruksi khusus, dll..."
                                  class="w-full px-4 py-3 bg-surface-50 border border-surface-100 rounded-xl text-sm font-medium focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:bg-white focus:border-primary-500 transition-all resize-none shadow-inner"></textarea>
                    </div>

                    <div class="bg-surface-900 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
                        <div class="space-y-3 relative z-10">
                            <div class="flex justify-between items-center text-emerald-100/40 uppercase tracking-widest text-[9px] font-black">
                                <span>Subtotal Keseluruhan</span>
                                <span class="font-bold">{{ formatCurrency(grandTotal) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-1">
                                <span class="text-[10px] font-black uppercase tracking-widest text-accent-400">Upfront / DP (IDR)</span>
                                <CurrencyInput v-model="form.dp_amount" 
                                               prefix=""
                                               input-class="w-32 bg-white/10 border border-white/20 rounded-lg px-3 py-1.5 text-xs text-right font-bold text-accent-400 focus:outline-none focus:ring-2 focus:ring-accent-500" />
                            </div>
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
                <div v-if="error" class="p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600 font-bold uppercase tracking-wide text-center">
                    {{ error }}
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-50">
                    <router-link :to="{ name: 'invoices' }"
                                 class="px-6 py-3 bg-surface-100 text-surface-700 rounded-xl text-sm font-bold hover:bg-surface-200 transition-all active:scale-95">
                        Batal
                    </router-link>
                    <button type="submit" :disabled="submitting"
                            class="px-12 py-3 bg-primary-800 text-white rounded-xl text-sm font-bold shadow-xl shadow-primary-950/20 hover:bg-primary-700 transition-all active:scale-95 disabled:opacity-50">
                        {{ submitting ? 'Memproses...' : (isEdit ? 'Simpan Perubahan' : 'Terbitkan Invoice') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useInvoiceStore } from '@/stores/invoice'
import clientService from '@/services/clientService'
import CurrencyInput from '@/components/ui/CurrencyInput.vue'
import { formatCurrency, formatNumber } from '@/utils/format'

const props = defineProps({
    id: { type: [String, Number], default: null }
})

const router = useRouter()
const invoiceStore = useInvoiceStore()
const submitting = ref(false)
const error = ref('')
const clients = ref([])

const isEdit = computed(() => !!props.id)

const form = reactive({
    client_id: '',
    invoice_date: new Date().toISOString().split('T')[0],
    due_date: '',
    dp_amount: 0,
    notes: '',
    items: [{ description: '', quantity: 1, unit_price: 0 }],
})

const grandTotal = computed(() =>
    form.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0)
)

onMounted(async () => {
    try {
        const { data } = await clientService.getAll({ per_page: 100 })
        clients.value = data.data

        if (isEdit.value) {
            await invoiceStore.fetchInvoice(props.id)
            const inv = invoiceStore.currentInvoice
            if (inv) {
                Object.assign(form, {
                    client_id: inv.client_id,
                    invoice_date: inv.invoice_date,
                    due_date: inv.due_date,
                    dp_amount: inv.dp_amount,
                    notes: inv.notes,
                    items: inv.items.map(item => ({
                        description: item.description,
                        quantity: item.quantity,
                        unit_price: item.unit_price
                    }))
                })
            }
        }
    } catch (err) {
        error.value = 'Gagal mengambil data.'
    }
})

const addItem = () => {
    form.items.push({ description: '', quantity: 1, unit_price: 0 })
}

const removeItem = (index) => {
    form.items.splice(index, 1)
}

const handleSubmit = async () => {
    submitting.value = true
    error.value = ''
    try {
        if (isEdit.value) {
            await invoiceStore.updateInvoice(props.id, form)
        } else {
            await invoiceStore.createInvoice(form)
        }
        router.push({ name: 'invoices' })
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal menyimpan invoice.'
    } finally {
        submitting.value = false
    }
}

</script>
