<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import Sidebar from './components/Sidebar.vue';
import Header from './components/Header.vue';

const isSidebarVisible = ref(false);

const toggleSidebar = () => {
  isSidebarVisible.value = !isSidebarVisible.value;
};

const isMobile = ref(window.innerWidth <= 1024);

const updateIsMobile = () => {
  isMobile.value = window.innerWidth <= 1024;
};

onMounted(() => {
  window.addEventListener('resize', updateIsMobile);
});

onUnmounted(() => {
  window.removeEventListener('resize', updateIsMobile);
});
</script>

<template>
  <div class="flex flex-col h-screen bg-gray-100">
    <!-- Header -->
    <Header class="mb-8" :toggleSidebar="toggleSidebar" />

    <!-- Wrapper Konten -->
    <div class="h-full flex overflow-hidden relative">
      <!-- Sidebar dengan Transisi -->
      <transition name="slide" appear>
        <Sidebar
          v-show="isSidebarVisible"
          :toggleSidebar="toggleSidebar"
          class="w-64 h-full inset-y-0 transform z-20 bg-white"
          :class=" !isMobile ? 'absolute' : 'fixed'"
        />
      </transition>

      <!-- Overlay -->
      <div
        v-if="isSidebarVisible && isMobile"
        class="fixed inset-0 bg-black bg-opacity-50 z-10"
        @click="toggleSidebar"
      ></div>

      <!-- Konten Utama -->
      <main
        :class="isSidebarVisible && !isMobile ? 'ml-72' : 'ml-0'"
        class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 rounded-3xl transition-all duration-300 ease-in-out"
      >
        <router-view />
      </main>
    </div>
  </div>
</template>

<style>
/* Transisi Geser */
.slide-enter-active,
.slide-leave-active {
  @apply transition-transform duration-300 ease-in-out;
}

.slide-enter-from {
  transform: translateX(-100%);
}

.slide-enter-to {
  transform: translateX(0);
}

.slide-leave-from {
  transform: translateX(0);
}

.slide-leave-to {
  transform: translateX(-100%);
}
</style>
