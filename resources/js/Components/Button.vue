<template>
    <button
        :class="[
            'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm transition-colors',
            variant === 'primary' && 'text-white bg-indigo-600 hover:bg-indigo-700',
            variant === 'danger' && 'text-white bg-red-600 hover:bg-red-700',
            variant === 'secondary' && 'text-gray-700 bg-gray-100 hover:bg-gray-200',
            variant === 'success' && 'text-white bg-green-600 hover:bg-green-700',
            variant === 'warning' && 'text-white bg-yellow-500 hover:bg-yellow-600',
            variant === 'info' && 'text-white bg-cyan-600 hover:bg-cyan-700',
            variant === 'light' && 'text-gray-700 bg-gray-50 hover:bg-gray-100',
            variant === 'dark' && 'text-white bg-gray-800 hover:bg-gray-900',
        ]"
        v-bind="$attrs"
    >
        <slot></slot>
    </button>
</template>

<script setup>
defineProps({
    variant: {
        type: String,
        default: 'primary'
    }
})
</script>
