<template>
    <div class="stat-card group relative overflow-hidden rounded-2xl p-6 transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl cursor-default"
         :class="cardClasses">
        <!-- Glassmorphism Background Layer -->
        <div class="absolute inset-0 rounded-2xl bg-white/60 backdrop-blur-xl border border-white/40 shadow-lg"></div>

        <!-- Gradient Accent Strip (top) -->
        <div class="absolute top-0 left-0 right-0 h-1 rounded-t-2xl opacity-80 group-hover:opacity-100 transition-opacity duration-500"
             :class="colorClasses.strip"></div>

        <!-- Decorative Gradient Orb -->
        <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full blur-3xl opacity-20 group-hover:opacity-35 transition-all duration-700 group-hover:scale-110"
             :class="colorClasses.orb"></div>
        <div class="absolute -bottom-8 -left-8 w-24 h-24 rounded-full blur-2xl opacity-10 group-hover:opacity-20 transition-all duration-700"
             :class="colorClasses.orb"></div>

        <!-- Content -->
        <div class="relative z-10 flex items-center gap-5">
            <!-- Icon Box with Gradient -->
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-lg"
                 :class="colorClasses.icon">
                <slot name="icon" />
            </div>

            <!-- Value & Label -->
            <div class="flex-1 min-w-0">
                <p class="text-[11px] font-bold text-surface-400 mb-1 tracking-tight group-hover:text-surface-600 transition-colors uppercase">{{ label }}</p>
                <h3 class="text-lg xl:text-xl font-extrabold text-surface-900 leading-none tracking-tight">
                    {{ formattedValue }}
                </h3>
            </div>
        </div>

        <!-- Subtle Shine Effect on Hover -->
        <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"
             style="background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.4) 45%, rgba(255,255,255,0.1) 50%, transparent 55%);">
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { formatCurrency } from '@/utils/format'

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    format: { type: String, default: 'text' },
    color: { type: String, default: 'primary' },
})

const formattedValue = computed(() => {
    if (props.format === 'currency') return formatCurrency(props.value)
    if (props.format === 'number') return new Intl.NumberFormat('id-ID').format(props.value)
    return props.value
})

const cardClasses = computed(() => {
    const maps = {
        success: 'hover:shadow-emerald-500/10',
        warning: 'hover:shadow-amber-500/10',
        danger:  'hover:shadow-blue-500/10',
        accent:  'hover:shadow-violet-500/10',
        primary: 'hover:shadow-sky-500/10',
    }
    return maps[props.color] || maps.primary
})

const colorClasses = computed(() => {
    const maps = {
        success: {
            icon: 'bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-emerald-500/30',
            strip: 'bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500',
            orb: 'bg-emerald-400',
        },
        warning: {
            icon: 'bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-amber-500/30',
            strip: 'bg-gradient-to-r from-amber-400 via-orange-400 to-orange-500',
            orb: 'bg-amber-400',
        },
        danger: {
            icon: 'bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-blue-500/30',
            strip: 'bg-gradient-to-r from-blue-400 via-blue-500 to-indigo-500',
            orb: 'bg-blue-400',
        },
        accent: {
            icon: 'bg-gradient-to-br from-violet-400 to-purple-600 text-white shadow-violet-500/30',
            strip: 'bg-gradient-to-r from-violet-400 via-purple-500 to-fuchsia-500',
            orb: 'bg-violet-400',
        },
        primary: {
            icon: 'bg-gradient-to-br from-sky-400 to-cyan-600 text-white shadow-sky-500/30',
            strip: 'bg-gradient-to-r from-sky-400 via-cyan-500 to-teal-500',
            orb: 'bg-sky-400',
        },
    }
    return maps[props.color] || maps.primary
})
</script>

<style scoped>
.stat-card {
    /* Extra glass shine border effect */
    background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
}
</style>
