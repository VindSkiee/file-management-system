<script setup lang="ts">
export interface BreadcrumbItem {
  id: number | null
  name: string
}

defineProps<{
  items: BreadcrumbItem[]
}>()

const emit = defineEmits<{
  navigate: [item: BreadcrumbItem]
}>()
</script>

<template>
  <nav
    class="flex min-w-0 flex-wrap items-center gap-1 text-sm text-gray-600 transition-colors duration-200 dark:text-gray-400"
    aria-label="Breadcrumb"
  >
    <template v-for="(item, index) in items" :key="item.id ?? 'root'">
      <button
        v-if="index < items.length - 1"
        type="button"
        class="max-w-[9rem] truncate transition hover:text-blue-600 hover:underline dark:hover:text-blue-400 sm:max-w-[14rem]"
        @click="emit('navigate', item)"
      >
        {{ item.name }}
      </button>

      <span v-else class="max-w-[9rem] truncate font-semibold text-gray-800 dark:text-gray-100 sm:max-w-[14rem]">
        {{ item.name }}
      </span>

      <svg
        v-if="index < items.length - 1"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke="currentColor"
        class="h-3.5 w-3.5 shrink-0 text-gray-400 dark:text-gray-500"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
      </svg>
    </template>
  </nav>
</template>
