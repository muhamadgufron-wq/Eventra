<template>
    <header class="relative z-50 h-20 flex items-center justify-between px-6 sm:px-8 bg-white border-b border-surface-200 shadow-sm">
        <!-- Left: Page Title -->
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-surface-900 tracking-tight">{{ pageTitle }}</h1>
        </div>

        <!-- Right: User Profile -->
        <div class="flex items-center">
            <div class="relative" ref="dropdownRef">
                <button @click="showUserMenu = !showUserMenu"
                        class="flex items-center gap-4 group transition-all duration-300 hover:scale-[1.02]">
                    <div class="text-right hidden sm:block">
                        <p class="text-base font-semibold text-surface-800 leading-none mb-1">{{ authStore.user?.name }}</p>
                        <p class="text-sm text-surface-500 capitalize">{{ authStore.user?.role }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 flex items-center justify-center text-white text-sm font-bold shadow-md ring-2 ring-white group-hover:shadow-lg transition-all duration-300 uppercase">
                        {{ userInitials }}
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <transition enter-active-class="transition ease-out duration-200"
                            enter-from-class="transform opacity-0 scale-95 translate-y-[-10px]"
                            enter-to-class="transform opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition ease-in duration-150"
                            leave-from-class="transform opacity-100 scale-100 translate-y-0"
                            leave-to-class="transform opacity-0 scale-95 translate-y-[-10px]">
                    <div v-if="showUserMenu"
                         class="absolute right-0 mt-3 w-64 rounded-2xl bg-white shadow-xl border border-surface-200 py-2 z-50">
                        <div class="px-5 py-4 border-b border-surface-100">
                            <p class="text-base font-bold text-surface-900 leading-tight">{{ authStore.user?.name }}</p>
                            <p class="text-sm text-surface-400 mt-1 truncate">{{ authStore.user?.email }}</p>
                        </div>
                        <button @click="handleLogout"
                                class="w-full flex items-center gap-3 px-5 py-3 text-base text-red-600 hover:bg-red-50 transition-all duration-200 font-semibold group">
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
        packages: 'Packages',
        'packages.create': 'Tambah Paket',
        'packages.edit': 'Edit Paket',
        invoices: 'Invoices',
        'invoices.create': 'Buat Invoice',
        'invoices.detail': 'Detail Invoice',
        payments: 'Payments',
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
