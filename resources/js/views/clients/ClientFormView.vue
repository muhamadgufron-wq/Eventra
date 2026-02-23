<template>
    <div class="max-w-screen-xl mx-auto">
        <div class="bg-white rounded-2xl border border-surface-100 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-8 pb-6 border-b border-surface-50">
                <div class="w-10 h-10 rounded-xl bg-primary-900 text-white flex items-center justify-center shadow-lg shadow-primary-900/10">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-surface-900 leading-tight">
                        {{ isEdit ? 'Detail Informasi Client' : 'Registrasi Client Baru' }}
                    </h3>
                    <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mt-0.5">Kelola informasi data klien dan acara</p>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-6">
                <!-- Section: Profil Mempelai -->
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-700 flex items-center gap-2">
                        <span class="w-1 h-3 bg-primary-600 rounded-full"></span>
                        Profil Mempelai
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-surface-500 ml-1">Nama Mempelai Wanita</label>
                            <input v-model="form.bride_name" type="text" required placeholder="Nama Lengkap"
                                   class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-surface-500 ml-1">Nama Mempelai Pria</label>
                            <input v-model="form.groom_name" type="text" required placeholder="Nama Lengkap"
                                   class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                        </div>
                    </div>
                </div>

                <!-- Section: Kontak -->
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-700 flex items-center gap-2">
                        <span class="w-1 h-3 bg-primary-600 rounded-full"></span>
                        Informasi Kontak
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-surface-500 ml-1">WhatsApp / Telepon</label>
                            <input v-model="form.phone" type="text" placeholder="08..."
                                   class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-surface-500 ml-1">Alamat Email</label>
                            <input v-model="form.email" type="email" placeholder="email@client.com"
                                   class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                        </div>
                    </div>
                </div>

                <!-- Section: Detail Acara -->
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary-700 flex items-center gap-2">
                        <span class="w-1 h-3 bg-primary-600 rounded-full"></span>
                        Detail Perhelatan
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-surface-500 ml-1">Tanggal Acara</label>
                            <input v-model="form.event_date" type="date"
                                   class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-surface-500 ml-1">Lokasi</label>
                            <input v-model="form.event_location" type="text" placeholder="Gedung / Hotel / Rumah"
                                   class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium" />
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-surface-500 ml-1">Catatan Tambahan</label>
                    <textarea v-model="form.notes" rows="3" placeholder="Informasi tambahan lainnya..."
                               class="w-full px-4 py-2 border border-surface-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-sm font-medium resize-none"></textarea>
                </div>

                <!-- Error -->
                <div v-if="error" class="p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600 font-bold uppercase tracking-wide text-center">
                    {{ error }}
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-surface-50">
                    <router-link :to="{ name: 'clients' }"
                                 class="px-6 py-2.5 bg-surface-100 text-surface-700 rounded-xl text-sm font-bold hover:bg-surface-200 transition-all active:scale-95">
                        Batal
                    </router-link>
                    <button type="submit" :disabled="submitting"
                            class="px-8 py-2.5 bg-primary-800 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-950/20 hover:bg-primary-700 transition-all active:scale-95 disabled:opacity-50">
                        {{ submitting ? 'Memproses...' : (isEdit ? 'Simpan Perubahan' : 'Simpan Client') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useClientStore } from '@/stores/client'

const route = useRoute()
const router = useRouter()
const clientStore = useClientStore()
const submitting = ref(false)
const error = ref('')

const isEdit = computed(() => route.name === 'clients.edit')

const form = reactive({
    bride_name: '',
    groom_name: '',
    phone: '',
    email: '',
    event_date: '',
    event_location: '',
    notes: '',
})

onMounted(async () => {
    if (isEdit.value) {
        await clientStore.fetchClient(route.params.id)
        const c = clientStore.currentClient
        if (c) {
            Object.assign(form, {
                bride_name: c.bride_name,
                groom_name: c.groom_name,
                phone: c.phone ?? '',
                email: c.email ?? '',
                event_date: c.event_date ?? '',
                event_location: c.event_location ?? '',
                notes: c.notes ?? '',
            })
        }
    }
})

const handleSubmit = async () => {
    submitting.value = true
    error.value = ''
    try {
        if (isEdit.value) {
            await clientStore.updateClient(route.params.id, form)
        } else {
            await clientStore.createClient(form)
        }
        router.push({ name: 'clients' })
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal menyimpan data.'
    } finally {
        submitting.value = false
    }
}
</script>
