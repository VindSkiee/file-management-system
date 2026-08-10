<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import BaseModal from '@/components/BaseModal.vue'
import BreadcrumbNav, { type BreadcrumbItem } from '@/components/BreadcrumbNav.vue'
import FolderCard from '@/components/FolderCard.vue'
import { useAuthStore } from '@/stores/authStore'
import { useFolder, type Folder } from '@/composables/useFolder'

const authStore = useAuthStore()
const { folders, isLoading, error, getFolders, createFolder, updateFolder, deleteFolder } = useFolder()

const currentParentId = ref<number | null>(null)
const breadcrumbs = ref<BreadcrumbItem[]>([{ id: null, name: 'Root' }])

const showModal = ref(false)
const editingId = ref<number | null>(null)
const form = reactive({ name: '' })
const submitting = ref(false)
const modalError = ref('')

onMounted(() => {
  loadFolders()
})

async function loadFolders(): Promise<void> {
  await getFolders(currentParentId.value)
}

function openFolder(folder: Folder): void {
  currentParentId.value = folder.id
  breadcrumbs.value = [...breadcrumbs.value, { id: folder.id, name: folder.name }]
  loadFolders()
}

function navigateTo(item: BreadcrumbItem): void {
  const index = breadcrumbs.value.findIndex((crumb) => crumb.id === item.id)

  if (index !== -1) {
    breadcrumbs.value = breadcrumbs.value.slice(0, index + 1)
  }

  currentParentId.value = item.id
  loadFolders()
}

function openCreate(): void {
  editingId.value = null
  form.name = ''
  modalError.value = ''
  showModal.value = true
}

function openRename(folder: Folder): void {
  editingId.value = folder.id
  form.name = folder.name
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
        ? await createFolder({ name, parent_id: currentParentId.value })
        : await updateFolder(editingId.value, { name })

    if (ok) {
      showModal.value = false
      await loadFolders()
    } else {
      modalError.value = error.value
    }
  } finally {
    submitting.value = false
  }
}

async function handleDelete(folder: Folder): Promise<void> {
  if (!window.confirm(`Hapus folder "${folder.name}"?`)) return

  const ok = await deleteFolder(folder.id)
  if (ok) await loadFolders()
}
</script>

<template>
  <AppLayout>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <BreadcrumbNav :items="breadcrumbs" @navigate="navigateTo" />

      <button
        v-if="authStore.isAdmin"
        @click="openCreate"
        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
      >
        Create Folder
      </button>
    </div>

    <p v-if="error" class="mt-4 rounded bg-red-50 px-4 py-3 text-sm text-red-600">
      {{ error }}
    </p>

    <div
      v-if="isLoading"
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
        @remove="handleDelete"
      />
    </div>

    <BaseModal
      :show="showModal"
      :title="editingId === null ? 'Create Folder' : 'Rename Folder'"
      @close="closeModal"
    >
      <p v-if="modalError" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600">
        {{ modalError }}
      </p>

      <form @submit.prevent="handleSubmit" class="mt-4">
        <input
          v-model="form.name"
          type="text"
          placeholder="Nama Folder"
          class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
        />

        <div class="mt-6 flex justify-end gap-2">
          <button
            type="button"
            @click="closeModal"
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
  </AppLayout>
</template>
