<template>
    <Link
        :href="href"
        :method="method"
        :as="as"
        preserve-scroll
        v-bind="$attrs"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm transition-colors"
        :class="{
            'text-white bg-indigo-600 hover:bg-indigo-700': variant === 'primary',
            'text-white bg-red-600 hover:bg-red-700': variant === 'danger',
            'text-gray-700 bg-gray-100 hover:bg-gray-200': variant === 'secondary',
        }"
    >
        <slot></slot>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        required: true,
    },
    variant: {
        type: String,
        default: 'primary',
    },
    method: {
        type: String,
        default: 'get',
    },
    as: {
        type: String,
        default: 'a',
    },
})
</script>
