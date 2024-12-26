<template>
  <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
    <!-- Page Controls -->
    <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
      <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
      <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-2">
          <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
          <select id="perPage" v-model="perPage"
            class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
            <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
          </select>
          <Button variant="primary" @click="toggleModal('add')">Tambah Jadwal</Button>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
          <Button variant="success">Export</Button>
          <div class="relative flex items-center">
            <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search schedules"
              class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />
            <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
          </div>
        </div>
      </div>
    </div>

    <!-- Kelas Schedule Table (Mobile View) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:hidden gap-4">
      <div v-if="classSchedules.length === 0" class="text-center py-4 text-secondary">No schedules found.</div>
      <div v-for="schedule in classSchedules" :key="schedule.id"
        class="relative bg-gray-50 rounded-lg p-4 shadow hover:bg-gray-100 transition">
        <div class="space-y-2">
          <h3 class="text-lg font-bold text-secondary">{{ schedule.subject.name }} - {{ schedule.class.name }} - {{
            schedule.class.academic_year }}</h3>
          <p class="text-sm text-secondary">Hari: {{ schedule.day }}</p>
          <p class="text-sm text-secondary">Guru: {{ schedule.teacher.user?.username }}</p>
          <p class="text-sm text-secondary">Jam Pelajaran Ke: {{ schedule.lesson_hours }}</p>
          <p class="text-sm text-secondary">Jumlah Jam: {{ schedule.duration }}</p>
        </div>

        <div class="absolute bottom-2 right-2">
          <Button :variant="'warning'" @click="toggleModal('edit', schedule)">Edit</Button>
        </div>
      </div>
    </div>

    <!-- Kelas Schedule Table (Desktop View) -->
    <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full hidden lg:block">
      <table class="min-w-full table-auto text-sm text-left">
        <thead>
          <tr class="bg-gray-100 grid grid-cols-7 rounded-xl">
            <th class="px-4 py-3 text-secondary">Kelas</th>
            <th class="px-4 py-3 text-secondary">Hari</th>
            <th class="px-4 py-3 text-secondary">Mapel</th>
            <th class="px-4 py-3 text-secondary">Guru</th>
            <th class="px-4 py-3 text-secondary">Jam Pelajaran Ke</th>
            <th class="px-4 py-3 text-secondary">Jumlah Jam</th>
            <th class="px-4 py-3 text-secondary">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-if="classSchedules.length === 0">
            <td colspan="6" class="text-center py-4 text-secondary">No schedules found.</td>
          </tr>
          <tr v-for="schedule in classSchedules" :key="schedule.id"
            class="hover:bg-gray-100 transition grid grid-cols-7 items-center">
            <td class="px-4 py-3 text-secondary">{{ schedule.class.name }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.day }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.subject.name }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.teacher.user?.username }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.lesson_hours }}</td>
            <td class="px-4 py-3 text-secondary">{{ schedule.duration }}</td>
            <td class="px-4 py-3">
              <Button :variant="'warning'" @click="toggleModal('edit', schedule)">Edit</Button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

    <!-- Add/Edit Schedule Modal -->
    <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
      <form @submit.prevent="handleFormSubmit">
        <FormField placeholder="Kelas" label="Kelas" id="class_id" type="select" v-model="form.class_id"
          :options="classOptions" required />
        <div v-if="formErrors.class_id" class="mt-1 text-sm text-red-500">{{ formErrors.class_id }}</div>

        <FormField placeholder="Mata Pelajaran" label="Mata Pelajaran" id="subject_id" type="select" v-model="form.subject_id"
          :options="subjectOptions" required />
        <div v-if="formErrors.subject_id" class="mt-1 text-sm text-red-500">{{ formErrors.subject_id }}</div>

        <FormField placeholder="Guru" label="Guru" id="teacher_id" type="select" v-model="form.teacher_id"
          :options="teacherOptions" required />
        <div v-if="formErrors.teacher_id" class="mt-1 text-sm text-red-500">{{ formErrors.teacher_id }}</div>

        <FormField placeholder="Hari" label="Hari" id="day" type="select" v-model="form.day" :options="dayOptions" required />
        <div v-if="formErrors.day" class="mt-1 text-sm text-red-500">{{ formErrors.day }}</div>

        <FormField placeholder="Jam Pelajaran" label="Jam Pelajaran" id="lesson_hours" v-model="form.lesson_hours"
          required />
        <div v-if="formErrors.lesson_hours" class="mt-1 text-sm text-red-500">{{ formErrors.lesson_hours }}</div>

        <FormField placeholder="2 Jam Pelajaran" label="Durasi" id="duration" v-model="form.duration" required />
        <div v-if="formErrors.duration" class="mt-1 text-sm text-red-500">{{ formErrors.duration }}</div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue';
import Button from '@/components/common/Button.vue';
import Modal from '@/components/common/Modal.vue';
import Pagination from '@/components/common/Pagination.vue';
import FormField from '@/components/common/FormField.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import apiClient from '@/helpers/axios';
import { ClassScheduleItem, ClassItem, Subject, Teacher } from '@/types';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '../stores/modalStore';

const API_ENDPOINTS = {
  CLASS_SCHEDULES: '/api/class-schedules',
  CLASSES: '/api/classes',
  TEACHERS: '/api/teachers',
  SUBJECTS: '/api/subjects',
};

const loadingStore = useLoadingStore();
const modalStore = useModalStore();

const pageTitle = ref('Jadwal Pembelajaran');
const classSchedules = ref<ClassScheduleItem[]>([]);
const classes = ref<ClassItem[]>([]);
const teachers = ref<Teacher[]>([]);
const subjects = ref<Subject[]>([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));
const showModal = ref(false);
const modalTitle = ref('');
const form = ref<ClassScheduleItem>({
  id: 0,
  class_id: 0,
  subject_id: 0,
  teacher_id: 0,
  day: '',
  lesson_hours: 0,
  duration: 0,
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

const fetchClassSchedules = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.CLASS_SCHEDULES, {
      params: { per_page: perPage.value, page: currentPage.value, search: searchQuery.value },
    });
    classSchedules.value = data.data;
    totalRecords.value = data.data.length;
  } catch (error: any) {
    console.error('Failed to fetch class schedules:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

const fetchClasses = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.CLASSES);
    classes.value = data.data;
  } catch (error: any) {
    console.error('Failed to fetch classes:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

const fetchTeachers = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.TEACHERS);
    teachers.value = data.data;
  } catch (error: any) {
    console.error('Failed to fetch teachers:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

const fetchSubjects = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.SUBJECTS);
    subjects.value = data.data;
  } catch (error: any) {
    console.error('Failed to fetch subjects:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

const classOptions = computed(() =>
  classes.value.map((classItem: ClassItem) => ({
    label: classItem.name,
    value: classItem.id,
  }))
);

const subjectOptions = computed(() =>
  subjects.value.map((subject: Subject) => ({
    label: subject.name,
    value: subject.id,
  }))
);

const teacherOptions = computed(() =>
  teachers.value.map((teacher: Teacher) => ({
    label: teacher.user?.username,
    value: teacher.id,
  }))
);

const toggleModal = (type: 'add' | 'edit', schedule?: ClassScheduleItem) => {
  modalTitle.value = type === 'add' ? 'Add Schedule' : 'Edit Schedule';
  showModal.value = true;
  form.value = type === 'edit' ? { ...schedule } : resetForm();
};

const resetForm = () => ({
  id: 0,
  class_id: 0,
  subject_id: 0,
  teacher_id: 0,
  day: '',
  lesson_hours: 0,
  duration: 0,
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
  try {
    await apiClient[method](endpoint, form.value);
    fetchClassSchedules();
    resetModal();
  } catch (error: any) {
    console.error('Failed to submit form:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

onMounted(() => {
  fetchClassSchedules();
  fetchClasses();
  fetchTeachers();
  fetchSubjects();
});

watch([perPage, currentPage, searchQuery], () => {
  fetchClassSchedules();
});
</script>