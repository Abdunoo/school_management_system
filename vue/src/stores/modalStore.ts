import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useModalStore = defineStore('modal', () => {
  const showErrorModal = ref(false);
  const errorTitle = ref('');
  const errorDescription = ref('');
  const errorTimestamp = ref('');

  const showError = (title: string, description: string) => {
    errorTitle.value = title;
    errorDescription.value = description;
    errorTimestamp.value = new Date().toLocaleString(); // Add timestamp
    showErrorModal.value = true;
  };

  const closeErrorModal = () => {
    showErrorModal.value = false;
  };

  return {
    showErrorModal,
    errorTitle,
    errorDescription,
    errorTimestamp,
    showError,
    closeErrorModal,
  };
});