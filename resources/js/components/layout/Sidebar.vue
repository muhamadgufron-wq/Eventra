<template>
    <aside class="fixed inset-y-0 left-0 z-30 transition-all duration-500 ease-out bg-white border-r border-surface-200 shadow-xl"
           :class="[collapsed ? 'w-20' : 'w-64']">
        <!-- Content -->
        <div class="flex flex-col h-full">
            <!-- Logo Section -->
            <div class="h-20 flex items-center justify-center px-3">
                <div :class="[collapsed ? 'w-10 h-10' : 'w-auto h-10']"
                     class="flex items-center justify-center flex-shrink-0 transition-all duration-500">
                    <img src="/images/logo.png" alt="Logo"
                         :class="[collapsed ? 'w-full h-full object-contain p-2' : 'h-10 w-auto']"
                         class="object-contain transition-transform duration-500 hover:scale-105">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1.5 custom-scrollbar">
                <ul class="space-y-1.5 focus:outline-none">
                    <SidebarLink :to="{ name: 'dashboard' }" icon="dashboard" label="Dashboard" :collapsed="collapsed" />
                    <SidebarLink :to="{ name: 'clients' }" icon="clients" label="Clients" :collapsed="collapsed" />
                    <SidebarLink :to="{ name: 'packages' }" icon="packages" label="Packages" :collapsed="collapsed" />
                    <SidebarLink :to="{ name: 'invoices' }" icon="invoices" label="Invoices" :collapsed="collapsed" />
                    <SidebarLink :to="{ name: 'payments' }" icon="payments" label="Payments" :collapsed="collapsed" />
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from 'vue'
import SidebarLink from '@/components/layout/SidebarLink.vue'
import { useAuthStore } from '@/stores/auth'

defineProps({
    collapsed: { type: Boolean, default: false },
})

defineEmits(['toggle'])

const authStore = useAuthStore()

const userInitials = computed(() => {
    if (!authStore.user?.name) return '?'
    return authStore.user.name
        .split(' ')
        .map(n => n[0])
        .join('')
        .toUpperCase()
        .substring(0, 2)
})
</script>
