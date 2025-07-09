<script setup>
import { ref, watch } from 'vue'
import { formatCurrency, parseCurrency } from '@/Utils/formatter.js'

const model = defineModel({ type: [Number, String] })

const displayValue = ref('')

watch(
    () => model.value,
    (newVal) => {
        if (newVal !== null && newVal !== undefined) {
            displayValue.value = formatCurrency(newVal)
        } else {
            displayValue.value = ''
        }
    },
    { immediate: true }
)

const onInput = (e) => {
    const raw = parseCurrency(e.target.value)
    model.value = raw
}
</script>

<template>
  <input
    type="text"
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
    :value="displayValue"
    @input="onInput"
  />
</template>
