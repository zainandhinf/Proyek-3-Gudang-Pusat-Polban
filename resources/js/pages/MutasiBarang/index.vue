<script setup>
import { ref, watch, computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { throttle } from 'lodash';
import { 
    Plus, 
    Search, 
    Edit2, 
    Trash2, 
    Eye, 
    Upload, 
    Download, 
    Filter 
} from "lucide-vue-next";

// Props
const props = defineProps({ 
    mutasis: Object, 
    filters: Object, 
});

// User & Role Logic
const page = usePage();
const user = computed(() => page.props.auth.user);
// Asumsi role operator adalah 'operator'
const isOperator = computed(() => user.value.role === 'operator');

// State Pencarian
const search = ref(props.filters?.search ?? '');

// Watcher Search
watch(search, throttle(value => {
    router.get(route('mutasi-barang.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Helper Format Tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-'; 
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit', month: 'long', year: 'numeric'
    }).format(date);
};

// Fungsi Hapus
const deleteMutasi = (id) => {
    if (confirm('Yakin ingin menghapus data mutasi ini? Stok barang akan dikembalikan.')) {
        router.delete(route('mutasi-barang.destroy', id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Mutasi Barang" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Mutasi Barang</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Kelola riwayat pencatatan barang masuk dan keluar
                </p>
            </div>

            <div v-if="isOperator" class="flex justify-end mt-6 gap-x-3">
                <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition">
                    <Download class="w-4 h-4 mr-2" />
                    Export
                </button>
                <Link
                    :href="route('mutasi-barang.create')"
                    class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm transition"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Buat Mutasi
                </Link>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Riwayat Mutasi</h3>
                            <p class="text-sm text-gray-500 mt-1">Daftar transaksi stok barang</p>
                        </div>
                        
                        <div class="flex gap-3 w-full md:w-auto">
                            <button class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm">
                                <Filter class="w-4 h-4 mr-2 text-gray-500" />
                                Filter
                            </button>
                            
                            <div class="relative w-full md:w-64">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <Search class="w-5 h-5 text-gray-400" />
                                </div>
                                <input 
                                    type="text" 
                                    v-model="search"
                                    placeholder="Cari nomor atau keterangan..."
                                    class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Mutasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                <th v-if="isOperator" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="props.mutasis.data.length === 0">
                                <td :colspan="isOperator ? 6 : 5" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data riwayat mutasi.
                                </td>
                            </tr>

                            <tr v-for="(item, idx) in props.mutasis.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ props.mutasis.from + idx }}</td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-teal-700">
                                    {{ item.nomor_mutasi }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span 
                                        class="px-2 py-1 rounded-full text-xs font-semibold"
                                        :class="item.jenis_mutasi === 'masuk' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'"
                                    >
                                        {{ item.jenis_mutasi.toUpperCase() }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ formatDate(item.tanggal_mutasi) }}
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">
                                    {{ item.keterangan || '-' }}
                                </td>
                                
                                <td v-if="isOperator" class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <Link 
                                            :href="route('mutasi-barang.show', item.id)" 
                                            class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            title="Detail"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </Link>
                                        
                                        <Link 
                                            :href="route('mutasi-barang.edit', item.id)" 
                                            class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded transition-colors"
                                            title="Edit"
                                        >
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        
                                        <button 
                                            @click="deleteMutasi(item.id)" 
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

                <div v-if="mutasis.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ mutasis.from ?? 0 }}</span>-<span class="font-medium">{{ mutasis.to ?? 0 }}</span> dari <span class="font-medium">{{ mutasis.total }}</span> data
                    </p>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in mutasis.links" :key="index">
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