<template>
  <button 
    :class="buttonClass" 
  >
    <slot></slot>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value: string) => ['primary', 'success', 'secondary', 'warning', 'danger'].includes(value),
  },
});

const buttonClass = computed(() => {
  const baseClasses = 'flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-md shadow transition';
  switch (props.variant) {
    case 'primary':
      return `${baseClasses} bg-sky-600 hover:bg-sky-500 focus:ring-2 focus:ring-sky-500`;
    case 'success':
      return `${baseClasses} bg-green-600 hover:bg-green-500 focus:ring-2 focus:ring-green-500`;
    case 'secondary':
      return `${baseClasses} bg-gray-600 hover:bg-gray-500 focus:ring-2 focus:ring-gray-500`;
    case 'warning':
      return `${baseClasses} bg-yellow-400 hover:bg-yellow-500 focus:ring-2 focus:ring-yellow-500`;
    case 'danger':
      return `${baseClasses} bg-red-600 hover:bg-red-500 focus:ring-2 focus:ring-red-500`;
    default:
      return baseClasses;
  }
});
</script>
