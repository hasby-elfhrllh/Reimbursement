<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Button from '@/Components/Button.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import FilterDropdown from '@/Components/FilterDropdown.vue'
import ReimbursementDetailModal from '@/Components/ReimbursementDetailModal.vue'
import { Head, Link } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import $ from 'jquery'
import 'datatables.net-bs5'
import 'datatables.net-buttons-bs5'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import jsZip from 'jszip'
import pdfMake from 'pdfmake/build/pdfmake'
import pdfFonts from 'pdfmake/build/vfs_fonts'
import 'datatables.net-buttons/js/buttons.html5'
import 'datatables.net-buttons/js/buttons.print'

pdfMake.vfs = pdfFonts.vfs
window.JSZip = jsZip

const user = ref(null)
const handleFlash = (event) => {
    if (event.detail.type === 'success') {
        toast.success(event.detail.message)
    } else {
        toast.error(event.detail.message)
    }
}
const showConfirm = ref(false)
const confirmTitle = ref('')
const confirmMessage = ref('')
const confirmAction = ref(null)
const selectedItem = ref(null)

const showDetailModal = ref(false)
const selectedReimbursement = ref(null)

const monthFilter = ref('')
const yearFilter = ref('')
const statusFilter = ref('')
const trashedFilter = ref('')

const months = [
    { value: '', label: 'Semua Bulan' },
    { value: '1', label: 'Januari' },
    { value: '2', label: 'Februari' },
    { value: '3', label: 'Maret' },
    { value: '4', label: 'April' },
    { value: '5', label: 'Mei' },
    { value: '6', label: 'Juni' },
    { value: '7', label: 'Juli' },
    { value: '8', label: 'Agustus' },
    { value: '9', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' },
]

const years = ref([])

const table = ref(null)

onMounted(async () => {
    await axios.get('/api/user')
        .then(res => {
            user.value = res.data
        })

    loadDataTable()
})

watch([monthFilter, yearFilter, statusFilter, trashedFilter], () => {
    if (table.value) {
        table.value.ajax.reload()
    }
})

const loadDataTable = () => {
    let columns = [
        { data: 'title', name: 'title' },
        {
            data: 'amount',
            name: 'amount',
            render: data => formatCurrency(data)
        },
        {
            data: 'category.name',
            name: 'category.name',
            defaultContent: '-'
        },
        {
            data: 'submitted_at',
            name: 'submitted_at',
            render: data => formatDate(data),
            defaultContent: '-'
        },
        {
            data: 'approved_at',
            name: 'approved_at',
            render: data => formatDate(data),
            defaultContent: '-'
        },
        {
            data: 'status',
            name: 'status',
            render: function (data) {
                return `<span class="${statusColor(data)}">${data}</span>`
            }
        },
    ]

    if (user.value?.role === 'admin') {
        columns.push({
            data: 'deleted_at',
            name: 'deleted_at',
            render: data => formatDate(data),
            defaultContent: '-'
        })
    }

    // Kolom Action
    columns.push({
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
            let buttons = ''
            buttons += `<button class="btn-detail btn btn-info btn-sm me-2" data-id="${row.id}">Lihat</button>`
            if (user.value?.role === 'employee') {
                buttons += `<button class="btn-delete btn btn-danger btn-sm" data-id="${row.id}">Hapus</button>`
            }
            return buttons
        }
    })

    table.value = $('#reimbursements-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '/api/reimbursements',
            data: function (d) {
                d.month = monthFilter.value
                d.year = yearFilter.value
                d.status = statusFilter.value
                d.trashed = trashedFilter.value
            }
        },
        columns,
        dom:
        '<"row mb-3"' +
            '<"col-md-6"B>' +
            '<"col-md-6 text-end"f>' +
        '>' +
        '<"row"' +
            '<"col-12"tr>' +
        '>' +
        '<"row mt-3"' +
            '<"col-md-5"i>' +
            '<"col-md-7"p>' +
        '>',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm'
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm'
            },
            {
                extend: 'print',
                className: 'btn btn-primary btn-sm'
            }
        ],
        drawCallback: function () {
            $('#reimbursements-table .btn-detail').on('click', function () {
                const id = $(this).data('id')
                openDetailModal(id)
            })

            $('#reimbursements-table .btn-delete').on('click', function () {
                const id = $(this).data('id')
                openConfirm(id, 'delete')
            })
        }
    })
}

const openDetailModal = (id) => {
    axios.get(`/api/reimbursements/${id}`)
        .then(res => {
            selectedReimbursement.value = res.data.data
            showDetailModal.value = true
        })
        .catch(() => alert('Gagal memuat detail reimbursement.'))
}

const openConfirm = (id, actionType) => {
    selectedItem.value = id
    if (actionType === 'delete') {
        confirmTitle.value = 'Konfirmasi Hapus'
        confirmMessage.value = 'Yakin ingin menghapus reimbursement ini?'
        confirmAction.value = () => deleteReimbursement(id)
    } else if (actionType === 'approve') {
        confirmTitle.value = 'Konfirmasi Approve'
        confirmMessage.value = 'Yakin ingin approve reimbursement ini?'
        confirmAction.value = () => approveReimbursement(id)
    } else if (actionType === 'reject') {
        confirmTitle.value = 'Konfirmasi Reject'
        confirmMessage.value = 'Yakin ingin reject reimbursement ini?'
        confirmAction.value = () => rejectReimbursement(id)
    }
    showConfirm.value = true
}

const handleConfirm = () => {
    confirmAction.value?.()
    showConfirm.value = false
}

const deleteReimbursement = (id) => {
    axios.delete(`/api/reimbursements/${id}`)
        .then(() => {
            table.value.ajax.reload()
        window.dispatchEvent(new CustomEvent('flash', {
                detail: {
                    type: 'success',
                    message: 'Reimbursement berhasil di hapus!'
                }
            }))
        })
        .catch(() => {
            window.dispatchEvent(new CustomEvent('flash', {
                detail: {
                    type: 'error',
                    message: 'Gagal hapus reimbursement.'
                }
            }))
        })
}

const approveReimbursement = (id) => {
    axios.put(`/api/reimbursements/${id}`, { status: 'approved' })
        .then(() => {
            table.value.ajax.reload()
            showDetailModal.value = false

            window.dispatchEvent(new CustomEvent('flash', {
                detail: {
                    type: 'success',
                    message: 'Reimbursement berhasil di-approve!'
                }
            }))
        })
        .catch(() => {
            window.dispatchEvent(new CustomEvent('flash', {
                detail: {
                    type: 'error',
                    message: 'Gagal approve reimbursement.'
                }
            }))
        })
}

const rejectReimbursement = (id) => {
    axios.put(`/api/reimbursements/${id}`, { status: 'rejected' })
        .then(() => {
            table.value.ajax.reload()
            showDetailModal.value = false

            window.dispatchEvent(new CustomEvent('flash', {
                detail: {
                    type: 'success',
                    message: 'Reimbursement berhasil di-reject!'
                }
            }))
        })
        .catch(() => {
            window.dispatchEvent(new CustomEvent('flash', {
                detail: {
                    type: 'error',
                    message: 'Gagal reject reimbursement.'
                }
            }))
        })
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount)
}

const formatDate = (date) => {
    if (!date) return '-'
    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    })
}

const statusColor = (status) => {
    switch (status) {
        case 'pending': return 'text-warning fw-semibold'
        case 'approved': return 'text-success fw-semibold'
        case 'rejected': return 'text-danger fw-semibold'
        default: return ''
    }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Reimbursements"/>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Reimbursements
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">

          <div class="mb-6 flex flex-wrap gap-4 items-center">
            <FilterDropdown
              v-model="monthFilter"
              :options="months"
              placeholder="Pilih Bulan"
            />
            <FilterDropdown
              v-model="yearFilter"
              :options="years"
              placeholder="Pilih Tahun"
            />
            <FilterDropdown
              v-model="statusFilter"
              :options="[
                { value: '', label: 'Semua Status' },
                { value: 'pending', label: 'Pending' },
                { value: 'approved', label: 'Approved' },
                { value: 'rejected', label: 'Rejected' },
              ]"
              placeholder="Pilih Status"
            />
            <FilterDropdown
              v-if="user?.role === 'admin'"
              v-model="trashedFilter"
              :options="[
                { value: '', label: 'Tampilkan Semua Data' },
                { value: 'only', label: 'Tampilkan Data yang Dihapus Saja' },
                { value: 'with', label: 'Tampilkan Data yang Belum Dihapus Saja' }
              ]"
              placeholder="Filter Data"
            />

            <Link 
              v-if="user?.role === 'employee'"
              href="/reimbursements/create"
              class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition"
            >
              <span class="text-sm font-medium">+ New Reimbursement</span>
            </Link>
          </div>

          <table id="reimbursements-table" class="table table-striped table-bordered w-100">
            <thead>
            <tr>
                <th>Judul</th>
                <th>Jumlah</th>
                <th>Kategori</th>
                <th>Tanggal Submit</th>
                <th>Tanggal Approve/Reject</th>
                <th>Status</th>
                <th v-if="user?.role === 'admin'">Tanggal Dihapus</th>
                <th>Aksi</th>
            </tr>
            </thead>
          </table>

        </div>
      </div>
    </div>

    <ConfirmModal
        :visible="showConfirm"
        :title="confirmTitle"
        :message="confirmMessage"
        confirmText="Ya"
        @confirm="handleConfirm"
        @cancel="showConfirm = false"
    >
        <template #footer>
            <Button variant="primary" @click="handleConfirm">Ya</Button>
            <Button variant="danger" @click="showConfirm = false">Batal</Button>
        </template>
    </ConfirmModal>

    <ReimbursementDetailModal
        :visible="showDetailModal"
        :data="selectedReimbursement"
        :user="user"
        @close="showDetailModal = false"
        @approve="approveReimbursement"
        @reject="rejectReimbursement"
    />
  </AuthenticatedLayout>
</template>
