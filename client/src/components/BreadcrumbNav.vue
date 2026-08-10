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
  <nav class="flex flex-wrap items-center gap-1 text-sm text-gray-600" aria-label="Breadcrumb">
    <template v-for="(item, index) in items" :key="item.id ?? 'root'">
      <button
        v-if="index < items.length - 1"
        type="button"
        class="transition hover:text-blue-600 hover:underline"
        @click="emit('navigate', item)"
      >
        {{ item.name }}
      </button>

      <span v-else class="font-semibold text-gray-800">{{ item.name }}</span>

      <svg
        v-if="index < items.length - 1"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke="currentColor"
        class="h-3.5 w-3.5 text-gray-400"
      >
        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
      </svg>
    </template>
  </nav>
</template>
