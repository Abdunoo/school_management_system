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
                        <option v-for="option in perPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
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
                        <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('nip')">
                            NIP
                            <span v-if="sortField === 'nip'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('subjects')">
                            Mapel
                            <span v-if="sortField === 'subjects'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('telepon')">
                            Telepon
                            <span v-if="sortField === 'telepon'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('username')">
                            Username
                            <span v-if="sortField === 'username'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
                        </th>
                        <th class="px-4 py-3 text-secondary cursor-pointer" @click="sort('email')">
                            Email
                            <span v-if="sortField === 'email'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
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
                    <tr v-if="teachers.length === 0">
                        <td colspan="7" class="text-center py-4 text-secondary">No teachers found.</td>
                    </tr>
                    <tr v-for="teacher in teachers" :key="teacher.id"
                        class="hover:bg-gray-100 transition grid grid-cols-7 items-center">
                        <td class="px-4 py-3 text-secondary">{{ teacher.nip }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <div class="flex flex-wrap gap-1">
                                <Badge v-for="subject in teacher.subjects" :key="subject.id" variant="primary">
                                    {{ subject.name }}
                                </Badge>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.telepon }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user?.username }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user?.email }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <Badge :variant="teacher.user?.is_active ? 'success' : 'danger'">{{
                                teacher.user?.is_active
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
                <!-- Status on Top Right -->
                <div class="absolute top-4 right-4">
                    <Badge :variant="teacher.user?.is_active ? 'success' : 'danger'">
                        {{ teacher.user?.is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </Badge>
                </div>

                <!-- Content -->
                <h3 class="text-lg font-bold text-secondary">{{ teacher.nip }}</h3>
                <div class="text-sm text-secondary mt-2">
                    <strong>Mapel:</strong>
                    <div class="flex flex-wrap gap-1">
                        <Badge v-for="subject in teacher.subjects" :key="subject.id" variant="primary">
                            {{ subject.name }}
                        </Badge>
                    </div>
                </div>
                <p class="text-sm text-secondary"><strong>Telepon:</strong> {{ teacher.telepon }}</p>
                <p class="text-sm text-secondary"><strong>Username:</strong> {{ teacher.user?.username }}</p>
                <p class="text-sm text-secondary"><strong>Email:</strong> {{ teacher.user?.email }}</p>

                <!-- Button on Bottom Right -->
                <div class="flex justify-end mt-4">
                    <Button variant="warning" @click="toggleModal('edit', teacher)">
                        Edit
                    </Button>
                </div>
            </div>
        </div>

        <Pagination :current-page="currentPage" :total-pages="totalPages" @page-changed="currentPage = $event" />

        <!-- Add/Edit Teacher Modal -->
        <Modal :visible="showModal" :title="modalTitle" @close="resetModal" @confirm="handleFormSubmit">
            <form @submit.prevent="handleFormSubmit">
                <FormField placeholder="T-001" label="NIP" id="nip" v-model="form.nip" required />
                <div v-if="formErrors.nip" class="mt-1 text-sm text-red-500">{{ formErrors.nip }}</div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <Badge v-for="subject in form.subjects" :key="subject.id" variant="primary"
                        class="flex items-center space-x-2">
                        <span>{{ subject.name }}</span>
                        <Button type="button" @click="removeSubject(subject.id!)"
                            class=" rounded-full w-5 h-5 flex items-center justify-center">
                            &times;
                        </Button>
                    </Badge>
                </div>

                <!-- Custom Select with Scrollable Options -->
                <div class="relative mb-4">
                    <label for="mapel" class="block mb-2 text-sm font-medium text-gray-700">Mata Pelajaran</label>
                    <div class="border border-gray-300 rounded-md max-h-40 overflow-y-auto p-2">
                        <div v-for="(option, index) in subjects" :key="index"
                            class="flex justify-between items-center py-1 hover:bg-gray-100 cursor-pointer">
                            <span class="text-gray-800">{{ option.name }}</span>
                            <Button type="button" @click="addSubject(option)"
                                class="text-sm bg-primary hover:bg-blue-600 text-white px-2 py-1 rounded"
                                :disabled="isSubjectSelected(option)">
                                +
                            </Button>
                        </div>
                    </div>
                </div>
                <div v-if="formErrors.subjects" class="mt-1 text-sm text-red-500">{{ formErrors.subjects }}</div>

                <FormField placeholder="08***" label="Telepon" id="telepon" v-model="form.telepon" required />
                <div v-if="formErrors.telepon" class="mt-1 text-sm text-red-500">{{ formErrors.telepon }}</div>

                <FormField id="user" label="User " type="select" :options="userOptions" v-model="form.user_id"
                    placeholder="User " />
                <div v-if="formErrors.user_id" class="mt-1 text-sm text-red-500">{{ formErrors.user_id }}</div>
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
import { Subject, Teacher, User } from '@/types';
import apiClient from '@/helpers/axios';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import debounce from 'lodash.debounce';
import { useLoadingStore } from '@/stores/loadingStore';
import { useModalStore } from '@/stores/modalStore';

const loadingStore = useLoadingStore();
const modalStore = useModalStore();

const pageTitle = ref('Daftar Guru');
const teachers = ref<Teacher[]>([]);
const subjects = ref<Subject[]>([]);
const users = ref<User[]>([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));
const showModal = ref(false);
const modalTitle = ref('');
const formErrors = ref<{ [key: string]: string }>({});
const form = ref<Teacher>({
    id: 0,
    user_id: 0,
    nip: '',
    telepon: '',
    gender: '',
    user: {
        id: 0,
        username: '',
        email: '',
        role: 'teacher',
        is_active: 1,
    },
    subjects: [],
    subject_ids: [],
});

const sortField = ref('nip');
const sortOrder = ref('asc');

const fetchTeachers = debounce(async () => {
    loadingStore.show();
    try {
        const response = await apiClient.get('/api/teachers', {
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
        modalStore.showError('Error', error.response.data.message);
    }
    loadingStore.hide();
}, 500);

const fetchSubjects = debounce(async () => {
    loadingStore.show();
    try {
        const response = await apiClient.get('/api/subjects', {
            params: {
                per_page: 100,
                page: 1,
            },
        });
        subjects.value = response.data.data;
    } catch (error: any) {
        console.error(error);
        modalStore.showError('Error', error.response.data.message);
    }
    loadingStore.hide();
}, 500);

const fetchUsers = debounce(async () => {
    loadingStore.show();
    try {
        const response = await apiClient.get('/api/users', {
            params: {
                role: 'teacher',
            },
        });
        users.value = response.data.data;
    } catch (error: any) {
        console.error(error);
        modalStore.showError('Error', error.response.data.message);
    }
    loadingStore.hide();
}, 500);

const userOptions = computed(() =>
    users.value.map((user) => ({
        label: user.username ?? '',
        value: user.id ?? 0,
    }))
);

onMounted(() => {
    fetchTeachers();
    fetchSubjects();
    fetchUsers();
});

watch([perPage, currentPage, searchQuery], () => {
    fetchTeachers();
});

const resetModal = () => {
    form.value = {
        id: 0,
        user_id: 0,
        nip: '',
        telepon: '',
        gender: '',
        user: {
            id: 0,
            username: '',
            email: '',
            role: 'teacher',
            is_active: 1,
        },
        subjects: []
    };
    showModal.value = false;
};

const toggleModal = (type: 'add' | 'edit', teacher?: Teacher) => {
    modalTitle.value = type === 'edit' ? 'Edit Teacher' : 'Add Teacher';
    form.value = type === 'edit' ? { ...teacher } as Teacher : resetForm();
    showModal.value = true;
    console.log('Form:', form.value);
};

const resetForm = (): Teacher => ({
    id: 0,
    user_id: 0,
    nip: '',
    telepon: '',
    gender: '',
    user: {
        id: 0,
        username: '',
        email: '',
        role: 'teacher',
        is_active: 1,
    },
    subjects: []
});

const handleFormSubmit = async () => {
    console.log('Form submitted:', form.value);
    formErrors.value = {};

    if (!form.value.nip) formErrors.value.nip = 'NIP harus diisi';
    if (!(form.value.subjects ?? []).length) formErrors.value.subjects = 'Mapel harus diisi';
    if (!form.value.telepon) formErrors.value.telepon = 'Telepon harus diisi';
    if (!form.value.user_id) formErrors.value.user_id = 'User  harus diisi';

    if (Object.keys(formErrors.value).length > 0) {
        return;
    }

    form.value.subject_ids = []; // Clear the array

    form.value.subjects.forEach(subject => {
        form.value.subject_ids.push(subject.id);
    });

    const dataToSend = {
        nip: form.value.nip,
        subject_ids: form.value.subject_ids,
        telepon: form.value.telepon,
        user_id: form.value.user_id
    };

    loadingStore.show();
    const endpoint = form.value.id ? `/api/teachers/${form.value.id}` : '/api/teachers';
    const method = form.value.id ? 'put' : 'post';

    try {
        await apiClient[method](endpoint, dataToSend);
        resetModal();
        fetchTeachers();
    } catch (error: any) {
        console.error('Failed to submit form:', error);
        modalStore.showError('Error', error.response.data.message);
    }
    loadingStore.hide();
};

const addSubject = (selectedSubject: Subject) => {
    if (!(form.value.subjects ?? []).some(s => s.id === selectedSubject.id)) {
        if (!form.value.subjects) {
            form.value.subjects = [];
        }
        form.value.subjects.push(selectedSubject);
    }
};

const removeSubject = (subjectId: number) => {
    if (form.value.subjects) {
        form.value.subjects = form.value.subjects.filter(subject => subject.id !== subjectId);
    }
};

const isSubjectSelected = (subject: Subject) => {
    return (form.value.subjects ?? []).some(s => s.id === subject.id);
};

const sort = (field: string) => {
    if (sortField.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortOrder.value = 'asc';
    }
    fetchTeachers();
};
</script>