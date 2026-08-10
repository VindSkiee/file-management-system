<script setup lang="ts">
import { computed } from 'vue'
import BaseModal from '@/components/BaseModal.vue'
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

const previewType = computed(() => (props.file ? getPreviewType(props.file.file_name) : null))

async function handleDownload(): Promise<void> {
  if (props.file) await downloadFile(props.file)
}
</script>

<template>
  <BaseModal :show="show" title="Preview File" size="lg" @close="emit('close')">
    <p v-if="error" class="rounded bg-red-50 px-3 py-2 text-sm text-red-600">
      {{ error }}
    </p>

    <p v-if="file" class="mb-3 truncate text-sm font-medium text-gray-800">
      {{ file.file_name }}
    </p>

    <div
      v-if="file && previewType === 'image'"
      class="flex justify-center rounded border border-gray-200 bg-gray-50 p-2"
    >
      <img
        :src="file.file_url"
        :alt="file.title"
        class="max-h-[60vh] max-w-full rounded object-contain"
      />
    </div>

    <div v-else-if="file && previewType === 'pdf'" class="overflow-hidden rounded border border-gray-200">
      <iframe :src="file.file_url" title="File Preview" class="h-[60vh] w-full"></iframe>
    </div>

    <p v-else class="rounded border border-gray-200 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
      Preview tidak tersedia untuk format file ini.
    </p>

    <div class="mt-6 flex flex-wrap justify-end gap-2">
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
