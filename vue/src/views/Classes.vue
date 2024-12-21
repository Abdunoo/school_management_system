<template>
  <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col" style="max-height: 100vh;">
    <!-- Table Controls -->
    <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
      <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
      <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-2">
          <div class="flex items-center space-x-2">
            <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
            <select id="perPage" v-model="perPage"
              class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
              <option v-for="option in perPageOptions" :key="option" :value="option">
                {{ option }}
              </option>
            </select>
          </div>
          <Button :variant="'primary'" @click="showAddClassModal = true">Tambah Kelas</Button>
        </div>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
          <Button :variant="'success'">Export</Button>
          <div class="relative flex items-center">
            <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search classes"
              class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none">
            <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
          </div>
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
            <td class="px-4 py-3 text-secondary">{{ classItem.homeroom_teacher.user.username }} - {{
              classItem.homeroom_teacher.nip }}</td>
            <td class="px-4 py-3 text-secondary">
              <Badge :variant="classItem.is_active ? 'success' : 'danger'">{{ classItem.is_active ? 'Aktif' : 'Tidak Aktif' }}</Badge>
            </td>
            <td class="px-4 py-3">
              <Button :variant="'warning'" @click="editClass(classItem)">Update</Button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center space-x-2 mt-4">
      <Button :variant="'secondary'" @click="currentPage--" :disabled="currentPage === 1"
        class="bg-gray-200 px-4 py-2 rounded">
        Previous
      </Button>
      <Button v-for="page in totalPages" :key="page" @click="currentPage = page" :variant="'secondary'" :class="{
        'bg-primary text-white': page === currentPage,
        'bg-gray-200': page !== currentPage,
      }" class="px-4 py-2 rounded">
        {{ page }}
      </Button>

      <Button :variant="'secondary'" @click="currentPage++" :disabled="currentPage === totalPages"
        class="bg-gray-200 px-4 py-2 rounded">
        Next
      </Button>
    </div>
  </div>

  <!-- Add/Edit Class Modal -->
<TransitionRoot as="template" :show="showAddClassModal || showEditClassModal" @close="resetModal">
    <Dialog class="relative z-30" @close="resetModal">
        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
            leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity" />
        </TransitionChild>

        <!-- Modal Content -->
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <TransitionChild as="template" enter="ease-out duration-300"
                    enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                    leave-from="opacity-100 translate-y-0 sm:scale-100"
                    leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <DialogPanel
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <!-- Modal Header -->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:size-10">
                                    <ExclamationTriangleIcon class="size-6 text-blue-600" aria-hidden="true" />
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <DialogTitle as="h3" class="text-base font-semibold text-gray-900">
                                        {{ showEditClassModal ? 'Edit' : 'Add' }} Class
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Please fill out the class details below.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <form @submit.prevent="showEditClassModal ? updateClass() : createClass()">
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Class Name</label>
                                        <input v-model="form.name" type="text" id="name" required
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div>
                                        <label for="academic_year"
                                            class="block text-sm font-medium text-gray-700">Academic Year</label>
                                        <input v-model="form.academic_year" type="text" id="academic_year" required
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                    </div>
                                    <div>
                                        <label for="homeroom_teacher_id"
                                            class="block text-sm font-medium text-gray-700">Homeroom Teacher</label>
                                        <select v-model="form.homeroom_teacher_id" id="homeroom_teacher_id" required
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option v-for="(teacher) in teachers" :key="teacher.id" :value="teacher.id">
                                                {{ teacher.user.username }} - {{ teacher.nip }}
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="isActive" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select v-model="form.is_active" id="isActive"
                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option :value="1">Active</option>
                                            <option :value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                          <Button
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                @click="resetModal">Cancel</Button>
                            <Button
                                class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto"
                                @click="showEditClassModal ? updateClass() : createClass()">
                                {{ showEditClassModal ? 'Update' : 'Add' }}
                            </Button>
                        </div>
                    </DialogPanel>
                </TransitionChild>
            </div>
        </div>
    </Dialog>
</TransitionRoot>

</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import Button from '@/components/common/Button.vue';
import apiClient from '@/helpers/axios';
import Badge from '@/components/common/Badge.vue';
import { ExclamationTriangleIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ClassItem } from '@/types';

const pageTitle = ref('Daftar Kelas');
const classes = ref<ClassItem[]>([]);
const teachers = ref([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));

const showAddClassModal = ref(false);
const showEditClassModal = ref(false);
const form = ref<ClassItem>({
  id: 0,
  name: '',
  academic_year: '',
  homeroom_teacher_id: 0,
  is_active: 1,
  homeroom_teacher: {
    id: 0,
    user_id: 0,
    nip: '',
    spesialisasi: '',
    telepon: '',
  },
});
const fetchClasses = async () => {
  try {
    const response = await apiClient.get('/api/classes', {
      params: {
        per_page: perPage.value,
        page: currentPage.value,
        search: searchQuery.value,
      },
    });
    classes.value = response.data;
    totalRecords.value = response.total;
  } catch (error) {
    console.error('Failed to fetch classes:', error);
  }
};

const fetchTeachers = async () => {
  try {
    const response = await apiClient.get('/api/teachers'); // Adjust the endpoint as necessary
    teachers.value = response.data;
  } catch (error) {
    console.error('Failed to fetch teachers:', error);
  }
};

const editClass = (classItem: ClassItem) => {
  form.value = { ...classItem };
  showEditClassModal.value = true;
};

const updateClass = async () => {
  try {
    const response = await apiClient.put(`/api/classes/${form.value.id}`, form.value);
    await fetchClasses(); 
    resetModal(); 
  } catch (error) {
    console.error('Failed to update class:', error);
  }
};

const createClass = async () => {
  try {
    const response = await apiClient.post('/api/classes', form.value);
    await fetchClasses(); // Refresh the classes list after creating
    resetModal(); // Close the modal after creating
  } catch (error) {
    console.error('Failed to create class:', error);
  }
};

const resetModal = () => {
  showAddClassModal.value = false;
  showEditClassModal.value = false;
  form.value = {
    id: 0,
    name: '',
    academic_year: '',
    homeroom_teacher_id: 0,
    is_active: 1,
    homeroom_teacher: {
      id: 0,
      user_id: 0,
      nip: '',
      spesialisasi: '',
      telepon: '',
    },
  };
};

onMounted(() => {
  fetchClasses();
  fetchTeachers(); // Fetch teachers when the component is mounted
});

watch([searchQuery, perPage, currentPage], fetchClasses);
</script>
