import { ref } from 'vue'
import api from '@/services/api'
import { extractErrorMessage } from '@/utils/error'

export interface ActivityLogItem {
  id: number
  action: 'created' | 'updated' | 'deleted' | 'restored'
  entity_type: string
  entity_name: string
  created_at: string
  user: { id: number; name: string; email: string } | null
}

export function useActivityLog() {
  const logs = ref<ActivityLogItem[]>([])
  const loading = ref(false)
  const error = ref('')
  const page = ref(1)
  const lastPage = ref(1)

  async function getLogs(nextPage = 1): Promise<void> {
    loading.value = true
    error.value = ''

    try {
      const { data } = await api.get<{
        data: ActivityLogItem[]
        meta: { last_page: number }
      }>('/activity-logs', { params: { page: nextPage } })

      logs.value = data.data
      page.value = nextPage
      lastPage.value = data.meta?.last_page ?? 1
    } catch (err: unknown) {
      error.value = extractErrorMessage(err, 'Gagal memuat activity log.')
    } finally {
      loading.value = false
    }
  }

  return { logs, loading, error, page, lastPage, getLogs }
}
