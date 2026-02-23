import { defineStore } from 'pinia'
import clientService from '@/services/clientService'

export const useClientStore = defineStore('client', {
    state: () => ({
        clients: [],
        currentClient: null,
        loading: false,
        pagination: { current_page: 1, last_page: 1, total: 0 },
    }),

    actions: {
        async fetchClients(params = {}) {
            this.loading = true
            try {
                const { data } = await clientService.getAll(params)
                this.clients = data.data
                this.pagination = data.meta
            } finally {
                this.loading = false
            }
        },

        async fetchClient(id) {
            this.loading = true
            try {
                const { data } = await clientService.getById(id)
                this.currentClient = data.data
            } finally {
                this.loading = false
            }
        },

        async createClient(payload) {
            const { data } = await clientService.create(payload)
            this.clients.unshift(data.data)
            return data.data
        },

        async updateClient(id, payload) {
            const { data } = await clientService.update(id, payload)
            const index = this.clients.findIndex((c) => c.id === id)
            if (index !== -1) this.clients[index] = data.data
            return data.data
        },

        async deleteClient(id) {
            await clientService.delete(id)
            this.clients = this.clients.filter((c) => c.id !== id)
        },
    },
})
