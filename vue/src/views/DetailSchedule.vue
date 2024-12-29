<template>
    <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
        <!-- Page Controls -->
        <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
            <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-2">
                    <Button variant="primary" @click="openModal('add')">Tambah Jadwal</Button>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <Button variant="success">Export</Button>
                    <Button variant="primary" @click="saveAllSchedules">Save All</Button>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap lg:flex-nowrap space-y-4 lg:space-y-0 lg:space-x-4">
            <!-- Right Panel (Schedules) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 w-full">
                <div v-for="(column, index) in columns" :key="index" :data-index="index"
                    class="column bg-gray-50 border border-gray-200 rounded-lg shadow-md p-4 flex flex-col">
                    <!-- Column Title -->
                    <div class="bg-white border border-gray-200 rounded-md shadow-sm p-4 mb-4">
                        <h2 class="text-lg font-semibold text-center text-gray-700">{{ column.name }}</h2>
                    </div>

                    <!-- Draggable Area -->
                    <draggable v-model="column.schedules" :group="{ name: 'schedules' }" item-key="id"
                        class="flex flex-col gap-4 min-h-[200px]" ghost-class="ghost" @end="onDragEnd">
                        <template #item="{ element: schedule }">
                            <div :id="`${schedule.id}`" @click="openModal('edit', schedule)"
                                class="relative h-36 p-4 bg-white border border-gray-200 rounded-md shadow-sm hover:shadow-md transition cursor-pointer">
                                <!-- Top Right Badge -->
                                <Badge class="absolute top-2 right-2" variant="primary">
                                    #{{ schedule.lesson_hours }}
                                </Badge>

                                <!-- Card Content -->
                                <p class="text-base font-medium text-gray-800">{{ schedule.subject.name }}</p>
                                <p class="text-sm text-gray-500">Guru: {{ schedule.teacher.user.username }}</p>
                                <p class="text-sm text-gray-500">Hari: {{ schedule.day }}</p>
                            </div>
                        </template>
                    </draggable>

                    <!-- Empty State -->
                    <div v-if="column.schedules.length === 0"
                        class="text-gray-400 text-sm text-center py-4 border-2 border-dashed border-gray-300 rounded-md">
                        No schedules
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
                    <v-select id="class_id" class="text-gray-500" v-model="selectedClass"
                         disabled label="name" required placeholder="Pilih Kelas" />
                    <div v-if="formErrors.class_id" class="mt-1 text-sm text-red-500">{{ formErrors.class_id }}</div>
                </div>

                <!-- Mata Pelajaran (Subject) Selection -->
                <div class="mb-4">
                    <label for="subject_id" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <v-select id="subject_id" v-model="form.subject_id" :options="subjects" @input="searchSubject($event.target.value)"
                        :reduce="(option) => option.id" label="name" placeholder="Pilih Mata Pelajaran" required
                        class="text-gray-500" :open-on-focus="false"
                        :class="formErrors.subject_id ? 'border-red-500' : 'border-gray-300'"
                        aria-invalid="formErrors.subject_id ? 'true' : 'false'" />
                    <div v-if="formErrors.subject_id" class="mt-1 text-sm text-red-500">{{ formErrors.subject_id }}
                    </div>
                </div>

                <!-- Guru (Teacher) Selection -->
                <div class="mb-4">
                    <label for="teacher_id" class="block text-sm font-medium text-gray-700">Guru</label>
                    <v-select id="teacher_id" v-model="form.teacher_id" :options="teacherOptions" @input="searchTeacher($event.target.value)"
                        :reduce="(option) => option.value" label="label" placeholder="Pilih Guru" required
                        class="text-gray-500" :open-on-focus="false" />
                    <div v-if="formErrors.teacher_id" class="mt-1 text-sm text-red-500">{{ formErrors.teacher_id }}
                    </div>
                </div>

                <!-- Hari (Day) Selection -->
                <div class="mb-4">
                    <label for="day" class="block text-sm font-medium text-gray-700">Hari</label>
                    <v-select id="day" class="text-gray-500" v-model="form.day" :options="dayOptions" label="label"
                    :reduce="(option) => option.value"
                        required placeholder="Pilih Hari" :open-on-focus="false" />
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
import { ref, onMounted, computed, watch } from 'vue';
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
import Badge from '@/components/common/Badge.vue';
import { switchMap } from 'rxjs/operators';

const className = router.currentRoute.value.params.name;
const pageTitle = ref(`Jadwal Pembelajaran ${className} `);
const API_ENDPOINTS = {
    DETAIL_CLASS_SCHEDULES: '/api/class-schedules/by-class/' + className,
    CLASS_SCHEDULES: '/api/class-schedules',
    CLASSES: '/api/classes',
    TEACHERS: '/api/teachers',
    SUBJECTS: '/api/subjects',
    BULK_UPDATE: '/api/class-schedules/bulk-update',
};

const loadingStore = useLoadingStore();
const modalStore = useModalStore();
const selectedClass = ref<ClassItem | null>(null);

interface Column {
    name: string;
    schedules: ClassScheduleItem[];
}

const lessonHours = ref<number[]>([]);

const addLessonHour = () => {
    const nextHour = lessonHours.value.length + 1;
    lessonHours.value.push(nextHour);
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
            console.log(teachers.value)
            console.log(teacherOptions.value)
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
const classes = ref<ClassItem[]>([]);
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

const fetchClasses = debounce(() => {
    loadingStore.show();
    from(apiClient.get(API_ENDPOINTS.CLASSES)).pipe(
        switchMap(response => {
            classes.value = response.data.data;
            return [];
        })
    ).subscribe({
        error: (error: any) => {
            console.error('Failed to fetch classes:', error);
            modalStore.showError('Error', error.response.data.message);
            loadingStore.hide();
        },
        complete: () => loadingStore.hide()
    });
}, 500);

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


const openModal = (action: 'add' | 'edit', schedule?: ClassScheduleItem) => {
    modalTitle.value = action === 'add' ? 'Add Schedule' : 'Edit Schedule';
    showModal.value = true;
    if (action === 'edit' && schedule) {
        subjects.value = [{ id: schedule.subject_id, name: schedule.subject.name }];
        teachers.value = [{ id: schedule.teacher_id, user: { username: schedule.teacher.user.username } }];
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
        // fetchTeachers(schedule.subject_id ?? 0); // Fetch teachers based on the selected subject
    } else {
        form.value = resetForm();
    }
    console.log('Form:', form.value);
};

watch(() => form.value.subject_id, (newSubjectId) => {
    if (newSubjectId) {
        // fetchTeachers(newSubjectId); // Fetch teachers when a new subject is selected
    }
});

const onDragEnd = (event: any) => {
    const { item, to, from, newIndex, oldIndex } = event;
    console.log(event)

    // Determine the new column and its associated day
    const toColumnIndex = parseInt(to.closest('.column').dataset.index);
    const fromColumnIndex = parseInt(from.closest('.column').dataset.index);
    const newDay = columns.value[toColumnIndex].name;
    const oldDay = columns.value[fromColumnIndex].name;

    // Log all relevant data for debugging
    console.log('=== Drag Event Details ===');
    console.log('Dragged Item:', item);
    console.log('From Column Index:', fromColumnIndex);
    console.log('From Index:', oldIndex);
    console.log('To Column Index:', toColumnIndex);
    console.log('To Index:', newIndex);
    console.log('Old Day:', oldDay);
    console.log('New Day:', newDay);

    // Update the dragged schedule's day and lesson_hours
    columns.value.forEach((column, colIndex) => {
        column.schedules.forEach((schedule, index) => {
            if (schedule.id === parseInt(item.id)) {
                console.log('true')
                schedule.day = newDay;
                schedule.lesson_hours = lessonHours.value[newIndex];
            }
            // Update the lesson_hours for all schedules in the "to" column
            console.log(colIndex, toColumnIndex)
            if (colIndex === toColumnIndex) {
                if (lessonHours.value[index] === undefined) addLessonHour();
                schedule.lesson_hours = lessonHours.value[index];
                console.log('to lesson_hours', schedule.lesson_hours)
            }
            // Update the lesson_hours for all schedules in the "from" column
            if (colIndex === fromColumnIndex) {
                schedule.lesson_hours = lessonHours.value[index];
            }
        });
    });

    console.log('Updated schedules:', columns.value);
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
    if (!form.value.class_id) formErrors.value.class_id = 'Kelas harus diisi';
    if (!form.value.subject_id) formErrors.value.subject_id = 'Mata Pelajaran harus diisi';
    if (!form.value.teacher_id) formErrors.value.teacher_id = 'Guru harus diisi';
    if (!form.value.day) formErrors.value.day = 'Hari harus diisi';
    if (!form.value.lesson_hours) formErrors.value.lesson_hours = 'Jam Pelajaran harus diisi';
    if (!form.value.duration) formErrors.value.duration = 'Durasi harus diisi';

    if (Object.keys(formErrors.value).length > 0) {
        return;
    }

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

onMounted(() => {
    fetchClassSchedules();
});
</script>