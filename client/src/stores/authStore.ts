import { computed, ref } from 'vue'
import { defineStore } from 'pinia'
import api, { clearToken, setToken } from '@/services/api'

export interface User {
  id: number
  name: string
  email: string
  role_id: number
  role_name: string | null
  created_at?: string
  updated_at?: string
}

interface LoginResponse {
  message: string
  access_token: string
  token_type: string
  user: User
}

interface UserResponse {
  user: User
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)

  const isAuthenticated = computed(() => user.value !== null)
  // Backend UserResource flattens role.name into role_name.
  const isAdmin = computed(() => user.value?.role_name === 'Administrator')
  const isViewer = computed(() => user.value?.role_name === 'Viewer')

  async function login(credentials: { email: string; password: string }): Promise<void> {
    const { data } = await api.post<LoginResponse>('/login', credentials)

    setToken(data.access_token)
    user.value = data.user
  }

  async function logout(): Promise<void> {
    try {
      await api.post('/logout')
    } finally {
      clearToken()
      user.value = null
    }
  }

  async function fetchUser(): Promise<void> {
    const { data } = await api.get<UserResponse>('/user')

    user.value = data.user
  }

  return {
    user,
    isAuthenticated,
    isAdmin,
    isViewer,
    login,
    logout,
    fetchUser,
  }
})
