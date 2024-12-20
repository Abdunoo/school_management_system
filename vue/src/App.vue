<script setup lang="ts">
import { ref } from 'vue';
import Sidebar from './components/Sidebar.vue';
import Header from './components/Header.vue';

const isSidebarVisible = ref(true);

const toggleSidebar = () => {
  isSidebarVisible.value = !isSidebarVisible.value;
};
</script>

<template>
  <div class="flex flex-col h-screen bg-gray-100">
    <Header class="mb-8" :toggleSidebar="toggleSidebar" />

    <div class="h-full flex overflow-hidden relative">
      <!-- Sidebar dengan Transisi Geser -->
      <transition name="slide" appear>
        <Sidebar
          v-show="isSidebarVisible"
          class="w-64 h-full absolute inset-y-0 transform"
        />
      </transition>

      <!-- Konten Utama dengan Transisi Geser -->
      <main
        :class="isSidebarVisible ? 'ml-72' : 'ml-0'"
        class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 rounded-3xl transition-all duration-300 ease-in-out"
      >
        <router-view></router-view>
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
  @apply -translate-x-full;
  /* Sidebar masuk dari kiri */
}

.slide-enter-to {
  @apply translate-x-0;
  /* Sidebar berada di posisi normal */
}

.slide-leave-from {
  @apply translate-x-0;
  /* Sidebar keluar dari posisi normal */
}

.slide-leave-to {
  @apply -translate-x-full;
  /* Sidebar keluar ke kiri */
}
</style>
