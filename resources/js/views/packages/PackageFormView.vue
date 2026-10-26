<template>
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-surface-900 mb-1">
                {{ isEdit ? 'Edit Paket' : 'Buat Paket Baru' }}
            </h1>
            <p class="text-surface-500 text-sm">
                {{ isEdit
                    ? 'Perbarui informasi paket layanan pernikahan atau acara lainnya di bawah ini.'
                    : 'Lengkapi informasi paket layanan pernikahan atau acara lainnya di bawah ini.' }}
            </p>
        </div>

        <div v-if="loading" class="flex items-center justify-center gap-3 rounded-3xl border border-surface-100 bg-white p-12 text-sm font-medium text-surface-500 shadow-sm">
            <Loader2Icon class="size-5 animate-spin text-primary-600" />
            Memuat data paket...
        </div>

        <div v-else-if="loadError" class="rounded-3xl border border-destructive/20 bg-white p-8 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="rounded-2xl bg-destructive/10 p-3 text-destructive">
                    <AlertCircleIcon class="size-6" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-surface-900">Gagal memuat paket</h2>
                    <p class="mt-1 text-sm text-surface-500">{{ loadError }}</p>
                </div>
            </div>
            <Button as-child variant="outline" class="mt-6 h-11 rounded-2xl">
                <RouterLink :to="{ name: 'packages' }">Kembali ke daftar paket</RouterLink>
            </Button>
        </div>

        <form v-else @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white rounded-3xl border border-surface-100 p-6 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <Label for="package-name" class="text-sm font-bold text-surface-900">Nama Paket</Label>
                            <Input
                                id="package-name"
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Contoh: Paket Pernikahan Sapphire"
                                :aria-invalid="Boolean(fieldError('name'))"
                                :aria-describedby="fieldError('name') ? 'package-name-error' : undefined"
                                class="h-12 rounded-2xl px-4 text-sm font-medium" />
                            <p v-if="fieldError('name')" id="package-name-error" class="text-xs font-medium text-destructive">
                                {{ fieldError('name') }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="package-price" class="text-sm font-bold text-surface-900">Harga Paket</Label>
                            <CurrencyInput
                                id="package-price"
                                v-model="form.price"
                                required
                                :input-class="priceInputClass" />
                            <p v-if="fieldError('price')" class="text-xs font-medium text-destructive">
                                {{ fieldError('price') }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="package-type" class="text-sm font-bold text-surface-900">Jenis Paket</Label>
                                <Select v-model="form.type">
                                    <SelectTrigger
                                        id="package-type"
                                        class="w-full data-[size=default]:h-12"
                                        :aria-invalid="Boolean(fieldError('type'))">
                                        <SelectValue placeholder="Pilih jenis paket" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="type in packageTypes" :key="type" :value="type">
                                            {{ type }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="fieldError('type')" class="text-xs font-medium text-destructive">
                                    {{ fieldError('type') }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="package-status" class="text-sm font-bold text-surface-900">Status Paket</Label>
                                <Select v-model="packageStatus">
                                    <SelectTrigger
                                        id="package-status"
                                        class="w-full data-[size=default]:h-12"
                                        :aria-invalid="Boolean(fieldError('is_active'))">
                                        <SelectValue placeholder="Pilih status paket" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="active">Aktif</SelectItem>
                                        <SelectItem value="inactive">Non-Aktif</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="fieldError('is_active')" class="text-xs font-medium text-destructive">
                                    {{ fieldError('is_active') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-2">
                        <Label for="package-description" class="text-sm font-bold text-surface-900">Deskripsi Paket</Label>
                        <Textarea
                            id="package-description"
                            v-model="form.description"
                            rows="9"
                            placeholder="Jelaskan detail paket secara singkat..."
                            :aria-invalid="Boolean(fieldError('description'))"
                            class="min-h-52 resize-none rounded-2xl px-4 py-3 text-sm" />
                        <p v-if="fieldError('description')" class="text-xs font-medium text-destructive">
                            {{ fieldError('description') }}
                        </p>
                    </div>
                </div>

                <hr class="my-6 border-surface-50" />

                <!-- Services Section -->
                <div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                                <ClipboardListIcon class="size-5" />
                            </div>
                            <h2 class="text-lg font-bold text-surface-900">Daftar Layanan Paket</h2>
                        </div>
                        <Button
                            type="button"
                            variant="outline"
                            class="h-9 rounded-full border-primary-200 bg-primary-50 px-4 text-xs font-bold text-primary-700 hover:bg-primary-100"
                            @click="addService">
                            <PlusIcon class="size-4" />
                            Tambah Layanan
                        </Button>
                    </div>

                    <div class="overflow-x-auto border border-surface-50 rounded-2xl">
                        <table class="w-full min-w-[640px] text-left">
                            <thead class="bg-surface-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-bold text-surface-400 uppercase tracking-widest">Nama Layanan</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-surface-400 uppercase tracking-widest text-center w-24">Qty</th>
                                    <th class="px-6 py-4 text-[10px] font-bold text-surface-400 uppercase tracking-widest">Keterangan</th>
                                    <th class="px-6 py-4 text-center w-16"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-50">
                                <tr v-for="(service, index) in form.services" :key="index" class="group hover:bg-surface-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <Input
                                            v-model="service.name"
                                            type="text"
                                            placeholder="Contoh: Catering Buffet 500 Pax"
                                            aria-label="Nama layanan"
                                            :aria-invalid="Boolean(fieldError('services.' + index + '.name'))"
                                            class="h-10 rounded-xl px-3 text-sm font-medium" />
                                        <p v-if="fieldError('services.' + index + '.name')" class="mt-1 text-[11px] font-medium text-destructive">
                                            {{ fieldError('services.' + index + '.name') }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Input
                                            v-model="service.qty"
                                            type="text"
                                            inputmode="numeric"
                                            placeholder="1"
                                            aria-label="Jumlah layanan"
                                            :aria-invalid="Boolean(fieldError('services.' + index + '.qty'))"
                                            class="h-10 w-20 rounded-xl px-2 text-center text-sm font-bold" />
                                        <p v-if="fieldError('services.' + index + '.qty')" class="mt-1 text-[11px] font-medium text-destructive">
                                            {{ fieldError('services.' + index + '.qty') }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Input
                                            v-model="service.description"
                                            type="text"
                                            placeholder="Pilihan menu utama A/B/C"
                                            aria-label="Keterangan layanan"
                                            :aria-invalid="Boolean(fieldError('services.' + index + '.description'))"
                                            class="h-10 rounded-xl px-3 text-sm" />
                                        <p v-if="fieldError('services.' + index + '.description')" class="mt-1 text-[11px] font-medium text-destructive">
                                            {{ fieldError('services.' + index + '.description') }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-surface-400 hover:bg-destructive/10 hover:text-destructive"
                                            aria-label="Hapus layanan"
                                            @click="removeService(index)">
                                            <Trash2Icon />
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="form.services.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-surface-400 italic">
                                        Belum ada layanan yang ditambahkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-if="fieldError('services')" class="mt-2 text-xs font-medium text-destructive">
                        {{ fieldError('services') }}
                    </p>
                </div>

                <hr class="my-6 border-surface-50" />

                <!-- Footer Section (Unified inside card) -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 text-surface-500">
                        <div class="rounded-full border border-surface-200 p-1.5 text-surface-400">
                            <InfoIcon class="size-3.5" />
                        </div>
                        <span class="text-xs font-medium">Pastikan semua data sudah terisi dengan benar.</span>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <Button as-child variant="outline" class="h-12 flex-1 rounded-2xl px-8 text-sm font-bold sm:flex-none">
                            <RouterLink :to="{ name: 'packages' }">Batal</RouterLink>
                        </Button>
                        <Button
                            type="submit"
                            :disabled="submitting"
                            class="h-12 flex-1 rounded-2xl px-8 text-sm font-bold shadow-lg shadow-primary-900/20 sm:flex-none">
                            <Loader2Icon v-if="submitting" class="size-4 animate-spin" />
                            <SaveIcon v-else class="size-4" />
                            {{ submitting ? 'Memproses...' : isEdit ? 'Perbarui Paket' : 'Simpan Paket' }}
                        </Button>
                    </div>
                </div>

                <!-- Error Alert (Inside card) -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-2"
                    enter-to-class="opacity-100 translate-y-0">
                    <div v-if="error" role="alert" class="mt-4 flex items-start gap-3 rounded-2xl border border-destructive/20 bg-destructive/10 p-4 text-sm font-medium text-destructive">
                        <AlertCircleIcon class="mt-0.5 size-5 shrink-0" />
                        <span>{{ error }}</span>
                    </div>
                </Transition>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import {
    AlertCircleIcon,
    ClipboardListIcon,
    InfoIcon,
    Loader2Icon,
    PlusIcon,
    SaveIcon,
    Trash2Icon,
} from '@lucide/vue'
import { toast } from 'vue-sonner'
import { usePackageStore } from '@/stores/package'
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

const route = useRoute()
const router = useRouter()
const packageStore = usePackageStore()

const submitting = ref(false)
const loading = ref(false)
const loadError = ref('')
const error = ref('')
const errors = ref({})

const packageTypes = ['Wedding', 'Engagement', 'Corporate', 'Other']

const isEdit = computed(() => route.name === 'packages.edit')

const form = reactive({
    name: '',
    type: 'Wedding',
    description: '',
    price: 0,
    is_active: true,
    services: [],
})

const packageStatus = computed({
    get: () => (form.is_active ? 'active' : 'inactive'),
    set: (value) => {
        form.is_active = value === 'active'
    },
})

const fieldError = (key) => errors.value[key]?.[0] ?? ''

const priceInputClass = computed(() => [
    'h-12 w-full rounded-2xl border border-input bg-transparent px-4 pl-12 text-sm font-bold shadow-xs transition-colors',
    'focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50',
    fieldError('price') ? 'border-destructive focus-visible:ring-destructive/20' : '',
].join(' '))

const normalizeService = (service) => {
    if (typeof service === 'string') {
        return { name: service, qty: '1', description: '' }
    }

    return {
        name: service?.name ?? '',
        qty: service?.qty ?? '',
        description: service?.description ?? '',
    }
}

onMounted(async () => {
    if (!isEdit.value) {
        addService()
        return
    }

    loading.value = true

    try {
        const { data } = await packageService.getById(route.params.id)
        const pkg = data.data

        Object.assign(form, {
            name: pkg.name,
            type: pkg.type ?? 'Wedding',
            description: pkg.description ?? '',
            price: Number(pkg.price),
            is_active: Boolean(pkg.is_active),
            services: (pkg.services ?? []).map(normalizeService),
        })
    } catch (err) {
        loadError.value = err.response?.data?.message || 'Gagal mengambil data paket.'
        toast.error(loadError.value)
    } finally {
        loading.value = false
    }
})

const addService = () => {
    form.services.push({
        name: '',
        qty: '1',
        description: '',
    })
}

const removeService = (index) => {
    form.services.splice(index, 1)
}

const handleSubmit = async () => {
    submitting.value = true
    error.value = ''
    errors.value = {}

    const payload = {
        ...form,
        name: form.name.trim(),
        description: form.description.trim(),
        services: form.services
            .filter((service) => (service.name ?? '').trim() !== '')
            .map((service) => ({
                name: service.name.trim(),
                qty: String(service.qty ?? '').trim(),
                description: (service.description ?? '').trim(),
            })),
    }

    try {
        if (isEdit.value) {
            await packageStore.updatePackage(route.params.id, payload)
        } else {
            await packageStore.createPackage(payload)
        }

        toast.success(isEdit.value ? 'Paket Diperbarui' : 'Paket Ditambahkan', {
            description: isEdit.value
                ? 'Perubahan paket berhasil disimpan.'
                : 'Paket baru berhasil ditambahkan.',
        })

        router.push({ name: 'packages' })
    } catch (err) {
        const data = err.response?.data

        if (data?.errors) {
            errors.value = data.errors
            toast.error('Periksa kembali data paket.')
        } else {
            error.value = data?.message || 'Gagal menyimpan data.'
            toast.error(error.value)
        }
    } finally {
        submitting.value = false
    }
}
</script>
