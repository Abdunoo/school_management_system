<template>
    <div class="mb-4">
        <label :for="id" class="block text-sm font-medium text-secondary mb-2">{{ label }}</label>

        <input v-if="type === 'text'" :id="id" :value="modelValue" :placeholder="placeholder" :required="required"
            @input="$emit('update:modelValue', $event.target?.value)"
            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />

        <select v-if="type === 'select'" :id="id" :value="modelValue" :required="required"
            @change="$emit('update:modelValue', $event.target?.value)"
            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 focus:outline-none">
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
    </div>
</template>


<script setup lang="ts">
//   import { defineProps, defineEmits } from 'vue';

defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' }, // 'text', 'select', etc.
    options: { type: Array, default: () => [] }, // For 'select' type
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    modelValue: [String, Number], // v-model value
});

const emit = defineEmits(['update:modelValue']);
</script>