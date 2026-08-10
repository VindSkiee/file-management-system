<script setup lang="ts">
defineProps<{
  show: boolean
  title: string
  size?: 'md' | 'lg'
}>()

const emit = defineEmits<{
  close: []
}>()
</script>

<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="emit('close')"></div>

      <div
        :class="size === 'lg' ? 'max-w-3xl' : 'max-w-md'"
        class="relative max-h-[85vh] w-full overflow-y-auto rounded-lg bg-white p-6 shadow-lg transition-colors duration-200 dark:bg-gray-800"
      >
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ title }}</h3>

        <div class="mt-4">
          <slot />
        </div>
      </div>
    </div>
  </Teleport>
</template>
