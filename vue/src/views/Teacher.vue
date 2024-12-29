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
                    <Button variant="primary" @click="toggleModal('add')">Tambah Guru</Button>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <Button variant="success">Export</Button>
                    <div class="relative flex items-center">
                        <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search teachers"
                            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none">
                        <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher Table -->
        <div class="rounded-xl border border-gray-300 bg-gray-50 max-h-full hidden lg:block">
            <table class="min-w-full table-auto text-sm text-left">
                <thead>
                    <tr class="bg-gray-100 grid grid-cols-7 rounded-xl">
                        <th v-for="header in tableHeaders" :key="header.field"
                            class="px-4 py-3 text-secondary cursor-pointer" @click="sort(header.field)">
                            {{ header.label }}
                            <span v-if="sortField === header.field">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full hidden lg:block ">
            <table class="min-w-full table-auto text-sm text-left">
                <tbody class="divide-y divide-gray-200">
                    <tr v-if="teachers.length === 0">
                        <td colspan="7" class="text-center py-4 text-secondary">No teachers found.</td>
                    </tr>
                    <tr v-for="teacher in teachers" :key="teacher.id"
                        class="hover:bg-gray-100 transition grid grid-cols-7 items-center">
                        <td class="px-4 py-3 text-secondary">{{ teacher.nip }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <div class="flex flex-wrap gap-1">
                                <Badge v-for="subject in teacher.subjects" :key="subject.id" variant="primary">{{
                                    subject.name }}</Badge>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.telepon }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user?.username }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user?.email }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <Badge :variant="teacher.user?.is_active ? 'success' : 'danger'">{{ teacher.user?.is_active
                                ? 'Active' : 'Inactive' }}</Badge>
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            <Button variant="warning" @click="toggleModal('edit', teacher)">Edit</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Teacher Cards (Mobile Only) -->
        <div class="lg:hidden space-y-4">
            <div v-if="teachers.length === 0" class="text-center py-4 text-secondary">
                No teachers found.
            </div>
            <div v-for="teacher in teachers" :key="teacher.id"
                class="relative p-4 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition">
                <div class="absolute top-4 right-4">
                    <Badge :variant="teacher.user?.is_active ? 'success' : 'danger'">
                        {{ teacher.user?.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </Badge>
                </div>
                <h3 class="text-lg font-bold text-secondary">{{ teacher.nip }}</h3>
                <div class="text-sm text-secondary mt-2">
                    <strong>Mapel:</strong>
                    <div class="flex flex-wrap gap-1">
                        <Badge v-for="subject in teacher.subjects" :key="subject.id" variant="primary">{{ subject.name
                            }}</Badge>
                    </div>
                </div>
                <p class="text-sm text-secondary"><strong>Telepon:</strong> {{ teacher.telepon }}</p>
                <p class="text-sm text-secondary"><strong>Username:</strong> {{ teacher.user?.username }}</p>
                <p class="text-sm text-secondary"><strong>Email:</strong> {{ teacher.user?.email }}</p>
                <div class="flex justify-end mt-4">
                    <Button variant="warning" @click="toggleModal('edit', teacher)">Edit</Button>
                </div>
            </div>
        </div>

        <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

        <!-- Add/Edit Teacher Modal -->
        <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
            <form @submit.prevent="handleFormSubmit" class="space-y-6">
                <!-- NIP -->
                <div class="space-y-2">
                    <FormField placeholder="T-001" label="NIP" id="nip" v-model="form.nip" required />
                    <div v-if="formErrors.nip" class="mt-1 text-sm text-red-500">{{ formErrors.nip }}</div>
                </div>

                <!-- Mata Pelajaran -->
                <div class="space-y-2">
                    <label for="subjects" class="block text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <v-select id="subjects" class="text-gray-500" v-model="form.subjects" :options="subjects"
                        label="name" multiple required placeholder="Pilih Mapel"
                        @input="searchSubjects($event.target.value)" />
                    <div v-if="formErrors.subjects" class="mt-1 text-sm text-red-500">{{ formErrors.subjects }}</div>
                </div>

                <!-- Telepon -->
                <div class="space-y-2">
                    <FormField placeholder="08***" label="Telepon" id="telepon" v-model="form.telepon" required />
                    <div v-if="formErrors.telepon" class="mt-1 text-sm text-red-500">{{ formErrors.telepon }}</div>
                </div>

                <!-- Pilih User -->
                <div class="space-y-2">
                    <label for="user" class="block text-sm font-medium text-gray-700">Pilih User</label>
                    <v-select id="user" class="text-gray-500" v-model="form.user" :options="users" label="username"
                        required placeholder="Pilih User" @input="searchUsers($event.target.value)" />
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
import { Subject, Teacher, User, ApiResponse, FormErrors } from '@/types';
import apiClient from '@/helpers/axios';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '@/stores/modalStore';

// Stores
const loadingStore = useLoadingStore();
const modalStore = useModalStore();

// Page title
const pageTitle = ref<string>('Daftar Guru');

// Data refs
const teachers = ref<Teacher[]>([]);
const subjects = ref<Subject[]>([]);
const users = ref<User[]>([]);

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
const formErrors = ref<FormErrors>({
    nip: '',
    subjects: '',
    telepon: '',
    user: ''
});
const form = ref<Teacher | null>(null);

// Sorting
const sortField = ref<string>('nip');
const sortOrder = ref<'asc' | 'desc'>('asc');

// Fetch teachers with debounce
const fetchTeachers = debounce(async (): Promise<void> => {
    loadingStore.show();
    try {
        const response = await apiClient.get<ApiResponse<Teacher>>('/api/teachers', {
            params: {
                per_page: perPage.value,
                page: currentPage.value,
                search: searchQuery.value,
                sortField: sortField.value,
                sortOrder: sortOrder.value,
            },
        });
        teachers.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error: any) {
        console.error(error);
        modalStore.showError('Error', error.response?.data?.message || 'Failed to fetch teachers.');
    } finally {
        loadingStore.hide();
    }
}, 500);

// Search subjects
const searchSubjects = debounce(async (query: string): Promise<void> => {
    loadingStore.show();
    try {
        const response = await apiClient.get<ApiResponse<Subject>>('/api/subjects', {
            params: {
                search: query,
                per_page: 5,
                page: 1,
            },
        });
        subjects.value = response.data.data;
    } catch (error: any) {
        console.error(error);
        modalStore.showError('Error', error.response?.data?.message || 'Failed to fetch subjects.');
    } finally {
        loadingStore.hide();
    }
}, 500);

// Search users
const searchUsers = debounce(async (query: string): Promise<void> => {
    loadingStore.show();
    try {
        const response = await apiClient.get<ApiResponse<User>>('/api/users', {
            params: {
                per_page: 5,
                search: query,
                role: 'teacher',
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
onMounted(fetchTeachers);

watch([perPage, currentPage, searchQuery], fetchTeachers);

// Reset modal
const resetModal = (): void => {
    form.value = resetForm();
    showModal.value = false;
};

// Toggle modal
const toggleModal = (type: 'add' | 'edit', teacher?: Teacher): void => {
    modalTitle.value = type === 'edit' ? 'Edit Teacher' : 'Add Teacher';
    form.value = type === 'edit' ? { ...teacher } : resetForm();
    showModal.value = true;
};

// Reset form
const resetForm = (): Teacher => ({
    id: 0,
    user_id: 0,
    nip: '',
    telepon: '',
    gender: '',
    user: null,
    subjects: [],
    subject_ids: [],
});

// Form submission
const handleFormSubmit = async (): Promise<void> => {
    formErrors.value = {};

    // Validation
    if (!form.value?.nip) formErrors.value.nip = 'NIP harus diisi';
    if (!(form.value?.subjects?.length)) formErrors.value.subjects = 'Mapel harus diisi';
    if (!form.value?.telepon) formErrors.value.telepon = 'Telepon harus diisi';
    if (!form.value?.user) formErrors.value.user = 'User harus diisi';

    if (Object.keys(formErrors.value).length > 0) return;

    if (form.value?.subjects) {
        form.value.subject_ids = form.value.subjects.map((subject) => subject.id);
    }
    const dataToSend = {
        nip: form.value?.nip,
        subject_ids: form.value?.subject_ids,
        telepon: form.value?.telepon,
        user_id: form.value?.user_id,
    };

    loadingStore.show();
    const endpoint = form.value?.id ? `/api/teachers/${form.value.id}` : '/api/teachers';
    const method: 'post' | 'put' = form.value?.id ? 'put' : 'post';

    try {
        await apiClient[method](endpoint, dataToSend);
        resetModal();
        fetchTeachers();
    } catch (error: any) {
        console.error('Failed to submit form:', error);
        modalStore.showError('Error', error.response?.data?.message || 'Failed to submit form.');
    } finally {
        loadingStore.hide();
    }
};

// Sorting
const sort = (field: string): void => {
    sortOrder.value = sortField.value === field ? (sortOrder.value === 'asc' ? 'desc' : 'asc') : 'asc';
    sortField.value = field;
    fetchTeachers();
};

// Table headers
const tableHeaders = ref<Array<{ field: string; label: string }>>([
    { field: 'nip', label: 'NIP' },
    { field: 'subjects', label: 'Mata Pelajaran' },
    { field: 'telepon', label: 'Telepon' },
    { field: 'username', label: 'Username' },
    { field: 'email', label: 'Email' },
    { field: 'is_active', label: 'Status' },
    { field: 'actions', label: 'Actions' },
]);

</script>
