<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h1 class="text-3xl font-serif font-bold text-surface-900">Database Client</h1>
                <p class="text-xs font-bold text-surface-400 uppercase tracking-widest mt-1">Kelola informasi mempelai dan detail acara</p>
            </div>
            <Button
                class="h-12 gap-2 rounded-2xl px-6 text-sm font-bold shadow-xl shadow-primary-900/20"
                @click="openCreateModal">
                <UserPlusIcon class="size-5" />
                Registrasi Client Baru
            </Button>
        </div>

        <DataTable
            :columns="columns"
            :data="clientStore.clients"
            :loading="clientStore.loading"
            :pagination="clientStore.pagination"
            searchable
            search-placeholder="Cari nama client atau lokasi..."
            @search="handleSearch"
            @page-change="handlePageChange">

            <template #cell-event_date="{ value }">
                {{ value ? formatDate(value) : '-' }}
            </template>

            <template #cell-actions="{ row }">
                <div class="flex items-center gap-2">
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:text-primary-600"
                        aria-label="Edit client"
                        @click="openEditModal(row)">
                        <PencilIcon />
                    </Button>
                    <Button
                        variant="ghost"
                        size="icon-sm"
                        class="text-surface-500 hover:bg-destructive/10 hover:text-destructive"
                        aria-label="Hapus client"
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
                    <AlertDialogTitle>Hapus Client</AlertDialogTitle>
                    <AlertDialogDescription>
                        Apakah Anda yakin ingin menghapus client
                        <strong>{{ clientToDelete?.bride_name }} & {{ clientToDelete?.groom_name }}</strong>?
                        Tindakan ini tidak dapat dibatalkan.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Batal</AlertDialogCancel>
                    <AlertDialogAction variant="destructive" @click="handleDelete">
                        Hapus
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>

        <!-- Client Form Modal -->
        <ClientFormModal
            :show="showFormModal"
            :client="selectedClient"
            @close="showFormModal = false"
            @saved="onClientSaved" />
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { PencilIcon, Trash2Icon, UserPlusIcon } from '@lucide/vue'
import { toast } from 'vue-sonner'
import { useClientStore } from '@/stores/client'
import DataTable from '@/components/ui/DataTable.vue'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import ClientFormModal from '@/components/clients/ClientFormModal.vue'

const clientStore = useClientStore()
const showDeleteModal = ref(false)
const showFormModal = ref(false)
const clientToDelete = ref(null)
const selectedClient = ref(null)
const searchTerm = ref('')

const columns = [
    { key: 'bride_name', label: 'Mempelai Wanita' },
    { key: 'groom_name', label: 'Mempelai Pria' },
    { key: 'bride_phone', label: 'Telepon (W)' },
    { key: 'event_date', label: 'Tanggal Acara' },
    { key: 'package_name', label: 'Paket' },
    { key: 'actions', label: '' },
]

onMounted(() => clientStore.fetchClients())

const handleSearch = (query) => {
    searchTerm.value = query
    clientStore.fetchClients({ search: query })
}

const handlePageChange = (page) => {
    clientStore.fetchClients({ page, search: searchTerm.value })
}

const confirmDelete = (client) => {
    clientToDelete.value = client
    showDeleteModal.value = true
}

const handleDelete = async () => {
    try {
        await clientStore.deleteClient(clientToDelete.value.id)
        showDeleteModal.value = false
        clientToDelete.value = null
    } catch (err) {
        toast.error(err.response?.data?.message || 'Gagal menghapus client.')
    }
}

const openCreateModal = () => {
    selectedClient.value = null
    showFormModal.value = true
}

const openEditModal = (client) => {
    selectedClient.value = client
    showFormModal.value = true
}

const onClientSaved = () => {
    const isEditing = selectedClient.value !== null

    toast.success('Berhasil Disimpan', {
        description: isEditing
            ? 'Data klien telah berhasil diperbarui ke dalam sistem secara otomatis.'
            : 'Data klien baru telah berhasil ditambahkan ke dalam sistem secara otomatis.',
    })

    clientStore.fetchClients({ search: searchTerm.value })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}
</script>
