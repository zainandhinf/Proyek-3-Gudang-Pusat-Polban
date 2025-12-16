<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { throttle } from 'lodash';
import { 
    Search, 
    Eye, 
    Check, 
    X, 
    FileText, 
    Download 
} from "lucide-vue-next";

const user = usePage().props.auth.user;

// Props dari Controller
const props = defineProps({
    permintaans: Object, // Ubah ke Object karena paginate return object
    filters: Object
});

// State search
const search = ref(props.filters?.search ?? '');

// Watcher Search
watch(search, throttle(value => {
    router.get(route('permintaan.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// State Modal Preview
const showPreviewModal = ref(false);
const selectedFileUrl = ref("");
const selectedNoPermintaan = ref("");

// Helper Status Badge
const getStatusBadgeClass = (status) => {
    switch (status) {
        case "Approved":
            return "bg-green-100 text-green-800";
        case "Processed":
            return "bg-yellow-100 text-yellow-800";
        case "Completed":
            return "bg-blue-100 text-blue-800";
        case "Rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

// Modal Logic
const openPreview = (permintaan) => {
    selectedFileUrl.value = `/storage/${permintaan.file_path}`;
    selectedNoPermintaan.value = permintaan.no_permintaan;
    showPreviewModal.value = true;
};

const closePreview = () => {
    showPreviewModal.value = false;
    selectedFileUrl.value = "";
};

// Actions
const approveRequest = (id) => {
    if (confirm("Apakah Anda yakin ingin menyetujui permintaan ini?")) {
        router.post(route("permintaan.approve", id));
    }
};

const rejectRequest = (id) => {
    const catatan_reject = prompt("Masukkan catatan penolakan:");
    if (catatan_reject) {
        router.post(route("permintaan.reject", id), { catatan_reject });
    }
};
</script>

<template>
    <Head title="Proses Permintaan" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ user.role === 'operator' ? 'Proses Permintaan' : 'Persetujuan Permintaan' }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ user.role === 'operator' ? 'Kelola dan proses permintaan barang' : 'Validasi permintaan barang dari unit' }}
                </p>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h3 class="text-xl font-semibold text-gray-800">Daftar Permintaan</h3>
                        
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <select class="form-select py-2 pl-3 pr-8 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm">
                                <option>Semua Status</option>
                                <option>Pending</option>
                                <option>Processed</option>
                                <option>Approved</option>
                                <option>Rejected</option>
                            </select>

                            <div class="relative w-full sm:w-64">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <Search class="w-5 h-5 text-gray-400" />
                                </div>
                                <input 
                                    v-model="search"
                                    type="text" 
                                    placeholder="Cari nomor / pemohon..."
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Permintaan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keperluan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="props.permintaans.data.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data permintaan.
                                </td>
                            </tr>

                            <tr v-for="(item, index) in props.permintaans.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900">{{ props.permintaans.from + index }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-teal-700 font-mono">{{ item.no_permintaan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.pemohon.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.jenis_keperluan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.tanggal_pengajuan }}</td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="['px-2 py-1 text-xs font-semibold rounded-full', getStatusBadgeClass(item.status)]">
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center gap-2">
                                        <button 
                                            @click="openPreview(item)" 
                                            class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            title="Preview Surat"
                                        >
                                            <FileText class="w-4 h-4" />
                                        </button>

                                        <template v-if="user.role === 'operator'">
                                            <Link 
                                                v-if="item.status !== 'Approved' && item.status !== 'Rejected'"
                                                :href="route('permintaan.proses', item.id)" 
                                                class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded transition-colors"
                                                title="Proses"
                                            >
                                                <Eye class="w-4 h-4" />
                                            </Link>
                                            <span v-else class="text-gray-400 p-1.5 cursor-not-allowed">
                                                <Check class="w-4 h-4" />
                                            </span>
                                        </template>

                                        <template v-else-if="user.role === 'approval'">
                                            <button 
                                                v-if="item.status === 'Processed'"
                                                @click="approveRequest(item.id)"
                                                class="p-1.5 text-green-600 hover:text-green-800 hover:bg-green-50 rounded transition-colors"
                                                title="Setujui"
                                            >
                                                <Check class="w-4 h-4" />
                                            </button>
                                            <button 
                                                v-if="item.status === 'Processed'"
                                                @click="rejectRequest(item.id)"
                                                class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                                title="Tolak"
                                            >
                                                <X class="w-4 h-4" />
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="props.permintaans.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ props.permintaans.from ?? 0 }}</span>-<span class="font-medium">{{ props.permintaans.to ?? 0 }}</span> dari <span class="font-medium">{{ props.permintaans.total }}</span> data
                    </p>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in props.permintaans.links" :key="index">
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

        <div v-if="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity" @click="closePreview">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden transform transition-all scale-100" @click.stop>
                
                <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Preview Surat Pengajuan</h3>
                        <p class="text-sm text-gray-500">No: {{ selectedNoPermintaan }}</p>
                    </div>
                    <button @click="closePreview" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 p-2 rounded-full transition">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <div class="flex-1 bg-gray-100 p-4 relative overflow-hidden">
                    <iframe 
                        v-if="selectedFileUrl" 
                        :src="selectedFileUrl" 
                        class="w-full h-full rounded border border-gray-300 bg-white shadow-sm"
                        frameborder="0"
                    ></iframe>
                    <div v-else class="flex flex-col items-center justify-center h-full text-gray-500">
                        <FileText class="w-12 h-12 mb-2 text-gray-300" />
                        <p>File tidak ditemukan atau format tidak didukung.</p>
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button @click="closePreview" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition shadow-sm">
                        Tutup
                    </button>
                    <a 
                        :href="selectedFileUrl" 
                        target="_blank" 
                        download 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2 transition shadow-sm"
                    >
                        <Download class="w-4 h-4" />
                        Download File
                    </a>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>