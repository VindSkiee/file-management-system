<script setup lang="ts">
import { computed } from 'vue'
import BaseModal from '@/components/BaseModal.vue'
import { useFile, type FileItem } from '@/composables/useFile'

const props = defineProps<{
  show: boolean
  file: FileItem | null
}>()

const emit = defineEmits<{
  close: []
}>()

const { downloadFile, error } = useFile()

const previewType = computed<'image' | 'pdf' | null>(() => {
  const name = props.file?.file_name.toLowerCase() ?? ''

  if (/\.(png|jpe?g|gif|webp)$/.test(name)) return 'image'
  if (name.endsWith('.pdf')) return 'pdf'

  return null
})

async function handleDownload(): Promise<void> {
  if (props.file) await downloadFile(props.file)
}
</script>

<template>
  <BaseModal :show="show" title="File Detail" @close="emit('close')">
    <p v-if="error" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600">
      {{ error }}
    </p>

    <dl v-if="file" class="mt-4 space-y-3 text-sm">
      <div class="flex justify-between gap-4">
        <dt class="text-gray-500">Folder</dt>
        <dd class="font-medium text-gray-800">{{ file.folder?.name ?? 'Root' }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="text-gray-500">Nama File</dt>
        <dd class="max-w-[60%] truncate font-medium text-gray-800">{{ file.file_name }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="text-gray-500">Title</dt>
        <dd class="font-medium text-gray-800">{{ file.title }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="text-gray-500">Department</dt>
        <dd class="font-medium text-gray-800">{{ file.department?.name ?? '-' }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="text-gray-500">Uploaded By</dt>
        <dd class="font-medium text-gray-800">{{ file.uploaded_by ?? '-' }}</dd>
      </div>
      <div class="flex justify-between gap-4">
        <dt class="text-gray-500">Upload Date</dt>
        <dd class="font-medium text-gray-800">{{ file.upload_date }}</dd>
      </div>
    </dl>

    <div v-if="file && previewType" class="mt-5">
      <img
        v-if="previewType === 'image'"
        :src="file.file_url"
        :alt="file.title"
        class="mx-auto max-h-72 rounded border border-gray-200"
      />
      <iframe
        v-else
        :src="file.file_url"
        title="File Preview"
        class="h-72 w-full rounded border border-gray-200"
      ></iframe>
    </div>

    <div class="mt-6 flex justify-end gap-2">
      <button
        type="button"
        @click="emit('close')"
        class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300"
      >
        Tutup
      </button>
      <button
        type="button"
        @click="handleDownload"
        class="inline-flex items-center gap-1 rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Download File
      </button>
    </div>
  </BaseModal>
</template>
