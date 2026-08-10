<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { isAxiosError } from 'axios'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function handleSubmit(): Promise<void> {
  error.value = ''
  loading.value = true

  try {
    await authStore.login({ email: email.value, password: password.value })
    router.push({ name: 'dashboard' })
  } catch (err: unknown) {
    if (isAxiosError(err)) {
      error.value = err.response?.data?.message ?? 'Kredensial tidak valid.'
    } else {
      error.value = 'Terjadi kesalahan. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-100">
    <form @submit.prevent="handleSubmit" class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
      <h1 class="mb-6 text-center text-2xl font-bold text-gray-800">FMS Login</h1>

      <p v-if="error" class="mb-4 rounded bg-red-50 px-3 py-2 text-sm text-red-600">
        {{ error }}
      </p>

      <input
        v-model="email"
        type="email"
        required
        placeholder="Email"
        autocomplete="email"
        class="mb-4 w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
      />

      <input
        v-model="password"
        type="password"
        required
        placeholder="Password"
        autocomplete="current-password"
        class="mb-6 w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
      />

      <button
        type="submit"
        :disabled="loading"
        class="w-full rounded bg-blue-600 py-2 font-medium text-white transition hover:bg-blue-700 disabled:opacity-50"
      >
        {{ loading ? 'Memproses...' : 'Masuk' }}
      </button>
    </form>
  </div>
</template>
