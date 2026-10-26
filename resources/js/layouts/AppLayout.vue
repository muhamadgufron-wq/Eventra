<template>
    <!-- App Container -->
    <div class="min-h-screen bg-[#f8fafc] text-surface-800 font-sans selection:bg-primary-100 selection:text-primary-700">
        <!-- Sidebar -->
        <Sidebar
            :collapsed="sidebarCollapsed"
            :mobile-open="mobileSidebarOpen"
            @close="mobileSidebarOpen = false" />

        <!-- Mobile backdrop -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-200"
            leave-to-class="opacity-0">
            <div
                v-if="mobileSidebarOpen"
                class="fixed inset-0 z-30 bg-primary-900/40 backdrop-blur-sm lg:hidden"
                @click="mobileSidebarOpen = false" />
        </Transition>

        <!-- Main Content -->
        <div class="transition-all duration-300 ease-in-out lg:pl-64"
             :class="{ 'lg:pl-20': sidebarCollapsed }">
            <Topbar :collapsed="sidebarCollapsed" @toggle-sidebar="handleToggleSidebar" />

            <main class="p-4 sm:p-6 lg:p-8">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from '@/components/layout/Sidebar.vue'
import Topbar from '@/components/layout/Topbar.vue'

const route = useRoute()
const mediaQuery = window.matchMedia('(min-width: 1024px)')

const isDesktop = ref(mediaQuery.matches)
const sidebarCollapsed = ref(false)
const mobileSidebarOpen = ref(false)

const handleToggleSidebar = () => {
    if (isDesktop.value) {
        sidebarCollapsed.value = !sidebarCollapsed.value
    } else {
        mobileSidebarOpen.value = !mobileSidebarOpen.value
    }
}

const syncViewport = (event) => {
    isDesktop.value = event.matches
    if (event.matches) mobileSidebarOpen.value = false
}

// Close the mobile drawer after navigating
watch(() => route.fullPath, () => {
    mobileSidebarOpen.value = false
})

onMounted(() => mediaQuery.addEventListener('change', syncViewport))
onUnmounted(() => mediaQuery.removeEventListener('change', syncViewport))
</script>
