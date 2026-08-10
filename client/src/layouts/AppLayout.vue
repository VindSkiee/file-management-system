<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useDarkMode } from '@/composables/useDarkMode'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { isDark, toggleDarkMode } = useDarkMode()

const navItems: { label: string; to: { name: string }; adminOnly?: boolean }[] = [
  { label: 'Dashboard', to: { name: 'dashboard' } },
  { label: 'Folders', to: { name: 'folders' } },
  { label: 'Departments', to: { name: 'departments' } },
  { label: 'Activity Log', to: { name: 'activity-logs' }, adminOnly: true },
]

async function handleLogout(): Promise<void> {
  await authStore.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <div class="flex min-h-screen bg-gray-100 transition-colors duration-200 dark:bg-gray-900">
    <aside class="hidden w-64 flex-col border-r border-gray-800 bg-gray-900 text-white md:flex">
      <div class="flex h-16 items-center border-b border-gray-800 px-6">
        <h1 class="text-lg font-bold">FMS</h1>
      </div>

      <nav class="flex-1 space-y-1 p-4">
        <template v-for="item in navItems" :key="item.label">
          <router-link
            v-if="!item.adminOnly || authStore.isAdmin"
            :to="item.to"
            class="block rounded px-3 py-2 text-sm font-medium transition"
            :class="route.name === item.to.name ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white'"
          >
            {{ item.label }}
          </router-link>
        </template>
      </nav>

      <div class="border-t border-gray-800 p-4">
        <div class="flex items-center justify-between gap-3">
          <div class="min-w-0">
            <p class="truncate text-sm font-medium text-white">{{ authStore.user?.name }}</p>
            <p class="mt-0.5 text-xs text-gray-400">
              {{ authStore.isAdmin ? 'Administrator' : authStore.isViewer ? 'Viewer' : '' }}
            </p>
          </div>
          <button
            type="button"
            class="shrink-0 rounded-lg bg-gray-800 p-2 text-gray-300 transition hover:bg-gray-700 hover:text-white"
            :title="isDark ? 'Mode terang' : 'Mode gelap'"
            @click="toggleDarkMode"
          >
            <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
            </svg>
          </button>
        </div>

        <button
          @click="handleLogout"
          class="mt-3 w-full rounded bg-red-600 py-2 text-sm font-medium transition hover:bg-red-700"
        >
          Logout
        </button>
      </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
      <div class="sticky top-0 z-30 md:hidden">
        <header class="flex h-16 items-center justify-between gap-3 bg-white px-4 shadow transition-colors duration-200 dark:bg-gray-800">
          <div class="min-w-0">
            <h1 class="text-lg font-bold leading-tight text-gray-800 dark:text-gray-100">FMS</h1>
            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
              {{ authStore.user?.name }}
              <template v-if="authStore.isAdmin || authStore.isViewer">
                · {{ authStore.isAdmin ? 'Administrator' : 'Viewer' }}
              </template>
            </p>
          </div>

          <div class="flex shrink-0 items-center gap-2">
            <button
              type="button"
              class="rounded-lg bg-gray-100 p-2 text-gray-600 transition hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
              :title="isDark ? 'Mode terang' : 'Mode gelap'"
              @click="toggleDarkMode"
            >
              <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
              </svg>
            </button>

            <button
              @click="handleLogout"
              class="shrink-0 rounded bg-red-50 px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50"
            >
              Logout
            </button>
          </div>
        </header>

        <nav class="flex space-x-2 overflow-x-auto border-b bg-white px-4 py-2 transition-colors duration-200 dark:border-gray-700 dark:bg-gray-800">
          <template v-for="item in navItems" :key="item.label">
            <router-link
              v-if="!item.adminOnly || authStore.isAdmin"
              :to="item.to"
              class="shrink-0 rounded px-3 py-1.5 text-sm font-medium transition"
              :class="route.name === item.to.name
                ? 'bg-blue-600 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'"
            >
              {{ item.label }}
            </router-link>
          </template>
        </nav>
      </div>

      <main class="flex-1 p-4 md:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>
