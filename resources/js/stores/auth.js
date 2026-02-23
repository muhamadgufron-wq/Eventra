import { defineStore } from 'pinia'
import authService from '@/services/authService'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        isAuthenticated: false,
        loading: false,
    }),

    getters: {
        userName: (state) => state.user?.name ?? '',
        userRole: (state) => state.user?.role ?? '',
        isOwner: (state) => state.user?.role === 'owner',
        isAdmin: (state) => ['owner', 'admin'].includes(state.user?.role),
    },

    actions: {
        async fetchUser() {
            console.log('Fetching user...')
            try {
                const { data } = await authService.getUser()
                console.log('User fetched successfully:', data)
                this.user = data
                this.isAuthenticated = true
            } catch (error) {
                console.warn('Fetch user failed:', error.response?.status || error.message)
                this.user = null
                this.isAuthenticated = false
            }
        },

        async login(credentials) {
            this.loading = true
            try {
                await authService.getCsrfCookie()
                await authService.login(credentials)
                await this.fetchUser()
            } finally {
                this.loading = false
            }
        },

        async logout() {
            await authService.logout()
            this.user = null
            this.isAuthenticated = false
        },
    },
})
