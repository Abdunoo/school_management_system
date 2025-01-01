import apiClient from '@/helpers/axios';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useScheduleConflictsStore = defineStore('scheduleConflicts', () => {

    const conflicts = ref([]);
    const showConflicts = ref(false);
    const conflictTitle = ref('');
    const conflictDescription = ref('');

    const checkConflicts = async () => {
        await apiClient.get('/api/class-schedule-conflicts').then((response) => {
            conflicts.value = response.data;
            // showConflict('Conflicts Found', 'There are conflicts in the schedule. Please resolve them before saving.');
            if (conflicts.value.length > 0) {
                showConflicts.value = true;
            } else {
                showConflicts.value = false;
            }
        })
    };

    const showConflict = (title: string, description: string) => {
        conflictTitle.value = title;
        conflictDescription.value = description;
        showConflicts.value = true;
    };

    const closeConflict = () => {
        showConflicts.value = false;
    };

    return {
        conflicts,
        showConflicts,
        conflictTitle,
        conflictDescription,
        showConflict,
        closeConflict,
        checkConflicts,
    };

});