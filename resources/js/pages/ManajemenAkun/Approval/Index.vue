<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { debounce } from 'lodash';
import { 
    Search, 
    Plus, 
    Edit2, 
    Trash2, 
    ShieldCheck 
} from 'lucide-vue-next';

// Terima Data dari Controller
const props = defineProps({
    approvals: Object, 
    filters: Object
});

// Setup Search
const search = ref(props.filters?.search || '');

// Logic Search Otomatis
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
            preserveScroll: true,
        });
    }
};

const page = usePage();
</script>

<template>
    <Head title="Manajemen Akun Approval" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Akun Approval</h1>
                    <p class="mt-1 text-sm text-gray-600">Kelola akun yang berwenang menyetujui permintaan barang.</p>
                </div>
                
                <Link 
                    :href="route('approval.create')" 
                    class="flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-sm transition font-medium text-sm"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Akun
                </Link>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md flex items-center">
                <ShieldCheck class="w-5 h-5 mr-2" />
                {{ $page.props.flash.success }}
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-end">
                    <div class="relative w-full md:w-72">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <Search class="w-5 h-5 text-gray-400" />
                        </div>
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama atau NIP..." 
                            class="w-full py-2 pl-10 text-sm border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                        >
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Approval</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="approvals.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Data akun approval tidak ditemukan.
                                </td>
                            </tr>
                            <tr v-for="(user, index) in approvals.data" :key="user.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ (approvals.current_page - 1) * approvals.per_page + index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-9 w-9 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xs mr-3 border border-orange-200">
                                            {{ user.name.substring(0, 2).toUpperCase() }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                    {{ user.nip }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <span v-if="user.unit_kerja" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        {{ user.unit_kerja.nama_unit }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <Link :href="route('approval.edit', user.id)" class="p-1.5 text-teal-600 hover:bg-teal-50 rounded-md transition-colors" title="Edit">
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        
                                        <button 
                                            @click="deleteUser(user.id, user.name)" 
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition-colors" 
                                            title="Hapus"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="approvals.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="text-sm text-gray-700">
                        Halaman {{ approvals.current_page }} dari {{ approvals.last_page }}
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, key) in approvals.links" :key="key">
                            <Link 
                                v-if="link.url" 
                                :href="link.url" 
                                v-html="link.label"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border rounded-md transition-colors shadow-sm"
                                :class="{ 
                                    'bg-teal-600 border-teal-600 text-white hover:bg-teal-700': link.active, 
                                    'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active 
                                }"
                            />
                            <span 
                                v-else 
                                v-html="link.label"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed rounded-md shadow-sm"
                            ></span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>