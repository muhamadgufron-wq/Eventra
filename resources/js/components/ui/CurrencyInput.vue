<template>
    <div class="relative">
        <div v-if="prefix" 
             class="absolute inset-y-0 left-0 flex items-center pointer-events-none"
             :class="prefixPadding">
            <span class="text-surface-400 font-bold" :class="prefixSize">{{ prefix }}</span>
        </div>
        <input
            type="text"
            :value="displayValue"
            @input="handleInput"
            :placeholder="placeholder"
            :required="required"
            :class="[
                'transition-all focus:outline-none',
                inputClass,
                prefix ? prefixInputPadding : 'px-4'
            ]"
        />
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: { type: [Number, String], default: 0 },
    prefix: { type: String, default: 'Rp' },
    placeholder: { type: String, default: '0' },
    required: { type: Boolean, default: false },
    inputClass: { 
        type: String, 
        default: 'w-full px-4 py-2 border border-surface-200 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 text-sm font-bold' 
    },
    prefixSize: { type: String, default: 'text-xs' },
    prefixPadding: { type: String, default: 'pl-4' },
    prefixInputPadding: { type: String, default: 'pl-10 pr-4' }
})

const emit = defineEmits(['update:modelValue'])

const displayValue = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') return ''
    
    // Convert to number and round to remove any decimals
    const num = Math.round(parseFloat(props.modelValue))
    if (isNaN(num)) return ''
    
    // Format with dots as thousand separators
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')
})

const handleInput = (e) => {
    let rawValue = e.target.value.replace(/\./g, '') // Remove dots
    
    // Only allow numbers
    if (rawValue !== '' && !/^\d*$/.test(rawValue)) {
        e.target.value = displayValue.value
        return
    }

    const numericValue = rawValue === '' ? 0 : parseInt(rawValue, 10)
    emit('update:modelValue', numericValue)
}
</script>
