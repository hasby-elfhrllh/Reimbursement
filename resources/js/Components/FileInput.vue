<template>
    <label class="block">
        <span
            v-if="label"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
        </span>

        <div
            class="flex items-center justify-between border border-gray-300 rounded-md shadow-sm px-3 py-2
                   focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 bg-white"
        >
            <span
                v-if="fileName"
                class="truncate text-gray-700 text-sm"
            >
                {{ fileName }}
            </span>
            <span
                v-else
                class="text-gray-400 text-sm"
            >
                {{ placeholder }}
            </span>

            <button
                type="button"
                class="ml-4 inline-flex items-center px-3 py-1 border border-transparent
                       text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none"
                @click="triggerFileInput"
                :disabled="disabled"
            >
                Choose File
            </button>
        </div>

        <input
            ref="fileInput"
            type="file"
            class="sr-only"
            accept=".jpg,.jpeg,.png,.webp,.pdf"
            @change="handleFileChange"
            v-bind="$attrs"
        />

        <p v-if="errorMessage" class="mt-2 text-sm text-red-600">
            {{ errorMessage }}
        </p>

        <!-- PREVIEW -->
        <div
            v-if="previewUrl"
            class="mt-3 space-y-2"
        >
            <img
                v-if="isImage"
                :src="previewUrl"
                alt="Preview"
                class="max-h-40 rounded border"
            />

            <iframe
                v-else-if="isPdf"
                :src="previewUrl"
                class="w-full h-72 border rounded"
            ></iframe>

            <p
                v-else
                class="text-sm text-gray-500"
            >
                Preview not available for this file type.
            </p>

            <button
                type="button"
                @click="clearFile"
                class="inline-flex items-center mt-2 px-3 py-1 border border-transparent
                       text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
            >
                Remove File
            </button>
        </div>
    </label>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    modelValue: File,
    placeholder: {
        type: String,
        default: 'No file selected',
    },
    label: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    maxSizeMb: {
        type: Number,
        default: 2,
    },
})

const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const fileName = ref(props.modelValue?.name || '')
const previewUrl = ref('')
const isImage = ref(false)
const isPdf = ref(false)
const errorMessage = ref('')

const allowedTypes = [
    'image/jpeg',
    'image/png',
    'image/jpg',
    'image/webp',
    'application/pdf',
]

const triggerFileInput = () => {
    fileInput.value?.click()
}

const handleFileChange = (event) => {
    const file = event.target.files[0]

    if (!file) {
        clearFile()
        return
    }

    if (!allowedTypes.includes(file.type)) {
        errorMessage.value = `File type ${file.type} not allowed.`
        clearFile()
        return
    }

    if (file.size > props.maxSizeMb * 1024 * 1024) {
        errorMessage.value = `File too large (max ${props.maxSizeMb} MB).`
        clearFile()
        return
    }

    errorMessage.value = ''
    fileName.value = file.name
    emit('update:modelValue', file)

    previewFile(file)
}

const previewFile = (file) => {
    const type = file.type

    if (type.startsWith('image/')) {
        const reader = new FileReader()
        reader.onload = () => {
            previewUrl.value = reader.result
            isImage.value = true
            isPdf.value = false
        }
        reader.readAsDataURL(file)
    } else if (type === 'application/pdf') {
        const reader = new FileReader()
        reader.onload = () => {
            previewUrl.value = reader.result
            isPdf.value = true
            isImage.value = false
        }
        reader.readAsDataURL(file)
    } else {
        previewUrl.value = ''
        isImage.value = false
        isPdf.value = false
    }
}

const clearFile = () => {
    fileInput.value.value = null
    fileName.value = ''
    previewUrl.value = ''
    isImage.value = false
    isPdf.value = false
    emit('update:modelValue', null)
}

watch(
    () => props.modelValue,
    (newFile) => {
        if (newFile) {
            fileName.value = newFile.name
            previewFile(newFile)
        } else {
            clearFile()
        }
    }
)
</script>
