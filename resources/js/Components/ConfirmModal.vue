<template>
  <div
    v-if="visible"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  >
    <div class="bg-white rounded-md shadow-lg max-w-md w-full p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ title }}</h3>
      <p class="text-gray-600 mb-6">
        {{ message }}
      </p>
      <div class="flex justify-end space-x-2">
        <button
          @click="cancel"
          class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium"
        >
          Batal
        </button>
        <button
          @click="confirm"
          class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium"
        >
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>

const props = defineProps({
  visible: Boolean,
  title: {
    type: String,
    default: 'Konfirmasi',
  },
  message: {
    type: String,
    default: 'Apakah Anda yakin?',
  },
  confirmText: {
    type: String,
    default: 'Ya',
  }
})

const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => {
  emit('confirm')
}

const cancel = () => {
  emit('cancel')
}
</script>
