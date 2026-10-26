<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[90vh] overflow-y-auto sm:max-w-4xl">
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2 text-xl font-bold">
                    <span class="rounded-lg bg-primary-50 p-1.5 text-primary-600">
                        <UserPlusIcon class="size-5" />
                    </span>
                    {{ isEdit ? 'Ubah Client' : 'Klien Baru' }}
                </DialogTitle>
                <DialogDescription class="sr-only">
                    Form untuk {{ isEdit ? 'mengubah' : 'menambahkan' }} data client.
                </DialogDescription>
            </DialogHeader>

            <form id="clientForm" class="space-y-4" @submit.prevent="handleSubmit">
                <!-- Bride info -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label for="bride_name" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                            Nama Pengantin Wanita
                        </Label>
                        <Input
                            id="bride_name"
                            v-model="form.bride_name"
                            type="text"
                            required
                            placeholder="Masukan nama pengantin wanita"
                            class="h-10" />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="groom_name" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                            Nama Pengantin Pria
                        </Label>
                        <Input
                            id="groom_name"
                            v-model="form.groom_name"
                            type="text"
                            required
                            placeholder="Masukan nama pengantin pria"
                            class="h-10" />
                    </div>
                </div>

                <!-- Phones -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label for="bride_phone" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                            No. Handphone Wanita
                        </Label>
                        <Input
                            id="bride_phone"
                            v-model="form.bride_phone"
                            type="tel"
                            placeholder="08..."
                            class="h-10" />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="groom_phone" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                            No. Handphone Pria
                        </Label>
                        <Input
                            id="groom_phone"
                            v-model="form.groom_phone"
                            type="tel"
                            placeholder="08..."
                            class="h-10" />
                    </div>
                </div>

                <!-- Address -->
                <div class="space-y-1.5">
                    <Label for="event_location" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                        Alamat Acara
                    </Label>
                    <Input
                        id="event_location"
                        v-model="form.event_location"
                        type="text"
                        placeholder="Masukan alamat acara"
                        class="h-10" />
                </div>

                <!-- Date & Package -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="space-y-1.5">
                        <Label for="event_date" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                            Tanggal Acara
                        </Label>
                        <Input
                            id="event_date"
                            v-model="form.event_date"
                            type="date"
                            class="h-10" />
                    </div>
                    <div class="space-y-1.5">
                        <Label for="package_id" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                            Paket Pernikahan
                        </Label>
                        <Select v-model="form.package_id">
                            <SelectTrigger id="package_id" class="w-full data-[size=default]:h-10">
                                <SelectValue placeholder="Pilih Paket" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="pkg in packageStore.packages"
                                    :key="pkg.id"
                                    :value="pkg.id">
                                    {{ pkg.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-1.5">
                    <Label for="notes" class="text-[10px] font-black uppercase tracking-widest text-surface-500">
                        Catatan
                    </Label>
                    <Textarea
                        id="notes"
                        v-model="form.notes"
                        rows="3"
                        placeholder="Masukan catatan"
                        class="resize-none" />
                </div>

                <div
                    v-if="error"
                    class="rounded-lg border border-destructive/20 bg-destructive/10 p-3 text-center text-xs font-bold uppercase tracking-wide text-destructive">
                    {{ error }}
                </div>
            </form>

            <DialogFooter>
                <Button
                    type="button"
                    variant="ghost"
                    size="lg"
                    class="h-10 px-4"
                    @click="$emit('close')">
                    Batal
                </Button>
                <Button
                    type="submit"
                    form="clientForm"
                    size="lg"
                    class="h-10 px-6"
                    :disabled="submitting">
                    <Loader2Icon v-if="submitting" class="size-4 animate-spin" />
                    {{ submitting ? 'Memproses...' : 'Simpan' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import { Loader2Icon, UserPlusIcon } from '@lucide/vue'
import { useClientStore } from '@/stores/client'
import { usePackageStore } from '@/stores/package'
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
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

const props = defineProps({
    show: Boolean,
    client: Object,
})

const emit = defineEmits(['close', 'saved'])

const clientStore = useClientStore()
const packageStore = usePackageStore()
const submitting = ref(false)
const error = ref('')

const open = computed({
    get: () => props.show,
    set: (value) => {
        if (!value) emit('close')
    },
})

const isEdit = computed(() => Boolean(props.client))

const blankForm = {
    bride_name: '',
    groom_name: '',
    bride_phone: '',
    groom_phone: '',
    event_date: '',
    event_location: '',
    package_id: '',
    notes: '',
}

const form = reactive({ ...blankForm })

const fillForm = (client) => {
    Object.assign(form, blankForm)

    if (!client) return

    for (const key of Object.keys(blankForm)) {
        form[key] = client[key] ?? ''
    }
}

onMounted(() => packageStore.fetchPackages())

watch(
    () => props.show,
    (visible) => {
        if (!visible) return
        error.value = ''
        fillForm(props.client)
    },
    { immediate: true },
)

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
