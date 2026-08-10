<script setup lang="ts">
defineProps<{
  show: boolean
  title: string
}>()

const emit = defineEmits<{
  close: []
}>()
</script>

<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="emit('close')"></div>

      <div class="relative w-full max-w-md rounded-lg bg-white p-6 shadow-lg">
        <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>

        <div class="mt-4">
          <slot />
        </div>
      </div>
    </div>
  </Teleport>
</template>
