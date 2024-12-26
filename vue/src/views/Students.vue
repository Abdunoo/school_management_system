<script setup lang="ts">
import { ref } from 'vue';
import PageHeader from '@/components/common/PageHeader.vue';

const pageTitle = ref('Students Management');
</script>

<template>
  <div class="space-y-6">
    <PageHeader :title="pageTitle" />
    <div class="bg-white rounded-lg shadow p-6">
      <!-- Student management content will go here -->
    </div>
  </div>
</template>