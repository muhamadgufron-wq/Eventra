<template>
    <div
        class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700"
    >
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <Card
                v-for="stat in stats"
                :key="stat.label"
                class="gap-0 rounded-3xl border border-surface-100 bg-white py-0 shadow-sm ring-0"
            >
                <CardContent class="flex items-center gap-4 p-6">
                    <div
                        :class="[
                            'flex size-12 shrink-0 items-center justify-center rounded-2xl',
                            stat.iconClass,
                        ]"
                    >
                        <component :is="stat.icon" class="size-6" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-md font-semibold text-surface-400">
                            {{ stat.label }}
                        </p>
                        <p
                            class="mt-1 truncate text-xl font-extrabold tracking-tight text-surface-900"
                        >
                            {{ stat.value }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Middle Section: Chart & Latest Invoices -->
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">
            <!-- Monthly Revenue Chart -->
            <Card
                class="gap-0 rounded-3xl border border-surface-100 bg-white py-0 shadow-sm ring-0 lg:col-span-8"
            >
                <CardHeader class="p-6 pb-0">
                    <CardTitle
                        class="text-xl font-bold tracking-tight text-surface-900"
                        >Pendapatan</CardTitle
                    >
                    <CardDescription
                        class="text-xs font-medium text-surface-400"
                    >
                        Analisis tren pemasukan dan performa bisnis
                    </CardDescription>
                    <CardAction>
                        <Badge
                            variant="outline"
                            class="h-7 rounded-lg px-3 text-[10px] font-bold uppercase tracking-widest text-surface-500"
                        >
                            6 Bulan
                        </Badge>
                    </CardAction>
                </CardHeader>

                <CardContent class="p-6 pt-4">
                    <div class="relative h-72 w-full">
                        <Line
                            v-if="chartData.datasets[0].data.length"
                            :data="chartData"
                            :options="chartOptions"
                        />
                        <div
                            v-else
                            class="flex h-full items-center justify-center text-[10px] font-bold uppercase tracking-widest text-surface-300"
                        >
                            {{
                                dashboardStore.loading
                                    ? "Memuat data tren..."
                                    : "Belum ada data pendapatan."
                            }}
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Latest Invoices -->
            <Card
                class="gap-0 rounded-3xl border border-surface-100 bg-white py-0 shadow-sm ring-0 lg:col-span-4"
            >
                <CardHeader class="p-6 pb-0">
                    <CardTitle
                        class="text-lg font-bold tracking-tight text-surface-900"
                        >Invoice Terbaru</CardTitle
                    >
                    <CardAction>
                        <Button
                            as-child
                            variant="ghost"
                            size="sm"
                            class="h-8 gap-1 rounded-lg px-2 text-[10px] font-black uppercase tracking-widest text-primary-700"
                        >
                            <RouterLink :to="{ name: 'invoices' }">
                                Lihat Semua
                                <ArrowUpRightIcon class="size-3.5" />
                            </RouterLink>
                        </Button>
                    </CardAction>
                </CardHeader>

                <CardContent class="p-6 pt-4">
                    <div class="space-y-1">
                        <RouterLink
                            v-for="inv in summary.latest_invoices"
                            :key="inv.id"
                            :to="{
                                name: 'invoices.detail',
                                params: { id: inv.id },
                            }"
                            class="group -mx-3 flex items-center gap-3 rounded-2xl p-3 transition-colors hover:bg-surface-50"
                        >
                            <div
                                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-surface-50 text-surface-400 transition-colors group-hover:bg-primary-50 group-hover:text-primary-600"
                            >
                                <UsersIcon class="size-5" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="truncate text-sm font-bold uppercase tracking-tight text-surface-900 transition-colors group-hover:text-primary-700"
                                >
                                    {{ inv.bride_name }} &amp;
                                    {{ inv.groom_name }}
                                </p>
                                <p
                                    class="truncate text-[10px] font-semibold text-surface-400"
                                >
                                    {{ inv.invoice_number }} &middot; Sisa
                                    {{ formatCurrency(inv.remaining_amount) }}
                                </p>
                            </div>
                            <Badge
                                :variant="statusVariant(inv.status)"
                                class="shrink-0"
                            >
                                {{ statusLabel(inv.status) }}
                            </Badge>
                        </RouterLink>

                        <p
                            v-if="!summary.latest_invoices?.length"
                            class="py-4 text-center text-[9px] font-bold uppercase tracking-widest text-surface-300"
                        >
                            {{
                                dashboardStore.loading
                                    ? "Memuat invoice..."
                                    : "Belum ada invoice terbaru."
                            }}
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Latest Payments -->
        <Card
            class="gap-0 rounded-3xl border border-surface-100 bg-white py-0 shadow-sm ring-0"
        >
            <CardHeader class="p-6 pb-0">
                <CardTitle
                    class="text-xl font-bold tracking-tight text-surface-900"
                    >Pembayaran Terkini</CardTitle
                >
                <CardDescription class="text-xs font-medium text-surface-400">
                    Daftar transaksi pembayaran terakhir dari pelanggan
                </CardDescription>
                <CardAction>
                    <Button
                        as-child
                        variant="outline"
                        size="icon"
                        class="size-10 rounded-xl border-surface-200 text-surface-500 hover:text-primary-600"
                    >
                        <RouterLink
                            :to="{ name: 'payments' }"
                            aria-label="Lihat semua pembayaran"
                        >
                            <ArrowRightIcon class="size-4" />
                        </RouterLink>
                    </Button>
                </CardAction>
            </CardHeader>

            <CardContent class="p-0 pt-4">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left">
                        <thead
                            class="border-y border-surface-100 bg-surface-50/50"
                        >
                            <tr>
                                <th
                                    class="px-6 py-3 text-[9px] font-black uppercase tracking-[0.2em] text-surface-400"
                                >
                                    Pelanggan
                                </th>
                                <th
                                    class="px-6 py-3 text-[9px] font-black uppercase tracking-[0.2em] text-surface-400"
                                >
                                    Metode
                                </th>
                                <th
                                    class="px-6 py-3 text-[9px] font-black uppercase tracking-[0.2em] text-surface-400"
                                >
                                    Tanggal
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-[9px] font-black uppercase tracking-[0.2em] text-surface-400"
                                >
                                    Jumlah
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-100">
                            <tr
                                v-for="pay in summary.latest_payments"
                                :key="pay.id"
                                class="transition-colors hover:bg-primary-50/30"
                            >
                                <td class="px-6 py-4">
                                    <p
                                        class="text-sm font-bold uppercase leading-tight tracking-tight text-surface-900"
                                    >
                                        {{ pay.client_name }}
                                    </p>
                                    <p
                                        class="mt-0.5 text-[10px] font-semibold text-surface-400"
                                    >
                                        {{ pay.invoice_number }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="flex size-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600"
                                        >
                                            <CreditCardIcon class="size-4" />
                                        </div>
                                        <span
                                            class="text-[10px] font-black uppercase tracking-widest text-surface-600"
                                            >{{ pay.method }}</span
                                        >
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 text-xs font-bold text-surface-400"
                                >
                                    {{ formatDate(pay.payment_date) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p
                                        class="text-sm font-bold tracking-tight text-emerald-600"
                                    >
                                        {{ formatCurrency(pay.amount) }}
                                    </p>
                                    <p
                                        class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-emerald-500"
                                    >
                                        Berhasil
                                    </p>
                                </td>
                            </tr>
                            <tr v-if="!summary.latest_payments?.length">
                                <td
                                    colspan="4"
                                    class="px-6 py-12 text-center text-[10px] font-bold uppercase tracking-widest text-surface-300"
                                >
                                    {{
                                        dashboardStore.loading
                                            ? "Memuat pembayaran..."
                                            : "Belum ada pembayaran tercatat."
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { RouterLink } from "vue-router";
import {
    ArrowRightIcon,
    ArrowUpRightIcon,
    ClockIcon,
    CreditCardIcon,
    FileTextIcon,
    UsersIcon,
    WalletIcon,
} from "@lucide/vue";
import { useDashboardStore } from "@/stores/dashboard";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { formatCurrency, formatDate, formatNumber } from "@/utils/format";
import {
    CategoryScale,
    Chart as ChartJS,
    Filler,
    LineElement,
    LinearScale,
    PointElement,
    Tooltip,
} from "chart.js";
import { Line } from "vue-chartjs";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Filler,
);

const dashboardStore = useDashboardStore();
const summary = computed(() => dashboardStore.summary);

const stats = computed(() => [
    {
        label: "Penghasilan Masuk",
        value: formatCurrency(summary.value.monthly_revenue),
        icon: WalletIcon,
        iconClass: "bg-emerald-50 text-emerald-600",
    },
    {
        label: "Menunggu Pembayaran",
        value: formatCurrency(summary.value.total_receivable),
        icon: ClockIcon,
        iconClass: "bg-amber-50 text-amber-600",
    },
    {
        label: "Invoice Belum Lunas",
        value: formatNumber(summary.value.unpaid_invoices),
        icon: FileTextIcon,
        iconClass: "bg-red-50 text-red-600",
    },
    {
        label: "Klien Baru (Bulan Ini)",
        value: formatNumber(summary.value.new_clients_this_month),
        icon: UsersIcon,
        iconClass: "bg-accent-50 text-accent-700",
    },
]);

onMounted(() => {
    dashboardStore.fetchSummary();
});

const statusVariant = (status) =>
    ({
        paid: "success",
        partial: "warning",
        unpaid: "danger",
    })[status] ?? "secondary";

const statusLabel = (status) =>
    ({
        paid: "Lunas",
        partial: "Sebagian",
        unpaid: "Belum Bayar",
    })[status] ?? status;

const chartData = computed(() => {
    const cashflow = summary.value.cashflow || [];

    return {
        labels: cashflow.map((item) => item.month),
        datasets: [
            {
                label: "Pendapatan",
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;

                    if (!chartArea) return null;

                    const gradient = ctx.createLinearGradient(
                        0,
                        chartArea.bottom,
                        0,
                        chartArea.top,
                    );
                    gradient.addColorStop(0, "rgba(6, 78, 59, 0)");
                    gradient.addColorStop(1, "rgba(6, 78, 59, 0.15)");

                    return gradient;
                },
                borderColor: "#065f46",
                pointBackgroundColor: "#fff",
                pointBorderColor: "#065f46",
                pointHoverBackgroundColor: "#065f46",
                pointHoverBorderColor: "#fff",
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                data: cashflow.map((item) => item.total),
            },
        ],
    };
});

const compactNumber = new Intl.NumberFormat("id-ID", {
    notation: "compact",
    maximumFractionDigits: 1,
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: "#064e3b",
            titleFont: { size: 12, weight: "bold" },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 12,
            displayColors: false,
            callbacks: {
                label: (context) => formatCurrency(context.raw),
            },
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            ticks: {
                font: { size: 10, weight: "bold" },
                color: "#94a3b8",
                autoSkip: true,
                maxRotation: 0,
            },
        },
        y: {
            grid: {
                color: "#f1f5f9",
                drawBorder: false,
            },
            ticks: {
                font: { size: 10, weight: "medium" },
                color: "#94a3b8",
                callback: (value) => "Rp " + compactNumber.format(value),
            },
        },
    },
};
</script>
