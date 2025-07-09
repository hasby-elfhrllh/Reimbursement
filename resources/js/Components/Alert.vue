<template>
  <transition name="fade">
    <div
      v-if="visible"
      :class="[
        'fixed top-4 right-4 w-72 max-w-full flex items-start gap-3 rounded shadow-lg px-4 py-3 z-50 text-sm',
        type === 'success' && 'bg-green-50 text-green-800 border border-green-200',
        type === 'error' && 'bg-red-50 text-red-800 border border-red-200',
        type === 'info' && 'bg-blue-50 text-blue-800 border border-blue-200',
        type === 'warning' && 'bg-yellow-50 text-yellow-800 border border-yellow-200',
      ]"
    >
      <span v-if="typeIcon" class="mt-0.5">
        <component :is="typeIcon" class="w-5 h-5" />
      </span>
      <span class="flex-1">{{ message }}</span>
      <button @click="visible = false" class="text-gray-400 hover:text-gray-600">
        &times;
      </button>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import {
  CheckCircleIcon,
  XCircleIcon,
  ExclamationCircleIcon,
  InformationCircleIcon,
} from '@heroicons/vue/24/solid'

const props = defineProps({
  message: String,
  type: {
    type: String,
    default: 'success',
  },
})

const visible = ref(false)

const typeIcon = computed(() => {
  switch (props.type) {
    case 'success':
      return CheckCircleIcon
    case 'error':
      return XCircleIcon
    case 'warning':
      return ExclamationCircleIcon
    case 'info':
      return InformationCircleIcon
    default:
      return null
  }
})

watch(
  () => props.message,
  (newVal) => {
    if (newVal) {
      visible.value = true
      setTimeout(() => {
        visible.value = false
      }, 5000)
    }
  },
  { immediate: true }
)
</script>

