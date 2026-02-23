import { defineStore } from 'pinia'
import packageService from '@/services/packageService'

export const usePackageStore = defineStore('package', {
    state: () => ({
        packages: [],
        loading: false,
        pagination: { current_page: 1, last_page: 1, total: 0 },
    }),

    actions: {
        async fetchPackages(params = {}) {
            this.loading = true
            try {
                const { data } = await packageService.getAll(params)
                this.packages = data.data
                this.pagination = data.meta
            } finally {
                this.loading = false
            }
        },

        async createPackage(payload) {
            const { data } = await packageService.create(payload)
            this.packages.unshift(data.data)
            return data.data
        },

        async updatePackage(id, payload) {
            const { data } = await packageService.update(id, payload)
            const index = this.packages.findIndex((p) => p.id === id)
            if (index !== -1) this.packages[index] = data.data
            return data.data
        },

        async deletePackage(id) {
            await packageService.delete(id)
            this.packages = this.packages.filter((p) => p.id !== id)
        },
    },
})
