<script setup lang="ts">
import { reactive, ref, watch } from 'vue'
import BaseModal from '@/components/BaseModal.vue'
import { useDepartment } from '@/composables/useDepartment'
import { useFile, type FileItem } from '@/composables/useFile'

const props = defineProps<{
  show: boolean
  folderId: number | null
  editingFile: FileItem | null
}>()

const emit = defineEmits<{
  close: []
  success: []
}>()

const { departments, getDepartments } = useDepartment()
const { uploadFile, updateFile, error } = useFile()

const MAX_FILE_SIZE = 10 * 1024 * 1024
const ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'gif', 'webp']

const form = reactive<{ title: string; department_id: number | null; folder_id: number | null }>({
  title: '',
  department_id: null,
  folder_id: null,
})
const selectedFile = ref<File | null>(null)
const fileInput = ref<HTMLInputElement | null>(null)
const submitting = ref(false)
const uploadError = ref('')

watch(
  () => props.show,
  (visible) => {
    if (!visible) return

    form.title = props.editingFile?.title ?? ''
    form.department_id = props.editingFile?.department?.id ?? null
    form.folder_id = props.editingFile ? (props.editingFile.folder?.id ?? null) : props.folderId
    selectedFile.value = null
    uploadError.value = ''

    getDepartments(false, 1, 100)
  },
)

function setSelectedFile(file: File): void {
  if (file.size > MAX_FILE_SIZE) {
    uploadError.value = 'Ukuran file maksimal 10MB.'
    return
  }

  const extension = file.name.split('.').pop()?.toLowerCase() ?? ''
  if (!ALLOWED_EXTENSIONS.includes(extension)) {
    uploadError.value = 'Format file tidak diizinkan. Gunakan dokumen/gambar (pdf, doc, xls, ppt, txt, csv, jpg, png, dll).'
    return
  }

  selectedFile.value = file
  uploadError.value = ''
}

function onDrop(event: DragEvent): void {
  const dropped = event.dataTransfer?.files?.[0]
  if (dropped) setSelectedFile(dropped)
}

function onFileChange(event: Event): void {
  const input = event.target as HTMLInputElement
  const picked = input.files?.[0]
  if (picked) setSelectedFile(picked)
  input.value = ''
}

function buildCreatePayload(): FormData {
  const payload = new FormData()
  payload.append('title', form.title.trim())
  payload.append('department_id', String(form.department_id))
  if (form.folder_id !== null) payload.append('folder_id', String(form.folder_id))
  if (selectedFile.value) payload.append('file', selectedFile.value)
  return payload
}

async function handleSubmit(): Promise<void> {
  const title = form.title.trim()
  if (!title || form.department_id === null || submitting.value) return

  if (props.editingFile === null && !selectedFile.value) {
    uploadError.value = 'File wajib dipilih.'
    return
  }

  submitting.value = true
  uploadError.value = ''

  try {
    const ok = props.editingFile
      ? await updateFile(props.editingFile.id, selectedFile.value ? buildCreatePayload() : {
          title,
          department_id: form.department_id,
          folder_id: form.folder_id,
        })
      : await uploadFile(buildCreatePayload())

    if (ok) emit('success')
    else uploadError.value = error.value
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <BaseModal
    :show="show"
    :title="editingFile ? 'Edit File' : 'Upload File'"
    @close="emit('close')"
  >
    <p v-if="uploadError" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
      {{ uploadError }}
    </p>

    <form @submit.prevent="handleSubmit" class="mt-4 space-y-4">
      <input
        v-model="form.title"
        type="text"
        placeholder="Title"
        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder-gray-400"
      />

      <select
        v-model="form.department_id"
        class="w-full rounded border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
      >
        <option :value="null" disabled>Pilih Department</option>
        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
          {{ dept.name }}
        </option>
      </select>

      <div
        class="cursor-pointer rounded-lg border-2 border-dashed border-gray-300 p-6 text-center transition hover:border-blue-500 hover:bg-blue-50/50 dark:border-gray-600 dark:hover:border-blue-400 dark:hover:bg-blue-900/20"
        @dragover.prevent
        @drop.prevent="onDrop"
        @click="fileInput?.click()"
      >
        <input ref="fileInput" type="file" class="hidden" @change="onFileChange" />

        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
        </svg>

        <p v-if="selectedFile" class="mt-2 text-sm font-medium text-blue-600 dark:text-blue-400">
          {{ selectedFile.name }}
        </p>
        <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">
          Tarik & letakkan file di sini, atau klik untuk memilih
        </p>
      </div>

      <div class="flex justify-end gap-2">
        <button
          type="button"
          @click="emit('close')"
          class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
        >
          Batal
        </button>
        <button
          type="submit"
          :disabled="!form.title.trim() || form.department_id === null || submitting"
          class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-50"
        >
          {{ submitting ? 'Menyimpan...' : editingFile ? 'Simpan' : 'Unggah' }}
        </button>
      </div>
    </form>
  </BaseModal>
</template>
