<template>
  <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
    <!-- Page Controls -->
    <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
      <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
      <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-2">
          <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
          <select id="perPage" v-model="perPage"
            class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
            <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
          </select>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
          <Button variant="success">Export</Button>
          <div class="relative flex items-center">
            <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search schedules"
              class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />
            <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:hidden gap-4">
      <div v-if="classSchedules.length === 0" class="text-center py-4 text-secondary">No schedules found.</div>

      <div v-for="schedule in classSchedules" :key="schedule.id"
        class="relative bg-gray-50 rounded-lg p-4 shadow hover:bg-gray-100 transition">
        <div class="space-y-2">
          <h3 class="text-lg font-bold text-secondary">{{ schedule.name }}</h3>
          <p class="text-sm text-secondary">Tahun Akademik: {{ schedule.academic_year }}</p>
          <p class="text-sm text-secondary">Wali Kelas: {{ schedule.homeroom_teacher }}</p>
          <p class="text-sm text-secondary">Total Mata Pelajaran: {{ schedule.total_subjects }}</p>
          <p class="text-sm text-secondary">Total Durasi: {{ schedule.total_duration }} hours</p>
        </div>

        <!-- Tombol Edit di pojok kanan bawah -->
        <div class="absolute bottom-2 right-2">
          <Button variant="warning">
            <RouterLink :to="{ name: 'DetailSchedule', params: { name: schedule.name } }">Edit</RouterLink>
          </Button>
        </div>
      </div>
    </div>

    <!-- Class Schedule Table (Desktop View) -->
    <div class="rounded-xl border border-gray-300 bg-gray-50 max-h-full hidden lg:block">
      <table class="min-w-full table-auto text-sm text-left">
        <thead>
          <tr class="bg-gray-100 grid grid-cols-6 rounded-xl">
            <th class="px-4 py-3 text-secondary">Class Name</th>
            <th class="px-4 py-3 text-secondary">Tahun Ajaran</th>
            <th class="px-4 py-3 text-secondary">Wali Kelas</th>
            <th class="px-4 py-3 text-secondary">Total Mata Pelajaran</th>
            <th class="px-4 py-3 text-secondary">Total Durasi</th>
            <th class="px-4 py-3 text-secondary">Actions</th>
          </tr>
        </thead>
      </table>
    </div>

    <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full hidden lg:block ">
      <table class="min-w-full table-auto text-sm text-left ">
        <tbody class="divide-y divide-gray-200 ">
          <tr v-if="classSchedules.length === 0">
            <td colspan="5" class="text-center py-4 text-secondary">No schedules found.</td>
          </tr>
          <tr v-for="schedule in classSchedules" :key="schedule.id"
            class="hover:bg-gray-100 transition grid grid-cols-6 items-center">
            <td class="px-4 py-3 text-secondary">{{ schedule.name }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.academic_year }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.homeroom_teacher }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.total_subjects }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.total_duration }} Jam Pelajaran</td>
            <td class="px-4 py-3 text-secondary">
              <Button variant="warning">
                <RouterLink :to="{ name: 'DetailSchedule', params: { name: schedule.name } }">Edit</RouterLink>
              </Button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import Button from '@/components/common/Button.vue';
import Pagination from '@/components/common/Pagination.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import apiClient from '@/helpers/axios';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '@/stores/modalStore';
import { ClassSchedule } from '@/types/index';

const API_ENDPOINTS = {
  CLASS_SCHEDULES: '/api/class-schedules',
  CLASSES: '/api/classes',
  TEACHERS: '/api/teachers',
  SUBJECTS: '/api/subjects',
};

const loadingStore = useLoadingStore();
const modalStore = useModalStore();

const pageTitle = ref('Jadwal Pembelajaran');
const classSchedules = ref<ClassSchedule[]>([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));

const fetchClassSchedules = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.CLASS_SCHEDULES, {
      params: { per_page: perPage.value, page: currentPage.value, search: searchQuery.value },
    });
    classSchedules.value = data.data;
    totalRecords.value = data.total;
  } catch (error: any) {
    console.error('Failed to fetch class schedules:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

onMounted(() => {
  fetchClassSchedules();
});

watch([perPage, currentPage, searchQuery], () => {
  fetchClassSchedules();
});
</script>