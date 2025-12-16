<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { FileText, Search, Calendar, Printer } from "lucide-vue-next";

const props = defineProps({ 
    laporan: Object,
    filters: Object 
});

// State Filter
const filterForm = ref({
    start_date: props.filters?.start_date ?? '',
    end_date: props.filters?.end_date ?? '',
    status: props.filters?.status ?? '',
    search: props.filters?.search ?? '',
});

// State Checkbox
const selectedIds = ref([]);

// Fungsi Filter
const applyFilter = () => {
    selectedIds.value = []; // Reset select saat filter
    router.get(route('laporan.permintaan'), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    filterForm.value = { start_date: '', end_date: '', status: '', search: '' };
    selectedIds.value = [];
    applyFilter();
};

// Logic Checkbox Select All
const toggleSelectAll = (e) => {
    if (e.target.checked) {
        selectedIds.value = props.laporan.data.map(item => item.id);
    } else {
        selectedIds.value = [];
    }
};

const isAllSelected = computed(() => {
    if (props.laporan.data.length === 0) return false;
    return props.laporan.data.every(item => selectedIds.value.includes(item.id));
});

// Logic Export
const exportData = (type) => {
    let params = { type: type }; // Set tipe: 'excel' atau 'pdf'

    if (selectedIds.value.length > 0) {
        // Jika checkbox dipilih
        params.selected_ids = selectedIds.value.join(',');
    } else {
        // Jika tidak, pakai filter
        params = { ...params, ...filterForm.value };
    }

    const url = route('laporan.permintaan.export', params);
    window.open(url, '_blank');
};

// Helper Warna Status
const getStatusClass = (status) => {
    switch(status) {
        case 'Approved': return 'bg-green-100 text-green-800';
        case 'Rejected': return 'bg-red-100 text-red-800';
        case 'Processed': return 'bg-blue-100 text-blue-800';
        default: return 'bg-yellow-100 text-yellow-800';
    }
};
</script>

<template>
    <Head title="Laporan Permintaan Barang" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Permintaan</h1>
                    <p class="mt-1 text-sm text-gray-600">Rekapitulasi dan cetak formulir permintaan barang.</p>
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
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari No / Pemohon</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Search class="h-4 w-4 text-gray-400" />
                                </div>
                                <input type="text" v-model="filterForm.search" placeholder="Cari..." 
                                    class="pl-10 block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dari</label>
                            <input type="date" v-model="filterForm.start_date" class="block w-full rounded-lg border-gray-300 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai</label>
                            <input type="date" v-model="filterForm.end_date" class="block w-full rounded-lg border-gray-300 sm:text-sm">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                            <select v-model="filterForm.status" class="block w-full rounded-lg border-gray-300 sm:text-sm">
                                <option value="">Semua</option>
                                <option value="Pending">Pending</option>
                                <option value="Processed">Processed</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
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
                                    <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Permintaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon / Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan Barang</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in laporan.data" :key="item.id" 
                                class="hover:bg-gray-50 transition-colors cursor-pointer"
                                :class="{'bg-teal-50': selectedIds.includes(item.id)}">
                                
                                <td class="px-4 py-4 text-center" @click.stop>
                                    <input type="checkbox" :value="item.id" v-model="selectedIds"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:border-teal-300 focus:ring focus:ring-teal-200 focus:ring-opacity-50" />
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ item.tanggal }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-teal-700">
                                    {{ item.no_permintaan }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="font-medium">{{ item.pemohon }}</div>
                                    <div class="text-xs text-gray-500">{{ item.unit }}</div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span v-if="item.total_item > 0">{{ item.ringkasan_barang }}</span>
                                    <span v-else class="italic text-gray-400">Belum ada item</span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="getStatusClass(item.status)">
                                        {{ item.status }}
                                    </span>
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