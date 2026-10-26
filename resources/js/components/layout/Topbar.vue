<template>
    <header class="sticky top-0 z-20 flex min-h-20 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8 bg-white border-b border-surface-200 shadow-sm">
        <!-- Left: Menu toggle + Page title -->
        <div class="flex items-center gap-3 min-w-0">
            <button type="button"
                    @click="$emit('toggleSidebar')"
                    class="p-2.5 rounded-xl border border-surface-100 text-surface-500 hover:text-surface-800 hover:bg-surface-50 transition-all active:scale-95 flex-shrink-0"
                    aria-label="Toggle menu">
                <!-- Mobile: hamburger -->
                <svg class="w-5 h-5 lg:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <!-- Desktop: collapse -->
                <svg class="hidden lg:block w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m-4.5-15h15a1.5 1.5 0 0 1 1.5 1.5v12a1.5 1.5 0 0 1-1.5 1.5h-15a1.5 1.5 0 0 1-1.5-1.5v-12a1.5 1.5 0 0 1 1.5-1.5Z" />
                </svg>
            </button>

            <div class="min-w-0">
                <div class="hidden md:block">
                    <Breadcrumbs />
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-surface-900 tracking-tight truncate">{{ pageTitle }}</h1>
            </div>
        </div>

        <!-- Right: User Profile -->
        <div class="flex items-center flex-shrink-0">
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
import Breadcrumbs from '@/components/ui/Breadcrumbs.vue'

defineEmits(['toggleSidebar'])

const authStore = useAuthStore()
const route = useRoute()
const router = useRouter()
const showUserMenu = ref(false)
const dropdownRef = ref(null)

const pageTitle = computed(() => route.meta.breadcrumb ?? 'WO Manager')

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
