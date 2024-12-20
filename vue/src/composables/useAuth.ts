import { ref } from 'vue';
import type { User } from '../types';

export function useAuth() {
  const currentUser = ref<User | null>(null);
  const isAuthenticated = ref(false);

  // Auth logic will go here

  return {
    currentUser,
    isAuthenticated,
  };
}