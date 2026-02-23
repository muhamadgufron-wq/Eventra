import { defineStore } from 'pinia'
import invoiceService from '@/services/invoiceService'

export const useInvoiceStore = defineStore('invoice', {
    state: () => ({
        invoices: [],
        currentInvoice: null,
        loading: false,
        pagination: { current_page: 1, last_page: 1, total: 0 },
    }),

    actions: {
        async fetchInvoices(params = {}) {
            this.loading = true
            try {
                const { data } = await invoiceService.getAll(params)
                this.invoices = data.data
                this.pagination = data.meta
            } finally {
                this.loading = false
            }
        },

        async fetchInvoice(id) {
            this.loading = true
            try {
                const { data } = await invoiceService.getById(id)
                this.currentInvoice = data.data
            } finally {
                this.loading = false
            }
        },

        async createInvoice(payload) {
            const { data } = await invoiceService.create(payload)
            this.invoices.unshift(data.data)
            return data.data
        },

        async updateInvoice(id, payload) {
            const { data } = await invoiceService.update(id, payload)
            const index = this.invoices.findIndex((i) => i.id === id)
            if (index !== -1) this.invoices[index] = data.data
            if (this.currentInvoice?.id === id) this.currentInvoice = data.data
            return data.data
        },

        async deleteInvoice(id) {
            await invoiceService.delete(id)
            this.invoices = this.invoices.filter((i) => i.id !== id)
        },
    },
})
