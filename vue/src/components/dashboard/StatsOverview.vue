<script setup lang="ts">
import { ref, onMounted } from 'vue';
import apiClient from '@/helpers/axios';
import StatsCard from '../common/StatsCard.vue';

const stats = ref([
  { title: 'Total Students', value: 0, color: 'text-blue-600' },
  { title: 'Total Classes', value: 0, color: 'text-green-600' },
  { title: 'Total Teachers', value: 0, color: 'text-purple-600' },
  { title: 'Total Subjects', value: 0, color: 'text-red-600' },
  { title: 'Total Users', value: 0, color: 'text-yellow-600' }
]);

const fetchStats = async () => {
  try {
    const response = await apiClient.get('/api/dashboard/counts');
    const data = response.data;

    stats.value = [
      { title: 'Total Siswa', value: data.students, color: 'text-blue-600' },
      { title: 'Total Kelas', value: data.classes, color: 'text-green-600' },
      { title: 'Total Guru', value: data.teachers, color: 'text-purple-600' },
      { title: 'Total Mata Pelajaran', value: data.subjects, color: 'text-red-600' },
      { title: 'Total Users', value: data.users, color: 'text-yellow-600' }
    ];
  } catch (error) {
    console.error('Failed to fetch stats:', error);
  }
};

onMounted(() => {
  fetchStats();
});
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <StatsCard
      v-for="stat in stats"
      :key="stat.title"
      :title="stat.title"
      :value="stat.value"
      :color="stat.color"
    />
  </div>
</template>