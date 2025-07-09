<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

import { ref, onMounted } from 'vue'
import axios from 'axios'
axios.defaults.withCredentials = true
axios.defaults.baseURL = import.meta.env.VITE_APP_URL || 'http://127.0.0.1:8000'
import { TruckIcon, HeartIcon, CakeIcon, StarIcon } from '@heroicons/vue/24/solid'

const categories = ref([])

onMounted(async () => {
    await axios.get('/sanctum/csrf-cookie')

    try {
        const response = await axios.get('/api/categories', {
            withCredentials: true,
        })

        if (response.data.success) {
            categories.value = response.data.data
        } else {
            console.error(response.data.message || 'Failed to fetch categories')
        }
    } catch (error) {
        if (error.response?.status === 401) {
            console.log('Unauthorized. Redirecting to login...')
            window.location.href = '/login'
        } else {
            console.error(error)
        }
    }
})

const getCategoryIcon = (categoryName) => {
    switch (categoryName.toLowerCase()) {
        case 'kesehatan':
            return HeartIcon
        case 'transportasi':
            return TruckIcon
        case 'makan':
            return CakeIcon
        default:
            return StarIcon
    }
}
</script>
<template>
    <AuthenticatedLayout>
        <Head title="Categories" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Categories
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <div
                            v-for="category in categories"
                            :key="category.id"
                            class="border rounded-lg p-4 flex items-center gap-3 hover:bg-gray-50"
                        >
                            <component
                                :is="getCategoryIcon(category.name)"
                                class="w-8 h-8 text-indigo-600"
                            />
                            <span class="text-lg text-gray-700 font-medium">
                                {{ category.name }}
                                <span v-if="category.sisa_limit !== undefined">
                                    - Sisa Limit:
                                    Rp {{ Number(category.sisa_limit).toLocaleString('id-ID') }}
                                </span>
                                <span v-else>
                                    - Limit:
                                    Rp {{ Number(category.limit_per_month).toLocaleString('id-ID') }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
