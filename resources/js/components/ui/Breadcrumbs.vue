<template>
  <nav v-if="breadcrumbs.length > 0" class="flex items-center space-x-2 text-[11px] font-bold uppercase tracking-[0.1em] mb-1">
    <div v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
      <!-- Separator -->
      <svg v-if="index > 0" class="w-3 h-3 text-surface-300 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
      </svg>

      <!-- Link / Text -->
      <router-link
        v-if="index < breadcrumbs.length - 1"
        :to="crumb.path"
        class="text-surface-400 hover:text-primary-600 transition-colors"
      >
        {{ crumb.label }}
      </router-link>
      <span v-else class="text-accent-500">
        {{ crumb.label }}
      </span>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const breadcrumbs = computed(() => {
  const crumbs = []
  
  // Start with root/Beranda if not on dashboard
  if (route.name !== 'dashboard') {
     crumbs.push({ label: 'Beranda', path: '/' })
  }

  // Get matched routes that have breadcrumb meta
  const matched = route.matched.filter(m => m.meta && m.meta.breadcrumb)
  
  matched.forEach((m, idx) => {
    // Avoid double "Beranda" or "Dashboard" if already added
    const label = m.meta.breadcrumb
    if (label === 'Beranda' || (label === 'Dashboard' && route.name === 'dashboard')) {
        if (!crumbs.find(c => c.label === label)) {
            crumbs.push({ label, path: m.path || '/' })
        }
        return
    }

    crumbs.push({
      label,
      path: m.path
    })
  })

  // Deduplicate and filter out empty paths/labels if any
  return crumbs.filter((c, i, self) => 
    c.label && self.findIndex(t => t.label === c.label) === i
  )
})
</script>
