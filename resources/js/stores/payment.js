import { defineStore } from 'pinia'
import paymentService from '@/services/paymentService'

export const usePaymentStore = defineStore('payment', {
    state: () => ({
        payments: [],
        loading: false,
        pagination: { current_page: 1, last_page: 1, total: 0 },
    }),

    actions: {
        async fetchPayments(params = {}) {
            this.loading = true
            try {
                const { data } = await paymentService.getAll(params)
                this.payments = data.data
                this.pagination = data.meta
            } finally {
                this.loading = false
            }
        },

        async createPayment(payload) {
            const { data } = await paymentService.create(payload)
            this.payments.unshift(data.data)
            return data.data
        },

        async deletePayment(id) {
            await paymentService.delete(id)
            this.payments = this.payments.filter((p) => p.id !== id)
        },
    },
})
