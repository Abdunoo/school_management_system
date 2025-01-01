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
  <header class="bg-white border-b px-4 sm:px-6 py-3 rounded-b-3xl shadow-sm">
    <div class="flex items-center justify-between">
      <!-- Left Section: Logo and Sidebar Toggle -->
      <div class="flex items-center space-x-4">
        <!-- Sidebar Toggle Button -->
        <button
          @click="toggleSidebar"
          class="p-2 hover:bg-gray-100 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary"
        >
          <Bars3BottomLeftIcon class="w-6 h-6 text-gray-600" />
        </button>

        <!-- Logo -->
        <img
          src="../assets/sms.webp"
          alt="High-Resolution Logo"
          class="w-10 h-10 lg:w-12 lg:h-12 rounded-full object-cover border-2 border-primary shadow-sm"
        />

        <!-- App Name -->
        <p class="text-lg md:text-xl font-semibold text-gray-800">School Management System</p>
      </div>

      <!-- Right Section: Notifications and User Profile -->
      <div class="flex items-center space-x-4">
        <!-- Notification Button -->
        <button
          class="p-2 hover:bg-gray-100 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary"
        >
          <BellIcon class="w-6 h-6 text-gray-600" />
          <span class="sr-only">Notifications</span>
        </button>

        <!-- User Profile Button -->
        <button
          class="flex items-center space-x-2 hover:bg-gray-100 rounded-lg px-3 py-2 transition-colors focus:outline-none focus:ring-2 focus:ring-primary"
        >
          <UserCircleIcon class="w-6 h-6 text-gray-600" />
          <span class="hidden md:block text-sm text-gray-700">Admin</span>
        </button>
      </div>
    </div>
  </header>
</template>