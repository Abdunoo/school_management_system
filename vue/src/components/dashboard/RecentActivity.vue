<template>
  <div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
    </div>
    <div class="p-6">
      <ul>
        <li v-for="activity in recentActivities" :key="activity.id" class="mb-4">
          <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
              <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                <i class="fas fa-user"></i>
              </div>
            </div>
            <div>
              <div class="text-gray-800 font-medium">{{ activity.user }}</div>
              <div class="text-gray-600">{{ activity.action }}</div>
              <div class="text-gray-400 text-sm">{{ activity.timestamp }}</div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';

interface Activity {
  id: number;
  user: string;
  action: string;
  timestamp: string;
}

const recentActivities = ref<Activity[]>([
  { id: 1, user: 'John Doe', action: 'Added a new student', timestamp: '2023-10-01 10:00:00' },
  { id: 2, user: 'Jane Smith', action: 'Updated class schedule', timestamp: '2023-10-01 09:30:00' },
  { id: 3, user: 'Alice Johnson', action: 'Deleted a subject', timestamp: '2023-09-30 15:45:00' },
  { id: 4, user: 'Bob Brown', action: 'Added a new teacher', timestamp: '2023-09-30 14:20:00' },
  { id: 5, user: 'Charlie Davis', action: 'Updated user profile', timestamp: '2023-09-29 11:10:00' }
]);

onMounted(() => {
  // Fetch recent activities from the API if needed
  fetchRecentActivities();
});

// Dummy function to simulate fetching recent activities from an API
const fetchRecentActivities = async () => {
  // Simulate an API call
  await new Promise(resolve => setTimeout(resolve, 1000));
  // Update recentActivities with fetched data
  recentActivities.value = [
    { id: 6, user: 'David Evans', action: 'Added a new class', timestamp: '2023-10-02 08:00:00' },
    { id: 7, user: 'Eva Green', action: 'Updated subject details', timestamp: '2023-10-01 16:30:00' }
  ];
};
</script>

<style scoped>
/* Add any additional styling here */
</style>