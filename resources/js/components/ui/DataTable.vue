<template>
    <div class="space-y-6">
        <!-- Table Header / Search -->
        <div v-if="searchable" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative max-w-sm w-full group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within:text-primary-600 text-surface-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input
                    type="text"
                    v-model="internalSearch"
                    :placeholder="searchPlaceholder"
                    class="block w-full pl-12 pr-4 py-3 bg-white border border-surface-100 rounded-2xl text-sm placeholder-surface-400 focus:outline-none focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all shadow-sm"
                />
            </div>
            <div class="flex items-center gap-3">
                <slot name="actions" />
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-[2rem] border border-surface-100 shadow-sm overflow-hidden transition-all hover:shadow-md">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-50/50 border-b border-surface-50">
                            <th v-for="col in columns" :key="col.key"
                                class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.15em] text-surface-500 whitespace-nowrap">
                                {{ col.label }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-50">
                        <tr v-if="loading" class="animate-pulse">
                            <td :colspan="columns.length" class="px-6 py-12 text-center text-surface-400 font-medium">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-primary-500 animate-bounce"></div>
                                    <div class="w-2 h-2 rounded-full bg-primary-500 animate-bounce [animation-delay:0.2s]"></div>
                                    <div class="w-2 h-2 rounded-full bg-primary-500 animate-bounce [animation-delay:0.4s]"></div>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="data.length === 0">
                            <td :colspan="columns.length" class="px-6 py-16 text-center text-surface-400 italic font-medium bg-surface-50/20">
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                        <tr v-for="row in data" :key="row.id" class="hover:bg-primary-50/30 transition-colors group">
                            <td v-for="col in columns" :key="col.key" class="px-6 py-5">
                                <slot :name="`cell-${col.key}`" :value="row[col.key]" :row="row">
                                    <span class="text-sm font-medium text-surface-700">{{ row[col.key] }}</span>
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="px-6 py-5 border-t border-surface-50 bg-surface-50/30 flex items-center justify-between">
                <p class="text-xs font-bold text-surface-500 uppercase tracking-widest">
                    Halaman <span class="text-primary-700">{{ pagination.current_page }}</span> dari {{ pagination.last_page }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        @click="$emit('page-change', pagination.current_page - 1)"
                        :disabled="pagination.current_page === 1"
                        class="p-2.5 rounded-xl border border-surface-200 bg-white text-surface-600 hover:bg-surface-50 disabled:opacity-30 disabled:hover:bg-white transition-all active:scale-95"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button
                        @click="$emit('page-change', pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="p-2.5 rounded-xl border border-surface-200 bg-white text-surface-600 hover:bg-surface-50 disabled:opacity-30 disabled:hover:bg-white transition-all active:scale-95"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    columns: { type: Array, required: true },
    data: { type: Array, required: true },
    loading: { type: Boolean, default: false },
    pagination: {
        type: Object,
        default: () => ({ current_page: 1, last_page: 1 })
    },
    searchable: { type: Boolean, default: false },
    searchPlaceholder: { type: String, default: 'Cari data...' }
})

const emit = defineEmits(['search', 'page-change'])

const internalSearch = ref('')
let debounceTimeout = null

watch(internalSearch, (newVal) => {
    if (debounceTimeout) clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
        emit('search', newVal)
    }, 500)
})
</script>
