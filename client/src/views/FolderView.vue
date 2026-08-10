<script setup lang="ts">
import { computed, onMounted, reactive, ref, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import BaseModal from '@/components/BaseModal.vue'
import BreadcrumbNav, { type BreadcrumbItem } from '@/components/BreadcrumbNav.vue'
import FileDetailModal from '@/components/FileDetailModal.vue'
import FileUploadModal from '@/components/FileUploadModal.vue'
import FolderCard from '@/components/FolderCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { useDepartment } from '@/composables/useDepartment'
import { useFile, type FileItem } from '@/composables/useFile'
import { useFolder, type Folder } from '@/composables/useFolder'

const authStore = useAuthStore()
const { departments, getDepartments } = useDepartment()
const {
  folders,
  isLoading: foldersLoading,
  error: foldersError,
  page: foldersPage,
  lastPage: foldersLastPage,
  getFolders,
  createFolder,
  updateFolder,
  deleteFolder,
  restoreFolder,
} = useFolder()
const {
  files,
  isLoading: filesLoading,
  error: filesError,
  page: filesPage,
  lastPage: filesLastPage,
  getFiles,
  deleteFile,
  restoreFile,
  downloadFile,
} = useFile()

const currentParentId = ref<number | null>(null)
const breadcrumbs = ref<BreadcrumbItem[]>([{ id: null, name: 'Root' }])

const searchQuery = ref('')
const departmentFilter = ref<number | null>(null)
const showTrashed = ref(false)

const canPrevFolders = computed(() => foldersPage.value > 1)
const canNextFolders = computed(() => foldersPage.value < foldersLastPage.value)
const canPrevFiles = computed(() => filesPage.value > 1)
const canNextFiles = computed(() => filesPage.value < filesLastPage.value)

const showFolderModal = ref(false)
const editingFolderId = ref<number | null>(null)
const form = reactive({ name: '' })
const submitting = ref(false)
const modalError = ref('')

const showFileModal = ref(false)
const editingFile = ref<FileItem | null>(null)
const showDetailModal = ref(false)
const detailFile = ref<FileItem | null>(null)

onMounted(() => {
  getDepartments(false, 1, 100)
  loadFolderContents()
})

let searchTimer: ReturnType<typeof setTimeout> | undefined

watch([searchQuery, departmentFilter], () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    filesPage.value = 1
    reloadFiles()
  }, 300)
})

watch(showTrashed, () => {
  foldersPage.value = 1
  filesPage.value = 1
  loadFolderContents()
})

async function loadFolderContents(): Promise<void> {
  await Promise.all([
    getFolders(currentParentId.value, showTrashed.value, foldersPage.value),
    getFiles(currentParentId.value, searchQuery.value, departmentFilter.value, showTrashed.value, filesPage.value),
  ])
}

function reloadFiles(): void {
  getFiles(currentParentId.value, searchQuery.value, departmentFilter.value, showTrashed.value, filesPage.value)
}

function goToFolders(nextPage: number): void {
  if (nextPage < 1 || nextPage > foldersLastPage.value) return
  foldersPage.value = nextPage
  getFolders(currentParentId.value, showTrashed.value, nextPage)
}

function goToFiles(nextPage: number): void {
  if (nextPage < 1 || nextPage > filesLastPage.value) return
  filesPage.value = nextPage
  getFiles(currentParentId.value, searchQuery.value, departmentFilter.value, showTrashed.value, nextPage)
}

function openFolder(folder: Folder): void {
  currentParentId.value = folder.id
  foldersPage.value = 1
  filesPage.value = 1
  breadcrumbs.value = [...breadcrumbs.value, { id: folder.id, name: folder.name }]
  loadFolderContents()
}

function navigateTo(item: BreadcrumbItem): void {
  const index = breadcrumbs.value.findIndex((crumb) => crumb.id === item.id)

  if (index !== -1) {
    breadcrumbs.value = breadcrumbs.value.slice(0, index + 1)
  }

  currentParentId.value = item.id
  foldersPage.value = 1
  filesPage.value = 1
  loadFolderContents()
}

function openCreateFolder(): void {
  editingFolderId.value = null
  form.name = ''
  modalError.value = ''
  showFolderModal.value = true
}

function openRename(folder: Folder): void {
  editingFolderId.value = folder.id
  form.name = folder.name
  modalError.value = ''
  showFolderModal.value = true
}

function closeFolderModal(): void {
  if (submitting.value) return
  showFolderModal.value = false
}

async function handleFolderSubmit(): Promise<void> {
  const name = form.name.trim()
  if (!name || submitting.value) return

  submitting.value = true
  modalError.value = ''

  try {
    const ok =
      editingFolderId.value === null
        ? await createFolder({ name, parent_id: currentParentId.value })
        : await updateFolder(editingFolderId.value, { name })

    if (ok) {
      showFolderModal.value = false
      await loadFolderContents()
    } else {
      modalError.value = foldersError.value
    }
  } finally {
    submitting.value = false
  }
}

async function handleFolderDelete(folder: Folder): Promise<void> {
  if (!window.confirm(`Hapus folder "${folder.name}"?`)) return

  const ok = await deleteFolder(folder.id)
  if (ok) await loadFolderContents()
}

async function handleFolderRestore(folder: Folder): Promise<void> {
  const ok = await restoreFolder(folder.id)
  if (ok) await loadFolderContents()
}

function openCreateFile(): void {
  editingFile.value = null
  showFileModal.value = true
}

function openEditFile(file: FileItem): void {
  editingFile.value = file
  showFileModal.value = true
}

function openFileDetail(file: FileItem): void {
  detailFile.value = file
  showDetailModal.value = true
}

function handleFileSaved(): void {
  showFileModal.value = false
  loadFolderContents()
}

async function handleFileDelete(file: FileItem): Promise<void> {
  if (!window.confirm(`Hapus file "${file.title}"?`)) return

  const ok = await deleteFile(file.id)
  if (ok) await loadFolderContents()
}

async function handleFileRestore(file: FileItem): Promise<void> {
  const ok = await restoreFile(file.id)
  if (ok) await loadFolderContents()
}

async function handleFileDownload(file: FileItem): Promise<void> {
  await downloadFile(file)
}
</script>

<template>
  <AppLayout>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <BreadcrumbNav :items="breadcrumbs" @navigate="navigateTo" />

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
          @click="openCreateFolder"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
        >
          Create Folder
        </button>
      </div>
    </div>

    <p v-if="foldersError" class="mt-4 rounded bg-red-50 px-4 py-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
      {{ foldersError }}
    </p>

    <div
      v-if="foldersLoading"
      class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
    >
      <div v-for="i in 8" :key="i" class="h-36 animate-pulse rounded-lg bg-gray-200 dark:bg-gray-700"></div>
    </div>

    <div
      v-else-if="folders.length === 0"
      class="mt-8 rounded-lg border border-dashed border-gray-300 py-16 text-center text-sm text-gray-500 transition-colors duration-200 dark:border-gray-600 dark:text-gray-400"
    >
      Folder ini kosong
    </div>

    <div
      v-else
      class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
    >
      <FolderCard
        v-for="folder in folders"
        :key="folder.id"
        :folder="folder"
        :is-admin="authStore.isAdmin"
        @open="openFolder"
        @rename="openRename"
        @remove="handleFolderDelete"
        @restore="handleFolderRestore"
      />
    </div>

    <div
      v-if="folders.length > 0"
      class="mt-4 flex flex-wrap items-center justify-between gap-3"
    >
      <p class="text-sm text-gray-500 dark:text-gray-400">
        Halaman {{ foldersPage }} dari {{ foldersLastPage }}
      </p>
      <div class="flex gap-2">
        <button
          type="button"
          :disabled="!canPrevFolders"
          @click="goToFolders(foldersPage - 1)"
          class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 disabled:opacity-40 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
        >
          Sebelumnya
        </button>
        <button
          type="button"
          :disabled="!canNextFolders"
          @click="goToFolders(foldersPage + 1)"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-40"
        >
          Berikutnya
        </button>
      </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b px-4 py-4 md:px-6 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Files</h3>
        <button
          v-if="authStore.isAdmin"
          @click="openCreateFile"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
        >
          Unggah File
        </button>
      </div>

      <div class="flex flex-col gap-3 border-b px-4 py-4 md:flex-row md:items-center md:px-6 dark:border-gray-700">
        <div class="relative flex-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 dark:text-gray-500"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari Nama File / Title..."
            class="w-full rounded border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
          />
        </div>

        <select
          v-model="departmentFilter"
          class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 md:w-56"
        >
          <option :value="null">Semua Department</option>
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">
            {{ dept.name }}
          </option>
        </select>
      </div>

      <p v-if="filesError" class="border-b border-gray-100 px-6 py-3 text-sm text-red-600 dark:border-gray-700 dark:text-red-300">
        {{ filesError }}
      </p>

      <p v-if="filesLoading" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Loading...</p>

      <p v-else-if="files.length === 0" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
        Belum ada file di folder ini.
      </p>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm md:min-w-full">
          <thead class="hidden md:table-header-group">
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Title</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Department</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Nama File</th>
              <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Upload Date</th>
              <th class="px-6 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="file in files"
              :key="file.id"
              :class="['block px-4 py-3 transition-colors duration-200 md:table-row md:px-0 md:py-0', file.deleted_at ? 'bg-red-50 dark:bg-red-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700']"
            >
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Title:</span>
                <span
                  :class="file.deleted_at
                    ? 'text-gray-400 line-through dark:text-gray-500'
                    : 'text-gray-700 dark:text-gray-200'"
                >
                  {{ file.title }}
                </span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Department:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ file.department?.name ?? '-' }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Nama File:</span>
                <span
                  :class="file.deleted_at
                    ? 'font-medium text-gray-400 line-through dark:text-gray-500'
                    : 'font-medium text-gray-800 dark:text-gray-100'"
                >
                  {{ file.file_name }}
                </span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3 md:text-center">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Upload Date:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ file.upload_date }}</span>
              </td>
              <td class="block pt-1 md:table-cell md:px-6 md:py-3 md:text-center">
                <div class="flex flex-wrap gap-2 md:justify-center">
                  <button
                    v-if="file.deleted_at"
                    @click="handleFileRestore(file)"
                    class="rounded bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600 transition hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50"
                  >
                    Restore
                  </button>

                  <template v-else>
                    <button
                      @click="openFileDetail(file)"
                      class="rounded bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-900/50"
                    >
                      Lihat Detail
                    </button>
                    <button
                      @click="handleFileDownload(file)"
                      class="rounded bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600 transition hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50"
                    >
                      Unduh
                    </button>
                    <button
                      v-if="authStore.isAdmin"
                      @click="openEditFile(file)"
                      class="rounded bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-600 transition hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:hover:bg-amber-900/50"
                    >
                      Ubah
                    </button>
                    <button
                      v-if="authStore.isAdmin"
                      @click="handleFileDelete(file)"
                      class="rounded bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
                    >
                      Hapus
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        v-if="files.length > 0"
        class="flex flex-wrap items-center justify-between gap-3 border-t px-4 py-3 md:px-6 dark:border-gray-700"
      >
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Halaman {{ filesPage }} dari {{ filesLastPage }}
        </p>
        <div class="flex gap-2">
          <button
            type="button"
            :disabled="!canPrevFiles"
            @click="goToFiles(filesPage - 1)"
            class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 disabled:opacity-40 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
          >
            Sebelumnya
          </button>
          <button
            type="button"
            :disabled="!canNextFiles"
            @click="goToFiles(filesPage + 1)"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-40"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>

    <BaseModal
      :show="showFolderModal"
      :title="editingFolderId === null ? 'Create Folder' : 'Rename Folder'"
      @close="closeFolderModal"
    >
      <p v-if="modalError" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
        {{ modalError }}
      </p>

      <form @submit.prevent="handleFolderSubmit" class="mt-4">
        <input
          v-model="form.name"
          type="text"
          placeholder="Nama Folder"
          class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
        />

        <div class="mt-6 flex justify-end gap-2">
          <button
            type="button"
            @click="closeFolderModal"
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
    </BaseModal>

    <FileUploadModal
      :show="showFileModal"
      :folder-id="currentParentId"
      :editing-file="editingFile"
      @close="showFileModal = false"
      @success="handleFileSaved"
    />

    <FileDetailModal
      :show="showDetailModal"
      :file="detailFile"
      @close="showDetailModal = false"
    />
  </AppLayout>
</template>
