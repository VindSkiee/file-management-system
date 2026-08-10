<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import BaseModal from '@/components/BaseModal.vue'
import BreadcrumbNav, { type BreadcrumbItem } from '@/components/BreadcrumbNav.vue'
import FileDetailModal from '@/components/FileDetailModal.vue'
import FileUploadModal from '@/components/FileUploadModal.vue'
import FolderCard from '@/components/FolderCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { useFile, type FileItem } from '@/composables/useFile'
import { useFolder, type Folder } from '@/composables/useFolder'

const authStore = useAuthStore()
const {
  folders,
  isLoading: foldersLoading,
  error: foldersError,
  getFolders,
  createFolder,
  updateFolder,
  deleteFolder,
} = useFolder()
const {
  files,
  isLoading: filesLoading,
  error: filesError,
  getFiles,
  deleteFile,
  downloadFile,
} = useFile()

const currentParentId = ref<number | null>(null)
const breadcrumbs = ref<BreadcrumbItem[]>([{ id: null, name: 'Root' }])

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
  loadFolderContents()
})

async function loadFolderContents(): Promise<void> {
  await Promise.all([
    getFolders(currentParentId.value),
    getFiles(currentParentId.value),
  ])
}

function openFolder(folder: Folder): void {
  currentParentId.value = folder.id
  breadcrumbs.value = [...breadcrumbs.value, { id: folder.id, name: folder.name }]
  loadFolderContents()
}

function navigateTo(item: BreadcrumbItem): void {
  const index = breadcrumbs.value.findIndex((crumb) => crumb.id === item.id)

  if (index !== -1) {
    breadcrumbs.value = breadcrumbs.value.slice(0, index + 1)
  }

  currentParentId.value = item.id
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

async function handleFileDownload(file: FileItem): Promise<void> {
  await downloadFile(file)
}
</script>

<template>
  <AppLayout>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <BreadcrumbNav :items="breadcrumbs" @navigate="navigateTo" />

      <button
        v-if="authStore.isAdmin"
        @click="openCreateFolder"
        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
      >
        Create Folder
      </button>
    </div>

    <p v-if="foldersError" class="mt-4 rounded bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ foldersError }}
    </p>

    <div
      v-if="foldersLoading"
      class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5"
    >
      <div v-for="i in 8" :key="i" class="h-36 animate-pulse rounded-lg bg-gray-200"></div>
    </div>

    <div
      v-else-if="folders.length === 0"
      class="mt-8 rounded-lg border border-dashed border-gray-300 py-16 text-center text-sm text-gray-500"
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
      />
    </div>

    <div class="mt-8 overflow-hidden rounded-lg bg-white shadow">
      <div class="flex flex-wrap items-center justify-between gap-3 border-b px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-800">Files</h3>
        <button
          v-if="authStore.isAdmin"
          @click="openCreateFile"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
        >
          Upload File
        </button>
      </div>

      <p v-if="filesError" class="border-b border-gray-100 px-6 py-3 text-sm text-red-600">
        {{ filesError }}
      </p>

      <p v-if="filesLoading" class="px-6 py-10 text-center text-sm text-gray-500">Loading...</p>

      <p v-else-if="files.length === 0" class="px-6 py-10 text-center text-sm text-gray-500">
        Belum ada file di folder ini.
      </p>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 font-semibold text-gray-600">Nama File</th>
              <th class="px-6 py-3 font-semibold text-gray-600">Title</th>
              <th class="px-6 py-3 font-semibold text-gray-600">Department</th>
              <th class="px-6 py-3 text-right font-semibold text-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="file in files" :key="file.id" class="hover:bg-gray-50">
              <td class="px-6 py-3 font-medium text-gray-800">{{ file.file_name }}</td>
              <td class="px-6 py-3 text-gray-700">{{ file.title }}</td>
              <td class="px-6 py-3 text-gray-700">{{ file.department?.name ?? '-' }}</td>
              <td class="px-6 py-3">
                <div class="flex flex-wrap justify-end gap-2">
                  <button
                    @click="openFileDetail(file)"
                    class="rounded bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-100"
                  >
                    View Detail
                  </button>
                  <button
                    @click="handleFileDownload(file)"
                    class="rounded bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-600 transition hover:bg-emerald-100"
                  >
                    Download
                  </button>
                  <button
                    v-if="authStore.isAdmin"
                    @click="openEditFile(file)"
                    class="rounded bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-600 transition hover:bg-amber-100"
                  >
                    Edit
                  </button>
                  <button
                    v-if="authStore.isAdmin"
                    @click="handleFileDelete(file)"
                    class="rounded bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600 transition hover:bg-red-100"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <BaseModal
      :show="showFolderModal"
      :title="editingFolderId === null ? 'Create Folder' : 'Rename Folder'"
      @close="closeFolderModal"
    >
      <p v-if="modalError" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600">
        {{ modalError }}
      </p>

      <form @submit.prevent="handleFolderSubmit" class="mt-4">
        <input
          v-model="form.name"
          type="text"
          placeholder="Nama Folder"
          class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
        />

        <div class="mt-6 flex justify-end gap-2">
          <button
            type="button"
            @click="closeFolderModal"
            class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300"
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
