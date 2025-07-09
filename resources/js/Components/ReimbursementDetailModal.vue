<template>
  <div
    v-if="visible"
    class="fixed inset-0 z-50 overflow-auto bg-gray-800 bg-opacity-50 flex items-center justify-center"
  >
    <div
      class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative"
    >
      <h2 class="text-lg font-bold mb-4">Detail Reimbursement</h2>

      <div class="mb-4">
        <p><strong>Judul:</strong> {{ data?.title || '-' }}</p>
        <p><strong>Deskripsi:</strong> {{ data?.description || '-' }}</p>
        <p><strong>Jumlah:</strong> {{ formatCurrency(data?.amount) }}</p>
        <p><strong>Kategori:</strong> {{ data?.category?.name || '-' }}</p>
        <p><strong>Status:</strong>
          <span :class="statusColor(data?.status)">
            {{ data?.status || '-' }}
          </span>
        </p>
        <p><strong>Diajukan:</strong> {{ formatDate(data?.submitted_at) }}</p>
        <p><strong>Disetujui/Ditolak:</strong> {{ formatDate(data?.approved_at) }}</p>
      </div>

      <div class="mb-4">
        <h3 class="font-semibold mb-2">Lampiran</h3>
        <div v-if="attachmentUrl">
          <div v-if="isImage(attachmentUrl)">
            <img
              :src="attachmentUrl"
              alt="Attachment"
              class="max-h-80 rounded border w-full object-contain"
            />
          </div>
          <div v-else-if="isPdf(attachmentUrl)">
            <iframe
              :src="attachmentUrl"
              class="w-full h-96 border rounded"
            ></iframe>
          </div>
          <div v-else>
            <p class="text-sm text-gray-500">
              Format file tidak didukung.
            </p>
          </div>

        <a
            :href="attachmentUrl"
            download
            target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 mt-3 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
            >
            <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"
                ></path>
            </svg>
            Download Lampiran
        </a>
        </div>
        <div v-else>
          <p class="text-sm text-gray-500">
            Tidak ada lampiran.
          </p>
        </div>
      </div>

      <div class="flex justify-end gap-2 mt-6">
        <Button variant="danger" @click="$emit('close')">Tutup</Button>

        <template v-if="user?.role === 'manager'">
          <Button variant="warning" @click="$emit('reject', data?.id)">
            Reject
          </Button>
          <Button variant="success" @click="$emit('approve', data?.id)">
            Approve
          </Button>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import Button from '@/Components/Button.vue'
import { computed } from 'vue'

const props = defineProps({
  visible: Boolean,
  data: Object,
  user: Object,
})

const emit = defineEmits(['close', 'approve', 'reject'])

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(amount || 0)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}

const statusColor = (status) => {
  switch (status) {
    case 'pending':
      return 'text-warning font-semibold'
    case 'approved':
      return 'text-success font-semibold'
    case 'rejected':
      return 'text-danger font-semibold'
    default:
      return ''
  }
}

const isImage = (url) => {
  return /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(url)
}

const isPdf = (url) => {
  return /\.pdf$/i.test(url)
}

const attachmentUrl = computed(() => {
  if (!props.data?.file_path) return null
  return `/storage/${props.data.file_path}`
})
</script>
