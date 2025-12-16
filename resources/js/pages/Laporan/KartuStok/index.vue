<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { throttle } from 'lodash';
import { 
    Search, 
    FileSpreadsheet, // Icon Excel
    Filter,
    Calendar
} from 'lucide-vue-next';

const props = defineProps({
    kartuStok: Object,
    filters: Object,
    years: Array,
});

const search = ref(props.filters.search || '');
const selectedYear = ref(props.filters.year || new Date().getFullYear());

const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

// Search & Filter Watcher
watch([search, selectedYear], throttle(([newSearch, newYear]) => {
    router.get(route('laporan.kartu-stok'), {
        search: newSearch,
        year: newYear
    }, { preserveState: true, replace: true });
}, 500));

// Export Function
const exportExcel = () => {
    const url = route('laporan.kartu-stok.export', { year: selectedYear.value });
    window.open(url, '_blank');
};

const getMutationClass = (val) => {
    if (val > 0) return 'text-green-600 font-bold'; // Masuk
    if (val < 0) return 'text-red-600 font-bold';   // Keluar
    return 'text-gray-300';
};
</script>

<template>
    <Head title="Laporan Kartu Stok" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kartu Stok Barang</h1>
                    <p class="mt-1 text-sm text-gray-600">Monitoring pergerakan stok per bulan (Tahun {{ selectedYear }}).</p>
                </div>
                
                <button 
                    @click="exportExcel" 
                    class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm font-medium text-sm"
                >
                    <FileSpreadsheet class="w-4 h-4 mr-2" /> 
                    Export Excel
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                        
                        <div class="md:col-span-3 flex items-center bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm">
                            <Calendar class="w-4 h-4 text-gray-500 mr-2" />
                            <select v-model="selectedYear" class="bg-transparent border-none text-sm w-full focus:ring-0 p-0 text-gray-700">
                                <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                            </select>
                        </div>

                        <div class="md:col-span-5"></div>

                        <div class="md:col-span-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="h-4 w-4 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                v-model="search" 
                                placeholder="Cari nama atau kode barang..." 
                                class="pl-10 block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th rowspan="2" class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase border-r">No</th>
                                <th rowspan="2" class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase border-r w-48">Barang</th>
                                <th rowspan="2" class="px-2 py-3 text-center text-xs font-bold text-gray-500 uppercase border-r">Sat</th>
                                <th rowspan="2" class="px-3 py-3 text-center text-xs font-bold text-gray-900 uppercase border-r bg-yellow-50">Awal</th>
                                
                                <th colspan="12" class="px-2 py-2 text-center text-xs font-bold text-gray-500 uppercase border-b">
                                    Mutasi Bulan ({{ selectedYear }})
                                </th>

                                <th rowspan="2" class="px-3 py-3 text-center text-xs font-bold text-gray-500 uppercase border-l">Pakai</th>
                                <th rowspan="2" class="px-3 py-3 text-center text-xs font-bold text-gray-900 uppercase border-l bg-teal-50">Akhir</th>
                            </tr>
                            <tr>
                                <th v-for="(m, i) in months" :key="i" class="px-2 py-1 text-center text-[10px] font-medium text-gray-500 border-r last:border-r-0 min-w-[40px]">
                                    {{ m }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="kartuStok.data.length === 0">
                                <td colspan="18" class="px-6 py-8 text-center text-gray-500">
                                    Data tidak ditemukan untuk tahun {{ selectedYear }}.
                                </td>
                            </tr>
                            <tr v-for="(item, idx) in kartuStok.data" :key="item.id" class="hover:bg-gray-50 transition-colors text-xs">
                                <td class="px-3 py-3 text-center text-gray-500 border-r">
                                    {{ kartuStok.from + idx }}
                                </td>
                                <td class="px-4 py-3 border-r">
                                    <div class="font-bold text-gray-800">{{ item.nama_barang }}</div>
                                    <div class="text-[10px] text-gray-500 font-mono">{{ item.kode_barang }}</div>
                                </td>
                                <td class="px-2 py-3 text-center text-gray-500 border-r">{{ item.satuan }}</td>
                                
                                <td class="px-3 py-3 text-center font-bold text-gray-900 bg-yellow-50 border-r">
                                    {{ item.saldo_awal }}
                                </td>

                                <template v-for="bulan in 12" :key="bulan">
                                    <td class="px-1 py-3 text-center border-r border-gray-100" 
                                        :class="getMutationClass(item.mutasi[bulan])">
                                        {{ item.mutasi[bulan] === 0 ? '-' : (item.mutasi[bulan] > 0 ? '+'+item.mutasi[bulan] : item.mutasi[bulan]) }}
                                    </td>
                                </template>

                                <td class="px-3 py-3 text-center text-red-700 font-medium border-l border-r">
                                    {{ item.total_pemakaian }}
                                </td>

                                <td class="px-3 py-3 text-center font-bold text-teal-900 bg-teal-50 border-l">
                                    {{ item.saldo_akhir }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="kartuStok.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="text-xs text-gray-700">
                        Halaman {{ kartuStok.current_page }} dari {{ kartuStok.last_page }}
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, key) in kartuStok.links" :key="key">
                            <Link 
                                v-if="link.url" :href="link.url" v-html="link.label"
                                class="px-3 py-1 border rounded text-xs font-medium transition"
                                :class="{ 
                                    'bg-teal-600 text-white border-teal-600': link.active, 
                                    'bg-white text-gray-700 hover:bg-gray-50 border-gray-300': !link.active 
                                }"
                            />
                            <span v-else v-html="link.label" class="px-3 py-1 border rounded text-xs text-gray-400 bg-gray-100 cursor-not-allowed"></span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>