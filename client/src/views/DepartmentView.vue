<script setup lang="ts">
import { onMounted, reactive, ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useAuthStore } from '@/stores/authStore'
import { useDepartment, type Department } from '@/composables/useDepartment'

const authStore = useAuthStore()
const {
  departments,
  isLoading,
  error,
  getDepartments,
  createDepartment,
  updateDepartment,
  deleteDepartment,
  restoreDepartment,
} = useDepartment()

const showTrashed = ref(false)
const showModal = ref(false)
const editingId = ref<number | null>(null)
const form = reactive({ name: '' })
const submitting = ref(false)
const modalError = ref('')

watch(showTrashed, () => {
  getDepartments(showTrashed.value)
})

onMounted(() => {
  getDepartments()
})

function openCreate(): void {
  editingId.value = null
  form.name = ''
  modalError.value = ''
  showModal.value = true
}

function openEdit(department: Department): void {
  editingId.value = department.id
  form.name = department.name
  modalError.value = ''
  showModal.value = true
}

function closeModal(): void {
  if (submitting.value) return
  showModal.value = false
}

async function handleSubmit(): Promise<void> {
  const name = form.name.trim()
  if (!name || submitting.value) return

  submitting.value = true
  modalError.value = ''

  try {
    const ok =
      editingId.value === null
        ? await createDepartment({ name })
        : await updateDepartment(editingId.value, { name })

    if (ok) {
      showModal.value = false
      await getDepartments()
    } else {
      modalError.value = error.value
    }
  } finally {
    submitting.value = false
  }
}

async function handleDelete(department: Department): Promise<void> {
  if (!window.confirm(`Hapus department "${department.name}"?`)) return

  const ok = await deleteDepartment(department.id)
  if (ok) await getDepartments(showTrashed.value)
}

async function handleRestore(department: Department): Promise<void> {
  const ok = await restoreDepartment(department.id)
  if (ok) await getDepartments(showTrashed.value)
}
</script>

<template>
  <AppLayout>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <h2 class="text-2xl font-bold text-gray-800 transition-colors duration-200 dark:text-gray-100">Departments</h2>

      <div class="flex flex-wrap items-center gap-4">
        <label
          v-if="authStore.isAdmin"
          class="flex cursor-pointer items-center gap-2 text-sm text-gray-600 transition-colors duration-200 dark:text-gray-300"
        >
          <input
            v-model="showTrashed"
            type="checkbox"
            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
          />
          Tampilkan Data Terhapus
        </label>

        <button
          v-if="authStore.isAdmin"
          @click="openCreate"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
        >
          Tambah Department
        </button>
      </div>
    </div>

    <p v-if="error" class="mt-4 rounded bg-red-50 px-4 py-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
      {{ error }}
    </p>

    <div class="mt-6 overflow-hidden rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
      <p v-if="isLoading" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Loading...</p>

      <p v-else-if="departments.length === 0" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
        Belum ada department. {{ authStore.isAdmin ? 'Klik "Tambah Department" untuk membuat.' : '' }}
      </p>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm md:min-w-full">
          <thead class="hidden md:table-header-group">
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">ID</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Nama Departemen</th>
              <th v-if="authStore.isAdmin" class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="dept in departments"
              :key="dept.id"
              :class="['block px-4 py-3 transition-colors duration-200 md:table-row md:px-0 md:py-0', dept.deleted_at ? 'bg-red-50 dark:bg-red-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700']"
            >
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">ID:</span>
                <span class="text-gray-500 dark:text-gray-400">{{ dept.id }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Nama:</span>
                <span
                  :class="dept.deleted_at
                    ? 'font-medium text-gray-400 line-through dark:text-gray-500'
                    : 'font-medium text-gray-800 dark:text-gray-100'"
                >
                  {{ dept.name }}
                </span>
              </td>
              <td v-if="authStore.isAdmin" class="block pt-1 md:table-cell md:px-6 md:py-3">
                <div class="flex flex-wrap gap-2 md:justify-center">
                  <button
                    v-if="dept.deleted_at"
                    @click="handleRestore(dept)"
                    class="inline-flex items-center gap-1 rounded bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-600 transition hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                    </svg>
                    Restore
                  </button>
                  <template v-else>
                    <button
                      @click="openEdit(dept)"
                      class="inline-flex items-center gap-1 rounded bg-blue-50 px-3 py-1.5 text-sm font-medium text-blue-600 transition hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                      </svg>
                      Edit
                    </button>
                    <button
                      @click="handleDelete(dept)"
                      class="inline-flex items-center gap-1 rounded bg-red-50 px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                      </svg>
                      Delete
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>

      <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg transition-colors duration-200 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
          {{ editingId === null ? 'Tambah Department' : 'Edit Department' }}
        </h3>

        <p v-if="modalError" class="mt-3 rounded bg-red-50 px-3 py-2 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
          {{ modalError }}
        </p>

        <form @submit.prevent="handleSubmit" class="mt-4">
          <input
            v-model="form.name"
            type="text"
            placeholder="Nama Department"
            class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
          />

          <div class="mt-6 flex justify-end gap-2">
            <button
              type="button"
              @click="closeModal"
              class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="!form.name.trim() || submitting"
              class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-50"
            >
              {{ submitting ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
