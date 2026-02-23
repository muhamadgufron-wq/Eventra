<template>
    <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-1000">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <StatCard label="Penghasilan Masuk" :value="summary.monthly_revenue" format="currency" color="success">
                <template #icon>
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                </template>
            </StatCard>
            
            <StatCard label="Menunggu Pembayaran" :value="summary.total_receivable" format="currency" color="warning">
                <template #icon>
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </template>
            </StatCard>
            
            <StatCard label="Invoice Belum Lunas" :value="summary.unpaid_invoices" format="number" color="danger">
                <template #icon>
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-18 0h18M9 15.75h.008v.008H9v-.008Zm0-3h.008v.008H9v-.008Zm3 3h.008v.008H12v-.008Zm0-3h.008v.008H12v-.008Zm3 3h.008v.008H15v-.008Zm0-3h.008v.008H15v-.008Z" />
                    </svg>
                </template>
            </StatCard>
            
            <StatCard label="Clients Baru (Bulan Ini)" :value="summary.new_clients_this_month" format="number" color="accent">
                <template #icon>
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                </template>
            </StatCard>
        </div>

        <!-- Middle Section: Chart & Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Pemasukan Bulanan (Real Chart) -->
            <div class="lg:col-span-8 bg-white rounded-[1.5rem] border border-surface-100 p-8 shadow-sm relative overflow-hidden group">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-surface-900 tracking-tight mb-0.5">Pendapatan Real-time</h3>
                        <p class="text-xs font-medium text-surface-400">Analisis tren pemasukan dan performa bisnis</p>
                    </div>
                    <div class="flex p-1 bg-surface-50 rounded-xl">
                        <button class="px-5 py-1.5 text-[11px] font-bold rounded-lg bg-white shadow-sm text-surface-900 transition-all">6 Bulan</button>
                    </div>
                </div>

                <!-- ChartJS Line Chart -->
                <div class="relative h-72 w-full mt-2">
                    <Line v-if="chartData.datasets[0].data.length" :data="chartData" :options="chartOptions" />
                    <div v-else class="h-full flex items-center justify-center text-surface-300 font-bold uppercase tracking-widest text-[10px]">
                        Mengambil data tren...
                    </div>
                </div>
            </div>

            <!-- Invoice Terbaru (Activity List) -->
            <div class="lg:col-span-4 bg-white rounded-[1.5rem] border border-surface-100 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-bold text-surface-900 tracking-tight">Invoice Terbaru</h3>
                    <router-link :to="{ name: 'invoices' }" class="text-[11px] font-bold text-[#005c4b] uppercase tracking-widest hover:underline transition-all underline-offset-4">
                        View All
                    </router-link>
                </div>

                <div class="space-y-6">
                    <router-link v-for="inv in summary.latest_invoices" :key="inv.id" 
                                 :to="{ name: 'invoices.detail', params: { id: inv.id } }"
                                 class="flex items-center gap-4 group cursor-pointer">
                        <div class="w-10 h-10 rounded-xl bg-surface-50 flex items-center justify-center text-surface-400 group-hover:bg-primary-50 group-hover:text-primary-600 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-surface-900 mb-0.5 truncate group-hover:text-primary-600 transition-colors uppercase tracking-tight">
                                {{ inv.bride_name }} & {{ inv.groom_name }}
                            </p>
                            <p class="text-[10px] font-semibold text-surface-400">{{ inv.invoice_number }} • Sisa {{ formatCurrency(inv.remaining_amount) }}</p>
                        </div>
                        <span :class="['px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase tracking-wider', statusVariantClass(inv.status)]">
                            {{ inv.status }}
                        </span>
                    </router-link>
                    <div v-if="!summary.latest_invoices?.length" class="py-4 text-center text-surface-300 font-bold uppercase tracking-widest text-[9px]">
                        No recent invoices
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Payments -->
        <div class="bg-white rounded-[1.5rem] border border-surface-100 p-8 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-surface-900 tracking-tight mb-0.5">Pembayaran Terkini</h3>
                    <p class="text-xs font-medium text-surface-400">Daftar transaksi pembayaran terakhir dari pelanggan</p>
                </div>
                <router-link :to="{ name: 'payments' }" class="w-10 h-10 flex items-center justify-center rounded-xl border border-surface-100 text-surface-400 hover:text-surface-600 hover:bg-surface-50 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5l6 6m0 0l-6 6m6-6H3" />
                    </svg>
                </router-link>
            </div>

            <div class="overflow-x-auto -mx-4">
                <table class="w-full text-left border-separate border-spacing-x-4">
                    <thead class="text-[9px] font-black uppercase tracking-[0.2em] text-surface-400">
                        <tr>
                            <th class="pb-5 pl-4 font-black">Pelanggan</th>
                            <th class="pb-5 font-black">Metode</th>
                            <th class="pb-5 font-black">Tanggal</th>
                            <th class="pb-5 font-black text-right pr-4">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-50">
                        <tr v-for="pay in summary.latest_payments" :key="pay.id" class="group hover:bg-surface-50/50 transition-all">
                            <td class="py-5 pl-4">
                                <p class="text-sm font-bold text-surface-900 leading-tight uppercase tracking-tight">{{ pay.client_name }}</p>
                                <p class="text-[10px] font-semibold text-surface-400 mt-0.5">{{ pay.invoice_number }}</p>
                            </td>
                            <td class="py-5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75-3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                        </svg>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-surface-600">{{ pay.method }}</span>
                                </div>
                            </td>
                            <td class="py-5">
                                <span class="text-xs font-bold text-surface-400">{{ pay.payment_date }}</span>
                            </td>
                            <td class="py-5 text-right pr-4">
                                <p class="text-sm font-bold text-emerald-600 tracking-tight">{{ formatCurrency(pay.amount) }}</p>
                                <p class="text-[9px] font-black text-emerald-300 uppercase tracking-widest mt-0.5">Success</p>
                            </td>
                        </tr>
                        <tr v-if="!summary.latest_payments?.length">
                            <td colspan="4" class="py-12 text-center text-surface-300 font-bold uppercase tracking-widest text-[10px]">
                                No payment records found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'
import StatCard from '@/components/ui/StatCard.vue'
import { formatCurrency } from '@/utils/format'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Filler,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Filler,
  Legend
)

const dashboardStore = useDashboardStore()
const summary = computed(() => dashboardStore.summary)

onMounted(() => {
    dashboardStore.fetchSummary()
})

const statusVariantClass = (status) => {
    switch (status) {
        case 'paid': return 'bg-emerald-50 text-emerald-600 border border-emerald-100'
        case 'partial': return 'bg-orange-50 text-orange-600 border border-orange-100'
        default: return 'bg-red-50 text-red-600 border border-red-100'
    }
}

const chartData = computed(() => {
    const cashflow = summary.value.cashflow || []
    return {
        labels: cashflow.map(item => item.month),
        datasets: [
            {
                label: 'Pendapatan',
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const {ctx, chartArea} = chart;
                    if (!chartArea) return null;
                    const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(0, 92, 75, 0)');
                    gradient.addColorStop(1, 'rgba(0, 92, 75, 0.15)');
                    return gradient;
                },
                borderColor: '#005c4b',
                pointBackgroundColor: '#fff',
                pointBorderColor: '#005c4b',
                pointHoverBackgroundColor: '#005c4b',
                pointHoverBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                data: cashflow.map(item => item.total)
            }
        ]
    }
})

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: '#005c4b',
            titleFont: { size: 12, weight: 'bold' },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 12,
            displayColors: false,
            callbacks: {
                label: (context) => formatCurrency(context.raw)
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks: {
                font: { size: 10, weight: 'bold' },
                color: '#94a3b8',
                autoSkip: true,
                maxRotation: 0
            }
        },
        y: {
            grid: {
                color: '#f1f5f9',
                drawBorder: false
            },
            ticks: {
                font: { size: 10, weight: 'medium' },
                color: '#94a3b8',
                callback: (value) => {
                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + 'jt'
                    return 'Rp ' + (value / 1000) + 'rb'
                }
            }
        }
    }
}
</script>

<style scoped>
@keyframes dash {
    to {
        stroke-dashoffset: 0;
    }
}
</style>
