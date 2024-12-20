<template>
    <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col" style="max-height: 100vh;">
        <!-- Table Controls -->
        <div class="space-y-4 bg-gray-100 p-4 rounded-lg">
            <span class="text-lg md:text-xl font-bold text-secondary">{{ pageTitle }}</span>
            <div class="flex w-full justify-between space-x-6">
                <div class="flex items-center space-x-2">
                    <label for="perPage" class="text-sm font-medium text-secondary">Show</label>
                    <select id="perPage" v-model="perPage"
                        class="border w-16 border-gray-300 bg-white text-secondary rounded-md text-sm px-3 py-2 focus:ring-primary focus:border-primary">
                        <option v-for="option in perPageOptions" :key="option" :value="option">
                            {{ option }}
                        </option>
                    </select>
                    <Button :variant="'primary'" @click="showAddTeacherModal = true">Tambah Guru</Button>
                </div>
                <div class="relative flex space-x-2 text-secondary">
                    <Button @click="exportTeachers" :variant="'success'">Export</Button>
                    <input type="text" v-model="searchQuery" placeholder="Search..." aria-label="Search teachers"
                        class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none">
                    <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
                </div>
            </div>
        </div>

        <!-- Teacher Table -->
        <div class="rounded-xl border border-gray-300 bg-gray-50 overflow-y-auto max-h-full">
            <table class="min-w-full table-auto text-sm text-left">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-secondary">NIP</th>
                        <th class="px-4 py-3 text-secondary">Mapel</th>
                        <th class="px-4 py-3 text-secondary">Telepon</th>
                        <th class="px-4 py-3 text-secondary">Username</th>
                        <th class="px-4 py-3 text-secondary">Email</th>
                        <th class="px-4 py-3 text-secondary">Status</th>
                        <th class="px-4 py-3 text-secondary">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-if="teachers.length === 0">
                        <td colspan="7" class="text-center py-4 text-secondary">No teachers found.</td>
                    </tr>
                    <tr v-for="teacher in teachers" :key="teacher.id" class="hover:bg-gray-100 transition">
                        <td class="px-4 py-3 text-secondary">{{ teacher.nip }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.spesialisasi }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.telepon }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user.username }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user.email }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <Badge>{{ teacher.user.is_active ? 'Aktif' : 'Tidak Aktif' }}</Badge>
                        </td>
                        <td class="px-4 py-3 space-x-2 flex">
                            <Button :variant="'warning'" @click="editTeacher(teacher)">Update</Button>
                            <Button :variant="'danger'" @click="deleteTeacher(teacher.id)">Delete</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center space-x-2 mt-4">
            <Button :variant="'secondary'" @click="currentPage--" class="bg-gray-200 px-4 py-2 rounded">
                Previous
            </Button>
            <Button v-for="page in totalPages" :key="page" @click="currentPage = page" :variant="'secondary'" :class="{
                'bg-primary text-white': page === currentPage,
                'bg-gray-200': page !== currentPage,
            }" class="px-4 py-2 rounded">
                {{ page }}
            </Button>

            <Button :variant="'secondary'" @click="currentPage++" class="bg-gray-200 px-4 py-2 rounded">
                Next
            </Button>
        </div>

        <!-- Add/Edit Teacher Modal -->
        <TransitionRoot as="template" :show="showAddTeacherModal || showEditTeacherModal" @close="open = false">
            <Dialog class="relative z-10" @close="open = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
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
                                            class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                                            <ExclamationTriangleIcon class="size-6 text-red-600" aria-hidden="true" />
                                        </div>
                                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                            <DialogTitle as="h3" class="text-base font-semibold text-gray-900">
                                                {{ showEditTeacherModal ? 'Edit' : 'Add' }} Teacher
                                            </DialogTitle>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">Please fill out the details for the
                                                    teacher.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Body -->
                                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                    <form @submit.prevent="showEditTeacherModal ? updateTeacher() : createTeacher()">
                                        <div class="space-y-4">
                                            <div>
                                                <label for="nip"
                                                    class="block text-sm font-medium text-gray-700">NIP</label>
                                                <input v-model="form.nip" type="text" id="nip" required
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>
                                            <div>
                                                <label for="spesialisasi"
                                                    class="block text-sm font-medium text-gray-700">Mapel</label>
                                                <input v-model="form.spesialisasi" type="text" id="spesialisasi"
                                                    required
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>
                                            <div>
                                                <label for="telepon"
                                                    class="block text-sm font-medium text-gray-700">Telepon</label>
                                                <input v-model="form.telepon" type="text" id="telepon" required
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>
                                            <div>
                                                <label for="email"
                                                    class="block text-sm font-medium text-gray-700">Email</label>
                                                <input v-model="form.email" type="email" id="email" required
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                                            </div>
                                            <div>
                                                <label for="isActive"
                                                    class="block text-sm font-medium text-gray-700">Status</label>
                                                <select v-model="form.is_active" id="isActive"
                                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    <option :value="true">Aktif</option>
                                                    <option :value="false">Tidak Aktif</option>
                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Modal Footer -->
                                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                    <button type="button"
                                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                        @click=" showAddTeacherModal = false">Cancel</button>
                                    <button type="button"
                                        class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto"
                                        @click="showEditTeacherModal ? updateTeacher() : createTeacher()">
                                        {{ showEditTeacherModal ? 'Update' : 'Add' }}
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import apiClient from '@/helpers/axios';
import Button from '@/components/common/Button.vue';
import Badge from '@/components/common/Badge.vue';
import { ExclamationTriangleIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const pageTitle = ref('Teachers Management');
const teachers = ref([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0);
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));

const showAddTeacherModal = ref(false);
const showEditTeacherModal = ref(false);
const form = ref({
    id: null,
    nip: '',
    spesialisasi: '',
    telepon: '',
    email: '',
    is_active: true,
});

const fetchTeachers = async () => {
    try {
        const response = await apiClient.get('/api/teachers', {
            params: {
                per_page: perPage.value,
                page: currentPage.value,
                search: searchQuery.value,
            },
        });
        teachers.value = response.data;
        totalRecords.value = response.total;
    } catch (error) {
        console.error('Failed to fetch teachers:', error);
    }
};

const createTeacher = async () => {
    try {
        await apiClient.post('/api/teachers', form.value);
        resetModal();
        fetchTeachers();
    } catch (error) {
        console.error('Failed to create teacher:', error);
    }
};

const editTeacher = (teacher) => {
    form.value = { ...teacher };
    showEditTeacherModal.value = true;
};

const updateTeacher = async () => {
    try {
        await apiClient.put(`/api/teachers/${form.value.id}`, form.value);
        resetModal();
        fetchTeachers();
    } catch (error) {
        console.error('Failed to update teacher:', error);
    }
};

const deleteTeacher = async (id) => {
    if (confirm('Are you sure you want to delete this teacher?')) {
        try {
            await apiClient.delete(`/api/teachers/${id}`);
            fetchTeachers();
        } catch (error) {
            console.error('Failed to delete teacher:', error);
        }
    }
};

const resetModal = () => {
    showAddTeacherModal.value = false;
    showEditTeacherModal.value = false;
    form.value = {
        id: null,
        nip: '',
        spesialisasi: '',
        telepon: '',
        email: '',
        is_active: true,
    };
};

onMounted(fetchTeachers);
watch([searchQuery, perPage, currentPage], fetchTeachers);
</script>
