<script setup lang="ts">
import {
  BellIcon,
  UserCircleIcon,
  Bars3BottomLeftIcon
} from '@heroicons/vue/24/outline';

defineProps<{
  toggleSidebar: () => void;
}>();
</script>

<template>
  <header class="bg-white border-b px-4 sm:px-6 py-4 rounded-b-3xl">
    <div class="flex items-center justify-between flex-wrap">
      <div class="flex items-center space-x-4">
        <Bars3BottomLeftIcon class="w-6 h-6 text-gray-600 cursor-pointer" @click="toggleSidebar" />
        <p class="text-lg md:text-xl font-semibold text-gray-800">School Management System</p>
      </div>

      <div class="hidden md:flex items-center space-x-4 mt-2 ">
        <button class="p-2 hover:bg-gray-100 rounded-full">
          <BellIcon class="w-6 h-6 text-gray-600" />
        </button>
        <button class="flex items-center space-x-2 hover:bg-gray-100 rounded-lg px-3 py-2">
          <UserCircleIcon class="w-6 h-6 text-gray-600" />
          <span class="text-sm text-gray-700">Admin</span>
        </button>
      </div>
    </div>
  </header>
</template>