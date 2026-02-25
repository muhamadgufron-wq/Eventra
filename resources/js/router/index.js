import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import AppLayout from '@/layouts/AppLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'

const routes = [
    {
        path: '/login',
        component: AuthLayout,
        children: [
            {
                path: '',
                name: 'login',
                component: () => import('@/views/auth/LoginView.vue'),
                meta: { guest: true, breadcrumb: 'Login' },
            },
        ],
    },
    {
        path: '/',
        component: AppLayout,
        meta: { requiresAuth: true, breadcrumb: 'Beranda' },
        children: [
            {
                path: '',
                name: 'dashboard',
                component: () => import('@/views/DashboardView.vue'),
                meta: { breadcrumb: 'Dashboard' },
            },
            {
                path: 'clients',
                name: 'clients',
                component: () => import('@/views/clients/ClientListView.vue'),
                meta: { breadcrumb: 'Client' },
            },
            {
                path: 'packages',
                name: 'packages',
                component: () => import('@/views/packages/PackageListView.vue'),
                meta: { breadcrumb: 'Paket Layanan' },
            },
            {
                path: 'packages/create',
                name: 'packages.create',
                component: () => import('@/views/packages/PackageFormView.vue'),
                meta: { breadcrumb: 'Tambah Paket' },
            },
            {
                path: 'packages/:id/edit',
                name: 'packages.edit',
                component: () => import('@/views/packages/PackageFormView.vue'),
                meta: { breadcrumb: 'Edit Paket' },
            },
            {
                path: 'invoices',
                name: 'invoices',
                component: () => import('@/views/invoices/InvoiceListView.vue'),
                meta: { breadcrumb: 'Invoice' },
            },
            {
                path: 'invoices/create',
                name: 'invoices.create',
                component: () => import('@/views/invoices/InvoiceFormView.vue'),
                meta: { breadcrumb: 'Terbitkan Invoice' },
            },
            {
                path: 'invoices/:id',
                name: 'invoices.detail',
                component: () => import('@/views/invoices/InvoiceDetailView.vue'),
                meta: { breadcrumb: 'Detail Invoice' },
            },
            {
                path: 'invoices/:id/edit',
                name: 'invoices.edit',
                component: () => import('@/views/invoices/InvoiceFormView.vue'),
                props: true,
                meta: { breadcrumb: 'Edit Invoice' },
            },
            {
                path: 'payments',
                name: 'payments',
                component: () => import('@/views/payments/PaymentListView.vue'),
                meta: { breadcrumb: 'Pembayaran' },
            },
        ],
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
    const auth = useAuthStore()

    // Fetch user if not loaded yet
    if (!auth.isAuthenticated && !to.meta.guest) {
        await auth.fetchUser()
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return next({ name: 'login' })
    }

    if (to.meta.guest && auth.isAuthenticated) {
        return next({ name: 'dashboard' })
    }

    next()
})

export default router
