<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed, nextTick } from 'vue';
import { throttle } from 'lodash';
import QrcodeVue from 'qrcode.vue';
import {
  Search,
  Upload,
  Plus,
  Edit2,
  Trash2,
  ChevronRight,
  ChevronLeft,
  QrCode,
  X,
  Download
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

// --- SVG Icons ---
const importIcon = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>`;
const plusIcon = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>`;
const searchIcon = `<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>`;
const editIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>`;
const deleteIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`;

// Logic Search
watch(search, throttle((value) => {
    router.get(route('barangs.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// Logic Delete
const deleteBarang = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data barang ini?')) {
        router.delete(route('barangs.destroy', id));
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
  // Tunggu DOM update selesai (jika baru dibuka)
  await nextTick();

  // Cari elemen canvas
  const canvas = document.querySelector('.qr-canvas-wrapper canvas');
  
  if (canvas) {
    try {
      // Ambil Data URL (format gambar PNG)
      const image = canvas.toDataURL("image/png");
      
      // Validasi apakah data URL valid (tidak kosong/pendek)
      if (image.length < 100) {
          alert("Gambar QR Code belum siap. Silakan coba lagi sebentar lagi.");
          return;
      }
      
      // Buat link sementara untuk trigger download
      const link = document.createElement('a');
      link.href = image;
      
      // Set nama file (misal: QR-ATK.001.png)
      // Sanitasi nama file agar aman
      const safeName = selectedBarangQr.value.kode_barang.replace(/[^a-z0-9]/gi, '_').toLowerCase();
      link.download = `QR-${safeName}.png`;
      
      // Klik otomatis & hapus link
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
</script>

<template>
    <Head title="Master Data Barang" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Master Data Barang</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="px-4 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-800">Master Data Barang</h1>
                    <p class="mt-2 text-gray-600">Kelola semua data barang, stok, dan harga di gudang.</p>
                </div>

                <div v-if="$page.props.flash?.success" class="mt-4 mx-4 sm:mx-0 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mt-4 mx-4 sm:mx-0 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="flex flex-col mt-6 md:flex-row md:justify-end gap-x-3 gap-y-3 px-4 sm:px-0">
                    <button v-if="isOperator" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm">
                        <span v-html="importIcon"></span> Import Excel
                    </button>
                    
                    <Link v-if="isOperator" :href="route('barangs.create')" class="flex items-center justify-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm">
                        <span v-html="plusIcon"></span> Tambah Barang
                    </Link>
                </div>

                <div class="mt-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden mx-4 sm:mx-0">
                    
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                        <div class="relative w-full md:w-72">
                            <span v-html="searchIcon" class="pointer-events-none"></span>
                            <input 
                                type="text" 
                                v-model="search" 
                                placeholder="Cari kode atau nama barang..." 
                                class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" 
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Info Barang</th>
                                    <!-- <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Kategori</th> -->
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Satuan</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Harga</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Stok</th>
                                    <th v-if="isOperator" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="barangs.data.length === 0">
                                    <td :colspan="isOperator ? 6 : 5" class="px-6 py-8 text-center text-gray-500">
                                        Data barang tidak ditemukan.
                                    </td>
                                </tr>
                                
                                <tr v-for="barang in barangs.data" :key="barang.id" class="hover:bg-gray-50 transition-colors">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img v-if="barang.foto" :src="`/storage/${barang.foto}`" class="h-10 w-10 rounded-md object-cover border" alt="Foto Barang">
                                                <div v-else class="h-10 w-10 rounded-md bg-gray-100 flex items-center justify-center text-gray-400 text-xs">No Img</div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ barang.nama_barang }}</div>
                                                <div class="text-xs text-gray-500 font-mono bg-gray-100 px-1 rounded">{{ barang.kode_barang }}</div>
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
                                        <span class="font-bold" :class="barang.stok > 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ barang.stok_saat_ini }}
                                        </span>
                                    </td>

                                    <td v-if="isOperator" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center gap-3">
                                            <button 
                                                @click="openQrModal(barang)" 
                                                class="p-1 hover:bg-[#F1F5F9] rounded transition-colors"
                                                title="Lihat QR Code"
                                            >
                                                <QrCode class="w-[14px] h-[14px] text-slate-600" />
                                            </button>
                                            <Link :href="route('barangs.edit', barang.id)" class="text-teal-600 hover:text-teal-900 p-1 rounded hover:bg-teal-50 transition-colors" title="Edit">
                                                <span v-html="editIcon"></span>
                                            </Link>
                                            <button @click="deleteBarang(barang.id)" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50 transition-colors" title="Hapus">
                                                <span v-html="deleteIcon"></span>
                                            </button>
                                        </div>
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="barangs.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">{{ barangs.from ?? 0 }}</span> sampai <span class="font-medium">{{ barangs.to ?? 0 }}</span> dari <span class="font-medium">{{ barangs.total }}</span> hasil
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, k) in barangs.links" :key="k">
                                <Link 
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-1 text-sm border rounded-md bg-white text-gray-700 border-gray-300 hover:bg-gray-50 transition-colors"
                                    :class="{ 'bg-teal-600 text-white border-teal-600 hover:bg-teal-600': link.active }"
                                    preserve-scroll
                                />
                                <span 
                                    v-else
                                    v-html="link.label"
                                    class="px-3 py-1 text-sm border rounded-md bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed"
                                ></span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>




        <div v-if="showQrModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm overflow-hidden transform transition-all">
                
                <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">QR Code Barang</h3>
                    <button @click="closeQrModal" class="text-gray-400 hover:text-gray-600 transition">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 flex flex-col items-center text-center">
                    <div class="qr-canvas-wrapper bg-white p-4 border-2 border-gray-100 rounded-lg mb-4 shadow-sm inline-block">
                        <QrcodeVue 
                            :key="selectedBarangQr.id"
                            :value="selectedBarangQr.kode_barang" 
                            :size="250" 
                            level="H" 
                            render-as="canvas"
                            foreground="#1e293b"
                            background="#ffffff"
                        />
                    </div>

                    <h4 class="text-lg font-bold text-slate-800">{{ selectedBarangQr.nama_barang }}</h4>
                    <p class="text-sm font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded mt-1">
                        {{ selectedBarangQr.kode_barang }}
                    </p>
                    <p class="text-xs text-slate-400 mt-4">
                        Scan QR ini untuk menemukan data barang di sistem.
                    </p>
                </div>

                <div class="p-4 border-t border-gray-100 flex gap-3">
                    <button 
                        @click="closeQrModal"
                        class="flex-1 py-2 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                    >
                        Tutup
                    </button>
                    
                    <button 
                        @click="downloadQrCode"
                        class="flex-1 py-2 px-4 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition font-medium flex items-center justify-center gap-2"
                    >
                        <Download class="w-4 h-4" />
                        Download PNG
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
