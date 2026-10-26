<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">Koleksi Paket</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">Atur penawaran layanan terbaik untuk setiap momen</p>
            </div>
            <Button
                as-child
                class="h-12 gap-2 rounded-2xl px-6 text-sm font-bold shadow-xl shadow-primary-900/20">
                <RouterLink :to="{ name: 'packages.create' }">
                    <PackagePlusIcon class="size-5" />
                    Tambah Paket Eksklusif
                </RouterLink>
            </Button>
        </div>

        <DataTable
            :columns="columns"
            :data="packageStore.packages"
            :loading="packageStore.loading"
            :pagination="packageStore.pagination"
            searchable
            search-placeholder="Cari nama atau deskripsi paket..."
            @search="handleSearch"
            @page-change="handlePageChange">

            <template #cell-price="{ value }">
                {{ formatCurrency(value) }}
            </template>

            <template #cell-is_active="{ value }">
                <Badge :variant="value ? 'default' : 'secondary'">
                    {{ value ? 'Aktif' : 'Nonaktif' }}
                </Badge>
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center gap-2">
                    <Button
                        as-child
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:text-primary-600"
                        aria-label="Edit paket">
                        <RouterLink :to="{ name: 'packages.edit', params: { id: row.id } }">
                            <PencilIcon />
                        </RouterLink>
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:bg-destructive/10 hover:text-destructive"
                        aria-label="Hapus paket"
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
                    <AlertDialogTitle>Hapus Paket</AlertDialogTitle>
                    <AlertDialogDescription>
                        Apakah Anda yakin ingin menghapus paket
                        <strong>{{ packageToDelete?.name }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
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
import { Loader2Icon, PackagePlusIcon, PencilIcon, Trash2Icon } from '@lucide/vue'
import { toast } from 'vue-sonner'
import { usePackageStore } from '@/stores/package'
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
import { formatCurrency } from '@/utils/format'

const packageStore = usePackageStore()
const showDeleteModal = ref(false)
const packageToDelete = ref(null)
const deleting = ref(false)
const searchTerm = ref('')

const columns = [
    { key: 'name', label: 'Nama Paket' },
    { key: 'type', label: 'Jenis' },
    { key: 'price', label: 'Harga' },
    { key: 'is_active', label: 'Status' },
    { key: 'actions', label: '' },
]

const loadPackages = (params = {}) => {
    packageStore.fetchPackages(params).catch(() => {
        toast.error('Gagal memuat daftar paket.')
    })
}

onMounted(() => loadPackages())

const handleSearch = (query) => {
    searchTerm.value = query
    loadPackages({ search: query })
}

const handlePageChange = (page) => {
    loadPackages({ page, search: searchTerm.value })
}

const confirmDelete = (pkg) => {
    packageToDelete.value = pkg
    showDeleteModal.value = true
}

const refreshPackages = async (page) => {
    try {
        await packageStore.fetchPackages({ page, search: searchTerm.value })

        if (packageStore.packages.length === 0 && page > 1) {
            await packageStore.fetchPackages({ page: page - 1, search: searchTerm.value })
        }
    } catch {
        toast.error('Daftar paket gagal dimuat ulang.')
    }
}

const handleDelete = async () => {
    if (!packageToDelete.value) return

    const deletedPackage = packageToDelete.value
    const currentPage = packageStore.pagination.current_page ?? 1

    deleting.value = true

    try {
        await packageStore.deletePackage(deletedPackage.id)
        showDeleteModal.value = false
        packageToDelete.value = null

        toast.success('Paket Dihapus', {
            description: `Paket "${deletedPackage.name}" telah dihapus.`,
        })

        await refreshPackages(currentPage)
    } catch (err) {
        toast.error(err.response?.data?.message || 'Gagal menghapus paket.')
    } finally {
        deleting.value = false
    }
}
</script>
