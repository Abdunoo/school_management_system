<script setup lang="ts">
import {
  HomeIcon,
  UserCircleIcon,
  UserGroupIcon,
  AcademicCapIcon,
  CalendarIcon,
  ClipboardDocumentListIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline';

const menuItems = [
  { name: 'Dashboard', icon: HomeIcon, route: '/' },
  { name: 'Teachers', icon: UserCircleIcon, route: '/teachers' },
  { name: 'Students', icon: UserGroupIcon, route: '/students' },
  { name: 'Classes', icon: AcademicCapIcon, route: '/classes' },
  { name: 'Schedule', icon: CalendarIcon, route: '/schedule' },
  { name: 'Reports', icon: ClipboardDocumentListIcon, route: '/reports' }
];


defineProps<{
  toggleSidebar: () => void;
}>();

</script>

<template>
  <aside class="w-52 lg:w-64 bg-white border-r rounded-r-3xl">
    <!-- Logo -->
    <div class="p-4 border-b flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-800">Logo</h1>
      <XMarkIcon class="w-5 h-5 lg:hidden" @click="toggleSidebar" />
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-2">
      <router-link v-for="item in menuItems" :key="item.name" :to="item.route" class="sidebar-link">
        <component :is="item.icon" class="w-5 h-5" />
        {{ item.name }}
      </router-link>
    </nav>
  </aside>
</template>

<style scoped>
.sidebar-link {
  @apply flex items-center gap-2 px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors;
}
</style>