<script setup lang="ts">
import StatsCard from '../common/StatsCard.vue';

const stats = [
  { title: 'Total Students', value: 1234, color: 'text-blue-600' },
  { title: 'Total Classes', value: 48, color: 'text-green-600' },
  { title: 'Total Teachers', value: 86, color: 'text-purple-600' }
];
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