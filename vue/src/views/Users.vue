<template>
    <div class="bg-white rounded-2xl shadow p-4 space-y-4 h-full flex flex-col overflow-auto">
        <!-- Table Controls -->
        <div class="space-y-4 bg-gray-100 p-4 rounded-xl">
            <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:space-x-6 space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-2">
                    <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
                    <select id="perPage" v-model="perPage"
                        class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
                        <option v-for="option in perPageOptions" :key="option" :value="option">{{ option }}</option>
                    </select>
                    <Button variant="primary" @click="toggleModal('add')">Tambah User</Button>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <Button variant="success">Export</Button>
                    <div class="relative flex items-center">
                        <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search users"
                            class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none">
                        <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
                    </div>
                </div>
            </div>
        </div>

        <!-- User Table -->
        <div class="rounded-xl border border-gray-300 bg-gray-50 max-h-full hidden lg:block">
            <table class="min-w-full table-auto text-sm text-left">
                <thead>
                    <tr class="bg-gray-100 grid grid-cols-6 rounded-xl">
                        <th v-for="header in tableHeaders" :key="header.field"
                            class="px-4 py-3 text-secondary cursor-pointer" @click="sort(header.field)">
                            {{ header.label }}
                            <span v-if="header.field && sortField === header.field">{{ sortOrder === 'asc' ? '▲' : '▼'
                                }}</span>
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto h-full hidden lg:block ">
            <table class="min-w-full table-auto text-sm text-left">
                <tbody>
                    <tr v-if="users.length === 0">
                        <td colspan="5" class="text-center py-4 text-secondary">No users found.</td>
                    </tr>
                    <tr v-for="user in users" :key="user.id"
                        class="hover:bg-gray-100 transition grid grid-cols-6 items-center border-b">
                        <td class="px-4 py-3 text-secondary line-clamp-1 flex flex-wrap">{{ user.username }}</td>
                        <td class="px-4 py-3 text-secondary line-clamp-1 flex flex-wrap">{{ user.email }}</td>
                        <td class="px-4 py-3 text-secondary line-clamp-1 flex flex-wrap">{{ user.role }}</td>
                        <td class="px-4 py-3 text-secondary line-clamp-1 flex flex-wrap">{{ user.updated_at }}</td>
                        <td class="px-4 py-3 text-secondary line-clamp-1 flex flex-wrap">
                            <Badge :variant="user.is_active ? 'success' : 'danger'">{{ user.is_active ? 'Active' :
                                'Inactive' }}</Badge>
                        </td>
                        <td class="px-4 py-3 text-secondary">
                            <Button variant="warning" @click="toggleModal('edit', user)">Edit</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- User Cards (Mobile Only) -->
        <div class="lg:hidden space-y-4">
            <div v-if="users.length === 0" class="text-center py-4 text-secondary">
                No users found.
            </div>
            <div v-for="user in users" :key="user.id"
                class="relative p-4 border rounded-lg bg-gray-50 shadow-sm hover:shadow-md transition">
                <div class="absolute top-4 right-4">
                    <Badge :variant="user.is_active ? 'success' : 'danger'">
                        {{ user.is_active ? 'Active' : 'Inactive' }}
                    </Badge>
                </div>
                <h3 class="text-lg font-bold text-secondary">{{ user.username }}</h3>
                <p class="text-sm text-secondary"><strong>Email:</strong> {{ user.email }}</p>
                <p class="text-sm text-secondary"><strong>Role:</strong> {{ user.role }}</p>
                <p class="text-sm text-secondary"><strong>Updated At:</strong> {{ user.updated_at }}</p>
                <div class="flex justify-end mt-4">
                    <Button variant="warning" @click="toggleModal('edit', user)">Edit</Button>
                </div>
            </div>
        </div>

        <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

        <!-- Add/Edit User Modal -->
        <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
            <form @submit.prevent="handleFormSubmit" class="space-y-6">
                <!-- Username -->
                <div class="space-y-2">
                    <FormField placeholder="Username" label="Username" id="username" v-model="form.username" required />
                    <div v-if="formErrors.username" class="mt-1 text-sm text-red-500">{{ formErrors.username }}</div>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <FormField placeholder="Email" label="Email" id="email" v-model="form.email" required />
                    <div v-if="formErrors.email" class="mt-1 text-sm text-red-500">{{ formErrors.email }}</div>
                </div>

                <!-- Role -->
                <div class="space-y-2">
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" v-model="form.role"
                        class="text-gray-500 text-sm border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-primary focus:border-primary w-full">
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                    </select>
                    <div v-if="formErrors.role" class="mt-1 text-sm text-red-500">{{ formErrors.role }}</div>
                </div>

                <!-- Status -->
                <div class="space-y-2">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="is_active" v-model="form.is_active"
                        class="text-gray-500 text-sm border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-primary focus:border-primary w-full">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <div v-if="formErrors.is_active" class="mt-1 text-sm text-red-500">{{ formErrors.is_active }}</div>
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
import { User, ApiResponse, FormErrors } from '@/types';
import apiClient from '@/helpers/axios';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '@/stores/modalStore';

// Stores
const loadingStore = useLoadingStore();
const modalStore = useModalStore();

// Page title
const pageTitle = ref<string>('Daftar User');

// Data refs
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
    username: '',
    email: '',
    role: '',
    is_active: ''
});
const form = ref<User | null>(null);

// Sorting
const sortField = ref<string>('username');
const sortOrder = ref<'asc' | 'desc'>('asc');

// Fetch users with debounce
const fetchUsers = debounce(async (): Promise<void> => {
    loadingStore.show();
    try {
        const response = await apiClient.get<ApiResponse<User>>('/api/users', {
            params: {
                per_page: perPage.value,
                page: currentPage.value,
                search: searchQuery.value,
                sort_by: sortField.value,
                sort_order: sortOrder.value
            }
        });
        users.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error) {
        console.error('Error fetching users:', error);
        modalStore.showError('Error', error.response?.data?.message || 'Failed to fetch users.');
    } finally {
        loadingStore.hide();
    }
}, 300);

// Watch for changes in pagination and search
watch([perPage, currentPage, searchQuery], fetchUsers);

// Sort users by a given field
const sort = (field: string): void => {
    if (!field) return;
    sortField.value = field;
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    fetchUsers();
};

// Toggle modal for add/edit
const toggleModal = (type: 'add' | 'edit', user: User | null = null): void => {
    showModal.value = true;
    modalTitle.value = type === 'add' ? 'Tambah User' : 'Edit User';
    form.value = user ? { ...user } : { username: '', email: '', role: 'teacher', is_active: true };
};

// Reset form
const resetModal = (): void => {
    formErrors.value = { username: '', email: '', role: '', is_active: '' };
    showModal.value = false;
};

// Handle form submit
const handleFormSubmit = async (): Promise<void> => {
    loadingStore.show();
    try {
        if (form.value) {
            if (!form.value.id) {
                form.value.password = 'password'; // Set a default password for new users
                await apiClient.post('/api/users', form.value);
            } else {
                await apiClient.put(`/api/users/${form.value.id}`, form.value);
            }
            fetchUsers();
            resetModal();
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        modalStore.showError('Error', error.response?.data?.message);
    } finally {
        loadingStore.hide();
    }
};

const tableHeaders = ref<Array<{ field: string; label: string }>>([
    { field: 'username', label: 'Username' },
    { field: 'email', label: 'Email' },
    { field: 'role', label: 'Role' },
    { field: 'updated_at', label: 'Updated At' },
    { field: '', label: 'Status' },
    { field: '', label: 'Actions' },
]);

onMounted(fetchUsers);
</script>

<style scoped></style>
