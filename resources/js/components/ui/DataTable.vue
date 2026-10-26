<template>
    <div class="space-y-6">
        <!-- Table Header / Search -->
        <div v-if="searchable" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative max-w-sm w-full group">
                <SearchIcon
                    class="absolute left-4 top-1/2 size-5 -translate-y-1/2 text-surface-400 transition-colors pointer-events-none group-focus-within:text-primary-600" />
                <Input
                    v-model="internalSearch"
                    type="text"
                    :placeholder="searchPlaceholder"
                    :aria-label="searchPlaceholder"
                    class="h-12 rounded-2xl border-surface-100 bg-white pl-12 pr-4 text-sm shadow-sm" />
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
                            <th v-for="header in headers" :key="header.id" scope="col"
                                class="px-6 py-5 text-[11px] font-bold uppercase tracking-[0.15em] text-surface-500 whitespace-nowrap">
                                {{ header.column.columnDef.header }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-50">
                        <tr v-if="loading" class="animate-pulse">
                            <td :colspan="columnCount" class="px-6 py-12 text-center text-surface-400 font-medium">
                                <div class="flex items-center justify-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-primary-500 animate-bounce"></div>
                                    <div class="w-2 h-2 rounded-full bg-primary-500 animate-bounce [animation-delay:0.2s]"></div>
                                    <div class="w-2 h-2 rounded-full bg-primary-500 animate-bounce [animation-delay:0.4s]"></div>
                                </div>
                            </td>
                        </tr>
                        <tr v-else-if="rows.length === 0">
                            <td :colspan="columnCount" class="px-6 py-16 text-center text-surface-400 italic font-medium bg-surface-50/20">
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                        <tr v-for="row in rows" :key="row.id" class="hover:bg-primary-50/30 transition-colors group">
                            <td v-for="cell in row.getAllCells()" :key="cell.id" class="px-6 py-5">
                                <slot :name="`cell-${cell.column.id}`" :value="cell.getValue()" :row="row.original">
                                    <span class="text-sm font-medium text-surface-700">{{ cell.getValue() }}</span>
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="pageCount > 1" class="px-6 py-5 border-t border-surface-50 bg-surface-50/30 flex items-center justify-between">
                <p class="text-xs font-bold text-surface-500 uppercase tracking-widest">
                    Halaman <span class="text-primary-700">{{ currentPage }}</span> dari {{ pageCount }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        @click="table.previousPage()"
                        :disabled="!canPreviousPage"
                        aria-label="Halaman sebelumnya"
                        class="p-2.5 rounded-xl border border-surface-200 bg-white text-surface-600 hover:bg-surface-50 disabled:opacity-30 disabled:hover:bg-white transition-all active:scale-95"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>
                    <button
                        @click="table.nextPage()"
                        :disabled="!canNextPage"
                        aria-label="Halaman berikutnya"
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
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { SearchIcon } from '@lucide/vue'
import { rowPaginationFeature, tableFeatures, useTable } from '@tanstack/vue-table'
import { Input } from '@/components/ui/input'

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

const features = tableFeatures({ rowPaginationFeature })

const tableColumns = computed(() =>
    props.columns.map((column) => ({
        id: column.key,
        accessorKey: column.key,
        header: column.label,
    }))
)

const tableData = computed(() => props.data)

const paginationState = computed(() => ({
    pageIndex: Math.max((props.pagination.current_page ?? 1) - 1, 0),
    pageSize: props.pagination.per_page ?? 15,
}))

const controlledState = computed(() => ({ pagination: paginationState.value }))

const table = useTable({
    features,
    columns: tableColumns,
    data: tableData,
    getRowId: (row, index) => String(row.id ?? index),
    manualPagination: true,
    autoResetPageIndex: false,
    pageCount: computed(() => Math.max(props.pagination.last_page ?? 1, 1)),
    state: controlledState,
    onPaginationChange: (updater) => {
        const next = typeof updater === 'function' ? updater(paginationState.value) : updater
        emit('page-change', next.pageIndex + 1)
    },
})

const headers = computed(() => table.getHeaderGroups()[0]?.headers ?? [])
const rows = computed(() => table.getRowModel().rows)
const columnCount = computed(() => props.columns.length)
const currentPage = computed(() => table.atoms.pagination.get().pageIndex + 1)
const pageCount = computed(() => table.getPageCount())
const canPreviousPage = computed(() => table.getCanPreviousPage())
const canNextPage = computed(() => table.getCanNextPage())

const internalSearch = ref('')
let debounceTimeout = null

watch(internalSearch, (newVal) => {
    if (debounceTimeout) clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
        emit('search', newVal)
    }, 500)
})

onBeforeUnmount(() => {
    if (debounceTimeout) clearTimeout(debounceTimeout)
})
</script>
