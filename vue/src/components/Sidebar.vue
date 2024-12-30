<script setup lang="ts">
import {
  HomeIcon,
  UserCircleIcon,
  UserGroupIcon,
  AcademicCapIcon,
  CalendarIcon,
  ClipboardDocumentListIcon,
  XMarkIcon,
  BellIcon,
  UserCircleIcon as AdminIcon,
  BookOpenIcon
} from '@heroicons/vue/24/outline';
import { onMounted, onUnmounted, ref } from 'vue';
const menuItems = [
  { name: 'Dashboard', icon: HomeIcon, route: '/' },
  { name: 'Guru', icon: UserCircleIcon, route: '/teachers' },
  { name: 'Siswa', icon: UserGroupIcon, route: '/students' },
  { name: 'Kelas', icon: AcademicCapIcon, route: '/classes' },
  { name: 'Mata Pelajaran', icon: BookOpenIcon, route: '/subjects' },
  { name: 'Jadwal', icon: CalendarIcon, route: '/schedule' },
  { name: 'Laporan', icon: ClipboardDocumentListIcon, route: '/reports' },
  { name: 'Users', icon: UserGroupIcon, route: '/users' }
];

// New bottom menu items
const bottomMenuItems = [
  { name: 'Notifikasi', icon: BellIcon, route: '/notifications' },
  { name: 'Admin', icon: AdminIcon, route: '/admin' }
];

const isMobileOrTablet = ref(false);

// Function to check screen size
const checkScreenSize = () => {
  isMobileOrTablet.value = window.innerWidth <= 768; // Adjust the width as needed for tablet
};

// Event listeners for resizing
onMounted(() => {
  checkScreenSize();
  window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize);
});

defineProps<{
  toggleSidebar: () => void;
}>();
</script>

<template>
  <aside class="w-52 lg:w-64 bg-white border-r rounded-r-3xl">
    <!-- Logo -->
    <div class="p-4 border-b flex items-center justify-between">
      <img 
        src="../assets/sms.webp" 
        alt="High-Resolution Logo" 
        class="
          w-10 h-10 
          lg:w-12 lg:h-12 
          rounded-full 
          object-cover 
          border-2 border-primary 
        "
        >
      <XMarkIcon class="w-5 h-5 lg:hidden" @click="toggleSidebar" />
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-2">
      <router-link 
        v-for="item in menuItems" 
        :key="item.name" 
        :to="item.route" 
        class="sidebar-link" 
        @click="isMobileOrTablet ? toggleSidebar() : null"
      >
        <component :is="item.icon" class="w-5 h-5" />
        {{ item.name }}
      </router-link>
    </nav>

    <!-- Bottom Menu -->
    <div class="mt-auto p-4 border-t">
      <nav class="space-y-2">
        <router-link 
          v-for="item in bottomMenuItems" 
          :key="item.name" 
          :to="item.route" 
          class="sidebar-link" 
          @click="isMobileOrTablet ? toggleSidebar() : null"
        >
          <component :is="item.icon" class="w-5 h-5" />
          {{ item.name }}
        </router-link>
      </nav>
    </div>
  </aside>
</template>

<style scoped>
.sidebar-link {
  @apply flex items-center gap-2 px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors;
}
</style>