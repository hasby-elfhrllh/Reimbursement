<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import axios from 'axios'

import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import CurrencyInput from '@/Components/CurrencyInput.vue'
import FileInput from '@/Components/FileInput.vue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import SelectInput from '@/Components/SelectInput.vue'
import Button from '@/Components/Button.vue'
import LinkButton from '@/Components/LinkButton.vue'

const categories = ref([])

const form = ref({
    title: '',
    description: '',
    amount: '',
    category_id: '',
    receipt: null,
})
const resetForm = () => {
    form.value = {
        title: '',
        description: '',
        amount: '',
        category_id: '',
        receipt: null,
    }
}

const errors = ref({})
const processing = ref(false)

onMounted(async () => {
    try {
        const response = await axios.get('/api/categories')
        categories.value = response.data.data
    } catch (error) {
        router.visit('/reimbursements', {
            only: ['flash'],
            onFinish: () => {
                window.dispatchEvent(
                    new CustomEvent('flash', {
                        detail: {
                            type: 'error',
                            message: 'Gagal mengambil data kategori.',
                        },
                    })
                )
            }
        })
    }
})

const submit = async () => {
    errors.value = {}
    processing.value = true

    try {
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('description', form.value.description || '')
        formData.append('amount', form.value.amount)
        formData.append('category_id', form.value.category_id)

        if (form.value.receipt) {
            formData.append('receipt', form.value.receipt)
        }

        const response = await axios.post('/api/reimbursements', formData, {
            headers: {
                'Accept': 'application/json'
            }
        })

        window.dispatchEvent(
            new CustomEvent('flash', {
                detail: {
                    type: 'success',
                    message: response.data.message,
                },
            })
        )

        resetForm()
        router.visit('/reimbursements', {
        onFinish: () => {
            window.dispatchEvent(new CustomEvent('flash', {
            detail: {
                type: 'success',
                message: 'Reimbursement berhasil diajukan!',
            },
            }))
        }
        })

    } catch (error) {
        console.error("API ERROR", error)
        console.error("Status:", error.response?.status)
        console.error("Data:", error.response?.data)
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors || {}
            const message =
                error.response.data.message ||
                Object.values(errors.value)?.[0]?.[0] ||
                'Terjadi kesalahan validasi.'

            window.dispatchEvent(
                new CustomEvent('flash', {
                    detail: {
                        type: 'error',
                        message,
                    },
                })
            )
        } else {
            window.dispatchEvent(
                new CustomEvent('flash', {
                    detail: {
                        type: 'error',
                        message: 'Terjadi kesalahan saat menyimpan data.',
                    },
                })
            )
        }
    } finally {
        processing.value = false
    }
}
</script>
<template>
    <AuthenticatedLayout>
        <Head title="New Reimbursement" />

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Buat Reimbursement
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm rounded p-6 space-y-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Judul" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="e.g. Biaya parkir RS"
                                autofocus
                            />
                            <InputError :message="errors.title" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Deskripsi" />
                            <TextArea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                                placeholder="Deskripsikan pengeluaranmu"
                            />
                            <InputError :message="errors.description" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="amount" value="Jumlah" />
                            <CurrencyInput
                                id="amount"
                                v-model="form.amount"
                                class="mt-1 block w-full"
                                placeholder="Rp 0"
                            />
                            <InputError :message="errors.amount" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="category_id" value="Category" />
                            <SelectInput v-model="form.category_id" class="mt-1 block w-full">
                                <option disabled value="">-- Pilih Kategori --</option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    {{ cat.name }}
                                </option>
                            </SelectInput>
                            <InputError :message="errors.category_id" class="mt-2" />
                        </div>

                        <div>
                            <FileInput
                                v-model="form.receipt"
                                label="Upload Kuitansi"
                                placeholder="Upload receipt gambar (jpg, jpeg, png, webp) atau PDF (max 2MB)"
                            />
                            <InputError :message="errors.receipt" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <LinkButton href="/reimbursements" variant="secondary">
                                Cancel
                            </LinkButton>
                            <Button type="submit" :disabled="processing">
                                Submit
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
