import { isAxiosError } from 'axios'

export function extractErrorMessage(err: unknown, fallback: string): string {
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
