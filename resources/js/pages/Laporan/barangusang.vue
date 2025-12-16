<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { FileText, Printer, Search } from "lucide-vue-next";

const props = defineProps({ 
    laporan: Object,
    filters: Object 
});

const filterForm = ref({
    start_date: props.filters?.start_date ?? '',
    end_date: props.filters?.end_date ?? '',
    search: props.filters?.search ?? '',
});

const selectedIds = ref([]);

const applyFilter = () => {
    selectedIds.value = [];
    router.get(route('laporan.barang-usang'), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    filterForm.value = { start_date: '', end_date: '', search: '' };
    selectedIds.value = [];
    applyFilter();
};

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

const exportData = (type) => {
    let params = { type: type };
    if (selectedIds.value.length > 0) {
        params.selected_ids = selectedIds.value.join(',');
    } else {
        params = { ...params, ...filterForm.value };
    }
    const url = route('laporan.barang-usang.export', params);
    window.open(url, '_blank');
};
</script>

<template>
    <Head title="Laporan Barang Rusak" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Laporan Barang Rusak</h1>
                    <p class="mt-1 text-sm text-gray-600">Rekapitulasi dan cetak berita acara barang rusak.</p>
                </div>
                
                <div class="flex gap-2">
                    <button @click="exportData('excel')" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm font-medium text-sm">
                        <FileText class="w-4 h-4 mr-2" /> 
                        {{ selectedIds.length > 0 ? `Excel (${selectedIds.length})` : 'Excel' }}
                    </button>
                    <button @click="exportData('pdf')" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-sm font-medium text-sm">
                        <Printer class="w-4 h-4 mr-2" /> 
                        {{ selectedIds.length > 0 ? `PDF (${selectedIds.length})` : 'PDF' }}
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
                
                <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cari No Dokumen / Bukti</label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                                <input type="text" v-model="filterForm.search" placeholder="Cari..." 
                                    class="pl-9 block w-full rounded-lg border-gray-300 sm:text-sm focus:ring-teal-500 focus:border-teal-500">
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Dari Tanggal</label>
                            <input type="date" v-model="filterForm.start_date" class="block w-full rounded-lg border-gray-300 sm:text-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sampai Tanggal</label>
                            <input type="date" v-model="filterForm.end_date" class="block w-full rounded-lg border-gray-300 sm:text-sm">
                        </div>

                        <div class="md:col-span-2 flex gap-2">
                            <button @click="applyFilter" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal-700">Filter</button>
                            <button @click="resetFilter" class="px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50">Reset</button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center w-10">
                                    <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-200" />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pencatat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ringkasan Barang</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Item</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in laporan.data" :key="item.id" 
                                class="hover:bg-gray-50 transition-colors cursor-pointer"
                                :class="{'bg-teal-50': selectedIds.includes(item.id)}">
                                
                                <td class="px-4 py-4 text-center" @click.stop>
                                    <input type="checkbox" :value="item.id" v-model="selectedIds"
                                        class="rounded border-gray-300 text-teal-600 shadow-sm focus:ring-teal-200" />
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.tanggal }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-teal-700">{{ item.no_catat }}</div>
                                    <div class="text-xs text-gray-500">Bukti: {{ item.no_bukti }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ item.pencatat }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">{{ item.ringkasan }}</td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ item.total_item }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="laporan.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="text-sm text-gray-700">Halaman {{ laporan.current_page }} dari {{ laporan.last_page }}</div>
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