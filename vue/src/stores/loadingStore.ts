import { defineStore } from "pinia";

export const useLoadingStore = defineStore("loading", {
  state: () => ({
    isLoading: false as boolean,
  }),
  actions: {
    show(): void {
      this.isLoading = true;
      console.log('show')
    },
    hide(): void {
      this.isLoading = false;
      console.log('hide')
    },
  },
});
