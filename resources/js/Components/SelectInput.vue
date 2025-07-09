<template>
    <select
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        v-bind="$attrs"
        :class="[
            'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm',
            modelValue === '' ? 'text-gray-500' : 'text-gray-900'
        ]"
    >
        <slot></slot>
    </select>
</template>

<script setup>
defineProps({
    modelValue: String
});
</script>