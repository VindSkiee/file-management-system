import { ref } from 'vue'
import api from '@/services/api'
import { extractErrorMessage } from '@/utils/error'

export interface Folder {
  id: number
  name: string
  parent_id: number | null
  children: Folder[]
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export interface FolderInput {
  name: string
  parent_id?: number | null
}

export function useFolder() {
  const folders = ref<Folder[]>([])
  const isLoading = ref(false)
  const error = ref('')

  async function getFolders(parentId: number | null = null, withTrashed = false): Promise<void> {
    isLoading.value = true
    error.value = ''

    try {
      const params: Record<string, string | number | boolean> = {}

      if (parentId !== null) params.parent_id = parentId
      if (withTrashed) params.trashed = true

      const { data } = await api.get<{ data: Folder[] }>('/folders', { params })
      folders.value = data.data
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memuat folder.')
    } finally {
      isLoading.value = false
    }
  }

  async function createFolder(input: FolderInput): Promise<boolean> {
    error.value = ''

    try {
      await api.post('/folders', input)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal membuat folder.')
      return false
    }
  }

  async function updateFolder(id: number, input: FolderInput): Promise<boolean> {
    error.value = ''

    try {
      await api.put(`/folders/${id}`, input)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memperbarui folder.')
      return false
    }
  }

  async function deleteFolder(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.delete(`/folders/${id}`)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal menghapus folder.')
      return false
    }
  }

  async function restoreFolder(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.post(`/folders/${id}/restore`)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memulihkan folder.')
      return false
    }
  }

  return {
    folders,
    isLoading,
    error,
    getFolders,
    createFolder,
    updateFolder,
    deleteFolder,
    restoreFolder,
  }
}
