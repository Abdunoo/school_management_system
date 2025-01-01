<template>
    <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
        <!-- Page Controls -->
        <div class="flex justify-between bg-gray-100 p-4 rounded-lg">
            <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>

            <!-- Desktop Menu -->
            <div class="hidden sm:flex sm:space-x-2">
                <Button variant="primary" @click="openModal('add')">Tambah Jadwal</Button>
                <Button variant="success">Export</Button>
                <Button variant="primary" @click="saveAllSchedules">Save All</Button>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div class="sm:hidden relative">
                <button @click="toggleDropdown"
                    class="p-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary">
                    <Bars3BottomLeftIcon class="w-6 h-6 text-gray-600" />
                </button>

                <!-- Dropdown Content -->
                <div v-if="isDropdownOpen"
                    class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    <div class="p-2 space-y-2">
                        <Button variant="primary" class="w-full justify-start" @click="openModal('add')">
                            Tambah Jadwal
                        </Button>
                        <Button variant="success" class="w-full justify-start" @click="exportSchedules">
                            Export
                        </Button>
                        <Button variant="primary" class="w-full justify-start" @click="saveAllSchedules">
                            Save All
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-1 overflow-x-auto">
            <!-- Lesson Hours Column -->
            <div class="flex-shrink-0 w-20 pt-20">
                <div class="space-y-4 mt-8">
                    <div v-for="hour in lessonHours" :key="hour" :class="{
                        'bg-primary-100 border-primary': isLessonHourHighlighted(hour)
                    }"
                        class="h-20 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200 shadow-sm transition-all duration-200">
                        <span class="text-sm font-medium text-gray-700">Jam ke-{{ hour }}</span>
                    </div>
                </div>
            </div>

            <!-- Schedule Columns -->
            <div class="flex-1 ml-4">
                <div class="flex gap-6 min-w-max">
                    <div v-for="(column, index) in columns" :key="index" :data-index="index"
                        class="column bg-gray-50 border border-gray-200 rounded-lg shadow-lg p-6 flex flex-col flex-1">
                        <!-- Column Title -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 mb-6">
                            <h2 class="text-lg font-semibold text-center text-gray-700">{{ column.name }}</h2>
                        </div>

                        <!-- Draggable Area -->
                        <draggable v-model="column.schedules" :group="{ name: 'schedules' }" item-key="id"
                            handle=".handle" class="flex flex-col gap-4 min-h-[200px]" ghost-class="ghost"
                            @end="onDragEnd">
                            <template #item="{ element: schedule }">
                                <div :id="`${schedule.id}`" @click="openModal('edit', schedule)"
                                    @mouseenter="setHoveredSchedule(schedule)" @mouseleave="clearHoveredSchedule"
                                    :style="{ height: `${schedule.duration * (5 + 1) - 1}rem` }"
                                    class="relative p-4 bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md hover:border-primary transition-all duration-200 cursor-pointer">
                                    <!-- Top Right Badge -->
                                    <span class="handle absolute top-2 right-2">
                                        <Bars3Icon class="w-4 h-4" />
                                    </span>

                                    <!-- Schedule Details -->
                                    <div class="space-y-2 mt-2">
                                        <h3 class="text-base font-medium text-gray-800 line-clamp-2">
                                            {{ schedule.subject.name }}
                                        </h3>
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-600 flex items-center gap-2">
                                                <UserIcon class="w-4 h-4" />
                                                {{ schedule.teacher.user.username }}
                                            </p>
                                            <p class="text-sm text-gray-600 flex items-center gap-2">
                                                <CalendarIcon class="w-4 h-4" />
                                                {{ schedule.day }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </draggable>

                        <!-- Empty State -->
                        <div v-if="column.schedules.length === 0"
                            class="flex items-center justify-center h-32 text-gray-400 text-sm border-2 border-dashed border-gray-300 rounded-lg">
                            <div class="text-center">
                                <CalendarIcon class="w-6 h-6 mx-auto mb-2" />
                                <p>No schedules yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add/Edit Schedule Modal -->
        <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
            <form @submit.prevent="handleFormSubmit">
                <!-- Kelas (Class) Selection -->
                <div class="mb-4">
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Kelas</label>
                    <v-select id="class_id" class="text-gray-500" v-model="selectedClass" disabled label="name" required
                        placeholder="Pilih Kelas" />
                    <div v-if="formErrors.class_id" class="mt-1 text-sm text-red-500">{{ formErrors.class_id }}</div>
                </div>

                <!-- Mata Pelajaran (Subject) Selection -->
                <div class="mb-4">
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <v-select id="subject_id" v-model="form.subject_id" :options="subjects"
                        @input="searchSubject($event.target.value)" :reduce="(option: any) => option.id" label="name"
                        placeholder="Pilih Mata Pelajaran" required class="text-gray-500" :open-on-focus="false"
                        :class="formErrors.subject_id ? 'border-red-500' : 'border-gray-300'"
                        aria-invalid="formErrors.subject_id ? 'true' : 'false'" />
                    <div v-if="formErrors.subject_id" class="mt-1 text-sm text-red-500">{{ formErrors.subject_id }}
                    </div>
                </div>

                <!-- Guru (Teacher) Selection -->
                <div class="mb-4">
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700">Guru</label>
                    <v-select id="teacher_id" v-model="form.teacher_id" :options="teacherOptions"
                        @input="searchTeacher($event.target.value)" :reduce="(option: any) => option.value"
                        label="label" placeholder="Pilih Guru" required class="text-gray-500" :open-on-focus="false" />
                    <div v-if="formErrors.teacher_id" class="mt-1 text-sm text-red-500">{{ formErrors.teacher_id }}
                    </div>
                </div>

                <!-- Hari (Day) Selection -->
                <div class="mb-4">
                    <label for="day" class="block text-sm font-medium text-gray-700">Hari</label>
                    <v-select id="day" class="text-gray-500" v-model="form.day" :options="dayOptions" label="label"
                        :reduce="(option: any) => option.value" required placeholder="Pilih Hari"
                        :open-on-focus="false" />
                    <div v-if="formErrors.day" class="mt-1 text-sm text-red-500">{{ formErrors.day }}</div>
                </div>

                <!-- Jam Pelajaran (Lesson Hour) Input -->
                <div class="mb-4">
                    <label for="lesson_hours" class="block text-sm font-medium text-gray-700">Jam Pelajaran Ke</label>
                    <input type="number" id="lesson_hours" v-model="form.lesson_hours" placeholder="1"
                        class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-gray-400"
                        required />
                    <div v-if="formErrors.lesson_hours" class="mt-1 text-sm text-red-500">{{ formErrors.lesson_hours }}
                    </div>
                </div>

                <!-- Durasi (Duration) Input -->
                <div class="mb-4">
                    <label for="duration" class="block text-sm font-medium text-gray-700">Durasi</label>
                    <input type="number" id="duration" v-model="form.duration" placeholder="1"
                        class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-gray-400"
                        required />
                    <div v-if="formErrors.duration" class="mt-1 text-sm text-red-500">{{ formErrors.duration }}</div>
                </div>
            </form>
        </Modal>

    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch, onUnmounted } from 'vue';
import draggable from 'vuedraggable';
import debounce from 'lodash.debounce';
import { from } from 'rxjs';
import apiClient from '@/helpers/axios';
import { useModalStore } from '@/stores/modalStore';
import { useLoadingStore } from '@/stores/loadingStore';
import { ClassScheduleItem, ClassItem, Subject, Teacher } from '@/types';
import router from '@/router';
import Button from '@/components/common/Button.vue';
import Modal from '@/components/common/Modal.vue';
import { switchMap } from 'rxjs/operators';
import { UserIcon, CalendarIcon, Bars3Icon, Bars3BottomLeftIcon } from '@heroicons/vue/24/outline';

const className = router.currentRoute.value.params.name;
const pageTitle = ref(`Jadwal Pembelajaran ${className} `);
const API_ENDPOINTS = {
    DETAIL_CLASS_SCHEDULES: '/api/class-schedules/by-class/' + className,
    CLASS_SCHEDULES: '/api/class-schedules',
    CLASSES: '/api/classes',
    TEACHERS: '/api/teachers',
    SUBJECTS: '/api/subjects',
    BULK_UPDATE: '/api/class-schedules/bulk-update',
    TEACHER_BY_SUBJECT: '/api/teachers/by-subject-id/',
};

const loadingStore = useLoadingStore();
const modalStore = useModalStore();
const selectedClass = ref<ClassItem | null>(null);

interface Column {
    name: string;
    schedules: ClassScheduleItem[];
}

const lessonHours = ref<number[]>([]);

const addLessonHour = (count: number) => {
    for (let i = 0; i < count; i++) {
        const nextHour = lessonHours.value.length + 1;
        lessonHours.value.push(nextHour);
    }
};

// Helper function to map API data to columns
const mapScheduleToColumns = (data: any[]) => {
    const daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    const columns = daysOfWeek.map((day) => ({
        name: day,
        schedules: data
            .filter((item) => item.day === day)
            .map((item) => ({
                id: item.id,
                class_id: item.class_id,
                subject_id: item.subject_id,
                teacher_id: item.teacher_id,
                day: item.day,
                class: item.class,
                subject: item.subject,
                teacher: item.teacher,
                lesson_hours: item.lesson_hours,
                duration: item.duration,
                time: `${item.lesson_hours}:00 - ${item.lesson_hours + item.duration}:00`,
            })),
    }));
    return columns;
};

const fetchClassSchedules = debounce(() => {
    loadingStore.show();
    from(apiClient.get(API_ENDPOINTS.DETAIL_CLASS_SCHEDULES)).pipe(
        switchMap(response => {
            const data = response.data;
            columns.value = mapScheduleToColumns(data.schedules);
            lessonHours.value = data.list_lesson_hours;
            selectedClass.value = data.schedules[0]?.class;
            return [];
        })
    ).subscribe({
        error: (error: any) => {
            console.error('Failed to fetch class schedules:', error);
            modalStore.showError('Error', error.response.data.message);
            loadingStore.hide();
        },
        complete: () => loadingStore.hide()
    });
}, 500);

// Initialize columns
const columns = ref<Column[]>([]);

// Modal state
const showModal = ref(false);
const modalTitle = ref('');
const teachers = ref<Teacher[]>([]);
const subjects = ref<Subject[]>([]);
const form = ref<ClassScheduleItem>({
    id: null,
    class_id: selectedClass.value?.id,
    subject_id: null,
    teacher_id: null,
    day: null,
    lesson_hours: null,
    duration: null,
    class: {},
    subject: {},
    teacher: {},
});

const formErrors = ref<{ [key: string]: string }>({});

const dayOptions = [
    { label: 'Senin', value: 'Senin' },
    { label: 'Selasa', value: 'Selasa' },
    { label: 'Rabu', value: 'Rabu' },
    { label: 'Kamis', value: 'Kamis' },
    { label: 'Jumat', value: 'Jumat' },
    { label: 'Sabtu', value: 'Sabtu' },
    { label: 'Minggu', value: 'Minggu' },
];

const searchTeacher = debounce((query) => {
    loadingStore.show();
    from(apiClient.get(API_ENDPOINTS.TEACHERS, {
        params: {
            search: query,
            per_page: 5,
        },
    })).pipe(
        switchMap(response => {
            teachers.value = response.data.data;
            return [];
        })
    ).subscribe({
        error: (error: any) => {
            console.error('Failed to fetch teachers:', error);
            modalStore.showError('Error', error.response.data.message);
            loadingStore.hide();
        },
        complete: () => loadingStore.hide()
    });
}, 500);

const searchSubject = debounce((query) => {
    loadingStore.show();
    from(apiClient.get(API_ENDPOINTS.SUBJECTS, {
        params: {
            search: query,
            per_page: 5
        },
    })).pipe(
        switchMap(response => {
            subjects.value = response.data.data;

            return [];
        })
    ).subscribe({
        error: (error: any) => {
            console.error('Failed to fetch subjects:', error);
            modalStore.showError('Error', error.response.data.message);
            loadingStore.hide();
        },
        complete: () => loadingStore.hide()
    });
}, 500);
const teacherOptions = computed(() => {
    const seen = new Set(); // Use Set to track unique values
    return teachers.value
        .map((teacher) => ({
            label: teacher.user?.username,
            value: teacher.id,
        }))
        .filter((teacherOption) => {
            if (seen.has(teacherOption.value)) {
                return false; // Skip duplicate values
            }
            seen.add(teacherOption.value);
            return true; // Keep unique values
        });
});

const fetchTeachers = (subjectId: number) => {
    apiClient.get(API_ENDPOINTS.TEACHER_BY_SUBJECT + subjectId).then((response) => {
        teachers.value = response.data;
    }).catch((error) => {
        console.error('Failed to fetch teachers:', error);
        modalStore.showError('Error', error.response.data.message);
    });
};


const openModal = (action: 'add' | 'edit', schedule?: ClassScheduleItem) => {
    isDropdownOpen.value = false;
    modalTitle.value = action === 'add' ? 'Tambah Jadwal' : 'Edit Jadwal';
    showModal.value = true;
    if (action === 'edit' && schedule) {
        subjects.value = schedule.subject ? [{ id: schedule.subject_id, name: schedule.subject.name }] : [];
        teachers.value = schedule.teacher && schedule.teacher.user ? [{ id: schedule.teacher_id, user: { username: schedule.teacher.user.username } }] : [];
        form.value = {
            id: schedule.id,
            class_id: schedule.class_id,
            subject_id: schedule.subject_id,
            teacher_id: schedule.teacher_id,
            day: schedule.day,
            lesson_hours: schedule.lesson_hours,
            duration: schedule.duration,
            class: schedule.class,
            subject: schedule.subject,
            teacher: schedule.teacher,
        };
    } else {
        form.value = resetForm();
    }
};

watch(() => form.value.subject_id, (newSubjectId) => {
    if (newSubjectId) {
        fetchTeachers(newSubjectId);
    }
});

const onDragEnd = (event: any) => {
    const { to, from, newIndex } = event;

    // Get column indices
    const toColumnIndex = parseInt(to.closest('.column').dataset.index);
    const fromColumnIndex = parseInt(from.closest('.column').dataset.index);

    // Recalculate lesson_hours for the source column
    let sourceAccumulatedHours = 1;
    columns.value[fromColumnIndex].schedules.forEach((schedule) => {
        schedule.lesson_hours = sourceAccumulatedHours;
        sourceAccumulatedHours += schedule.duration ?? 0;
    });

    const draggedSchedule = columns.value[toColumnIndex].schedules[newIndex];
    // Update the dragged schedule's day to the target column's day
    draggedSchedule.day = columns.value[toColumnIndex].name;

    // Recalculate lesson_hours for the target column
    let targetAccumulatedHours = 1;
    columns.value[toColumnIndex].schedules.forEach((schedule) => {
        schedule.lesson_hours = targetAccumulatedHours;
        targetAccumulatedHours += schedule.duration ?? 0;
    });

    const latestLessonHours = lessonHours.value.length;
    if (targetAccumulatedHours > latestLessonHours) {
        if (typeof draggedSchedule.duration === 'number') {
            addLessonHour(draggedSchedule.duration);
        }
    }
};

// Helper function to check schedule overlaps
const isOverlapping = (draggedSchedule: any, existingSchedules: any[]): boolean => {
    const draggedStart = draggedSchedule.lesson_hours;
    const draggedEnd = draggedStart + draggedSchedule.duration;

    return existingSchedules.some(schedule => {
        if (schedule.id === draggedSchedule.id || schedule.day !== draggedSchedule.day) {
            return false;
        }

        const scheduleStart = schedule.lesson_hours;
        const scheduleEnd = scheduleStart + schedule.duration;

        return (draggedStart < scheduleEnd && draggedEnd > scheduleStart);
    });
};

// Reactive state for the hovered schedule
const hoveredSchedule = ref<ClassScheduleItem | null>(null);

// Set the hovered schedule
const setHoveredSchedule = (schedule: ClassScheduleItem) => {
    hoveredSchedule.value = schedule;
};

// Clear the hovered schedule
const clearHoveredSchedule = () => {
    hoveredSchedule.value = null;
};

// Check if a lesson hour should be highlighted
const isLessonHourHighlighted = (hour: number) => {
    if (!hoveredSchedule.value) return false;
    const { lesson_hours, duration } = hoveredSchedule.value;
    return lesson_hours !== null && lesson_hours !== undefined && duration !== null && duration !== undefined && hour >= lesson_hours && hour < lesson_hours + duration;
};

const resetForm = () => ({
    id: null,
    class_id: selectedClass.value?.id,
    subject_id: null,
    teacher_id: null,
    day: null,
    lesson_hours: null,
    duration: null,
    class: {},
    subject: {},
    teacher: {},
});

const resetModal = () => {
    showModal.value = false;
    form.value = resetForm();
};

const handleFormSubmit = debounce(async () => {
    formErrors.value = {};

    // Basic validation
    if (!form.value.class_id) formErrors.value.class_id = 'Kelas harus diisi';
    if (!form.value.subject_id) formErrors.value.subject_id = 'Mata Pelajaran harus diisi';
    if (!form.value.teacher_id) formErrors.value.teacher_id = 'Guru harus diisi';
    if (!form.value.day) formErrors.value.day = 'Hari harus diisi';
    if (!form.value.lesson_hours) formErrors.value.lesson_hours = 'Jam Pelajaran harus diisi';
    if (!form.value.duration) formErrors.value.duration = 'Durasi harus diisi';

    if (Object.keys(formErrors.value).length > 0) {
        return;
    }

    // Check for overlapping lesson hours
    const existingSchedules = columns.value.find(column => column.name === form.value.day)?.schedules || [];
    if (isOverlapping(form.value, existingSchedules)) {
        modalStore.showError('Error', 'Jadwal bertabrakan dengan jadwal yang sudah ada pada hari yang sama.');
        return;
    }

    // Proceed with form submission
    loadingStore.show();
    const endpoint = form.value.id ? `${API_ENDPOINTS.CLASS_SCHEDULES}/${form.value.id}` : API_ENDPOINTS.CLASS_SCHEDULES;
    const method = form.value.id ? 'put' : 'post';
    from(apiClient[method](endpoint, form.value)).subscribe({
        error: (error: any) => {
            console.error('Failed to submit form:', error);
            modalStore.showError('Error', error.response.data.message);
            loadingStore.hide();
        },
        complete: () => {
            fetchClassSchedules();
            resetModal();
            loadingStore.hide();
        }
    });
}, 500);

const saveAllSchedules = debounce(async () => {
    isDropdownOpen.value = false;
    loadingStore.show();
    const schedules = columns.value.flatMap(column => column.schedules);
    from(apiClient.post(`${API_ENDPOINTS.BULK_UPDATE}`, { schedules })).subscribe({
        error: (error: any) => {
            console.error('Failed to save all schedules:', error);
            modalStore.showError('Error', error.response.data.message);
            loadingStore.hide();
        },
        complete: () => loadingStore.hide()
    });
}, 500);

const isDropdownOpen = ref(false);

// Function to toggle dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Function to close dropdown when clicking outside
const closeDropdownOnClickOutside = (event: MouseEvent) => {
    const dropdown = document.querySelector('.relative');
    if (dropdown && !dropdown.contains(event.target as Node)) {
        isDropdownOpen.value = false;
    }
};

const exportSchedules = () => {
    console.log('Export schedules');
    isDropdownOpen.value = false; // Close dropdown after clicking
};


onMounted(() => {
    fetchClassSchedules();
    document.addEventListener('click', closeDropdownOnClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdownOnClickOutside);
});
</script>

<style scoped>
/* Add custom styles if needed */
.bg-primary-100 {
    background-color: #dbeafe;
    /* Light blue */
}

.border-primary {
    border-color: #3b82f6;
    /* Blue */
}
</style>