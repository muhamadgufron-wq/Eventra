<template>
    <Teleport to="body">
        <Transition name="success-modal">
            <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-primary-950/30 backdrop-blur-sm" @click="$emit('close')" />

                <!-- Modal -->
                <div class="relative bg-white rounded-3xl shadow-[0_25px_60px_rgba(0,0,0,0.15)] w-full max-w-sm overflow-hidden border border-surface-100">
                    <!-- Content -->
                    <div class="flex flex-col items-center text-center px-8 pt-10 pb-8 gap-4">
                        <!-- Animated Check Icon -->
                        <div class="relative">
                            <div class="w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center ring-8 ring-emerald-50/50">
                                <svg class="w-8 h-8 text-emerald-500 success-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 13l4 4L19 7" class="check-path" />
                                </svg>
                            </div>
                            <!-- Pulse ring -->
                            <div class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping-slow" />
                        </div>

                        <!-- Title -->
                        <h3 class="text-xl font-bold text-surface-900 tracking-tight">{{ title }}</h3>

                        <!-- Message -->
                        <p class="text-sm text-surface-400 leading-relaxed max-w-[260px]">{{ message }}</p>
                    </div>

                    <!-- Button -->
                    <div class="px-8 pb-8">
                        <button @click="$emit('close')"
                                class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-sm font-bold shadow-lg shadow-emerald-600/25 transition-all active:scale-[0.97] focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: 'Berhasil Disimpan' },
    message: { type: String, default: 'Data telah berhasil disimpan ke dalam sistem.' },
})

defineEmits(['close'])
</script>

<style scoped>
.success-modal-enter-active,
.success-modal-leave-active {
    transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.success-modal-enter-from,
.success-modal-leave-to {
    opacity: 0;
    transform: scale(0.85) translateY(20px);
}

/* Checkmark draw animation */
.check-path {
    stroke-dasharray: 24;
    stroke-dashoffset: 24;
    animation: draw-check 0.5s ease-out 0.3s forwards;
}

@keyframes draw-check {
    to {
        stroke-dashoffset: 0;
    }
}

/* Slow ping for the glow ring */
@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 0.4;
    }
    100% {
        transform: scale(1.6);
        opacity: 0;
    }
}

.animate-ping-slow {
    animation: ping-slow 1.5s cubic-bezier(0, 0, 0.2, 1) 0.2s 1 forwards;
}
</style>
