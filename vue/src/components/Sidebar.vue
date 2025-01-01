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
  BookOpenIcon,
  ChevronRightIcon,
  BellAlertIcon,
} from '@heroicons/vue/24/outline';
import { onMounted, onUnmounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useScheduleConflictsStore } from '../stores/scheduleConflicts';

const route = useRoute();
const scheduleConflictsStore = useScheduleConflictsStore();

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

const bottomMenuItems = [
  { name: 'Notifikasi', icon: BellIcon, route: '/notifications' },
  { name: 'Admin', icon: AdminIcon, route: '/admin' }
];

const isMobileOrTablet = ref(false);

// Check if route is active
const isRouteActive = (menuRoute: string) => {
  if (menuRoute === '/') {
    return route.path === '/';
  }
  return route.path.startsWith(menuRoute);
};

const checkScreenSize = () => {
  isMobileOrTablet.value = window.innerWidth <= 768;
};

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
  <aside class="flex flex-col h-full bg-white border-r rounded-r-3xl shadow-sm">
    <!-- Header -->
    <div class="p-4 flex items-center justify-between border-b">
      <div class="flex items-center space-x-3">
        <img src="../assets/sms.webp" alt="Logo" class="w-8 h-8 rounded-full object-cover border-2 border-primary">
        <span class="font-semibold text-gray-800 text-sm">SMS Admin</span>
      </div>
      <button class="p-1 hover:bg-gray-100 rounded-full lg:hidden" @click="toggleSidebar">
        <XMarkIcon class="w-5 h-5 text-gray-500" />
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4">
  <div class="mb-4">
    <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
      Menu Utama
    </h3>
  </div>

  <div class="space-y-1">
    <router-link
      v-for="item in menuItems"
      :key="item.name"
      :to="item.route"
      :class="[
        'sidebar-link group',
        isRouteActive(item.route) ? 'bg-primary/10 text-primary' : 'text-gray-600'
      ]"
      @click="isMobileOrTablet ? toggleSidebar() : null"
    >
      <component
        :is="item.icon"
        :class="[
          'w-5 h-5',
          isRouteActive(item.route) ? 'text-primary' : 'text-gray-500 group-hover:text-gray-600'
        ]"
      />
      <span class="text-sm">{{ item.name }}</span>

      <ChevronRightIcon
        :class="[
          'w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition-opacity',
          isRouteActive(item.route) ? 'text-primary' : 'text-gray-400'
        ]"
      />

      <!-- Danger Notification Count Icon -->
      <div
        v-if="item.route === '/schedule' && scheduleConflictsStore.conflicts.length > 0"
        class="relative ml-auto"
      >
        <BellAlertIcon class="w-6 h-6 text-danger" />
        <div
          class="absolute -top-2 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-danger border-2 border-white rounded-full"
        >
          {{ scheduleConflictsStore.conflicts.length }}
        </div>
      </div>
    </router-link>
  </div>
</nav>

    <!-- Bottom Section -->
    <div class="border-t p-3">
      <div class="space-y-1">
        <router-link v-for="item in bottomMenuItems" :key="item.name" :to="item.route" :class="[
          'sidebar-link group',
          isRouteActive(item.route) ? 'bg-primary/10 text-primary' : 'text-gray-600'
        ]" @click="isMobileOrTablet ? toggleSidebar() : null">
          <component :is="item.icon" :class="[
            'w-5 h-5',
            isRouteActive(item.route) ? 'text-primary' : 'text-gray-500 group-hover:text-gray-600'
          ]" />
          <span class="text-sm">{{ item.name }}</span>
          <ChevronRightIcon :class="[
            'w-4 h-4 ml-auto opacity-0 group-hover:opacity-100 transition-opacity',
            isRouteActive(item.route) ? 'text-primary' : 'text-gray-400'
          ]" />
        </router-link>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.sidebar-link {
  @apply flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 hover:bg-gray-100 relative;
}

::-webkit-scrollbar {
  width: 4px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  @apply bg-gray-300 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
  @apply bg-gray-400;
}
</style>