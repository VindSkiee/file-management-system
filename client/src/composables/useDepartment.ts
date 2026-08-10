import { ref } from 'vue'
import { isAxiosError } from 'axios'
import api from '@/services/api'

export interface Department {
  id: number
  name: string
  created_at: string
  updated_at: string
}

export interface DepartmentInput {
  name: string
}

function extractError(err: unknown, fallback: string): string {
  if (isAxiosError(err)) {
    const status = err.response?.status
    const message = err.response?.data?.message
    const errors = err.response?.data?.errors as Record<string, string[]> | undefined

    if (status === 403) {
      return 'Anda tidak memiliki izin untuk melakukan aksi ini.'
    }

    if (status === 422) {
      const firstError = errors ? Object.values(errors)[0]?.[0] : undefined
      if (typeof firstError === 'string') return firstError
    }

    if (typeof message === 'string') return message
  }

  return fallback
}

export function useDepartment() {
  const departments = ref<Department[]>([])
  const isLoading = ref(false)
  const error = ref('')

  async function getDepartments(): Promise<void> {
    isLoading.value = true
    error.value = ''

    try {
      const { data } = await api.get<{ data: Department[] }>('/departments')
      departments.value = data.data
    } catch (err: unknown) {
      error.value = extractError(err, 'Gagal memuat daftar department.')
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
      error.value = extractError(err, 'Gagal menambah department.')
      return false
    }
  }

  async function updateDepartment(id: number, input: DepartmentInput): Promise<boolean> {
    error.value = ''

    try {
      await api.put(`/departments/${id}`, input)
      return true
    } catch (err: unknown) {
      error.value = extractError(err, 'Gagal memperbarui department.')
      return false
    }
  }

  async function deleteDepartment(id: number): Promise<boolean> {
    error.value = ''

    try {
      await api.delete(`/departments/${id}`)
      return true
    } catch (err: unknown) {
      error.value = extractError(err, 'Gagal menghapus department.')
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
  }
}
