<template>
    <div class="group bg-white rounded-[1.5rem] border border-surface-100 p-6 shadow-sm hover:shadow-xl hover:shadow-primary-900/5 transition-all duration-500 relative overflow-hidden">
        <div class="flex items-center gap-5 relative z-10">
            <!-- Icon Box -->
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:scale-105 shadow-sm"
                 :class="colorClasses.icon">
                <slot name="icon" />
            </div>

            <!-- Value & Label -->
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-bold text-surface-400 mb-0.5 tracking-tight group-hover:text-surface-600 transition-colors uppercase">{{ label }}</p>
                <h3 class="text-lg xl:text-xl font-bold text-surface-900 leading-none">
                    {{ formattedValue }}
                </h3>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatCurrency } from '@/utils/format'

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    format: { type: String, default: 'text' }, // text | currency | number
    color: { type: String, default: 'primary' },
})

const formattedValue = computed(() => {
    if (props.format === 'currency') return formatCurrency(props.value)
    if (props.format === 'number') return new Intl.NumberFormat('id-ID').format(props.value)
    return props.value
})

const colorClasses = computed(() => {
    const maps = {
        success: { icon: 'bg-[#ecfdf5] text-[#10b981]' },
        warning: { icon: 'bg-[#fff7ed] text-[#f97316]' },
        danger: { icon: 'bg-[#eff6ff] text-[#3b82f6]' }, // Blue in mockup for invoices
        accent: { icon: 'bg-[#f5f3ff] text-[#8b5cf6]' }, // Purple for total clients
        primary: { icon: 'bg-[#f0f9ff] text-[#0ea5e9]' },
    }
    return maps[props.color] || maps.primary
})
</script>
