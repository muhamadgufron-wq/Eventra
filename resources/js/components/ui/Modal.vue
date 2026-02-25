<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-primary-950/40 backdrop-blur-sm" @click="$emit('close')" />

                <!-- Modal content -->
                <div class="relative bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] w-full transition-all border border-surface-100 overflow-hidden"
                     :class="sizeClass">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-6 sm:p-8 border-b border-surface-50 bg-surface-50/30">
                        <slot name="title">
                            <h3 class="text-2xl font-serif font-bold text-surface-900">{{ title }}</h3>
                        </slot>
                        <button @click="$emit('close')"
                                class="p-2 rounded-xl hover:bg-white text-surface-400 hover:text-red-500 transition-all active:scale-95 border border-transparent hover:border-surface-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 sm:p-8">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div v-if="$slots.footer" class="flex items-center justify-end gap-3 p-6 sm:p-8 border-t border-surface-50 bg-surface-50/20">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    size: { type: String, default: 'md' },
})

defineEmits(['close'])

const sizeClass = computed(() => ({
    sm: 'max-w-sm',
    md: 'max-w-lg',
    lg: 'max-w-2xl',
    xl: 'max-w-4xl',
}[props.size] ?? 'max-w-lg'))
</script>

<style scoped>
.modal-enter-active, .modal-leave-active {
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
}
</style>
