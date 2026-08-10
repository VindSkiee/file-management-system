<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const navItems = [
  { label: 'Dashboard', to: { name: 'dashboard' } },
  { label: 'Folders', to: { name: 'folders' } },
  { label: 'Departments', to: { name: 'departments' } },
]

async function handleLogout(): Promise<void> {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="flex min-h-screen bg-gray-100">
    <aside class="hidden w-64 flex-col bg-gray-900 text-white md:flex">
      <div class="flex h-16 items-center border-b border-gray-800 px-6">
        <h1 class="text-lg font-bold">FMS</h1>
      </div>

      <nav class="flex-1 space-y-1 p-4">
        <router-link
          v-for="item in navItems"
          :key="item.label"
          :to="item.to"
          class="block rounded px-3 py-2 text-sm font-medium transition"
          :class="route.name === item.to.name ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'"
        >
          {{ item.label }}
        </router-link>
      </nav>

      <div class="border-t border-gray-800 p-4">
        <p class="text-sm font-medium text-white">{{ authStore.user?.name }}</p>
        <p class="mt-0.5 text-xs text-gray-400">
          {{ authStore.isAdmin ? 'Administrator' : authStore.isViewer ? 'Viewer' : '' }}
        </p>
        <button
          @click="handleLogout"
          class="mt-3 w-full rounded bg-red-600 py-2 text-sm font-medium transition hover:bg-red-700"
        >
          Logout
        </button>
      </div>
    </aside>

    <div class="flex flex-1 flex-col">
      <header class="flex h-16 items-center justify-between bg-white px-6 shadow md:hidden">
        <h1 class="text-lg font-bold text-gray-800">FMS</h1>
        <button @click="handleLogout" class="text-sm font-medium text-red-600 hover:underline">
          Logout
        </button>
      </header>

      <nav class="flex space-x-2 overflow-x-auto border-b bg-white px-4 py-2 md:hidden">
        <router-link
          v-for="item in navItems"
          :key="item.label"
          :to="item.to"
          class="shrink-0 rounded px-3 py-1.5 text-sm font-medium transition"
          :class="route.name === item.to.name ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
        >
          {{ item.label }}
        </router-link>
      </nav>

      <main class="flex-1 p-4 md:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>
