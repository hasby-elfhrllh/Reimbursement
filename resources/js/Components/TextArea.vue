<script setup>
const model = defineModel({ type: String })
</script>

<template>
  <textarea
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
    v-model="model"
    v-bind="$attrs"
  ></textarea>
</template>