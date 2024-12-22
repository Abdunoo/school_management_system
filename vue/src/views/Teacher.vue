<template>
    <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col overflow-auto">
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
                    <Button :variant="'primary'" @click="toggleModal('add')">Tambah Guru</Button>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                    <Button :variant="'success'">Export</Button>
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
                        <th class="px-4 py-3 text-secondary">NIP</th>
                        <th class="px-4 py-3 text-secondary">Mapel</th>
                        <th class="px-4 py-3 text-secondary">Telepon</th>
                        <th class="px-4 py-3 text-secondary">Username</th>
                        <th class="px-4 py-3 text-secondary">Email</th>
                        <th class="px-4 py-3 text-secondary">Status</th>
                        <th class="px-4 py-3 text-secondary">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full hidden lg:block ">
            <table class="rounded-xl min-w-full table-auto text-sm text-left">
                <tbody class="divide-y divide-gray-200">
                    <tr v-if="teachers.length === 0">
                        <td colspan="7" class="text-center py-4 text-secondary">No teachers found.</td>
                    </tr>
                    <tr v-for="teacher in teachers" :key="teacher.id"
                        class="hover:bg-gray-100 transition grid grid-cols-7 items-center">
                        <td class="px-4 py-3 text-secondary">{{ teacher.nip }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.subject?.name }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.telepon }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user.username }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user.email }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <Badge :variant="teacher.user.is_active ? 'success' : 'danger'">{{ teacher.user.is_active
                                ? 'Aktif' : 'Tidak Aktif' }}</Badge>
                        </td>
                        <td class="px-4 py-3">
                            <Button :variant="'warning'" @click="toggleModal('edit', teacher)">Update</Button>
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
                <!-- Status on Top Right -->
                <div class="absolute top-4 right-4">
                    <Badge :variant="teacher.user.is_active ? 'success' : 'danger'">
                        {{ teacher.user.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </Badge>
                </div>

                <!-- Content -->
                <h3 class="text-lg font-bold text-secondary">{{ teacher.nip }}</h3>
                <p class="text-sm text-secondary mt-2"><strong>Mapel:</strong> {{ teacher.subject?.name }}</p>
                <p class="text-sm text-secondary"><strong>Telepon:</strong> {{ teacher.telepon }}</p>
                <p class="text-sm text-secondary"><strong>Username:</strong> {{ teacher.user.username }}</p>
                <p class="text-sm text-secondary"><strong>Email:</strong> {{ teacher.user.email }}</p>

                <!-- Button on Bottom Right -->
                <div class="flex justify-end mt-4">
                    <Button :variant="'warning'" @click="toggleModal('edit', teacher)">
                        Update
                    </Button>
                </div>
            </div>
        </div>

        <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

        <!-- Add/Edit Teacher Modal -->
        <Modal :visible="showModal" :title="modalTitle" :confirmButtonText="modalTitle" @close="resetModal"
            @confirm="handleFormSubmit">
            <form @submit.prevent="handleFormSubmit">
                <FormField label="NIP" id="nip" v-model="form.nip" required />
                <FormField id="mapel" label="Mapel" type="select" :options="subjectOption" v-model="form.subject_id"
                    placeholder="Pilih Mapel" />
                <FormField label="Telepon" id="telepon" v-model="form.telepon" required />
                <FormField id="user" label="User" type="select" :options="usertOption" v-model="form.user_id"
                    placeholder="Pilih Mapel" />
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
import { Subject, Teacher } from '@/types';
import apiClient from '@/helpers/axios';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';
import Pagination from '@/components/common/Pagination.vue';
import { useLoadingStore } from '@/stores/loadingStore';
import { User } from '../types/index';

const loadingStore = useLoadingStore();

const pageTitle = ref('Daftar Guru');
const teachers = ref<Teacher[]>([]);
const subjects = ref<Subject[]>([]);
const users = ref<(User[])>([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));

const showModal = ref(false);
const modalTitle = ref('');
const form = ref<Teacher>({
    id: 0,
    user_id: 0,
    nip: '',
    subject_id: 0,
    telepon: '',
    user: {
        id: 0,
        username: '',
        email: '',
        is_active: 1,
    },
    subject: {
        id: 0,
        name: '',
    }
});


const fetchTeachers = debounce(async () => {
    loadingStore.show();
    try {
        const response = await apiClient.get('/api/teachers', {
            params: {
                perPage: perPage.value,
                page: currentPage.value,
                search: searchQuery.value,
            },
        });
        teachers.value = response.data;
        totalRecords.value = response.total;
    } catch (error) {
        console.error(error);
    }
    loadingStore.hide();
}, 300);

const fetchMapel = debounce(async () => {
    loadingStore.show();
    try {
        const response = await apiClient.get('/api/subjects');
        subjects.value = response.data;
    } catch (error) {
        console.error(error);
    }
    loadingStore.hide();
}, 300);

const fetchUser = debounce(async () => {
    loadingStore.show();
    try {
        const response = await apiClient.get('/api/users');
        users.value = response.data;
    } catch (error) {
        console.error(error);
    }
    loadingStore.hide();
}, 300);

const subjectOption = computed(() =>
    subjects.value.map((subject) => ({
        label: subject.name,
        value: subject.id,
    }))
);

const usertOption = computed(() =>
    users.value.map((user) => ({
        label: user.username,
        value: user.id,
    }))
);

onMounted(() => {
    fetchTeachers();
    fetchMapel();
    fetchUser();
});

watch([perPage, currentPage, searchQuery], () => {
    fetchTeachers();
});

const resetModal = () => {
    form.value = {
        id: 0,
        user_id: 0,
        nip: '',
        subject_id: 0,
        telepon: '',
        user: {
            id: 0,
            username: '',
            email: '',
            is_active: 1,
        },
        subject: {
            id: 0,
            name: '',
        }
    };
    showModal.value = false;
};

const toggleModal = (type: 'add' | 'edit', teacher?: Teacher) => {
    modalTitle.value = type === 'edit' ? 'Edit Teacher' : 'Add Teacher';
    form.value = type === 'edit' ? { ...teacher } : resetForm();
    showModal.value = true;
};

const resetForm = () => ({
    id: 0,
    user_id: 0,
    nip: '',
    subject_id: 0,
    telepon: '',
    user: {
        id: 0,
        username: '',
        email: '',
        password: '',
        is_active: 1,
    },
    subject: {
        id: 0,
        name: '',
    }
});

const handleFormSubmit = debounce(async () => {
    console.log(form.value);
    loadingStore.show();
    const endpoint = form.value.id ? `/api/teachers/${form.value.id}` : '/api/teachers';
    const method = form.value.id ? 'put' : 'post';

    try {
        await apiClient[method](endpoint, form.value);
        resetModal();
        fetchTeachers();
    } catch (error) {
        console.error('Failed to submit form:', error);
    }
    loadingStore.hide();
}, 300);
</script>