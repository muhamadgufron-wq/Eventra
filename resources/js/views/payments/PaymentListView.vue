<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">Daftar Pembayaran</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">Riwayat pembayaran dari klien</p>
            </div>
            <router-link :to="{ name: 'payments.create' }"
                         class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-800 text-white rounded-2xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-950/20 active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Catat Pembayaran
            </router-link>
        </div>

        <DataTable
            :columns="columns"
            :data="paymentStore.payments"
            :loading="paymentStore.loading"
            :pagination="paymentStore.pagination"
            searchable
            search-placeholder="Cari invoice atau catatan..."
            @search="handleSearch"
            @page-change="handlePageChange">

            <template #cell-invoice="{ row }">
                <router-link :to="{ name: 'invoices.detail', params: { id: row.invoice_id } }"
                             class="text-primary-600 hover:underline font-medium">
                    {{ row.invoice?.invoice_number ?? '-' }}
                </router-link>
            </template>

            <template #cell-amount="{ value }">
                <span class="font-medium text-emerald-600">{{ formatCurrency(value) }}</span>
            </template>

            <template #cell-payment_date="{ value }">
                {{ formatDate(value) }}
            </template>

            <template #cell-method="{ value }">
                <Badge variant="info" class="capitalize">{{ value }}</Badge>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center gap-2">
                    <router-link :to="{ name: 'payments.edit', params: { id: row.id } }"
                                 class="p-1.5 rounded-lg hover:bg-surface-100 text-surface-500 hover:text-emerald-600 transition-colors"
                                 title="Edit Pembayaran">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                        </svg>
                    </router-link>
                    <button @click="confirmDelete(row)"
                            class="p-1.5 rounded-lg hover:bg-red-50 text-surface-500 hover:text-red-600 transition-colors"
                            title="Hapus Pembayaran">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </template>
        </DataTable>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" title="Hapus Pembayaran" @close="showDeleteModal = false">
            <p class="text-sm text-surface-600">
                Apakah Anda yakin ingin menghapus catatan pembayaran sebesar
                <strong>{{ formatCurrency(paymentToDelete?.amount || 0) }}</strong>
                untuk invoice <strong>{{ paymentToDelete?.invoice?.invoice_number }}</strong>?
            </p>
            <p class="text-xs text-amber-600 font-medium mt-2">
                * Menghapus pembayaran akan mengembalikan sisa tagihan invoice terkait secara otomatis.
            </p>
            <template #footer>
                <div class="flex items-center justify-end gap-3 w-full">
                    <button type="button" @click="showDeleteModal = false"
                            class="px-5 py-2 text-sm font-semibold text-surface-600 hover:text-surface-900 transition-colors">
                        Batal
                    </button>
                    <button type="button" :disabled="deleting" @click="handleDelete"
                            class="px-5 py-2 bg-red-600 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors disabled:opacity-50">
                        {{ deleting ? 'Menghapus...' : 'Ya, Hapus' }}
                    </button>
                </div>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePaymentStore } from '@/stores/payment'
import DataTable from '@/components/ui/DataTable.vue'
import Badge from '@/components/ui/Badge.vue'
import Modal from '@/components/ui/Modal.vue'
import { formatCurrency, formatDate } from '@/utils/format'

const paymentStore = usePaymentStore()
const searchTerm = ref('')
const showDeleteModal = ref(false)
const paymentToDelete = ref(null)
const deleting = ref(false)

const columns = [
    { key: 'invoice', label: 'Invoice' },
    { key: 'amount', label: 'Jumlah' },
    { key: 'payment_date', label: 'Tanggal' },
    { key: 'method', label: 'Metode' },
    { key: 'notes', label: 'Catatan' },
    { key: 'actions', label: 'Aksi', class: 'text-right' },
]

onMounted(() => paymentStore.fetchPayments())

const handleSearch = (query) => {
    searchTerm.value = query
    paymentStore.fetchPayments({ search: query })
}

const handlePageChange = (page) => {
    paymentStore.fetchPayments({ page, search: searchTerm.value })
}

const confirmDelete = (payment) => {
    paymentToDelete.value = payment
    showDeleteModal.value = true
}

const handleDelete = async () => {
    if (!paymentToDelete.value) return
    deleting.value = true
    try {
        await paymentStore.deletePayment(paymentToDelete.value.id)
        showDeleteModal.value = false
        paymentToDelete.value = null
    } catch (error) {
        console.error('Gagal menghapus pembayaran:', error)
    } finally {
        deleting.value = false
    }
}
</script>
