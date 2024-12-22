<template>
  <TransitionRoot as="template" :show="visible" @close="closeModal">
    <Dialog class="relative z-30" @close="closeModal">
      <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
        leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 bg-gray-900/75 transition-opacity" />
      </TransitionChild>

      <!-- Modal Content -->
      <div class="fixed inset-0 z-10 w-screen h-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:items-center sm:p-0">
          <TransitionChild as="template" enter="ease-out duration-300"
            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
            leave-from="opacity-100 translate-y-0 sm:scale-100"
            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <DialogPanel
              class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full h-full sm:my-8 sm:max-w-lg">
              <!-- Modal Header -->
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                  <div v-if="icon"
                    class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                    <component :is="icon" class="h-6 w-6 text-red-600" aria-hidden="true" />
                  </div>
                  <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                    <DialogTitle as="h3" class="text-base font-semibold text-gray-900">
                      {{ title }}
                    </DialogTitle>
                    <div v-if="description" class="mt-2">
                      <p class="text-sm text-gray-500">{{ description }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Modal Body -->
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 h-full overflow-y-auto">
                <slot></slot>
              </div>

              <!-- Modal Footer -->
              <div class="bg-gray-50 px-4 py-3 space-y-3 md:space-y-0 sm:flex sm:flex-row-reverse sm:px-6">
                <button v-if="confirmButtonText" @click="confirmAction"
                  class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">
                  {{ confirmButtonText }}
                </button>
                <button v-if="cancelButtonText" @click="closeModal"
                  class="inline-flex w-full justify-center rounded-md bg-gray-200 px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300 sm:w-auto">
                  {{ cancelButtonText }}
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>


<script setup lang="ts">
import { ref } from "vue";
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from "@headlessui/vue";
// import { emit } from "process";

defineProps({
  visible: { type: Boolean, required: true },
  title: { type: String, default: "" },
  description: { type: String, default: "" },
  icon: { type: Object, default: null }, // Ex: Import and pass `ExclamationTriangleIcon`
  confirmButtonText: { type: String, default: "Add" },
  cancelButtonText: { type: String, default: "Cancel" },
});

const emit = defineEmits(["close", "confirm"]);

const closeModal = () => {
  emit("close");
};

const confirmAction = () => {
  emit("confirm");
};
</script>

<style scoped>
/* Add custom styles if needed */
</style>