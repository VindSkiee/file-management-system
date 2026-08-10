<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

async function handleLogout(): Promise<void> {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="flex items-center justify-between bg-white px-6 py-4 shadow">
      <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
      <button @click="handleLogout" class="text-sm font-medium text-red-600 hover:underline">
        Logout
      </button>
    </header>

    <main class="p-6">
      <p class="text-gray-800">
        Selamat datang, <span class="font-semibold">{{ authStore.user?.name }}</span>
      </p>
      <p v-if="authStore.isAdmin" class="mt-1 text-sm font-medium text-green-600">Role: Administrator</p>
      <p v-else-if="authStore.isViewer" class="mt-1 text-sm font-medium text-blue-600">Role: Viewer</p>
    </main>
  </div>
</template>
