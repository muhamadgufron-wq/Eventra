<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">Daftar Pembayaran</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">Riwayat pembayaran dari klien</p>
            </div>
            <Button
                as-child
                class="h-12 gap-2 rounded-2xl px-6 text-sm font-bold shadow-xl shadow-primary-900/20">
                <RouterLink :to="{ name: 'payments.create' }">
                    <PlusIcon class="size-5" />
                    Catat Pembayaran
                </RouterLink>
            </Button>
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
                <RouterLink :to="{ name: 'invoices.detail', params: { id: row.invoice_id } }"
                            class="text-primary-600 hover:underline font-medium">
                    {{ row.invoice?.invoice_number ?? '-' }}
                </RouterLink>
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
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:text-primary-600"
                        aria-label="Cetak kwitansi"
                        :disabled="pdfLoading !== null"
                        @click="printReceipt(row)">
                        <Loader2Icon v-if="pdfLoading === row.id" class="size-4 animate-spin" />
                        <PrinterIcon v-else />
                    </Button>
                    <Button
                        as-child
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:text-primary-600"
                        aria-label="Edit pembayaran">
                        <RouterLink :to="{ name: 'payments.edit', params: { id: row.id } }">
                            <PencilIcon />
                        </RouterLink>
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:bg-destructive/10 hover:text-destructive"
                        aria-label="Hapus pembayaran"
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
                    <AlertDialogTitle>Hapus Pembayaran</AlertDialogTitle>
                    <AlertDialogDescription>
                        Apakah Anda yakin ingin menghapus catatan pembayaran sebesar
                        <strong>{{ formatCurrency(paymentToDelete?.amount || 0) }}</strong>
                        untuk invoice <strong>{{ paymentToDelete?.invoice?.invoice_number }}</strong>?
                        <span class="mt-2 block text-xs text-amber-600">
                            Menghapus pembayaran akan mengembalikan sisa tagihan invoice terkait secara otomatis.
                        </span>
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
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { Loader2Icon, PencilIcon, PlusIcon, PrinterIcon, Trash2Icon } from '@lucide/vue'
import { toast } from 'vue-sonner'
import { usePaymentStore } from '@/stores/payment'
import pdfService from '@/services/pdfService'
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
import { formatCurrency, formatDate } from '@/utils/format'
import { openPdfRequest, pdfErrorMessage } from '@/utils/pdf'

const paymentStore = usePaymentStore()
const searchTerm = ref('')
const showDeleteModal = ref(false)
const paymentToDelete = ref(null)
const deleting = ref(false)
const pdfLoading = ref(null)

const columns = [
    { key: 'invoice', label: 'Invoice' },
    { key: 'amount', label: 'Jumlah' },
    { key: 'payment_date', label: 'Tanggal' },
    { key: 'method', label: 'Metode' },
    { key: 'notes', label: 'Catatan' },
    { key: 'actions', label: 'Aksi', class: 'text-right' },
]

const loadPayments = (params = {}) => {
    paymentStore.fetchPayments(params).catch(() => {
        toast.error('Gagal memuat daftar pembayaran.')
    })
}

onMounted(() => loadPayments())

const handleSearch = (query) => {
    searchTerm.value = query
    loadPayments({ search: query })
}

const handlePageChange = (page) => {
    loadPayments({ page, search: searchTerm.value })
}

const confirmDelete = (payment) => {
    paymentToDelete.value = payment
    showDeleteModal.value = true
}

const printReceipt = async (payment) => {
    if (pdfLoading.value !== null) return

    pdfLoading.value = payment.id

    try {
        await openPdfRequest(
            () => pdfService.paymentReceipt(payment.id),
            `Kwitansi-${payment.invoice?.invoice_number ?? payment.id}-${payment.id}.pdf`,
        )
    } catch (err) {
        toast.error(await pdfErrorMessage(err, 'Gagal membuat kwitansi pembayaran.'))
    } finally {
        pdfLoading.value = null
    }
}

const refreshPayments = async (page) => {
    try {
        await paymentStore.fetchPayments({ page, search: searchTerm.value })

        if (paymentStore.payments.length === 0 && page > 1) {
            await paymentStore.fetchPayments({ page: page - 1, search: searchTerm.value })
        }
    } catch {
        toast.error('Daftar pembayaran gagal dimuat ulang.')
    }
}

const handleDelete = async () => {
    if (!paymentToDelete.value) return

    const deletedPayment = paymentToDelete.value
    const currentPage = paymentStore.pagination.current_page ?? 1

    deleting.value = true

    try {
        await paymentStore.deletePayment(deletedPayment.id)
        showDeleteModal.value = false
        paymentToDelete.value = null

        toast.success('Pembayaran Dihapus', {
            description: `${formatCurrency(deletedPayment.amount)} untuk invoice ${deletedPayment.invoice?.invoice_number ?? '-'} telah dihapus.`,
        })

        await refreshPayments(currentPage)
    } catch (err) {
        toast.error(err.response?.data?.message || 'Gagal menghapus pembayaran.')
    } finally {
        deleting.value = false
    }
}
</script>
