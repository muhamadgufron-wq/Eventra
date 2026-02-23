<template>
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider"
          :class="variantClass">
        <slot />
    </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    variant: { type: String, default: 'primary' },
})

const variantClass = computed(() => ({
    primary: 'bg-primary-100 text-primary-700 border border-primary-200',
    success: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
    warning: 'bg-amber-100 text-amber-700 border border-amber-200',
    danger: 'bg-red-100 text-red-700 border border-red-200',
    info: 'bg-blue-100 text-blue-700 border border-blue-200',
    surface: 'bg-surface-100 text-surface-600 border border-surface-200',
}[props.variant] ?? 'bg-primary-100 text-primary-700 border border-primary-200'))
</script>
