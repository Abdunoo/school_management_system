<template>
    <div v-if="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <!-- Spinner -->
        <div class="relative w-16 h-16">
            <div class="absolute w-12 h-12 border-4 border-t-transparent border-success rounded-full animate-spin-fast">
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useLoadingStore } from "@/stores/loadingStore";
import { computed } from "vue";

const loadingStore = useLoadingStore();

const isLoading = computed(() => loadingStore.isLoading);
</script>

<style scoped>
@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

.animate-spin-fast {
    animation: spin 0.5s linear infinite;
}
</style>