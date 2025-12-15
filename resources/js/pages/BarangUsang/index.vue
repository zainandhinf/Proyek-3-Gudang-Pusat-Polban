<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { throttle } from 'lodash';
import { Search, Plus, Edit2, Trash2 } from 'lucide-vue-next';

// Props
const props = defineProps({
    barangUsangs: Object, // Ubah ke Object karena paginate return object
    filters: Object,
});

// User & Role
const page = usePage();
const user = computed(() => page.props.auth.user);
// Asumsi role operator adalah 'operator'
const isOperator = computed(() => user.value.role === 'operator');

// State Search
const search = ref(props.filters?.search ?? '');

// Watcher Search
watch(search, throttle((value) => {
    router.get(route('barang-usang.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// Fungsi Delete
const deleteItem = (id) => {
    if (confirm('Hapus laporan barang usang ini?')) {
        router.delete(route('barang-usang.destroy', id), {
            preserveScroll: true
        });
    }
};

// Helper Status Class
const getStatusClass = (status) => {
    switch (status) {
        case 'Disetujui': return 'bg-green-100 text-green-800 border-green-200';
        case 'Ditolak': return 'bg-red-100 text-red-800 border-red-200';
        case 'Pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        default: return 'bg-gray-100 text-gray-800 border-gray-200';
    }
};
</script>

<template>
    <Head title="Barang Usang/Rusak" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Barang Usang/Rusak</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Kelola pelaporan barang yang rusak atau sudah usang (penghapusan aset).
                </p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ $page.props.flash?.success }}
            </div>

            <div class="flex justify-end mt-6 px-4 sm:px-0">
                <Link 
                    v-if="isOperator" 
                    :href="route('barang-usang.create')" 
                    class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm transition font-medium text-sm"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Lapor Barang Rusak
                </Link>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h3 class="text-xl font-semibold text-gray-800">Daftar Laporan</h3>
                        
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <Search class="w-5 h-5 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                v-model="search" 
                                placeholder="Cari barang..." 
                                class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm" 
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th v-if="isOperator" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="barangUsangs.data.length === 0">
                                <td :colspan="isOperator ? 7 : 6" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data laporan.
                                </td>
                            </tr>

                            <tr v-for="(item, index) in barangUsangs.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ barangUsangs.from + index }}</td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.tanggal_laporan }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ item.barang?.nama_barang || 'Barang Terhapus' }}
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">{{ item.barang?.kode_barang }}</div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-red-600">
                                    {{ item.jumlah }}
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                    {{ item.keterangan }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="['px-2.5 py-1 text-xs font-semibold rounded-full border', getStatusClass(item.status)]">
                                        {{ item.status }}
                                    </span>
                                </td>
                                
                                <td v-if="isOperator" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <Link 
                                            :href="route('barang-usang.edit', item.id)" 
                                            class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded transition-colors"
                                            title="Edit"
                                        >
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        
                                        <button 
                                            @click="deleteItem(item.id)" 
                                            class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
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

                <div v-if="barangUsangs.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ barangUsangs.from ?? 0 }}</span>-<span class="font-medium">{{ barangUsangs.to ?? 0 }}</span> dari <span class="font-medium">{{ barangUsangs.total }}</span> data
                    </p>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in barangUsangs.links" :key="index">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
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