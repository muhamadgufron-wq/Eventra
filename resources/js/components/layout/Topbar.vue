<template>
    <header class="relative z-40 h-16 flex items-center justify-between px-6 bg-white/80 backdrop-blur-xl border-b border-surface-100/80">
        <!-- Left: Page Title -->
        <div class="flex items-center">
            <h1 class="text-lg font-bold text-surface-900 tracking-tight">{{ pageTitle }}</h1>
        </div>

        <!-- Right: User Profile -->
        <div class="flex items-center">
            <div class="relative" ref="dropdownRef">
                <button @click="showUserMenu = !showUserMenu"
                        class="flex items-center gap-3 group">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-surface-800 leading-none mb-0.5">{{ authStore.user?.name }}</p>
                        <p class="text-[11px] text-surface-400 capitalize">{{ authStore.user?.role }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-700 to-primary-900 flex items-center justify-center text-white text-xs font-bold shadow-sm ring-2 ring-white group-hover:shadow-md transition-all uppercase">
                        {{ userInitials }}
                    </div>
                </button>

                <!-- Dropdown Menu -->
                <transition enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95">
                    <div v-if="showUserMenu"
                         class="absolute right-0 mt-2 w-56 rounded-xl bg-white shadow-xl border border-surface-100 py-1.5 z-50">
                        <div class="px-4 py-3 border-b border-surface-50">
                            <p class="text-sm font-bold text-surface-900 leading-tight">{{ authStore.user?.name }}</p>
                            <p class="text-xs text-surface-400 mt-0.5 truncate">{{ authStore.user?.email }}</p>
                        </div>
                        <button @click="handleLogout"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors mt-0.5 font-medium">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
