<template>
    <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto"> <!-- Table Controls -->
      <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
        <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
          <div class="flex items-center space-x-2">
            <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
            <select id="perPage" v-model="perPage"
              class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
              <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
            </select>
            <Button variant="primary" @click="toggleModal('add')">Tambah Mata Pelajaran</Button>
          </div>
          <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
            <Button variant="success">Export</Button>
            <div class="relative flex items-center">
              <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search subjects"
                class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none" />
              <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
            </div>
          </div>
        </div>
      </div>
  
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:hidden gap-4">
        <div v-if="subjects.length === 0" class="text-center py-4 text-secondary">No subjects found.</div>
  
        <div v-for="subject in subjects" :key="subject.id"
          class="relative bg-gray-50 rounded-lg p-4 shadow hover:bg-gray-100 transition">
          <div class="absolute top-2 right-2">
            <Badge :variant="'success'">
              Aktif
            </Badge>
          </div>
  
          <div class="space-y-2">
            <h3 class="text-lg font-bold text-secondary">{{ subject.name }}</h3>
            <p class="text-sm text-secondary">Created At: {{ subject.created_at }}</p>
          </div>
  
          <div class="absolute bottom-2 right-2">
            <Button :variant="'warning'" @click="toggleModal('edit', subject)">Edit</Button>
          </div>
        </div>
      </div>
  
      <!-- Subject Table -->
      <div class="rounded-xl border border-gray-300 bg-gray-50 max-h-full hidden lg:block">
        <table class="min-w-full table-auto text-sm text-left">
          <thead>
            <tr class="bg-gray-100 grid grid-cols-3 rounded-xl">
              <th class="px-4 py-3 text-secondary">Nama Mata Pelajaran</th>
              <th class="px-4 py-3 text-secondary">Created At</th>
              <th class="px-4 py-3 text-secondary">Actions</th>
            </tr>
          </thead>
        </table>
      </div>
      <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full hidden lg:block ">
        <table class="min-w-full table-auto text-sm text-left ">
          <tbody class="divide-y divide-gray-200 ">
            <tr v-if="subjects.length === 0">
              <td colspan="3" class="text-center py-4 text-secondary">No subjects found.</td>
            </tr>
            <tr v-for="subject in subjects" :key="subject.id"
              class="hover:bg-gray-100 transition grid grid-cols-3 items-center">
              <td class="px-4 py-3 text-secondary">{{ subject.name }}</td>
              <td class="px-4 py-3 text-secondary">{{ subject.created_at }}</td>
              <td class="px-4 py-3">
                <Button :variant="'warning'" @click="toggleModal('edit', subject)">Edit</Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- Pagination -->
      <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />
  
      <!-- Add/Edit Subject Modal -->
      <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
        <form @submit.prevent="handleFormSubmit">
          <FormField placeholder="Nama Mata Pelajaran" label="Nama Mata Pelajaran" id="name" v-model="form.name" required />
          <div v-if="formErrors.name" class="mt-1 text-sm text-red-500">{{ formErrors.name }}</div>
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
import { Subject } from '@/types';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '../stores/modalStore';

const API_ENDPOINTS = {
  SUBJECTS: '/api/subjects',
};

const loadingStore = useLoadingStore();
const modalStore = useModalStore();

const pageTitle = ref('Daftar Mata Pelajaran');
const subjects = ref<Subject[]>([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));
const showModal = ref(false);
const modalTitle = ref('');
const form = ref<Subject>({
  id: 0,
  name: '',
});

const formErrors = ref<{ [key: string]: string }>({});

const fetchSubjects = debounce(async () => {
  loadingStore.show();
  try {
    const { data } = await apiClient.get(API_ENDPOINTS.SUBJECTS, {
      params: { per_page: perPage.value, page: currentPage.value, search: searchQuery.value },
    });
    subjects.value = data.data;
    totalRecords.value = data.data.length;
  } catch (error: any) {
    console.error('Failed to fetch subjects:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

const toggleModal = (type: 'add' | 'edit', subject?: Subject) => {
  modalTitle.value = type === 'add' ? 'Add Subject' : 'Edit Subject';
  showModal.value = true;
  form.value = type === 'edit' ? { ...subject } : resetForm();
};

const resetForm = () => ({
  id: 0,
  name: '',
});

const resetModal = () => {
  showModal.value = false;
  form.value = resetForm();
};

const handleFormSubmit = debounce(async () => {
  formErrors.value = {};
  if (!form.value.name) formErrors.value.name = 'Nama Mata Pelajaran harus diisi';

  if (Object.keys(formErrors.value).length > 0) {
    return;
  }

  loadingStore.show();
  const endpoint = form.value.id ? `${API_ENDPOINTS.SUBJECTS}/${form.value.id}` : API_ENDPOINTS.SUBJECTS;
  const method = form.value.id ? 'put' : 'post';
  try {
    await apiClient[method](endpoint, form.value);
    fetchSubjects();
    resetModal();
  } catch (error: any) {
    console.error('Failed to submit form:', error);
    modalStore.showError('Error', error.response.data.message);
  }
  loadingStore.hide();
}, 500);

onMounted(() => {
  fetchSubjects();
});

watch([perPage, currentPage, searchQuery], () => {
  fetchSubjects();
});
</script>