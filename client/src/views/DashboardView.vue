<script setup lang="ts">
import { onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useDashboard } from '@/composables/useDashboard'

const { stats, loading, error, fetchDashboardStats } = useDashboard()

onMounted(() => {
  fetchDashboardStats()
})
</script>

<template>
  <AppLayout>
    <h2 class="mb-6 text-2xl font-bold text-gray-800 transition-colors duration-200 dark:text-gray-100">Dashboard</h2>

    <p v-if="error" class="mb-4 rounded bg-red-50 px-4 py-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
      {{ error }}
    </p>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <div class="flex items-center justify-between rounded-lg bg-white p-6 shadow transition-colors duration-200 dark:bg-gray-800">
        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Folder</p>
          <div class="mt-1">
            <p v-if="loading" class="h-8 w-16 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></p>
            <p v-else class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ stats?.total_folders ?? 0 }}</p>
          </div>
        </div>
        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
          </svg>
        </span>
      </div>

      <div class="flex items-center justify-between rounded-lg bg-white p-6 shadow transition-colors duration-200 dark:bg-gray-800">
        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total File</p>
          <div class="mt-1">
            <p v-if="loading" class="h-8 w-16 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></p>
            <p v-else class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ stats?.total_files ?? 0 }}</p>
          </div>
        </div>
        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
        </span>
      </div>

      <div class="flex items-center justify-between rounded-lg bg-white p-6 shadow transition-colors duration-200 dark:bg-gray-800">
        <div>
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Department</p>
          <div class="mt-1">
            <p v-if="loading" class="h-8 w-16 animate-pulse rounded bg-gray-200 dark:bg-gray-700"></p>
            <p v-else class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ stats?.total_departments ?? 0 }}</p>
          </div>
        </div>
        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
          </svg>
        </span>
      </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
      <div class="border-b px-4 py-4 md:px-6 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">10 File Terbaru</h3>
      </div>

      <p v-if="loading" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Loading...</p>

      <p v-else-if="stats && stats.recent_files.length === 0" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
        Belum ada file yang diunggah.
      </p>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm md:min-w-full">
          <thead class="hidden md:table-header-group">
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Title</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Department</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Nama File</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Upload Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="file in stats?.recent_files"
              :key="file.id"
              class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 md:table-row md:px-0 md:py-0"
            >
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Title:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ file.title }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Department:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ file.department?.name ?? '-' }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Nama File:</span>
                <span class="font-medium text-gray-800 dark:text-gray-100">{{ file.file_name }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3 text-center">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Upload Date:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ file.upload_date }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
