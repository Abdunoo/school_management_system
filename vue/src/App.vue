<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';
import Sidebar from '@/components/Sidebar.vue';
import Header from '@/components/Header.vue';
import LoadingIndicator from '@/components/LoadingIndicator.vue';
import Modal from './components/common/Modal.vue';
import { useModalStore } from './stores/modalStore';
import { useScheduleConflictsStore } from './stores/scheduleConflicts';

const isSidebarVisible = ref(false);
const isMobile = ref(window.innerWidth <= 1024);
const scheduleConflictsStore = useScheduleConflictsStore();

const toggleSidebar = () => {
  isSidebarVisible.value = !isSidebarVisible.value;
};

const updateIsMobile = () => {
  isMobile.value = window.innerWidth <= 1024;
};

const modalStore = useModalStore();

onMounted(() => {
  window.addEventListener('resize', updateIsMobile);
  if (!isMobile.value) {
    isSidebarVisible.value = true;
  }
  scheduleConflictsStore.checkConflicts();
});

onUnmounted(() => {
  window.removeEventListener('resize', updateIsMobile);
});
</script>

<template>
  <div class="flex flex-col h-screen bg-gray-100">
    <Header class="mb-4" :toggleSidebar="toggleSidebar" />

    <div class="h-full flex overflow-hidden relative">
      <transition name="slide" appear>
        <Sidebar v-show="isSidebarVisible" :toggleSidebar="toggleSidebar"
          class="h-full inset-y-0 transform z-40 bg-white" :class="!isMobile ? 'absolute' : 'fixed'" />
      </transition>

      <div v-if="isSidebarVisible && isMobile" class="fixed inset-0 bg-black bg-opacity-50 z-30" @click="toggleSidebar">
      </div>

      <main :class="isSidebarVisible && !isMobile ? 'ml-[228px]' : 'ml-0'"
        class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 rounded-3xl transition-all duration-300 ease-in-out">
        <router-view />
      </main>
    </div>
    <LoadingIndicator />
    <Modal
      :visible="modalStore.showErrorModal"
      :title="modalStore.errorTitle"
      :description="modalStore.errorDescription"
      :timestamp="modalStore.errorTimestamp"
      type="error"
      cancelButtonText="Close"
      @close="modalStore.closeErrorModal"
    />
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
