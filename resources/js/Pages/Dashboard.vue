<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import { CurrencyDollarIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'
import { onMounted, ref } from 'vue'
import axios from 'axios'

const user = usePage().props.auth.user

const dashboardData = ref({
    pending: { count: 0, total_amount: 0 },
    approved: { count: 0, total_amount: 0 },
    rejected: { count: 0, total_amount: 0 },
})

const loading = ref(true)

const fetchDashboard = async () => {
    try {
        const response = await axios.get('/api/dashboard')
        if (response.data.success) {
            dashboardData.value = response.data.data
        }
    } catch (error) {
        console.error('Error fetching dashboard data', error)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchDashboard()
})

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount)
}

const monthYear = ref(
    new Date().toLocaleDateString('id-ID', {
        month: 'long',
        year: 'numeric',
    })
)
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12 bg-gradient-to-b from-blue-50 to-white min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="mb-8 text-gray-700">
                    <p class="text-sm">
                        Data di bawah ini adalah rekapitulasi reimbursement
                        untuk bulan <span class="font-semibold text-blue-600">{{ monthYear }}</span>.
                    </p>
                </div>

                <div v-if="loading" class="text-center py-12">
                    <span class="text-gray-600">Loading data dashboard...</span>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                    <!-- Pending -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                        <div class="flex items-center space-x-4">
                            <CurrencyDollarIcon class="w-10 h-10 text-yellow-500" />
                            <div>
                                <p class="text-gray-600 text-sm">Pending</p>
                                <p class="text-xl font-bold text-gray-800">
                                    {{ formatCurrency(dashboardData.pending.total_amount) }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ dashboardData.pending.count }} pengajuan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Approved -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                        <div class="flex items-center space-x-4">
                            <CheckCircleIcon class="w-10 h-10 text-green-500" />
                            <div>
                                <p class="text-gray-600 text-sm">Disetujui</p>
                                <p class="text-xl font-bold text-gray-800">
                                    {{ formatCurrency(dashboardData.approved.total_amount) }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ dashboardData.approved.count }} pengajuan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Rejected -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                        <div class="flex items-center space-x-4">
                            <XCircleIcon class="w-10 h-10 text-red-500" />
                            <div>
                                <p class="text-gray-600 text-sm">Ditolak</p>
                                <p class="text-xl font-bold text-gray-800">
                                    {{ formatCurrency(dashboardData.rejected.total_amount) }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ dashboardData.rejected.count }} pengajuan
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col md:flex-row items-center justify-between gap-8"
                >
                    <div class="max-w-xl">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">
                            Selamat datang, {{ user.name }}!
                        </h3>
                        <p class="text-gray-600">
                            Anda berhasil login ke aplikasi <span class="font-semibold text-blue-600">Reimbursement</span>.
                            Gunakan menu di atas untuk mengelola pengajuan, memeriksa status, atau melihat laporan reimbursement Anda.
                        </p>
                        <p class="mt-4 text-sm text-gray-500">
                            Dashboard ini menampilkan total reimbursement bulan <span class="font-semibold text-blue-600">{{ monthYear }}</span>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>