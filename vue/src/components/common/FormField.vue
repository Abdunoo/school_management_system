<template>
    <div class="mb-4">
        <label :for="id" class="block text-sm font-medium text-secondary mb-2">{{ label }}</label>

        <!-- General Input Types -->
        <input v-if="['text', 'number', 'email', 'password', 'date'].includes(type)" 
            :id="id" 
            :type="type" 
            :value="modelValue" 
            :placeholder="placeholder" 
            :required="required"
            @input="$emit('update:modelValue', type === 'number' ? $event.target.valueAsNumber : $event.target.value)"
            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />

        <!-- Select Dropdown -->
        <select v-if="type === 'select'" 
            :id="id" 
            :value="modelValue" 
            @change="$emit('update:modelValue', $event.target.value)"
            :required="required"
            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none">
            <!-- Placeholder option -->
            <option v-if="placeholder" value="" disabled selected>{{ placeholder }}</option>
            <!-- Options -->
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
    </div>
</template>

<script setup lang="ts">
defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { 
        type: String, 
        default: 'text', // Default type is 'text'
        validator: (value: string) => ['text', 'number', 'email', 'password', 'date', 'select'].includes(value) // Supported types
    },
    options: { 
        type: Array as () => Array<{ label: string, value: string | number }>, 
        default: () => [] 
    }, // For 'select' type, array of objects { label, value }
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    modelValue: [String, Number, Object], // Supports binding to various data types
});

defineEmits(['update:modelValue']);
</script>
