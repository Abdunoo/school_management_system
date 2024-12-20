<template>
    <div class="bg-white rounded-lg shadow p-6 space-y-6 h-full flex flex-col" style="max-height: 100vh;">

        <!-- Table Controls -->
        <div class="space-y-4 bg-gray-100 p-4 rounded-lg ">
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
                    <Button :variant="'primary'">Tambah Guru</Button>
                </div>
                <div class="relative flex space-x-2 text-secondary">
                    <Button @click="exportTeachers" :variant="'success'">Export</Button>
                    <input type="text" v-model="searchQuery" placeholder="Search..."
                        aria-label="Search teachers"
                        class="block w-full text-sm border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary px-3 py-2 placeholder-secondary focus:outline-none">
                    <MagnifyingGlassIcon class="absolute right-3 top-1/2 -translate-y-1/2 h-5 w-5 text-secondary" />
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-300 bg-gray-50 hidden md:table">
            <table class="min-w-full table-auto text-sm text-left space-y-8 hidden md:flex">
                <thead>
                    <tr class="bg-gray-100 grid grid-cols-7">
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

        <div class="overflow-y-auto max-h-full h-3/6 rounded-xl border border-gray-300 bg-gray-50 hidden md:flex">
            <table class="min-w-full table-auto text-sm text-left space-y-8 hidden md:table">
                <tbody class="divide-y divide-gray-200">
                    <tr v-if="teachers.length === 0">
                        <td colspan="7" class="text-center py-4 text-secondary">No teachers found.</td>
                    </tr>
                    <tr v-for="teacher in teachers" :key="teacher.id"
                        class="hover:bg-gray-100 transition grid grid-cols-7 items-center">
                        <td class="px-4 py-3 text-secondary">{{ teacher.nip }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.spesialisasi }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.telepon }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user.username }}</td>
                        <td class="px-4 py-3 text-secondary">{{ teacher.user.email }}</td>
                        <td class="px-4 py-3 text-secondary">
                            <Badge> {{ teacher.user.is_active ? 'Aktif' : 'Tidak Aktif' }}</Badge>
                        </td>
                        <td class="px-4 py-3 space-x-2 flex">
                            <Button :variant="'warning'">Update</Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Card for Mobile -->
        <div class="block md:hidden space-y-4">
            <div v-for="teacher in teachers" :key="teacher.id"
                class="bg-white shadow-md rounded-lg border border-gray-300 p-4">
                <div class="flex flex-col space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-secondary">NIP</span>
                        <span class="text-sm">{{ teacher.nip }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-secondary">Mapel</span>
                        <span class="text-sm">{{ teacher.spesialisasi }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-secondary">Telepon</span>
                        <span class="text-sm">{{ teacher.telepon }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-secondary">Username</span>
                        <span class="text-sm">{{ teacher.user.username }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-secondary">Email</span>
                        <span class="text-sm">{{ teacher.user.email }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-secondary">Status</span>
                        <Badge>{{ teacher.user.is_active ? 'Aktif' : 'Tidak Aktif' }}</Badge>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <Button :variant="'warning'">Update</Button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center space-x-2 mt-4">
            <Button :variant="'secondary'" @click="currentPage--"
                class="bg-gray-200 px-4 py-2 rounded">
                Previous
            </Button>

            <Button v-for="page in totalPages" :key="page" @click="currentPage = page" :variant="'secondary'" :class="{
                'bg-primary text-white': page === currentPage,
                'bg-gray-200': page !== currentPage,
            }" class="px-4 py-2 rounded">
                {{ page }}
            </Button>

            <Button :variant="'secondary'" @click="currentPage++" 
                class="bg-gray-200 px-4 py-2 rounded">
                Next
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue';
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
import Button from '@/components/common/Button.vue';
import { Teacher } from '@/types';
import apiClient from '@/helpers/axios';
import Badge from '@/components/common/Badge.vue';

const pageTitle = ref('Teachers Management');
const teachers = ref<Teacher[]>([]);
const perPageOptions = [10, 20, 30];
const perPage = ref(perPageOptions[0]);
const currentPage = ref(1);
const searchQuery = ref('');
const totalRecords = ref(0); 
const totalPages = computed(() => Math.ceil(totalRecords.value / perPage.value));


const fetchTeachers = async () => {
    console.log('totalPages ', totalPages)
    try {
        const response = await apiClient.get('/api/teachers', {
            params: {
                per_page: perPage.value,
                page: currentPage.value,
                search: searchQuery.value,
            },
        });
        console.log(response)
        teachers.value = response.data; 
        totalRecords.value = response.total;
    } catch (error) {
        console.error('Failed to fetch teachers:', error);
    }
};

const exportTeachers = async () => {
    try {
        const response = await apiClient.get('/api/teachers/export', {
            params: { search: searchQuery.value },
            responseType: 'blob',
        });
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', ' teachers_export.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link); // Clean up the link element
    } catch (error) {
        console.error('Failed to export teachers:', error);
    }
};

onMounted(() => {
    fetchTeachers();
});

watch([searchQuery, perPage, currentPage], fetchTeachers);
</script>