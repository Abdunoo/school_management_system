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
                <div class="flex items-center">
                  <div v-if="icon"
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gray-100 sm:h-10 sm:w-10">
                    <component :is="icon" class="h-6 w-6" aria-hidden="true" :class="{
                      'text-red-500': type === 'error',
                      'text-yellow-500': type === 'confirm',
                      'text-blue-500': type === 'form'
                    }" />
                  </div>
                  <div class="ml-4 text-left">
                    <DialogTitle as="h3" class="text-base font-semibold text-gray-900">
                      {{ title }}
                    </DialogTitle>
                    <div v-if="description" class="mt-2">
                      <p class="text-sm text-gray-500">{{ description }}</p>
                    </div>
                    <div v-if="type === 'error'" class="mt-2">
                      <p class="text-sm text-gray-500">{{ timestamp }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Modal Body -->
              <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 h-full overflow-y-auto">
                <slot></slot>
              </div>

              <!-- Modal Footer -->
              <div class="px-4 py-3 flex space-x-4 w-full justify-end">
                <Button v-if="cancelButtonText" @click="closeModal" variant="secondary">
                  {{ cancelButtonText }}
                </Button>
                <Button v-if="type === 'confirm' || type === 'form'" @click="confirmAction" variant="primary">
                  {{ confirmButtonText }}
                </Button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup lang="ts">
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { ExclamationTriangleIcon, InformationCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';
import { computed, watch } from 'vue';
import Button from '@/components/common/Button.vue';

const props = defineProps({
  visible: { type: Boolean, required: true },
  title: { type: String, default: "" },
  description: { type: String, default: "" },
  type: { type: String, default: "form" },
  confirmButtonText: { type: String, default: "Save" },
  cancelButtonText: { type: String, default: "Cancel" },
  timestamp: { type: String, default: "" },
});

const emit = defineEmits(["close", "confirm"]);

const closeModal = () => {
  emit("close");
};

const confirmAction = () => {
  emit("confirm");
};

watch(() => props.timestamp, (newValue) => {
    console.log(newValue);
});

const icon = computed(() => {
  console.log(props.type);
  switch (props.type) {
    case 'confirm':
      return ExclamationTriangleIcon;
    case 'error':
      return XCircleIcon;
    case 'form':
      return InformationCircleIcon;
    default:
      return null;
  }
});
</script>