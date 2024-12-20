<template>
    <span :class="badgeClass">
      <slot></slot>
    </span>
  </template>
  
  <script setup lang="ts">
  import { defineProps, computed } from 'vue';
  
  const props = defineProps({
    variant: {
      type: String,
      default: 'primary',
      validator: (value: string) => ['primary', 'success', 'secondary', 'warning', 'danger'].includes(value),
    },
  });
  
  const badgeClass = computed(() => {
    const baseClasses = 'inline-flex items-center px-4 py-1 text-xs font-medium text-white rounded-full';
    switch (props.variant) {
      case 'primary':
        return `${baseClasses} bg-blue-600`;
      case 'success':
        return `${baseClasses} bg-green-600`;
      case 'secondary':
        return `${baseClasses} bg-gray-600`;
      case 'warning':
        return `${baseClasses} bg-yellow-400`;
      case 'danger':
        return `${baseClasses} bg-red-600`;
      default:
        return baseClasses; 
    }
  });
  </script>
  
  <style scoped>
  /* You can add additional styles here if needed */
  </style>