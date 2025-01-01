<template>
  <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
    <!-- Table Controls -->
    <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
      <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
      <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-2">
          <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
          <select
            id="perPage"
            v-model="perPage"
            class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary"
          >
            <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
          </select>
          <Button variant="primary" @click="toggleModal('add')">Tambah Siswa</Button>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
          <Button variant="success">Export</Button>
          <div class="relative flex items-center">
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Search..."
              aria-label="Search students"
              class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none"
            />
            <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
          </div>
        </div>
      </div>
    </div>

    <!-- Student Table (Desktop) -->
    <div class="rounded-xl border border-gray-300 bg-gray-50 max-h-full hidden lg:block">
      <table class="min-w-full table-auto text-sm text-left">
        <thead>
          <tr class="bg-gray-100 grid grid-cols-7 rounded-xl">
            <th v-for="header in tableHeaders" :key="header.field" class="px-4 py-3 text-secondary cursor-pointer" @click="sort(header.field)">
              {{ header.label }}
              <span v-if="header.field && sortField === header.field">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
            </th>
          </tr>
        </thead>
      </table>
    </div>
    <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto h-full hidden lg:block">
      <table class="min-w-full table-auto text-sm text-left">
        <tbody>
          <tr v-if="students.length === 0">
            <td colspan="7" class="text-center py-4 text-secondary">No students found.</td>
          </tr>
          <tr v-for="student in students" :key="student.id" class="hover:bg-gray-100 transition grid grid-cols-7 items-center border-b">
            <td class="px-4 py-3 text-secondary">{{ student.nis }}</td>
            <td class="px-4 py-3 text-secondary">{{ student.tanggal_lahir }}</td>
            <td class="px-4 py-3 text-secondary">{{ student.alamat }}</td>
            <td class="px-4 py-3 text-secondary">{{ student.gender }}</td>
            <td class="px-4 py-3 text-secondary">{{ student.user?.username }}</td>
            <td class="px-4 py-3 text-secondary">
              <Badge :variant="student.user?.is_active ? 'success' : 'danger'">{{ student.user?.is_active ? 'Active' : 'Inactive' }}</Badge>
            </td>
            <td class="px-4 py-3 text-secondary">
              <Button variant="warning" @click="toggleModal('edit', student)">Edit</Button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Student Cards (Mobile) -->
    <div class="lg:hidden space-y-4">
      <div v-if="students.length === 0" class="text-center py-4 text-secondary">No students found.</div>
      <div v-for="student in students" :key="student.id" class="relative p-4 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition">
        <div class="absolute top-4 right-4">
          <Badge :variant="student.user?.is_active ? 'success' : 'danger'">{{ student.user?.is_active ? 'Active' : 'Inactive' }}</Badge>
        </div>
        <h3 class="text-lg font-bold text-secondary">{{ student.nis }}</h3>
        <p class="text-sm text-secondary"><strong>Tanggal Lahir:</strong> {{ student.tanggal_lahir }}</p>
        <p class="text-sm text-secondary"><strong>Alamat:</strong> {{ student.alamat }}</p>
        <p class="text-sm text-secondary"><strong>Gender:</strong> {{ student.gender }}</p>
        <p class="text-sm text-secondary"><strong>Username:</strong> {{ student.user?.username }}</p>
        <div class="flex justify-end mt-4">
          <Button variant="warning" @click="toggleModal('edit', student)">Edit</Button>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

    <!-- Add/Edit Student Modal -->
    <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
      <form @submit.prevent="handleFormSubmit" class="space-y-6">
        <!-- NIS -->
        <div class="space-y-2">
          <FormField placeholder="S001" label="NIS" id="nis" v-model="form.nis" required />
          <div v-if="formErrors.nis" class="mt-1 text-sm text-red-500">{{ formErrors.nis }}</div>
        </div>

        <!-- Tanggal Lahir -->
        <div class="space-y-2">
          <FormField type="date" label="Tanggal Lahir" id="tanggal_lahir" v-model="form.tanggal_lahir" required />
          <div v-if="formErrors.tanggal_lahir" class="mt-1 text-sm text-red-500">{{ formErrors.tanggal_lahir }}</div>
        </div>

        <!-- Alamat -->
        <div class="space-y-2">
          <FormField placeholder="Jl. Siswa No. 1" label="Alamat" id="alamat" v-model="form.alamat" required />
          <div v-if="formErrors.alamat" class="mt-1 text-sm text-red-500">{{ formErrors.alamat }}</div>
        </div>

        <!-- Gender -->
        <div class="space-y-2">
          <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
          <select
            id="gender"
            v-model="form.gender"
            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2"
            required
          >
            <option value="male">Male</option>
            <option value="female">Female</option>
          </select>
          <div v-if="formErrors.gender" class="mt-1 text-sm text-red-500">{{ formErrors.gender }}</div>
        </div>

        <!-- Pilih User -->
        <div class="space-y-2">
          <label for="user" class="block text-sm font-medium text-gray-700">Pilih User</label>
          <v-select
            id="user"
            class="text-gray-500"
            v-model="form.user"
            :options="users"
            label="username"
            required
            placeholder="Pilih User"
            @input="searchUsers($event.target.value)"
          />
          <div v-if="formErrors.user" class="mt-1 text-sm text-red-500">{{ formErrors.user }}</div>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import Button from '@/components/common/Button.vue';
import Badge from '@/components/common/Badge.vue';
import Modal from '@/components/common/Modal.vue';
import FormField from '@/components/common/FormField.vue';
import Pagination from '@/components/common/Pagination.vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import apiClient from '@/helpers/axios';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '@/stores/modalStore';

// Stores
const loadingStore = useLoadingStore();
const modalStore = useModalStore();

// Page title
const pageTitle = ref<string>('Daftar Siswa');

// Data refs
const students = ref<any[]>([]);
const users = ref<any[]>([]);

// Pagination and search
const perPageOptions = [10, 20, 30];
const perPage = ref<number>(perPageOptions[0]);
const currentPage = ref<number>(1);
const searchQuery = ref<string>('');
const totalRecords = ref<number>(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));

// Modal state and form
const showModal = ref<boolean>(false);
const modalTitle = ref<string>('');
const formErrors = ref<any>({});
const form = ref<any>(null);

// Sorting
const sortField = ref<string>('nis');
const sortOrder = ref<'asc' | 'desc'>('asc');

// Fetch students with debounce
const fetchStudents = debounce(async (): Promise<void> => {
  loadingStore.show();
  try {
    const response = await apiClient.get('/api/students', {
      params: {
        per_page: perPage.value,
        page: currentPage.value,
        search: searchQuery.value,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      },
    });
    students.value = response.data.data;
    totalRecords.value = response.data.total;
  } catch (error: any) {
    console.error(error);
    modalStore.showError('Error', error.response?.data?.message || 'Failed to fetch students.');
  } finally {
    loadingStore.hide();
  }
}, 500);

// Search users
const searchUsers = debounce(async (query: string): Promise<void> => {
  loadingStore.show();
  try {
    const response = await apiClient.get('/api/users', {
      params: {
        per_page: 5,
        search: query,
        role: 'student',
      },
    });
    users.value = response.data.data;
  } catch (error: any) {
    console.error(error);
    modalStore.showError('Error', error.response?.data?.message || 'Failed to fetch users.');
  } finally {
    loadingStore.hide();
  }
}, 500);

// Lifecycle hooks
onMounted(fetchStudents);

watch([perPage, currentPage, searchQuery], fetchStudents);

// Reset modal
const resetModal = (): void => {
  form.value = resetForm();
  showModal.value = false;
};

// Toggle modal
const toggleModal = (type: 'add' | 'edit', student?: any): void => {
  modalTitle.value = type === 'add' ? 'Tambah Siswa' : 'Edit Siswa';
  form.value = type === 'edit' ? { ...student } : resetForm();
  showModal.value = true;
};

// Reset form
const resetForm = (): any => ({
  id: 0,
  user_id: 0,
  nis: '',
  tanggal_lahir: '',
  alamat: '',
  gender: '',
  user: null,
});

// Form submission
const handleFormSubmit = async (): Promise<void> => {
  formErrors.value = {};

  // Validation
  if (!form.value?.nis) formErrors.value.nis = 'NIS harus diisi';
  if (!form.value?.tanggal_lahir) formErrors.value.tanggal_lahir = 'Tanggal Lahir harus diisi';
  if (!form.value?.alamat) formErrors.value.alamat = 'Alamat harus diisi';
  if (!form.value?.gender) formErrors.value.gender = 'Gender harus diisi';
  if (!form.value?.user) formErrors.value.user = 'User harus diisi';

  if (Object.keys(formErrors.value).length > 0) return;

  const dataToSend = {
    nis: form.value?.nis,
    tanggal_lahir: form.value?.tanggal_lahir,
    alamat: form.value?.alamat,
    gender: form.value?.gender,
    user_id: form.value?.user?.id,
  };

  loadingStore.show();
  const endpoint = form.value?.id ? `/api/students/${form.value.id}` : '/api/students';
  const method: 'post' | 'put' = form.value?.id ? 'put' : 'post';

  try {
    await apiClient[method](endpoint, dataToSend);
    resetModal();
    fetchStudents();
  } catch (error: any) {
    console.error('Failed to submit form:', error);
    modalStore.showError('Error', error.response?.data?.message || 'Failed to submit form.');
  } finally {
    loadingStore.hide();
  }
};

// Sorting
const sort = (field: string): void => {
  if (field === '') return;
  sortOrder.value = sortField.value === field ? (sortOrder.value === 'asc' ? 'desc' : 'asc') : 'asc';
  sortField.value = field;
  fetchStudents();
};

// Table headers
const tableHeaders = ref<Array<{ field: string; label: string }>>([
  { field: 'nis', label: 'NIS' },
  { field: 'tanggal_lahir', label: 'Tanggal Lahir' },
  { field: 'alamat', label: 'Alamat' },
  { field: 'gender', label: 'Gender' },
  { field: 'username', label: 'Username' },
  { field: 'is_active', label: 'Status' },
  { field: 'actions', label: 'Actions' },
]);
</script>