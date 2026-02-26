<template>
    <div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-1000">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatCard label="Penghasilan Masuk" :value="summary.monthly_revenue" format="currency" color="success">
                <template #icon>
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.584 2.376a.75.75 0 0 1 .832 0l9 6a.75.75 0 1 1-.832 1.248L12 3.901 3.416 9.624a.75.75 0 0 1-.832-1.248l9-6Z" />
                        <path fill-rule="evenodd" d="M20.25 10.332v9.918H21a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1 0-1.5h.75v-9.918a.75.75 0 0 1 .634-.74A49.109 49.109 0 0 1 12 9c2.59 0 5.134.202 7.616.592a.75.75 0 0 1 .634.74Zm-7.5 2.418a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Zm3-.75a.75.75 0 0 1 .75.75v6.75a.75.75 0 0 1-1.5 0v-6.75a.75.75 0 0 1 .75-.75ZM9 12.75a.75.75 0 0 0-1.5 0v6.75a.75.75 0 0 0 1.5 0v-6.75Z" clip-rule="evenodd" />
                    </svg>
                </template>
            </StatCard>
            
            <StatCard label="Menunggu Pembayaran" :value="summary.total_receivable" format="currency" color="warning">
                <template #icon>
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z" clip-rule="evenodd" />
                    </svg>
                </template>
            </StatCard>
            
            <StatCard label="Invoice Belum Lunas" :value="summary.unpaid_invoices" format="number" color="danger">
                <template #icon>
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z" />
                        <path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                    </svg>
                </template>
            </StatCard>
            
            <StatCard label="Clients Baru (Bulan Ini)" :value="summary.new_clients_this_month" format="number" color="accent">
                <template #icon>
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M5.25 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM2.25 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM18.75 7.5a.75.75 0 0 1 .75.75v1.5h1.5a.75.75 0 0 1 0 1.5h-1.5v1.5a.75.75 0 0 1-1.5 0v-1.5h-1.5a.75.75 0 0 1 0-1.5h1.5v-1.5a.75.75 0 0 1 .75-.75Z" />
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
