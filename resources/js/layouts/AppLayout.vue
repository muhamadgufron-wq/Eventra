<template>
    <!-- App Container -->
    <div class="min-h-screen bg-[#f8fafc] text-surface-800 font-sans selection:bg-primary-100 selection:text-primary-700">
        <!-- Sidebar -->
        <Sidebar :collapsed="sidebarCollapsed" />

        <!-- Main Content -->
        <div class="transition-all duration-300 ease-in-out lg:pl-64"
             :class="{ 'lg:pl-20': sidebarCollapsed }">
            <Topbar :collapsed="sidebarCollapsed" @toggle-sidebar="sidebarCollapsed = !sidebarCollapsed" />

            <main class="p-4 sm:p-6 lg:p-8">
                <router-view v-slot="{ Component }">
                    <transition name="page" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '@/components/layout/Sidebar.vue'
import Topbar from '@/components/layout/Topbar.vue'

const sidebarCollapsed = ref(false)
</script>
