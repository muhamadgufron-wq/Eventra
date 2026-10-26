<template>
    <aside
        class="fixed inset-y-0 left-0 z-40 transition-all duration-300 ease-out bg-white border-r border-surface-200 shadow-xl"
        :class="[
            collapsed ? 'w-64 lg:w-20' : 'w-64',
            mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        ]"
    >
        <!-- Content -->
        <div class="flex flex-col h-full">
            <!-- Logo Section -->
            <div
                class="relative h-20 flex items-center justify-center px-3 border-b border-surface-50"
            >
                <div
                    :class="[collapsed ? 'w-10 h-10' : 'w-auto h-10']"
                    class="flex items-center justify-center flex-shrink-0 transition-all duration-300"
                >
                    <img
                        src="/images/logo.png"
                        alt="Logo"
                        :class="[
                            collapsed
                                ? 'w-full h-full object-contain p-2'
                                : 'h-10 w-auto',
                        ]"
                        class="object-contain transition-transform duration-500 hover:scale-105"
                    />
                </div>

                <!-- Mobile close -->
                <button
                    type="button"
                    class="lg:hidden absolute right-3 top-1/2 -translate-y-1/2 p-2 rounded-xl text-surface-400 hover:text-surface-700 hover:bg-surface-100 transition-colors"
                    aria-label="Tutup menu"
                    @click="$emit('close')"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18 18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav
                class="flex-1 overflow-y-auto py-4 px-3 space-y-1.5 custom-scrollbar"
                @click="$emit('close')"
            >
                <ul class="space-y-1.5 focus:outline-none">
                    <SidebarLink
                        :to="{ name: 'dashboard' }"
                        icon="dashboard"
                        label="Dashboard"
                        :collapsed="collapsed"
                    />
                    <SidebarLink
                        :to="{ name: 'clients' }"
                        icon="clients"
                        label="Klien"
                        :collapsed="collapsed"
                    />
                    <SidebarLink
                        :to="{ name: 'packages' }"
                        icon="packages"
                        label="Paket"
                        :collapsed="collapsed"
                    />
                    <SidebarLink
                        :to="{ name: 'invoices' }"
                        icon="invoices"
                        label="Invoice"
                        :collapsed="collapsed"
                    />
                    <SidebarLink
                        :to="{ name: 'payments' }"
                        icon="payments"
                        label="Pembayaran"
                        :collapsed="collapsed"
                    />
                </ul>
            </nav>

            <!-- Footer -->
            <div
                class="px-4 py-4 border-t border-surface-50"
                :class="{ 'lg:px-2': collapsed }"
            >
                <p
                    class="text-[9px] font-bold uppercase tracking-[0.2em] text-surface-300 text-center whitespace-nowrap overflow-hidden"
                    :class="{ 'lg:hidden': collapsed }"
                >
                    Eventra &copy; 2026
                </p>
            </div>
        </div>
    </aside>
</template>

<script setup>
import SidebarLink from "@/components/layout/SidebarLink.vue";

defineProps({
    collapsed: { type: Boolean, default: false },
    mobileOpen: { type: Boolean, default: false },
});

defineEmits(["close"]);
</script>
