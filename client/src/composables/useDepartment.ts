import { ref } from 'vue'
import api from '@/services/api'
import { extractErrorMessage } from '@/utils/error'

export interface Department {
  id: number
  name: string
  created_at: string
  updated_at: string
  deleted_at: string | null
}

export interface DepartmentInput {
  name: string
}

export function useDepartment() {
  const departments = ref<Department[]>([])
  const isLoading = ref(false)
  const error = ref('')

  async function getDepartments(withTrashed = false): Promise<void> {
    isLoading.value = true
    error.value = ''

    try {
      const params = withTrashed ? { trashed: true } : {}
      const { data } = await api.get<{ data: Department[] }>('/departments', { params })
      departments.value = data.data
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memuat daftar department.')
    } finally {
      isLoading.value = false
    }
  }

  async function createDepartment(input: DepartmentInput): Promise<boolean> {
    error.value = ''

    try {
      await api.post('/departments', input)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal menambah department.')
      return false
    }
  }

  async function updateDepartment(id: number, input: DepartmentInput): Promise<boolean> {
    error.value = ''

    try {
      await api.put(`/departments/${id}`, input)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memperbarui department.')
      return false
    }
  }

  async function deleteDepartment(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.delete(`/departments/${id}`)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal menghapus department.')
      return false
    }
  }

  async function restoreDepartment(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.post(`/departments/${id}/restore`)
      return true
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memulihkan department.')
      return false
    }
  }

  return {
    departments,
    isLoading,
    error,
    getDepartments,
    createDepartment,
    updateDepartment,
    deleteDepartment,
    restoreDepartment,
  }
}
