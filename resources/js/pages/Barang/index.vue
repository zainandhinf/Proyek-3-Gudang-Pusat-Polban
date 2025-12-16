<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage, useForm } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick } from 'vue';
import { throttle } from 'lodash';
import QrcodeVue from 'qrcode.vue';
import {
    Search,
    Upload,
    Plus,
    Edit2,
    Trash2,
    QrCode,
    X,
    Download,
    FileSpreadsheet
} from "lucide-vue-next";

// Props dari Controller
const props = defineProps({
    barangs: Object,
    filters: Object,
});

// Akses Data User & Role
const page = usePage();
const user = computed(() => page.props.auth.user);
const isOperator = computed(() => user.value.role === 'operator');

// State Pencarian
const search = ref(props.filters?.search ?? '');

// Helper Format Rupiah
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Logic Search
watch(search, throttle((value) => {
    router.get(route('barangs.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// Logic Delete
const deleteBarang = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data barang ini?')) {
        router.delete(route('barangs.destroy', id), {
            preserveScroll: true,
        });
    }
};

// --- LOGIC MODAL QR CODE ---
const showQrModal = ref(false);
const selectedBarangQr = ref(null);

const openQrModal = (barang) => {
    selectedBarangQr.value = barang;
    showQrModal.value = true;
};

const closeQrModal = () => {
    showQrModal.value = false;
    selectedBarangQr.value = null;
};

// --- LOGIC DOWNLOAD QR ---
const downloadQrCode = async () => {
    await nextTick();
    const canvas = document.querySelector('.qr-canvas-wrapper canvas');
  
    if (canvas) {
        try {
            const image = canvas.toDataURL("image/png");
            if (image.length < 100) {
                alert("Gambar QR Code belum siap. Silakan coba lagi sebentar lagi.");
                return;
            }
          
            const link = document.createElement('a');
            link.href = image;
            const safeName = selectedBarangQr.value.kode_barang.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            link.download = `QR-${safeName}.png`;
          
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          
        } catch (e) {
            console.error("Gagal download QR:", e);
            alert("Gagal mendownload QR Code.");
        }
    } else {
        console.error("Canvas QR Code tidak ditemukan di DOM");
    }
};

// --- LOGIC IMPORT ---
const showImportModal = ref(false);
const importForm = useForm({ file: null });

const submitImport = () => {
    importForm.post(route('barangs.import'), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Master Data Barang" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Master Data Barang</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola semua data barang, stok, dan harga di gudang.</p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                {{ $page.props.flash.error }}
            </div>

            <div class="flex justify-end mt-6 gap-x-3">
                <button 
                    v-if="isOperator" 
                    @click="showImportModal = true"
                    class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition"
                >
                    <Upload class="w-4 h-4 mr-2" />
                    Import Excel
                </button>

                <a :href="route('laporan.data-barang')" target="_blank" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition">
                    <FileSpreadsheet class="w-4 h-4 mr-2" />
                    Laporan
                </a>
                
                <Link v-if="isOperator" :href="route('barangs.create')" class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm transition">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Barang
                </Link>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Daftar Barang</h3>
                            <p class="text-sm text-gray-500 mt-1">List seluruh item inventory</p>
                        </div>
                        
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <Search class="w-5 h-5 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Cari kode atau nama barang..."
                                class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Info Barang</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Satuan</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Stok</th>
                                <th v-if="isOperator" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="barangs.data.length === 0">
                                <td :colspan="isOperator ? 5 : 4" class="px-6 py-8 text-center text-gray-500">
                                    Data barang tidak ditemukan.
                                </td>
                            </tr>
                            
                            <tr v-for="barang in barangs.data" :key="barang.id" class="hover:bg-gray-50 transition-colors">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img v-if="barang.foto" :src="`/storage/${barang.foto}`" class="h-10 w-10 rounded-md object-cover border" alt="Foto Barang">
                                            <div v-else class="h-10 w-10 rounded-md bg-gray-100 flex items-center justify-center text-gray-400 text-xs font-medium border border-gray-200">No Img</div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ barang.nama_barang }}</div>
                                            <div class="text-xs text-teal-600 font-mono bg-teal-50 px-1.5 py-0.5 rounded inline-block mt-1 border border-teal-100">{{ barang.kode_barang }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ barang.satuan?.nama_satuan || '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ formatRupiah(barang.harga) }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="font-bold px-2 py-1 rounded text-xs" :class="barang.stok_saat_ini > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                        {{ barang.stok_saat_ini }}
                                    </span>
                                </td>

                                <td v-if="isOperator" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <button 
                                            @click="openQrModal(barang)" 
                                            class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-colors"
                                            title="Lihat QR Code"
                                        >
                                            <QrCode class="w-4 h-4" />
                                        </button>
                                        <Link :href="route('barangs.edit', barang.id)" class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded transition-colors" title="Edit">
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        <button @click="deleteBarang(barang.id)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="barangs.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ barangs.from ?? 0 }}</span> sampai <span class="font-medium">{{ barangs.to ?? 0 }}</span> dari <span class="font-medium">{{ barangs.total }}</span> data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in barangs.links" :key="index">
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

        <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm overflow-hidden transform transition-all scale-100">
                
                <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">QR Code Barang</h3>
                    <button @click="closeQrModal" class="text-gray-400 hover:text-gray-600 transition bg-white rounded-full p-1 hover:bg-gray-200">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 flex flex-col items-center text-center">
                    <div class="qr-canvas-wrapper bg-white p-4 border border-gray-200 rounded-xl mb-5 shadow-sm inline-block">
                        <QrcodeVue 
                            :key="selectedBarangQr.id"
                            :value="selectedBarangQr.kode_barang" 
                            :size="200" 
                            level="H" 
                            render-as="canvas"
                            foreground="#1e293b"
                            background="#ffffff"
                        />
                    </div>

                    <h4 class="text-lg font-bold text-slate-800 leading-tight mb-1">{{ selectedBarangQr.nama_barang }}</h4>
                    <span class="text-xs font-mono font-medium text-teal-700 bg-teal-50 px-2 py-1 rounded border border-teal-100">
                        {{ selectedBarangQr.kode_barang }}
                    </span>
                    <p class="text-xs text-slate-400 mt-4 max-w-[250px]">
                        Scan QR Code ini menggunakan pemindai untuk mengakses detail barang.
                    </p>
                </div>

                <div class="p-4 border-t border-gray-100 bg-gray-50 flex gap-3">
                    <button 
                        @click="closeQrModal"
                        class="flex-1 py-2.5 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-white hover:shadow-sm transition font-medium text-sm"
                    >
                        Tutup
                    </button>
                    
                    <button 
                        @click="downloadQrCode"
                        class="flex-1 py-2.5 px-4 bg-teal-600 text-white rounded-lg hover:bg-teal-700 shadow-sm transition font-medium text-sm flex items-center justify-center gap-2"
                    >
                        <Download class="w-4 h-4" />
                        Download
                    </button>
                </div>
            </div>
        </div>

        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
                <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Import Data Barang</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-1 transition">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="bg-teal-50 border border-teal-200 rounded-lg p-4 mb-5 text-xs text-teal-800">
                        <p class="font-bold mb-1 flex items-center gap-1">
                            <FileSpreadsheet class="w-3 h-3" /> Konsep Import (Akumulasi):
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 ml-1">
                            <li>Gunakan file Excel hasil export laporan.</li>
                            <li>
                                <strong>Barang Sudah Ada:</strong> Stok di Excel akan 
                                <span class="font-bold underline">DITAMBAHKAN</span> ke stok sistem saat ini.
                                <br>
                                <span class="italic text-gray-500">(Contoh: Sistem 10 + Excel 5 = Jadi 15)</span>
                            </li>
                            <li>
                                <strong>Barang Belum Ada:</strong> Akan otomatis dibuat sebagai Data Baru.
                            </li>
                        </ul>
                    </div>

                    <form @submit.prevent="submitImport">
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel (.xlsx)</label>
                            <input type="file" @input="importForm.file = $event.target.files[0]" accept=".xlsx, .xls"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition cursor-pointer border border-gray-300 rounded-lg" />
                            <div v-if="importForm.errors.file" class="text-red-500 text-xs mt-1">{{ importForm.errors.file }}</div>
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