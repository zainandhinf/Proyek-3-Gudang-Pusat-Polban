<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router, usePage, useForm } from "@inertiajs/vue3"; // Import usePage
import { ref, watch, computed } from "vue"; // Import computed
import { throttle } from 'lodash';
import { 
    Search, 
    Plus, 
    Edit, 
    Eye, 
    Play, 
    Filter,
    Upload,         
    FileSpreadsheet,
    X               
} from "lucide-vue-next";

// Props
const props = defineProps({ 
    opnames: Object,
    filters: Object 
});

// User & Role Logic
const page = usePage();
const user = computed(() => page.props.auth.user);
const isOperator = computed(() => user.value.role === 'operator');

// State Search
const search = ref(props.filters?.search ?? '');

// Watcher Search
watch(search, throttle(value => {
    router.get(route('stock-opname.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Helper Status Badge
const getStatusClass = (status) => {
    if (status === 'Selesai') {
        return 'bg-green-100 text-green-800 border-green-200';
    } else if (status === 'Proses') {
        return 'bg-blue-100 text-blue-800 border-blue-200';
    }
    return 'bg-gray-100 text-gray-800 border-gray-200';
};

// Helper Format Tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// --- LOGIC IMPORT ---
const showImportModal = ref(false);
const importForm = useForm({ file: null });

const submitImport = () => {
    importForm.post(route('stock-opname.import'), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Stock Opname</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Riwayat pengecekan stok fisik (Audit Gudang)
                </p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                {{ $page.props.flash.error }}
            </div>

            <div v-if="isOperator" class="flex justify-end mt-6 gap-x-3">
                <button @click="showImportModal = true" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition font-medium text-sm">
                    <Upload class="w-4 h-4 mr-2" />
                    Import Excel
                </button>

                <a :href="route('laporan.stock-opname')" target="_blank" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition font-medium text-sm">
                    <FileSpreadsheet class="w-4 h-4 mr-2" />
                    Laporan
                </a>

                <Link
                    :href="route('stock-opname.store')" 
                    method="post"
                    as="button"
                    class="flex items-center px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 shadow-sm transition font-medium text-sm"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Mulai Opname Baru
                </Link>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h3 class="text-xl font-semibold text-gray-800">Daftar Opname</h3>
                        
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
                                    v-model="search"
                                    type="text" 
                                    placeholder="Cari nomor opname..."
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
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Opname</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="opnames.data.length === 0">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Belum ada data stock opname.
                                </td>
                            </tr>

                            <tr v-for="(item, index) in opnames.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ opnames.from + index }}</td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-teal-700 font-mono">
                                    {{ item.no_opname }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(item.tanggal_opname) }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ item.dicatat_oleh?.name || '-' }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="['px-2.5 py-1 rounded-full text-xs font-medium border', getStatusClass(item.status)]">
                                        {{ item.status }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-2">
                                        <Link 
                                            v-if="item.status === 'Proses' && isOperator" 
                                            :href="route('stock-opname.edit', item.id)" 
                                            class="text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1 hover:bg-blue-50 px-2 py-1 rounded transition"
                                        >
                                            <Play class="w-4 h-4" /> Lanjutkan
                                        </Link>
                                        
                                        <Link 
                                            v-else 
                                            :href="route('stock-opname.show', item.id)" 
                                            class="text-teal-600 hover:text-teal-800 font-medium flex items-center gap-1 hover:bg-teal-50 px-2 py-1 rounded transition"
                                        >
                                            <Eye class="w-4 h-4" /> Detail
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="opnames.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ opnames.from ?? 0 }}</span>-<span class="font-medium">{{ opnames.to ?? 0 }}</span> dari <span class="font-medium">{{ opnames.total }}</span> data
                    </p>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in opnames.links" :key="index">
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

        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
                <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Import Stock Opname</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-1 transition">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-5 text-xs text-orange-800">
                        <p class="font-bold mb-1 flex items-center gap-1">
                            <FileSpreadsheet class="w-3 h-3" /> Perhatian:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 ml-1">
                            <li>Gunakan file Excel hasil export <strong>Laporan Stock Opname</strong>.</li>
                            <li>Import ini akan <strong>MENGUBAH STOK MASTER</strong> sesuai kolom "Stok Fisik".</li>
                            <li>Status transaksi akan otomatis menjadi <strong>Selesai</strong>.</li>
                        </ul>
                    </div>

                    <form @submit.prevent="submitImport">
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                            <input type="file" @input="importForm.file = $event.target.files[0]" accept=".xlsx, .xls"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition cursor-pointer border border-gray-300 rounded-lg" />
                            <div v-if="importForm.errors.file" class="text-red-500 text-xs mt-1">{{ importForm.errors.file }}</div>
                        </div>

                        <div v-if="importForm.progress" class="w-full bg-gray-100 rounded-full h-2 mb-4 overflow-hidden">
                            <div class="bg-teal-600 h-2 rounded-full transition-all duration-300" :style="{ width: importForm.progress.percentage + '%' }"></div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showImportModal = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="importForm.processing" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-medium shadow-sm flex items-center transition disabled:opacity-50">
                                <Upload v-if="!importForm.processing" class="w-4 h-4 mr-2" />
                                <span v-else class="mr-2">Mengupload...</span>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>