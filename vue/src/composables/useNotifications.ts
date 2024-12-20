import { ref } from 'vue';

export function useNotifications() {
  const notifications = ref([]);
  const unreadCount = ref(0);

  // Notification logic will go here

  return {
    notifications,
    unreadCount,
  };
}