import { ref } from 'vue'
import { isAxiosError } from 'axios'
import api from '@/services/api'

export interface RecentFile {
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
}

export interface DashboardStats {
  total_folders: number
  total_files: number
  total_departments: number
  recent_files: RecentFile[]
}

export function useDashboard() {
  const stats = ref<DashboardStats | null>(null)
  const loading = ref(false)
  const error = ref('')

  async function fetchDashboardStats(): Promise<void> {
    loading.value = true
    error.value = ''

    try {
      const { data } = await api.get<DashboardStats>('/dashboard')
      stats.value = data
    } catch (err: unknown) {
      if (isAxiosError(err)) {
        error.value = err.response?.data?.message ?? 'Gagal memuat data dashboard.'
      } else {
        error.value = 'Terjadi kesalahan. Silakan coba lagi.'
      }
    } finally {
      loading.value = false
    }
  }

  return { stats, loading, error, fetchDashboardStats }
}
