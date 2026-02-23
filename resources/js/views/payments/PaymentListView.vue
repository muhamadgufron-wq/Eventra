<template>
    <div>
        <DataTable
            :columns="columns"
            :data="paymentStore.payments"
            :loading="paymentStore.loading"
            :pagination="paymentStore.pagination"
            searchable
            search-placeholder="Cari pembayaran..."
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
        </DataTable>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePaymentStore } from '@/stores/payment'
import DataTable from '@/components/ui/DataTable.vue'
import Badge from '@/components/ui/Badge.vue'
import { formatCurrency, formatDate } from '@/utils/format'

const paymentStore = usePaymentStore()
const searchTerm = ref('')

const columns = [
    { key: 'invoice', label: 'Invoice' },
    { key: 'amount', label: 'Jumlah' },
    { key: 'payment_date', label: 'Tanggal' },
    { key: 'method', label: 'Metode' },
    { key: 'notes', label: 'Catatan' },
]

onMounted(() => paymentStore.fetchPayments())

const handleSearch = (query) => {
    searchTerm.value = query
    paymentStore.fetchPayments({ search: query })
}

const handlePageChange = (page) => {
    paymentStore.fetchPayments({ page, search: searchTerm.value })
}
</script>
