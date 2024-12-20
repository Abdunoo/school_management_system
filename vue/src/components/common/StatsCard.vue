<script setup lang="ts">
defineProps<{
  title: string;
  value: number | string;
  color?: string;
}>();
</script>

<template>
  <div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
    <p :class="`text-3xl font-bold mt-2 ${color || 'text-blue-600'}`">{{ value }}</p>
  </div>
</template>