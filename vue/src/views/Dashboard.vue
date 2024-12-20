<script setup lang="ts">
import PageHeader from '../components/common/PageHeader.vue';
import StatsOverview from '../components/dashboard/StatsOverview.vue';
import RecentActivity from '../components/dashboard/RecentActivity.vue';

const pageTitle = "Dashboard";
</script>

<template>
  <div class="space-y-6 p-6 bg-white">
    <PageHeader :title="pageTitle" />
    <div class="bg-primary text-white p-6 rounded-lg shadow-card">
      <h1 class="text-2xl font-bold">Welcome to the School Management System</h1>
      <p class="mt-2 text-neutral-light">
        Manage classes, students, and teachers efficiently.
      </p>
    </div>
    <StatsOverview />
    <RecentActivity />
  </div>
</template>