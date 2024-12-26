<template>
  <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
    <!-- Table Controls -->
    <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
      <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
      <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-2">
          <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
          <select id="perPage" v-model="perPage"
            class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
            <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
          </select>
          <Button variant="primary" @click="toggleModal('add')">Tambah Kelas</Button>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
          <Button variant="success">Export</Button>
          <div class="relative flex items-center">
            <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search classes"
              class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />
            <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:hidden gap-4">
      <!-- Tampilkan pesan jika tidak ada kelas -->
      <div v-if="classes.length === 0" class="text-center py-4 text-secondary">No classes found.</div>

      <!-- Iterasi untuk setiap kelas -->
      <div v-for="classItem in classes" :key="classItem.id"
        class="relative bg-gray-50 rounded-lg p-4 shadow hover:bg-gray-100 transition">
        <!-- Status di pojok kanan atas -->
        <div class="absolute top-2 right-2">
          <Badge :variant="classItem.is_active ? 'success' : 'danger'">
            {{ classItem.is_active ? 'Aktif' : 'Tidak Aktif' }}
          </Badge>
        </div>

        <!-- Informasi Kelas -->
        <div class="space-y-2">
          <h3 class="text-lg font-bold text-secondary">{{ classItem.name }}</h3>
          <p class="text-sm text-secondary">Tahun Akademik: {{ classItem.academic_year }}</p>
          <p class="text-sm text-secondary">
            Wali Kelas: {{ classItem.homeroom_teacher?.user?.username }} - {{ classItem?.homeroom_teacher?.nip }}
          </p>
        </div>

        <!-- Tombol Edit di pojok kanan bawah -->
        <div class="absolute bottom-2 right-2">
          <Button :variant="'warning'" @click="toggleModal('edit', classItem)">Edit</Button>
        </div>
      </div>
    </div>


    <!-- Class Table -->
    <div class="rounded-xl border border-gray-300 bg-gray-50 max-h-full hidden lg:block">
      <table class="min-w-full table-auto text-sm text-left">
        <thead>
          <tr class="bg-gray-100 grid grid-cols-5 rounded-xl">
            <th class="px-4 py-3 text-secondary">Nama Kelas</th>
            <th class="px-4 py-3 text-secondary">Tahun Akademik</th>
            <th class="px-4 py-3 text-secondary">Wali Kelas</th>
            <th class="px-4 py-3 text-secondary">Status</th>
            <th class="px-4 py-3 text-secondary">Actions</th>
          </tr>
        </thead>
      </table>
    </div>
    <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full hidden lg:block ">
      <table class="min-w-full table-auto text-sm text-left ">
        <tbody class="divide-y divide-gray-200 ">
          <tr v-if="classes.length === 0">
            <td colspan="5" class="text-center py-4 text-secondary">No classes found.</td>
          </tr>
          <tr v-for="classItem in classes" :key="classItem.id"
            class="hover:bg-gray-100 transition grid grid-cols-5 items-center">
            <td class="px-4 py-3 text-secondary">{{ classItem.name }}</td>
            <td class="px-4 py-3 text-secondary">{{ classItem.academic_year }}</td>
            <td class="px-4 py-3 text-secondary">{{ classItem.homeroom_teacher?.user?.username }} - {{
              classItem?.homeroom_teacher?.nip }}</td>
            <td class="px-4 py-3 text-secondary">
              <Badge :variant="classItem.is_active ? 'success' : 'danger'">
                {{ classItem.is_active ? 'Aktif' : 'Tidak Aktif' }}
              </Badge>
            </td>
            <td class="px-4 py-3">
              <Button :variant="'warning'" @click="toggleModal('edit', classItem)">Edit</Button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

    <!-- Add/Edit Class Modal -->
    <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
      <form @submit.prevent="handleFormSubmit">
        <FormField placeholder="Nama Kelas" label="Nama Kelas" id="name" v-model="form.name" required />
        <div v-if="formErrors.name" class="mt-1 text-sm text-red-500">{{ formErrors.name }}</div>

        <FormField placeholder="Tahun Ajaran" label="Tahun Ajaran" id="academic_year" type="select"
          v-model="form.academic_year" :options="academicYearOptions" required />
        <div v-if="formErrors.academic_year" class="mt-1 text-sm text-red-500">{{ formErrors.academic_year }}</div>

        <FormField placeholder="Wali Kelas" label="Wali Kelas" id="homeroom_teacher_id" type="select"
          v-model="form.homeroom_teacher_id" :options="teacherOptions" required />
        <div v-if="formErrors.homeroom_teacher_id" class="mt-1 text-sm text-red-500">{{ formErrors.homeroom_teacher_id }}</div>

        <FormField placeholder="Tidak Aktif" label="Status" id="isActive" type="select" v-model="form.is_active"
          :options="statusOptions" />
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
import Badge from '@/components/common/Badge.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import apiClient from '@/helpers/axios';
import { ClassItem, Teacher } from '@/types';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '../stores/modalStore';

const API_ENDPOINTS = {
  CLASSES: '/api/classes',
  TEACHERS: '/api/teachers',
};

const loadingStore = useLoadingStore();
const modalStore = useModalStore();

const pageTitle = ref('Daftar Kelas');
const classes = ref<ClassItem[]>([]);
const teachers = ref([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));
const showModal = ref(false);
const modalTitle = ref('');
const form = ref<ClassItem>({
  id: 0,
  name: '',
  academic_year: '',
  homeroom_teacher_id: 0,
  is_active: 1,
  homeroom_teacher: { id: 0, user_id: 0, nip: '', subject_id: 0, telepon: '' },
});

const formErrors = ref<{ [key: string]: string }>({});

const statusOptions = [
  { label: 'Active', value: 1 },
  { label: 'Inactive', value: 0 },
];
const teacherOptions = computed(() =>
  teachers.value.map((teacher: Teacher) => ({
    label: `${teacher.user?.username} - ${teacher.nip}`,
    value: teacher.id,
  }))
);

const fetchClasses = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.CLASSES, {
      params: { per_page: perPage.value, page: currentPage.value, search: searchQuery.value },
    });
    classes.value = data.data;
    totalRecords.value = data.data.length;
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

interface AcademicYearOption {
  label: string;
  value: string;
}

const currentYear: number = new Date().getFullYear();

const academicYearOptions = ref<AcademicYearOption[]>([]);

for (let i = -100; i <= 100; i++) {
  const year: number = currentYear + i;
  const next: number = year + 1;
  academicYearOptions.value.push({
    label: `${year}/${next}`,
    value: `${year}/${next}`
  });
}

const toggleModal = (type: 'add' | 'edit', classItem?: ClassItem) => {
  modalTitle.value = type === 'add' ? 'Add Class' : 'Edit Class';
  showModal.value = true;
  form.value = type === 'edit' ? { ...classItem } : resetForm();
};

const resetForm = () => ({
  id: 0,
  name: '',
  academic_year: '',
  homeroom_teacher_id: 0,
  is_active: 1,
  homeroom_teacher: { id: 0, user_id: 0, nip: '', subject_id: 0, telepon: '' },
});

const resetModal = () => {
  showModal.value = false;
  form.value = resetForm();
};

const handleFormSubmit = debounce(async () => {
  formErrors.value = {};
  if (!form.value.name) formErrors.value.name = 'Nama Kelas harus diisi';
  if (!form.value.academic_year) formErrors.value.academic_year = 'Tahun Ajaran harus diisi';
  if (!form.value.homeroom_teacher_id) formErrors.value.homeroom_teacher_id = 'Wali Kelas harus diisi';

  if (Object.keys(formErrors.value).length > 0) {
    return;
  }

  loadingStore.show();
  const endpoint = form.value.id ? `${API_ENDPOINTS.CLASSES}/${form.value.id}` : API_ENDPOINTS.CLASSES;
  const method = form.value.id ? 'put' : 'post';
  try {
    await apiClient[method](endpoint, form.value);
    fetchClasses();
    resetModal();
  } catch (error: any) {
    console.error('Failed to submit form:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

onMounted(() => {
  fetchClasses();
  fetchTeachers()
});

watch([perPage, currentPage, searchQuery], () => {
  fetchClasses();
});
</script>