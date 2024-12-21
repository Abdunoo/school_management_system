<template>
    <nav class="flex items-center justify-between">
      <button
        :disabled="currentPage === 1"
        @click="onPageChange(currentPage - 1)"
        class="px-4 py-2 text-sm bg-gray-100 border rounded-l-md disabled:bg-gray-200 disabled:cursor-not-allowed"
      >
        Previous
      </button>
      <span class="px-4 py-2 text-sm">{{ currentPage }} / {{ totalPages }}</span>
      <button
        :disabled="currentPage === totalPages"
        @click="onPageChange(currentPage + 1)"
        class="px-4 py-2 text-sm bg-gray-100 border rounded-r-md disabled:bg-gray-200 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </nav>
  </template>
  
  <script setup lang="ts">
  defineProps({
    currentPage: { type: Number, required: true },
    totalPages: { type: Number, required: true },
  });
  
  const emit = defineEmits(['page-changed']);
  
  const onPageChange = (page: number) => {
    emit('page-changed', page);
  };
  </script>
  