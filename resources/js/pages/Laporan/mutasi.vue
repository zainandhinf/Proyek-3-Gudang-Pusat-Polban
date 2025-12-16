<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3"; // Tambahkan Link
import { ref, watch, computed } from "vue";
import { Download, FileText, Printer, Search, Calendar, Filter } from "lucide-vue-next";

const props = defineProps({ 
    laporan: Object,
    filters: Object 
});

// State untuk Filter
const filterForm = ref({
    start_date: props.filters?.start_date ?? '',
    end_date: props.filters?.end_date ?? '',
    jenis: props.filters?.jenis ?? '',
    search: props.filters?.search ?? '', // Tambah Search
});

// State untuk Checkbox (Selection)
const selectedIds = ref([]);

// Fungsi Filter
const applyFilter = () => {
    // Reset selection saat filter berubah agar data konsisten
    selectedIds.value = [];
    
    router.get(route('laporan.mutasi'), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    filterForm.value = { start_date: '', end_date: '', jenis: '', search: '' };
    selectedIds.value = [];
    applyFilter();
};

// --- LOGIKA CHECKBOX ---

// Toggle Select All (Hanya untuk halaman ini)
const toggleSelectAll = (e) => {
    if (e.target.checked) {
        // Masukkan semua ID dari halaman ini ke selectedIds
        selectedIds.value = props.laporan.data.map(item => item.id);
    } else {
        selectedIds.value = [];
    }
};

// Computed property untuk status checkbox "Select All" header
const isAllSelected = computed(() => {
    if (props.laporan.data.length === 0) return false;
    // Cek apakah semua ID di halaman ini ada di selectedIds
    return props.laporan.data.every(item => selectedIds.value.includes(item.id));
});

// --- FUNGSI EXPORT PINTAR ---
const exportData = (type) => {
    let params = { type: type };

    if (selectedIds.value.length > 0) {
        // KASUS 1: USER MEMILIH DATA (CHECKBOX)
        // Kirim hanya ID yang dipilih (gabungkan jadi string "1,2,3")
        params.selected_ids = selectedIds.value.join(',');
    } else {
        // KASUS 2: TIDAK ADA YANG DIPILIH
        // Kirim filter yang sedang aktif
        params = { 
            ...params, 
            ...filterForm.value 
        };
    }

    const url = route('laporan.mutasi.export', params);
    window.open(url, '_blank');
};
</script>

<template>
    <Head title="Laporan Mutasi Barang" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Mutasi Barang</h1>
                    <p class="mt-1 text-sm text-gray-600">Rekapitulasi arus keluar masuk barang per item.</p>
                </div>
                
                <div class="flex gap-2">
                    <button @click="exportData('excel')" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm font-medium text-sm">
                        <FileText class="w-4 h-4 mr-2" /> 
                        {{ selectedIds.length > 0 ? `Excel (${selectedIds.length})` : 'Excel Semua' }}
                    </button>
                    <button @click="exportData('pdf')" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-sm font-medium text-sm">
                        <Printer class="w-4 h-4 mr-2" /> 
                        {{ selectedIds.length > 0 ? `PDF (${selectedIds.length})` : 'PDF Semua' }}
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Nomor Mutasi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Search class="h-4 w-4 text-gray-400" />
                                </div>
                                <input type="text" v-model="filterForm.search" placeholder="Contoh: MUT-2025..." 
                                    class="pl-10 block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                            <input type="date" v-model="filterForm.start_date" class="block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                            <input type="date" v-model="filterForm.end_date" class="block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Jenis</label>
                            <select v-model="filterForm.jenis" class="block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500">
                                <option value="">Semua</option>
                                <option value="masuk">Masuk</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>

                        <div class="md:col-span-3 flex gap-2">
                            <button @click="applyFilter" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal-700 transition">
                                Filter
                            </button>
                            <button @click="resetFilter" class="px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center w-10">
                                    <input type="checkbox" 
                                        :checked="isAllSelected"
                                        @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" 
                                    />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Mutasi</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan Barang</th> 
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(item, index) in laporan.data" :key="item.id" 
                                class="hover:bg-gray-50 transition-colors cursor-pointer"
                                :class="{'bg-teal-50': selectedIds.includes(item.id)}">
                                
                                <td class="px-4 py-4 text-center" @click.stop>
                                    <input type="checkbox" 
                                        :value="item.id" 
                                        v-model="selectedIds"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" 
                                    />
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                    {{ laporan.from + index }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ item.tanggal }}
                                </td>

                                <td class="px-6 py-4 text-sm font-mono text-teal-700 font-bold">
                                    {{ item.nomor_mutasi }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="item.jenis === 'masuk' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'">
                                        {{ item.jenis.toUpperCase() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-500 text-xs">
                                    <div v-if="item.no_dokumen !== '-'">No Dokumen: {{ item.no_dokumen }}</div>
                                    <div v-if="item.no_bukti !== '-'">No Bukti: {{ item.no_bukti }}</div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ item.ringkasan_barang }}
                                </td>

                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-800">
                                    {{ item.total_item }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="laporan.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="text-sm text-gray-700">
                        Halaman {{ laporan.current_page }} dari {{ laporan.last_page }}
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in laporan.links" :key="index">
                            <Link v-if="link.url" :href="link.url" preserve-scroll v-html="link.label" 
                                  class="relative inline-flex items-center px-3 py-1 text-sm font-medium border rounded-md transition-colors shadow-sm"
                                  :class="{'bg-teal-600 border-teal-600 text-white': link.active, 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active}" />
                            <span v-else v-html="link.label" class="relative inline-flex items-center px-3 py-1 text-sm font-medium border bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed rounded-md"></span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>