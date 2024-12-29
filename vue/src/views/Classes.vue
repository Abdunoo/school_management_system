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
      <div v-for="(classItem, index) in classes" :key="index"
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
            <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('name')">
              Nama Kelas
              <span v-if="sortField === 'name'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('academic_year')">
              Tahun Akademik
              <span v-if="sortField === 'academic_year'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('homeroom_teacher_id')">
              Wali Kelas
              <span v-if="sortField === 'homeroom_teacher_id'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
            <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('is_active')">
              Status
              <span v-if="sortField === 'is_active'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
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
          <tr v-for="(classItem, index) in classes" :key="index"
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

    <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

    <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
      <form @submit.prevent="handleFormSubmit" class="space-y-6">
        <!-- Nama Kelas -->
        <div class="space-y-2">
          <label for="name" class="block text-sm font-medium text-secondary">Nama Kelas</label>
          <FormField id="name" placeholder="Masukkan Nama Kelas" v-model="form?.name" required
            :errorMessage="formErrors.name" class="w-full" />
        </div>

        <!-- Tahun Akademik -->
        <div class="space-y-2">
          <label for="academic_year" class="block text-sm font-medium text-secondary">Tahun Akademik</label>
          <v-select id="academic_year" v-model="form?.academic_year" :options="academicYearOptions" label="label"
            placeholder="Pilih Tahun Ajar" required class="text-gray-500" />
            <div v-if="formErrors.academic_year" class="mt-1 text-sm text-red-500">{{ formErrors.academic_year }}</div>
        </div>

        <!-- Wali Kelas -->
        <div class="space-y-2">
          <label for="homeroom_teacher_id" class="block text-sm font-medium text-secondary">Wali Kelas</label>
          <v-select id="homeroom_teacher_id" v-model="form?.homeroom_teacher_id" :options="teacherOptions"
            :reduce="teacher => teacher.value" placeholder="Pilih Wali Kelas" required @search="searchTeacher" class="text-gray-500" />
            <div v-if="formErrors.homeroom_teacher_id" class="mt-1 text-sm text-red-500">{{ formErrors.homeroom_teacher_id }}</div>
        </div>

        <!-- Status -->
        <div class="space-y-2">
          <label for="is_active" class="block text-sm font-medium text-secondary">Status</label>
          <v-select id="is_active" v-model="form?.is_active" :options="statusOptions" label="label"
            :reduce="status => status.value" placeholder="Pilih Status" required class="text-gray-500" />
            <div v-if="formErrors.is_active" class="mt-1 text-sm text-red-500">{{ formErrors.is_active }}</div>
        </div>
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
import { ClassItem, Teacher, AcademicYearOption, FormErrors } from '@/types';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '@/stores/modalStore';

// API Endpoints
const API_ENDPOINTS = {
  CLASSES: '/api/classes',
  TEACHERS: '/api/teachers',
};

// Store instances
const loadingStore = useLoadingStore();
const modalStore = useModalStore();

// Constants and reactive variables
const pageTitle = ref<string>('Daftar Kelas');
const classes = ref<ClassItem[]>([]);
const teachers = ref<Teacher[]>([]);
const perPageOptions = [10, 20, 30] as const;
const perPage = ref<typeof perPageOptions[number]>(perPageOptions[0]);
const currentPage = ref<number>(1);
const searchQuery = ref<string>('');
const totalRecords = ref<number>(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));
const showModal = ref<boolean>(false);
const modalTitle = ref<string>('');
const form = ref<ClassItem | null>(null);
const formErrors = ref<FormErrors>({});

// Options for selects
const statusOptions = [
  { label: 'Active', value: 1 },
  { label: 'Inactive', value: 0 },
] as const;

const teacherOptions = computed(() =>
  teachers.value.map((teacher: Teacher) => ({
    label: `${teacher.user?.username} - ${teacher.nip}`,
    value: teacher.id,
  }))
);

// Academic Year Options
const currentYear = new Date().getFullYear();
const academicYearOptions = computed<AcademicYearOption[]>(() => {
  const options: AcademicYearOption[] = [];
  for (let i = -100; i <= 100; i++) {
    const year = currentYear + i;
    const next = year + 1;
    options.push({
      label: `${year}/${next}`,
      value: `${year}/${next}`,
    });
  }
  return options;
});

// Utility Functions
const resetForm = (): ClassItem => ({
  id: null,
  name: '',
  academic_year: '',
  homeroom_teacher_id: null,
  is_active: null,
  homeroom_teacher: {
    id: null,
    user_id: null,
    nip: null,
    telepon: null,
  },
});

const resetModal = (): void => {
  showModal.value = false;
  form.value = resetForm();
};

// Fetch Functions
const fetchClasses = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.CLASSES, {
      params: {
        per_page: perPage.value,
        page: currentPage.value,
        search: searchQuery.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      },
    });
    classes.value = data.data;
    teachers.value = classes.value.map((classItem: ClassItem) => classItem.homeroom_teacher);
    totalRecords.value = data.total;
  } catch (error: any) {
    console.error('Failed to fetch classes:', error);
    modalStore.showError('Error', error.response?.data?.message || 'Unknown error occurred');
  } finally {
    loadingStore.hide();
  }
}, 500);

const searchTeacher = debounce(async (query: string) => {
  if (!query) return;
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.TEACHERS, { params: { search: query, per_page: 5 } });
    teachers.value = data.data;
  } catch (error: any) {
    console.error('Failed to fetch teachers:', error);
    modalStore.showError('Error', error.response?.data?.message || 'Unknown error occurred');
  } finally {
    loadingStore.hide();
  }
}, 500);

// Modal and Form Handlers
const toggleModal = (type: 'add' | 'edit', classItem?: ClassItem): void => {
  modalTitle.value = type === 'add' ? 'Add Class' : 'Edit Class';
  showModal.value = true;
  form.value = type === 'edit' ? { ...classItem } : resetForm();
};

const handleFormSubmit = debounce(async (): Promise<void> => {
  formErrors.value = {};
  if (!form.value?.name) formErrors.value.name = 'Class Name is required';
  if (!form.value?.academic_year) formErrors.value.academic_year = 'Academic Year is required';
  if (!form.value?.homeroom_teacher_id) formErrors.value.homeroom_teacher_id = 'Homeroom Teacher is required';
  if (!form.value?.is_active) formErrors.value.is_active = 'Status is required';

  if (Object.keys(formErrors.value).length > 0) return;

  loadingStore.show();
  const endpoint = form.value?.id ? `${API_ENDPOINTS.CLASSES}/${form.value.id}` : API_ENDPOINTS.CLASSES;
  const method = form.value?.id ? 'put' : 'post';

  try {
    await apiClient[method](endpoint, form.value);
    fetchClasses();
    resetModal();
  } catch (error: any) {
    console.error('Failed to submit form:', error);
    modalStore.showError('Error', error.response?.data?.message || 'Unknown error occurred');
  } finally {
    loadingStore.hide();
  }
}, 500);

// Sorting
const sortField = ref<keyof ClassItem>('name');
const sortOrder = ref<'asc' | 'desc'>('asc');

const sort = (field: keyof ClassItem): void => {
  if (sortField.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortOrder.value = 'asc';
  }
  fetchClasses();
};

// Lifecycle Hooks
onMounted(fetchClasses);
watch([perPage, currentPage, searchQuery], fetchClasses);
</script>
