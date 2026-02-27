<template>
    <div class="max-w-6xl mx-auto px-4 py-4">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-surface-900 mb-1">Buat Paket Baru</h1>
            <p class="text-surface-500 text-sm">Lengkapi informasi paket layanan pernikahan atau acara lainnya di bawah ini.</p>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-6">
            <!-- Main Info Card -->
            <div class="bg-white rounded-3xl border border-surface-100 p-6 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-surface-900">Nama Paket</label>
                            <input v-model="form.name" type="text" required placeholder="Contoh: Paket Pernikahan Sapphire"
                                   class="w-full px-5 py-3 border border-surface-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium placeholder:text-surface-300" />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-surface-900">Harga Paket</label>
                            <CurrencyInput v-model="form.price" required />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-surface-900">Jenis Paket</label>
                                <select v-model="form.type" class="w-full px-5 py-3 border border-surface-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20aria-hidden%3D%22true%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2010%206%22%3E%3Cpath%20stroke%3D%22%236B7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%222%22%20d%3D%22m1%201%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1.25rem_center] bg-no-repeat">
                                    <option value="Wedding">Wedding</option>
                                    <option value="Engagement">Engagement</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-surface-900">Status Paket</label>
                                <select v-model="form.is_active" class="w-full px-5 py-3 border border-surface-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium appearance-none bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20aria-hidden%3D%22true%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2010%206%22%3E%3Cpath%20stroke%3D%22%236B7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%222%22%20d%3D%22m1%201%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1.25rem_center] bg-no-repeat">
                                    <option :value="true">Aktif</option>
                                    <option :value="false">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-surface-900">Deskripsi Paket</label>
                        <textarea v-model="form.description" rows="9" placeholder="Jelaskan detail paket secara singkat..."
                                  class="w-full px-5 py-3 border border-surface-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium resize-none placeholder:text-surface-300"></textarea>
                    </div>
                </div>

                <hr class="my-6 border-surface-50" />

                <!-- Services Section -->
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-2">
                            <div class="p-2 bg-emerald-50 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-surface-900">Daftar Layanan Paket</h2>
                        </div>
                        <button type="button" @click="addService"
                                class="flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold hover:bg-emerald-100 transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Layanan
                        </button>
                    </div>

                    <div class="overflow-hidden border border-surface-50 rounded-2xl">
                        <table class="w-full text-left">
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
                                        <input v-model="service.name" type="text" placeholder="Contoh: Catering Buffet 500 Pax"
                                               class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium text-surface-900 p-0 placeholder:text-surface-300" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <input v-model="service.qty" type="text" placeholder="1"
                                               class="w-full bg-surface-50 border-none focus:ring-0 text-sm font-bold text-surface-900 py-1.5 rounded-lg text-center" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <input v-model="service.description" type="text" placeholder="Pilihan menu utama A/B/C"
                                               class="w-full bg-transparent border-none focus:ring-0 text-sm font-medium text-surface-500 p-0 placeholder:text-surface-300 italic" />
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button" @click="removeService(index)"
                                                class="text-surface-400 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Empty state or placeholder -->
                                <tr v-if="form.services.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-surface-400 italic">
                                        Belum ada layanan yang ditambahkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr class="my-6 border-surface-50" />

                <!-- Footer Section (Unified inside card) -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 text-surface-500">
                        <div class="p-1 px-3 border border-surface-200 rounded-full text-xs font-bold font-sans italic">i</div>
                        <span class="text-xs font-medium">Pastikan semua data sudah terisi dengan benar.</span>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <router-link :to="{ name: 'packages' }"
                                     class="flex-1 sm:flex-none px-8 py-3 bg-white border border-surface-200 text-surface-700 rounded-2xl text-sm font-bold hover:bg-surface-50 transition-all active:scale-95 text-center shadow-sm">
                            Batal
                        </router-link>
                        <button type="submit" :disabled="submitting"
                                class="flex-1 sm:flex-none px-8 py-3 bg-emerald-800 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-950/20 hover:bg-emerald-700 transition-all active:scale-95 disabled:opacity-50">
                            <div class="flex items-center justify-center gap-2">
                                <svg v-if="!submitting" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ submitting ? 'Memproses...' : 'Simpan Paket' }}</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Error Alert (Inside card) -->
                <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="error" class="mt-4 p-4 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-3 text-red-600 font-bold text-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ error }}
                    </div>
                </transition>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePackageStore } from '@/stores/package'
import packageService from '@/services/packageService'
import CurrencyInput from '@/components/ui/CurrencyInput.vue'

const route = useRoute()
const router = useRouter()
const packageStore = usePackageStore()
const submitting = ref(false)
const error = ref('')

const isEdit = computed(() => route.name === 'packages.edit')

const form = reactive({
    name: '',
    type: 'Wedding',
    description: '',
    price: 0,
    is_active: true,
    services: [],
})

onMounted(async () => {
    if (isEdit.value) {
        try {
            const { data } = await packageService.getById(route.params.id)
            const pkg = data.data
            Object.assign(form, {
                name: pkg.name,
                type: pkg.type ?? 'Wedding',
                description: pkg.description ?? '',
                price: Number(pkg.price),
                is_active: pkg.is_active,
                services: pkg.services ?? [],
            })
        } catch (err) {
            error.value = 'Gagal mengambil data paket.'
        }
    } else {
        // Initial empty service for new package
        addService()
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
    
    // Clean services list (remove empty rows if needed, or just send as is)
    const payload = {
        ...form,
        services: form.services.filter(s => s.name.trim() !== '')
    }

    try {
        if (isEdit.value) {
            await packageStore.updatePackage(route.params.id, payload)
        } else {
            await packageStore.createPackage(payload)
        }
        router.push({ name: 'packages' })
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal menyimpan data.'
    } finally {
        submitting.value = false
    }
}
</script>
