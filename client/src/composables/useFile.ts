import { ref } from 'vue'
import api from '@/services/api'
import { extractErrorMessage } from '@/utils/error'

export interface FileItem {
  id: number
  title: string
  file_name: string
  folder: { id: number; name: string } | null
  department: { id: number; name: string } | null
  uploaded_by: string | null
  upload_date: string
  file_url: string
  download_url: string
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export interface FileUpdateInput {
  title: string
  department_id: number
  folder_id: number | null
}

export function useFile() {
  const files = ref<FileItem[]>([])
  const fileDetail = ref<FileItem | null>(null)
  const isLoading = ref(false)
  const error = ref('')

  async function getFiles(
    folderId: number | null = null,
    search = '',
    departmentId: number | null = null,
    withTrashed = false,
  ): Promise<void> {
    isLoading.value = true
    error.value = ''

    try {
      const params: Record<string, string | number | boolean> = {}

      if (folderId !== null) params.folder_id = folderId
      if (search.trim() !== '') params.search = search.trim()
      if (departmentId !== null) params.department_id = departmentId
      if (withTrashed) params.trashed = true

      const { data } = await api.get<{ data: FileItem[] }>('/files', { params })
      files.value = data.data
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memuat file.')
    } finally {
      isLoading.value = false
    }
  }

  async function uploadFile(formData: FormData): Promise<boolean> {
    error.value = ''

    try {
      await api.post('/files', formData)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal mengunggah file.')
      return false
    }
  }

  async function updateFile(id: number, data: FormData | FileUpdateInput): Promise<boolean> {
    error.value = ''

    try {
      await api.put(`/files/${id}`, data)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memperbarui file.')
      return false
    }
  }

  async function deleteFile(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.delete(`/files/${id}`)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal menghapus file.')
      return false
    }
  }

  async function restoreFile(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.post(`/files/${id}/restore`)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memulihkan file.')
      return false
    }
  }

  async function downloadFile(file: FileItem): Promise<boolean> {
    error.value = ''

    try {
      const response = await api.get(`/files/${file.id}/download`, { responseType: 'blob' })

      const url = URL.createObjectURL(response.data)
      const link = document.createElement('a')
      link.href = url
      link.download = file.file_name
      document.body.appendChild(link)
      link.click()
      link.remove()
      URL.revokeObjectURL(url)

      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal mengunduh file.')
      return false
    }
  }

  return {
    files,
    fileDetail,
    isLoading,
    error,
    getFiles,
    uploadFile,
    updateFile,
    deleteFile,
    restoreFile,
    downloadFile,
  }
}
