<template>
    <header class="h-20 flex items-center justify-between px-8 bg-transparent transition-all duration-300">
        <!-- Left: Page Title & Search -->
        <div class="flex items-center gap-10">
            <h1 class="text-xl font-bold text-surface-900 tracking-tight">Overview</h1>
            
            <!-- Search Bar -->
            <div class="hidden md:flex items-center w-72 h-11 bg-white border border-surface-100 rounded-2xl px-4 shadow-sm group focus-within:ring-4 focus-within:ring-primary-500/10 focus-within:border-primary-500 transition-all">
                <svg class="w-4.5 h-4.5 text-surface-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" placeholder="Search data..." 
                       class="ml-3 bg-transparent border-none focus:ring-0 text-sm font-medium text-surface-700 placeholder-surface-400 w-full" />
            </div>
        </div>

        <!-- Right: Actions & User -->
        <div class="flex items-center gap-6">
            <!-- Icons -->
            <div class="flex items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-surface-100 text-surface-400 hover:text-surface-600 hover:bg-surface-50 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </button>
                <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-surface-100 text-surface-400 hover:text-surface-600 hover:bg-surface-50 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.127c-.332.183-.582.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>

            <!-- Profile Widget -->
            <div class="h-12 w-[1px] bg-surface-100 hidden sm:block"></div>
            
            <div class="relative" ref="dropdownRef">
                <button @click="showUserMenu = !showUserMenu"
                        class="flex items-center gap-4 group">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-surface-900 leading-none mb-1 group-hover:text-primary-600 transition-colors">{{ authStore.user?.name }}</p>
                        <p class="text-[11px] font-semibold text-surface-400 capitalize tracking-wide">{{ authStore.user?.role }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-[#f0e4d7] flex items-center justify-center text-primary-950 font-black text-sm shadow-sm ring-2 ring-white group-hover:shadow-md transition-all uppercase">
                        {{ userInitials }}
                    </div>
                </button>

                    <!-- Menu -->
                    <transition enter-active-class="transition ease-out duration-100"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                        <div v-if="showUserMenu"
                             class="absolute right-0 mt-3 w-56 rounded-2xl bg-white shadow-2xl border border-surface-100 py-2 ring-1 ring-black/5">
                            <div class="px-4 py-3 border-b border-surface-50">
                                <p class="text-sm font-bold text-surface-900">{{ authStore.user?.name }}</p>
                                <p class="text-xs text-surface-500 truncate">{{ authStore.user?.email }}</p>
                            </div>
                            <button @click="handleLogout"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors mt-1">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Log out
                            </button>
                        </div>
                    </transition>
                </div>
            </div>
    </header>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Breadcrumbs from '@/components/ui/Breadcrumbs.vue'

defineEmits(['toggleSidebar'])

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()
const showUserMenu = ref(false)
const dropdownRef = ref(null)

const pageTitle = computed(() => {
    const titles = {
        dashboard: 'Dashboard',
        clients: 'Clients',
        'clients.create': 'Tambah Client',
        'clients.edit': 'Edit Client',
        packages: 'Paket',
        'packages.create': 'Tambah Paket',
        'packages.edit': 'Edit Paket',
        invoices: 'Invoice',
        'invoices.create': 'Buat Invoice',
        'invoices.detail': 'Detail Invoice',
        payments: 'Pembayaran',
    }
    return titles[route.name] ?? 'WO Manager'
})

const userInitials = computed(() => {
    const name = authStore.user?.name
    if (!name) return '?'
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
})

const handleLogout = async () => {
    await authStore.logout()
    router.push({ name: 'login' })
}

// Close dropdown on outside click
const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        showUserMenu.value = false
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>
