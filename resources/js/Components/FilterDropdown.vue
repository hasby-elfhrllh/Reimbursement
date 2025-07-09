<template>
  <div class="relative inline-block w-48">
    <button
      @click="toggleDropdown"
      class="w-full border border-gray-300 bg-white rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-pointer focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 flex justify-between items-center"
    >
      <span class="block truncate">
        {{ selectedLabel || placeholder }}
      </span>
      <svg
        class="h-5 w-5 text-gray-400"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M10 3a1 1 0 01.832.445l4.93 7a1 1 0 01-.832 1.555H5.07a1 1 0 01-.832-1.555l4.93-7A1 1 0 0110 3zm0 11a1 1 0 01-.832-.445l-4.93-7A1 1 0 015.07 5h9.86a1 1 0 01.832 1.555l-4.93 7A1 1 0 0110 14z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <div
      v-if="open"
      class="absolute mt-1 w-full rounded-md bg-white shadow-lg z-50 max-h-60 overflow-auto border border-gray-200"
    >
      <ul class="py-1 text-gray-700 text-sm">
        <li
          v-for="option in options"
          :key="option.value"
          @click="select(option)"
          class="cursor-pointer hover:bg-indigo-50 px-4 py-2"
        >
          {{ option.label }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: String,
  options: {
    type: Array,
    required: true,
  },
  placeholder: {
    type: String,
    default: 'Select...',
  },
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)

const selectedLabel = computed(() => {
  const found = props.options.find(
    (opt) => opt.value === props.modelValue
  )
  return found ? found.label : null
})

const toggleDropdown = () => {
  open.value = !open.value
}

const select = (option) => {
  emit('update:modelValue', option.value)
  open.value = false
}

watch(
  () => props.modelValue,
  () => {
    open.value = false
  }
)
</script>

<style scoped>
/* Optional: extra styling if you want */
</style>
