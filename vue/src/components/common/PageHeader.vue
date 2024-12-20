<script setup lang="ts">
defineProps<{
  title: string;
}>();
</script>

<template>
  <div class="flex justify-between items-center">
    <h1 class="text-2xl font-semibold text-gray-800">{{ title }}</h1>
    <div class="flex space-x-4">
      <!-- Action buttons will go here -->
    </div>
  </div>
</template>