<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">Arsip Invoice</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">Pantau penagihan dan status pembayaran real-time</p>
            </div>
            <div class="flex items-center gap-3">
                <select v-model="statusFilter" @change="handleFilter"
                        class="px-4 py-3 bg-white border border-surface-100 rounded-2xl text-xs font-bold uppercase tracking-widest text-surface-500 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all shadow-sm cursor-pointer appearance-none">
                    <option value="">Semua Status</option>
                    <option value="unpaid">Belum Bayar</option>
                    <option value="partial">Sebagian</option>
                    <option value="paid">Lunas</option>
                </select>
                <router-link :to="{ name: 'invoices.create' }"
                             class="inline-flex items-center gap-2 px-6 py-3 bg-primary-800 text-white rounded-2xl text-sm font-bold hover:bg-primary-700 transition-all shadow-xl shadow-primary-950/20 active:scale-95">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Terbitkan Invoice
                </router-link>
            </div>
        </div>

        <DataTable
            :columns="columns"
            :data="invoiceStore.invoices"
            :loading="invoiceStore.loading"
            :pagination="invoiceStore.pagination"
            searchable
            search-placeholder="Cari nomor invoice atau nama client..."
            @search="handleSearch"
            @page-change="handlePageChange">

            <template #cell-client="{ row }">
                {{ row.client?.bride_name }} & {{ row.client?.groom_name }}
            </template>

            <template #cell-total="{ value }">
                {{ formatCurrency(value) }}
            </template>

            <template #cell-remaining_amount="{ value }">
                {{ formatCurrency(value) }}
            </template>

            <template #cell-status="{ value }">
                <Badge :variant="statusVariant(value)">
                    {{ statusLabel(value) }}
                </Badge>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center gap-2">
                    <router-link :to="{ name: 'invoices.detail', params: { id: row.id } }"
                                 class="p-1.5 rounded-lg hover:bg-surface-100 text-surface-500 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </router-link>
                    <router-link :to="{ name: 'invoices.edit', params: { id: row.id } }"
                                 class="p-1.5 rounded-lg hover:bg-surface-100 text-surface-500 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </router-link>
                    <button @click="confirmDelete(row)"
                            class="p-1.5 rounded-lg hover:bg-red-50 text-surface-500 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <Modal :show="showDeleteModal" title="Hapus Invoice" @close="showDeleteModal = false">
            <p class="text-sm text-surface-600">
                Hapus invoice <strong>{{ invoiceToDelete?.invoice_number }}</strong>? Semua item dan pembayaran terkait akan ikut dihapus.
            </p>
            <template #footer>
                <button @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-surface-700 bg-surface-100 rounded-lg hover:bg-surface-200 transition-colors">
                    Batal
                </button>
                <button @click="handleDelete"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                    Hapus
                </button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useInvoiceStore } from '@/stores/invoice'
import invoiceService from '@/services/invoiceService'
import DataTable from '@/components/ui/DataTable.vue'
import Modal from '@/components/ui/Modal.vue'
import Badge from '@/components/ui/Badge.vue'
import { formatCurrency, formatDate } from '@/utils/format'

const invoiceStore = useInvoiceStore()
const showDeleteModal = ref(false)
const invoiceToDelete = ref(null)
const searchTerm = ref('')
const statusFilter = ref('')

const columns = [
    { key: 'invoice_number', label: 'No. Invoice' },
    { key: 'client', label: 'Client' },
    { key: 'total', label: 'Total' },
    { key: 'remaining_amount', label: 'Sisa' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: '' },
]

onMounted(() => invoiceStore.fetchInvoices())

const handleSearch = (query) => {
    searchTerm.value = query
    invoiceStore.fetchInvoices({ search: query, status: statusFilter.value })
}

const handlePageChange = (page) => {
    invoiceStore.fetchInvoices({ page, search: searchTerm.value, status: statusFilter.value })
}

const handleFilter = () => {
    invoiceStore.fetchInvoices({ search: searchTerm.value, status: statusFilter.value })
}

const confirmDelete = (inv) => {
    invoiceToDelete.value = inv
    showDeleteModal.value = true
}

const handleDelete = async () => {
    await invoiceStore.deleteInvoice(invoiceToDelete.value.id)
    showDeleteModal.value = false
}

const statusVariant = (s) => ({ unpaid: 'danger', partial: 'warning', paid: 'success' }[s] ?? 'default')
const statusLabel = (s) => ({ unpaid: 'Belum Bayar', partial: 'Sebagian', paid: 'Lunas' }[s] ?? s)
</script>
