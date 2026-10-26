<template>
  <nav v-if="breadcrumbs.length > 0" class="flex items-center space-x-1 text-[10px] font-bold uppercase tracking-[0.15em] mb-1" aria-label="Breadcrumb">
    <template v-for="(crumb, index) in breadcrumbs" :key="crumb.label + index">
      <!-- Separator -->
      <svg v-if="index > 0" class="w-3 h-3 text-surface-300 mx-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
      </svg>

      <!-- Link / Text -->
      <router-link
        v-if="index < breadcrumbs.length - 1"
        :to="crumb.to"
        class="text-surface-400 hover:text-primary-600 transition-colors whitespace-nowrap"
      >
        {{ crumb.label }}
      </router-link>
      <span v-else class="text-accent-600 whitespace-nowrap" aria-current="page">
        {{ crumb.label }}
      </span>
    </template>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const breadcrumbs = computed(() => {
  // The dashboard is the root page — a breadcrumb would be redundant.
  if (route.name === 'dashboard') return []

  const current = [...route.matched].reverse().find(record => record.meta?.breadcrumb)
  if (!current) return []

  const trail = []
  const seen = new Set()

  const push = (record, params = {}) => {
    const key = record.name ?? record.path
    if (!record.meta?.breadcrumb || seen.has(key)) return

    seen.add(key)
    trail.unshift({
      label: record.meta.breadcrumb,
      to: record.name ? { name: record.name, params } : '/',
    })
  }

  // Current page
  push(current, route.params)

  // Walk up optional parent chain (e.g. Invoice → Edit Invoice)
  let parentName = current.meta?.parent
  while (parentName) {
    const parent = router.getRoutes().find(record => record.name === parentName)
    if (!parent) break

    push(parent)
    parentName = parent.meta?.parent
  }

  trail.unshift({ label: 'Beranda', to: '/' })
  return trail
})
</script>
