<template>
    <aside class="fixed inset-y-0 left-0 z-40 transition-all duration-300 ease-in-out bg-white border-r border-surface-100 shadow-sm"
           :class="[collapsed ? 'w-20' : 'w-64']">
        <div class="flex flex-col h-full">
            <div class="h-32 flex items-center justify-center px-1">
                <div :class="[collapsed ? 'w-10 h-10' : 'w-full h-28']" 
                     class="rounded-xl bg-white overflow-hidden flex items-center justify-center flex-shrink-0 transition-all duration-300">
                    <img src="/images/logo.png" alt="Logo" 
                         :class="[collapsed ? 'w-full h-full' : 'h-24 w-auto scale-110']"
                         class="object-contain">
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-2 px-4 space-y-1 custom-scrollbar">
                <ul class="space-y-1 focus:outline-none">
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
