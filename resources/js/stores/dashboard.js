import { defineStore } from 'pinia'
import dashboardService from '@/services/dashboardService'

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        summary: {
            monthly_revenue: 0,
            unpaid_invoices: 0,
            total_receivable: 0,
            new_clients_this_month: 0,
            cashflow: [],
        },
        loading: false,
    }),

    actions: {
        async fetchSummary() {
            this.loading = true
            try {
                const { data } = await dashboardService.getSummary()
                this.summary = data.data
            } finally {
                this.loading = false
            }
        },
    },
})
