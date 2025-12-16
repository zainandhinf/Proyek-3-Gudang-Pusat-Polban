<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { debounce } from 'lodash';

// Terima Data dari Controller (variable 'approvals' bukan 'pemohons')
const props = defineProps({
    approvals: Object, 
    filters: Object // Terima filter search
});

// Setup Search
const search = ref(props.filters?.search || '');

// Logic Search Otomatis (300ms delay)
watch(search, debounce((value) => {
    router.get(route('manajemen-akun.approval.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Fungsi Hapus
const deleteUser = (id, name) => {
    if (confirm(`Apakah Anda yakin ingin menghapus akun Approval ${name}?`)) {
        router.delete(route('approval.destroy', id), {
            onSuccess: () => {
                // Optional: action setelah sukses hapus
            }
        });
    }
};

const page = usePage();
const successMessage = computed(() => page.props.flash?.success);
</script>

<template>
    <Head title="Manajemen Akun Approval" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Akun Approval
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div v-if="successMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ successMessage }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Daftar Akun Approval</h3>
                            <p class="text-sm text-gray-500">Kelola akun approval barang dari berbagai unit kerja</p>
                        </div>
                        <Link :href="route('approval.create')" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center gap-2">
                            <span>+ Tambah Akun</span>
                        </Link>
                    </div>

                    <div class="flex gap-4 mb-4 justify-end">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama atau NIP..." 
                            class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500 w-64"
                        >
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Approval</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(user, index) in approvals.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ (approvals.current_page - 1) * approvals.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-xs mr-3">
                                                {{ user.name.substring(0, 2).toUpperCase() }}
                                            </div>
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.nip }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.unit_kerja?.nama_unit || '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center gap-3">
                                            <Link :href="route('approval.edit', user.id)" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                </svg>
                                            </Link>
                                            <button @click="deleteUser(user.id, user.name)" class="text-red-600 hover:text-red-900" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="approvals.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada data approval.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <template v-for="(link, key) in approvals.links" :key="key">
                            <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-3 py-1 border rounded mx-1 text-sm" :class="{ 'bg-blue-600 text-white': link.active, 'bg-white text-gray-700 hover:bg-gray-50': !link.active }" />
                            <span v-else v-html="link.label" class="px-3 py-1 border rounded mx-1 text-sm text-gray-400 bg-gray-100"></span>
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>