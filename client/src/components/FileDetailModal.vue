<script setup lang="ts">
import { computed, ref } from 'vue'
import BaseModal from '@/components/BaseModal.vue'
import FilePreviewModal from '@/components/FilePreviewModal.vue'
import { useFile, type FileItem } from '@/composables/useFile'
import { getPreviewType } from '@/utils/preview'

const props = defineProps<{
  show: boolean
  file: FileItem | null
}>()

const emit = defineEmits<{
  close: []
}>()

const { downloadFile, error } = useFile()

const showPreview = ref(false)
const previewType = computed(() => (props.file ? getPreviewType(props.file.file_name) : null))

function openPreview(): void {
  showPreview.value = true
}

function closePreview(): void {
  showPreview.value = false
}

async function handleDownload(): Promise<void> {
  if (props.file) await downloadFile(props.file)
}
</script>

<template>
  <BaseModal :show="show" title="File Detail" @close="emit('close')">
    <p v-if="error" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
      {{ error }}
    </p>

    <dl v-if="file" class="mt-4 space-y-3 text-sm">
      <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <dt class="text-gray-500 transition-colors duration-200 dark:text-gray-400">Folder</dt>
        <dd class="min-w-0 break-words font-medium text-gray-800 dark:text-gray-100">{{ file.folder?.name ?? 'Root' }}</dd>
      </div>
      <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <dt class="text-gray-500 transition-colors duration-200 dark:text-gray-400">Nama File</dt>
        <dd class="min-w-0 break-words font-medium text-gray-800 dark:text-gray-100 sm:max-w-[60%] sm:truncate">{{ file.file_name }}</dd>
      </div>
      <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <dt class="text-gray-500 transition-colors duration-200 dark:text-gray-400">Title</dt>
        <dd class="min-w-0 break-words font-medium text-gray-800 dark:text-gray-100">{{ file.title }}</dd>
      </div>
      <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <dt class="text-gray-500 transition-colors duration-200 dark:text-gray-400">Department</dt>
        <dd class="min-w-0 break-words font-medium text-gray-800 dark:text-gray-100">{{ file.department?.name ?? '-' }}</dd>
      </div>
      <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <dt class="text-gray-500 transition-colors duration-200 dark:text-gray-400">Uploaded By</dt>
        <dd class="min-w-0 break-words font-medium text-gray-800 dark:text-gray-100">{{ file.uploaded_by ?? '-' }}</dd>
      </div>
      <div class="flex flex-col gap-0.5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
        <dt class="text-gray-500 transition-colors duration-200 dark:text-gray-400">Upload Date</dt>
        <dd class="min-w-0 break-words font-medium text-gray-800 dark:text-gray-100">{{ file.upload_date }}</dd>
      </div>
    </dl>

    <div class="mt-6 flex flex-wrap justify-end gap-2">
      <button
        type="button"
        @click="emit('close')"
        class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
      >
        Tutup
      </button>
      <button
        v-if="previewType"
        type="button"
        @click="openPreview"
        class="inline-flex items-center gap-1 rounded bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        Preview File
      </button>
      <button
        type="button"
        @click="handleDownload"
        class="inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Unduh File
      </button>
    </div>

    <FilePreviewModal :show="showPreview" :file="file" @close="closePreview" />
  </BaseModal>
</template>
