<template>
    <div class="mb-4">
        <label :for="id" class="block text-sm font-medium text-secondary mb-2">{{ label }}</label>

        <input v-if="type === 'text'" :id="id" :value="modelValue" :placeholder="placeholder" :required="required"
            @input="$emit('update:modelValue', $event.target?.value)"
            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />

        <Listbox v-if="type === 'select'" as="div" :model="modelValue">
            <div class="relative mt-2">
                <ListboxButton class="grid w-full cursor-default grid-cols-1 rounded-md bg-white py-1.5 pl-3 pr-2 text-left text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                    <span class="col-start-1 row-start-1 flex items-center gap-3 pr-6">
                        <span class="block truncate">{{ modelValue ? modelValue : placeholder }}</span>
                    </span>
                    <ChevronUpDownIcon class="col-start-1 row-start-1 size-5 self-center justify-self-end text-gray-500 sm:size-4" aria-hidden="true" />
                </ListboxButton>
                
                <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <ListboxOptions class="absolute z-10 mt-1 max-h-56 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm">
                        <ListboxOption v-for="option in options" :key="option.value" :value="option.value" v-slot="{ active, selected }" @click="$emit('update:modelValue', option.label)">
                            
                            <li :class="[active ? 'bg-indigo-600 text-white' : 'text-gray-900', 'relative cursor-default select-none py-2 pl-3 pr-9']">
                                <span :class="[selected ? 'font-semibold' : 'font-normal', 'ml-3 block truncate']">{{ option.label }}</span>
                                <span v-if="selected" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600">
                                    <CheckIcon class="size-5" aria-hidden="true" />
                                </span>
                            </li>
                        </ListboxOption>
                    </ListboxOptions>
                </transition>
            </div>
        </Listbox>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { ChevronUpDownIcon } from '@heroicons/vue/16/solid'
import { CheckIcon } from '@heroicons/vue/20/solid'

defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' }, // 'text', 'select', etc.
    options: { type: Array, default: () => [] }, // For 'select' type
    placeholder: { type: String, default: '' },
    required: { type: Boolean, default: false },
    modelValue: [String, Number], // :model value
});

const emit = defineEmits(['update:modelValue']);
</script>