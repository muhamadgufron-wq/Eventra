<template>
    <div class="max-w-screen-lg mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-surface-50">
                <div class="w-10 h-10 rounded-xl bg-accent-600 text-white flex items-center justify-center shadow-lg shadow-accent-600/10">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-surface-900 leading-tight">
                        {{ isEdit ? 'Detail Paket Layanan' : 'Rancang Paket Baru' }}
                    </h3>
                    <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mt-0.5">Konfigurasi produk dan harga layanan</p>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-surface-500 ml-1">Nama Paket Layanan</label>
                    <input v-model="form.name" type="text" required placeholder="Contoh: Premium Wedding"
                           class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-surface-500 ml-1">Deskripsi & Keunggulan</label>
                    <textarea v-model="form.description" rows="3" placeholder="Apa saja yang didapatkan di paket ini?"
                              class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-bold text-surface-500 ml-1">Harga Layanan</label>
                        <CurrencyInput v-model="form.price" required />
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input v-model="form.is_active" type="checkbox" class="sr-only peer" />
                                <div class="w-10 h-5 bg-surface-100 border border-surface-200 rounded-full peer peer-checked:bg-emerald-500 transition-all after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:shadow-sm after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                            </div>
                            <span class="text-[11px] font-bold text-surface-500 uppercase tracking-widest group-hover:text-surface-700 transition-colors">Paket Aktif</span>
                        </label>
                    </div>
                </div>

                <!-- Error -->
                <div v-if="error" class="p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600 font-bold uppercase tracking-wide text-center">
                    {{ error }}
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-50">
                    <router-link :to="{ name: 'packages' }"
                                 class="px-6 py-2.5 bg-surface-100 text-surface-700 rounded-xl text-sm font-bold hover:bg-surface-200 transition-all active:scale-95 text-center">
                        Batal
                    </router-link>
                    <button type="submit" :disabled="submitting"
                            class="px-8 py-2.5 bg-primary-800 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-950/20 hover:bg-primary-700 transition-all active:scale-95 disabled:opacity-50">
                        {{ submitting ? 'Memproses...' : (isEdit ? 'Update Paket' : 'Simpan Paket') }}
                    </button>
                </div>
            </form>
        </div>
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
    description: '',
    price: 0,
    is_active: true,
})

onMounted(async () => {
    if (isEdit.value) {
        try {
            const { data } = await packageService.getById(route.params.id)
            const pkg = data.data
            Object.assign(form, {
                name: pkg.name,
                description: pkg.description ?? '',
                price: Number(pkg.price),
                is_active: pkg.is_active,
            })
        } catch (err) {
            error.value = 'Gagal mengambil data paket.'
        }
    }
})

const handleSubmit = async () => {
    submitting.value = true
    error.value = ''
    try {
        if (isEdit.value) {
            await packageStore.updatePackage(route.params.id, form)
        } else {
            await packageStore.createPackage(form)
        }
        router.push({ name: 'packages' })
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal menyimpan data.'
    } finally {
        submitting.value = false
    }
}
</script>
