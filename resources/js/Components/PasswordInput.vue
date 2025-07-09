<script setup>
import { ref } from 'vue'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'

const props = defineProps({
    modelValue: String,
    placeholder: String,
})

const emit = defineEmits(['update:modelValue'])

const show = ref(false)

const toggle = () => {
    show.value = !show.value
}
</script>

<template>
  <div class="relative">
    <input
      :type="show ? 'text' : 'password'"
      :placeholder="placeholder"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      class="w-full border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500 pr-10"
    />
    <button
      type="button"
      @click="toggle"
      class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400"
    >
      <component
        :is="show ? EyeSlashIcon : EyeIcon"
        class="w-5 h-5"
      />
    </button>
  </div>
</template>