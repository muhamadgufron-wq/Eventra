<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">Arsip Invoice</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">Pantau penagihan dan status pembayaran real-time</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <Select :model-value="statusFilter" @update:model-value="handleFilter">
                    <SelectTrigger
                        class="h-12 w-full rounded-2xl border-surface-100 bg-white text-xs font-bold uppercase tracking-widest text-surface-500 shadow-sm sm:w-48">
                        <SelectValue placeholder="Semua Status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Status</SelectItem>
                        <SelectItem value="unpaid">Belum Bayar</SelectItem>
                        <SelectItem value="partial">Sebagian</SelectItem>
                        <SelectItem value="paid">Lunas</SelectItem>
                    </SelectContent>
                </Select>
                <Button
                    as-child
                    class="h-12 gap-2 rounded-2xl px-6 text-sm font-bold shadow-xl shadow-primary-900/20">
                    <RouterLink :to="{ name: 'invoices.create' }">
                        <FilePlus2Icon class="size-5" />
                        Terbitkan Invoice
                    </RouterLink>
                </Button>
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
                    <Button
                        as-child
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:text-primary-600"
                        aria-label="Lihat invoice">
                        <RouterLink :to="{ name: 'invoices.detail', params: { id: row.id } }">
                            <EyeIcon />
                        </RouterLink>
                    </Button>
                    <Button
                        as-child
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:text-primary-600"
                        aria-label="Edit invoice">
                        <RouterLink :to="{ name: 'invoices.edit', params: { id: row.id } }">
                            <PencilIcon />
                        </RouterLink>
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:bg-destructive/10 hover:text-destructive"
                        aria-label="Hapus invoice"
                        @click="confirmDelete(row)">
                        <Trash2Icon />
                    </Button>
                </div>
            </template>
        </DataTable>

        <!-- Delete Confirmation -->
        <AlertDialog v-model:open="showDeleteModal">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Hapus Invoice</AlertDialogTitle>
                    <AlertDialogDescription>
                        Apakah Anda yakin ingin menghapus invoice
                        <strong>{{ invoiceToDelete?.invoice_number }}</strong>?
                        Semua item dan pembayaran terkait akan ikut dihapus.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel :disabled="deleting">Batal</AlertDialogCancel>
                    <Button
                        variant="destructive"
                        :disabled="deleting"
                        @click="handleDelete">
                        <Loader2Icon v-if="deleting" class="size-4 animate-spin" />
                        {{ deleting ? 'Menghapus...' : 'Hapus' }}
                    </Button>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { EyeIcon, FilePlus2Icon, Loader2Icon, PencilIcon, Trash2Icon } from '@lucide/vue'
import { toast } from 'vue-sonner'
import { useInvoiceStore } from '@/stores/invoice'
import DataTable from '@/components/ui/DataTable.vue'
import {
    AlertDialog,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { formatCurrency } from '@/utils/format'

const invoiceStore = useInvoiceStore()
const showDeleteModal = ref(false)
const invoiceToDelete = ref(null)
const deleting = ref(false)
const searchTerm = ref('')
const statusFilter = ref('all')

const columns = [
    { key: 'invoice_number', label: 'No. Invoice' },
    { key: 'client', label: 'Client' },
    { key: 'total', label: 'Total' },
    { key: 'remaining_amount', label: 'Sisa' },
    { key: 'status', label: 'Status' },
    { key: 'actions', label: '' },
]

const statusValue = computed(() => statusFilter.value === 'all' ? '' : statusFilter.value)

const loadInvoices = (params = {}) => {
    invoiceStore.fetchInvoices(params).catch(() => {
        toast.error('Gagal memuat daftar invoice.')
    })
}

onMounted(() => loadInvoices())

const handleSearch = (query) => {
    searchTerm.value = query
    loadInvoices({ search: query, status: statusValue.value })
}

const handlePageChange = (page) => {
    loadInvoices({ page, search: searchTerm.value, status: statusValue.value })
}

const handleFilter = (value) => {
    statusFilter.value = value
    loadInvoices({ search: searchTerm.value, status: statusValue.value })
}

const confirmDelete = (invoice) => {
    invoiceToDelete.value = invoice
    showDeleteModal.value = true
}

const refreshInvoices = async (page) => {
    try {
        await invoiceStore.fetchInvoices({ page, search: searchTerm.value, status: statusValue.value })

        if (invoiceStore.invoices.length === 0 && page > 1) {
            await invoiceStore.fetchInvoices({ page: page - 1, search: searchTerm.value, status: statusValue.value })
        }
    } catch {
        toast.error('Daftar invoice gagal dimuat ulang.')
    }
}

const handleDelete = async () => {
    if (!invoiceToDelete.value) return

    const deletedInvoice = invoiceToDelete.value
    const currentPage = invoiceStore.pagination.current_page ?? 1

    deleting.value = true

    try {
        await invoiceStore.deleteInvoice(deletedInvoice.id)
        showDeleteModal.value = false
        invoiceToDelete.value = null

        toast.success('Invoice Dihapus', {
            description: `Invoice "${deletedInvoice.invoice_number}" telah dihapus.`,
        })

        await refreshInvoices(currentPage)
    } catch (err) {
        toast.error(err.response?.data?.message || 'Gagal menghapus invoice.')
    } finally {
        deleting.value = false
    }
}

const statusVariant = (status) => ({
    unpaid: 'danger',
    partial: 'warning',
    paid: 'success',
}[status] ?? 'secondary')

const statusLabel = (status) => ({
    unpaid: 'Belum Bayar',
    partial: 'Sebagian',
    paid: 'Lunas',
}[status] ?? status)
</script>
