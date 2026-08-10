<script setup lang="ts">
import { ref, watch } from 'vue'
import BaseModal from '@/components/BaseModal.vue'
import { useDepartment } from '@/composables/useDepartment'
import { useFile } from '@/composables/useFile'

const props = defineProps<{
  show: boolean
  folderId: number | null
  files: File[]
}>()

const emit = defineEmits<{
  close: []
  success: []
}>()

const { departments, getDepartments } = useDepartment()
const { uploadFile, error } = useFile()

interface BatchItem {
  file: File
  title: string
}

const items = ref<BatchItem[]>([])
const departmentId = ref<number | null>(null)
const uploading = ref(false)
const uploadedCount = ref(0)
const uploadErrors = ref<string[]>([])

watch(
  () => props.show,
  (visible) => {
    if (!visible) return

    items.value = props.files.map((file) => ({ file, title: stripExtension(file.name) }))
    departmentId.value = null
    uploading.value = false
    uploadedCount.value = 0
    uploadErrors.value = []

    getDepartments(false, 1, 100)
  },
)

function stripExtension(name: string): string {
  const dot = name.lastIndexOf('.')
  return dot > 0 ? name.slice(0, dot) : name
}

function formatSize(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

function removeItem(index: number): void {
  items.value.splice(index, 1)
}

async function handleSubmit(): Promise<void> {
  if (items.value.length === 0 || departmentId.value === null || uploading.value) return

  uploading.value = true
  uploadErrors.value = []

  for (const item of items.value) {
    const payload = new FormData()
    payload.append('title', item.title.trim())
    payload.append('department_id', String(departmentId.value))
    if (props.folderId !== null) payload.append('folder_id', String(props.folderId))
    payload.append('file', item.file)

    const ok = await uploadFile(payload)
    if (ok) uploadedCount.value++
    else uploadErrors.value.push(`${item.file.name}: ${error.value}`)
  }

  uploading.value = false
  emit('success')
}
</script>

<template>
  <BaseModal :show="show" title="Unggah Banyak File" @close="emit('close')">
    <p
      v-if="uploadErrors.length > 0"
      class="rounded bg-red-50 px-3 py-2 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300"
    >
      {{ uploadErrors.join(' • ') }}
    </p>

    <div class="mt-4 space-y-4">
      <select
        v-model="departmentId"
        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
      >
        <option :value="null" disabled>Pilih Department</option>
        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
          {{ dept.name }}
        </option>
      </select>

      <div v-if="items.length > 0" class="max-h-72 space-y-2 overflow-y-auto">
        <div
          v-for="(item, index) in items"
          :key="index"
          class="flex items-center gap-2 rounded border border-gray-200 p-2 dark:border-gray-700"
        >
          <div class="min-w-0 flex-1">
            <input
              v-model="item.title"
              type="text"
              placeholder="Title"
              class="w-full rounded border border-gray-300 bg-white px-2 py-1 text-sm text-gray-900 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
            />
            <p class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">
              {{ item.file.name }} · {{ formatSize(item.file.size) }}
            </p>
          </div>
          <button
            type="button"
            @click="removeItem(index)"
            class="shrink-0 rounded bg-red-50 px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
          >
            Hapus
          </button>
        </div>
      </div>

      <p v-else class="text-center text-sm text-gray-500 dark:text-gray-400">
        Tidak ada file untuk diunggah.
      </p>

      <p v-if="uploading" class="text-sm font-medium text-blue-600 dark:text-blue-300">
        Mengunggah {{ uploadedCount }} dari {{ items.length }}...
      </p>
    </div>

    <div class="mt-6 flex justify-end gap-2">
      <button
        type="button"
        :disabled="uploading"
        @click="emit('close')"
        class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 disabled:opacity-40 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
      >
        Batal
      </button>
      <button
        type="button"
        :disabled="items.length === 0 || departmentId === null || uploading"
        @click="handleSubmit"
        class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-40"
      >
        {{ uploading ? 'Mengunggah...' : 'Unggah Semua' }}
      </button>
    </div>
  </BaseModal>
</template>
