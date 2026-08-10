<script setup lang="ts">
import { computed, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useActivityLog } from '@/composables/useActivityLog'

const { logs, loading, error, page, lastPage, getLogs } = useActivityLog()

const badgeClasses: Record<string, string> = {
  created: 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300',
  updated: 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
  deleted: 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-300',
  restored: 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300',
}

function badgeClass(action: string): string {
  return badgeClasses[action] ?? 'bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
}

function formatDate(value: string): string {
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value

  const pad = (n: number): string => String(n).padStart(2, '0')
  const day = pad(date.getDate())
  const month = date.toLocaleString('id-ID', { month: 'short' })
  const time = `${pad(date.getHours())}:${pad(date.getMinutes())}`

  return `${day} ${month} ${date.getFullYear()} ${time}`
}

const canPrev = computed(() => page.value > 1)
const canNext = computed(() => page.value < lastPage.value)

function goTo(nextPage: number): void {
  if (nextPage < 1 || nextPage > lastPage.value) return
  getLogs(nextPage)
}

onMounted(() => {
  getLogs(1)
})
</script>

<template>
  <AppLayout>
    <h2 class="mb-6 text-2xl font-bold text-gray-800 transition-colors duration-200 dark:text-gray-100">
      Activity Log
    </h2>

    <p v-if="error" class="mb-4 rounded bg-red-50 px-4 py-3 text-sm text-red-600 dark:bg-red-900/30 dark:text-red-300">
      {{ error }}
    </p>

    <div class="overflow-hidden rounded-lg bg-white shadow transition-colors duration-200 dark:bg-gray-800">
      <p v-if="loading" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Loading...</p>

      <p v-else-if="logs.length === 0" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
        Belum ada aktivitas tercatat.
      </p>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm md:min-w-full">
          <thead class="hidden md:table-header-group">
            <tr class="bg-gray-50 dark:bg-gray-700">
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-left">Tanggal/Waktu</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Pengguna</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Tipe Entitas</th>
              <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Nama Entitas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="log in logs"
              :key="log.id"
              class="block px-4 py-3 transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 md:table-row md:px-0 md:py-0"
            >
              <td class="block py-1 md:table-cell md:px-6 md:py-3">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Tanggal:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ formatDate(log.created_at) }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3 text-center">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Pengguna:</span>
                <span class="font-medium text-gray-800 dark:text-gray-100">{{ log.user?.name ?? '-' }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3 text-center">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Aksi:</span>
                <span
                  class="inline-block rounded px-2 py-0.5 text-xs font-semibold capitalize"
                  :class="badgeClass(log.action)"
                >
                  {{ log.action }}
                </span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3 text-center">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Tipe:</span>
                <span class="text-gray-700 dark:text-gray-200">{{ log.entity_type }}</span>
              </td>
              <td class="block py-1 md:table-cell md:px-6 md:py-3 text-center">
                <span class="mr-2 inline font-medium text-gray-500 dark:text-gray-400 md:hidden">Nama:</span>
                <span class="break-words text-gray-700 dark:text-gray-200">{{ log.entity_name }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 border-t px-4 py-3 md:px-6 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Halaman {{ page }} dari {{ lastPage }}
        </p>
        <div class="flex gap-2">
          <button
            type="button"
            :disabled="!canPrev"
            @click="goTo(page - 1)"
            class="rounded bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-300 disabled:opacity-40 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
          >
            Sebelumnya
          </button>
          <button
            type="button"
            :disabled="!canNext"
            @click="goTo(page + 1)"
            class="rounded bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700 disabled:opacity-40"
          >
            Berikutnya
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
