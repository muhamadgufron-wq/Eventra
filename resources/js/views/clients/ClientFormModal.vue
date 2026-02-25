<template>
    <Modal :show="show" size="xl" @close="$emit('close')">
        <template #title>
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>
                </div>
                <span class="text-xl font-bold">{{ isEdit ? 'Ubah Client' : 'Klien Baru' }}</span>
            </div>
        </template>
        <form id="clientForm" @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Bride info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Nama Pengantin Wanita</label>
                    <input v-model="form.bride_name" type="text" required placeholder="Masukan nama pengantin wanita"
                           class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Nama Pengantin Pria</label>
                    <input v-model="form.groom_name" type="text" required placeholder="Masukan nama pengantin pria"
                           class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium" />
                </div>
            </div>

            <!-- Phones -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">No. Handphone Wanita</label>
                    <input v-model="form.bride_phone" type="text" placeholder="08..."
                           class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">No. Handphone Pria</label>
                    <input v-model="form.groom_phone" type="text" placeholder="08..."
                           class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium" />
                </div>
            </div>

            <!-- Address -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Alamat Acara</label>
                <input v-model="form.event_location" type="text" placeholder="Masukan alamat acara"
                       class="w-full px-4 py-3 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium" />
            </div>

            <!-- Date & Package -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Tanggal Acara</label>
                    <input v-model="form.event_date" type="date"
                           class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium" />
                </div>
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Paket Pernikahan</label>
                    <select v-model="form.package_id"
                            class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium appearance-none">
                        <option value="">Pilih Paket</option>
                        <option v-for="pkg in packageStore.packages" :key="pkg.id" :value="pkg.id">
                            {{ pkg.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-1.5">
                <label class="block text-[10px] font-black uppercase tracking-widest text-surface-500 ml-1">Catatan</label>
                <textarea v-model="form.notes" rows="3" placeholder="Masukan catatan"
                          class="w-full px-4 py-2 bg-surface-50 border border-surface-100 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm font-medium resize-none"></textarea>
            </div>

            <div v-if="error" class="p-3 bg-red-50 border border-red-100 rounded-xl text-xs text-red-600 font-bold uppercase tracking-wide text-center">
                {{ error }}
            </div>
        </form>

        <template #footer>
            <div class="flex items-center justify-end gap-3 w-full">
                <button type="button" @click="$emit('close')"
                        class="px-6 py-2.5 text-sm font-bold text-surface-600 hover:text-surface-900 transition-colors">
                    Batal
                </button>
                <button type="submit" form="clientForm" :disabled="submitting"
                        class="px-8 py-2.5 bg-[#065f46] text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-900/20 hover:bg-emerald-900 transition-all active:scale-95 disabled:opacity-50">
                    {{ submitting ? 'Memproses...' : 'Simpan' }}
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import { useClientStore } from '@/stores/client'
import { usePackageStore } from '@/stores/package'
import Modal from '@/components/ui/Modal.vue'

const props = defineProps({
    show: Boolean,
    client: Object,
})

const emit = defineEmits(['close', 'saved'])

const clientStore = useClientStore()
const packageStore = usePackageStore()
const submitting = ref(false)
const error = ref('')

const isEdit = ref(false)

const form = reactive({
    bride_name: '',
    groom_name: '',
    bride_phone: '',
    groom_phone: '',
    email: '',
    event_date: '',
    event_location: '',
    package_id: '',
    notes: '',
})

onMounted(() => {
    packageStore.fetchPackages()
})

watch(() => props.show, (newShow) => {
    if (newShow) {
        error.value = ''
        if (props.client) {
            isEdit.value = true
            Object.assign(form, {
                bride_name: props.client.bride_name || '',
                groom_name: props.client.groom_name || '',
                bride_phone: props.client.bride_phone || '',
                groom_phone: props.client.groom_phone || '',
                email: props.client.email || '',
                event_date: props.client.event_date || '',
                event_location: props.client.event_location || '',
                package_id: props.client.package_id || '',
                notes: props.client.notes || '',
            })
        } else {
            isEdit.value = false
            Object.assign(form, {
                bride_name: '',
                groom_name: '',
                bride_phone: '',
                groom_phone: '',
                email: '',
                event_date: '',
                event_location: '',
                package_id: '',
                notes: '',
            })
        }
    }
})

const handleSubmit = async () => {
    submitting.value = true
    error.value = ''
    try {
        if (isEdit.value) {
            await clientStore.updateClient(props.client.id, form)
        } else {
            await clientStore.createClient(form)
        }
        emit('saved')
        emit('close')
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal menyimpan data.'
    } finally {
        submitting.value = false
    }
}
</script>
