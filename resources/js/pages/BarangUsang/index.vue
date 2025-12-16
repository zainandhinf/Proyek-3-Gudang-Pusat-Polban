<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { throttle } from 'lodash';
import { Search, Plus, Eye, Trash2, Upload, FileSpreadsheet, X } from 'lucide-vue-next';

const props = defineProps({
  barangUsangs: Object,
  filters: Object,
});

const search = ref(props.filters?.search ?? '');

watch(search, throttle((value) => {
  router.get(route('barang-usang.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const deleteCatatan = (id) => {
  if (confirm('Hapus seluruh dokumen catatan ini? Data detail barang juga akan terhapus.')) {
    router.delete(route('barang-usang.destroy', id), { preserveScroll: true });
  }
};

// Helper Format Tanggal
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

// --- LOGIC IMPORT ---
const showImportModal = ref(false);
const importForm = useForm({ file: null });

const submitImport = () => {
    importForm.post(route('barang-usang.import'), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};
</script>

<template>
  <Head title="Barang Usang/Rusak" />
  <AuthenticatedLayout>
    <div class="py-8 px-4 md:px-6 lg:px-8">
      
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Catatan Barang Usang</h1>
        <p class="mt-1 text-sm text-gray-600">Dokumen pencatatan kerusakan atau penghapusan aset.</p>
      </div>

      <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
          {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
          {{ $page.props.flash.error }}
      </div>
      
      <div class="flex justify-end mt-6 gap-x-3">
         <button @click="showImportModal = true" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition font-medium text-sm">
            <Upload class="w-4 h-4 mr-2" />
            Import Excel
         </button>

         <a :href="route('laporan.barang-usang')" target="_blank" class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition font-medium text-sm">
            <FileSpreadsheet class="w-4 h-4 mr-2" />
            Laporan
         </a>

        <Link :href="route('barang-usang.create')" 
          class="flex items-center px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 shadow-sm transition font-medium text-sm">
          <Plus class="w-4 h-4 mr-2" /> Buat Catatan Baru
        </Link>
      </div>

      <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-end">
            <div class="relative w-full md:w-64">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type="text" v-model="search" placeholder="Cari No Catat / Bukti..." 
                  class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500 focus:border-teal-500 shadow-sm" />
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Item</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
             <tr v-if="barangUsangs.data.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data catatan.</td>
             </tr>
             <tr v-for="(item, idx) in barangUsangs.data" :key="item.id" class="hover:bg-gray-50">
               <td class="px-6 py-4 text-center text-sm text-gray-500">{{ barangUsangs.from + idx }}</td>
               <td class="px-6 py-4">
                 <div class="font-bold text-gray-900">{{ item.no_catat }}</div>
                 <div class="text-xs text-gray-500 mt-1">Bukti: {{ item.no_bukti }}</div>
               </td>
               <td class="px-6 py-4 text-sm text-gray-700">{{ formatDate(item.tanggal_catat) }}</td>
               <td class="px-6 py-4 text-sm text-gray-600 truncate max-w-xs">{{ item.keterangan || '-' }}</td>
               <td class="px-6 py-4 text-center text-sm font-bold text-gray-800">
                  {{ item.detail ? item.detail.length : 0 }} Barang
               </td>
               <td class="px-6 py-4 text-center">
                  <div class="flex justify-center gap-2">
                    <Link :href="route('barang-usang.show', item.id)" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded" title="Lihat Detail">
                      <Eye class="w-4 h-4" />
                    </Link>
                    <button @click="deleteCatatan(item.id)" class="text-red-600 hover:bg-red-50 p-1.5 rounded" title="Hapus Dokumen">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
               </td>
             </tr>
          </tbody>
        </table>
        
        <div v-if="barangUsangs.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
            <div class="text-sm text-gray-700">
                Halaman {{ barangUsangs.current_page }} dari {{ barangUsangs.last_page }}
            </div>
            <div class="flex gap-1">
                <template v-for="(link, index) in barangUsangs.links" :key="index">
                    <Link v-if="link.url" :href="link.url" preserve-scroll v-html="link.label" 
                          class="relative inline-flex items-center px-3 py-1 text-sm font-medium border rounded-md transition-colors shadow-sm"
                          :class="{'bg-teal-600 border-teal-600 text-white': link.active, 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active}" />
                    <span v-else v-html="link.label" class="relative inline-flex items-center px-3 py-1 text-sm font-medium border bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed rounded-md"></span>
                </template>
            </div>
        </div>

      </div>
    </div>

    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-opacity">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
                <div class="flex justify-between items-center p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Import Barang Rusak</h3>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full p-1 transition">
                        <X class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-5 text-xs text-red-800">
                        <p class="font-bold mb-1 flex items-center gap-1">
                            <FileSpreadsheet class="w-3 h-3" /> Perhatian:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 ml-1">
                            <li>Gunakan file Excel hasil export <strong>Laporan Barang Rusak</strong>.</li>
                            <li>Import ini akan <strong>MENGURANGI STOK MASTER</strong> sesuai jumlah kerusakan.</li>
                            <li>Pastikan Nama Barang di file sama dengan di sistem.</li>
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
                                <span v-else class="mr-2">Proses...</span>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
  </AuthenticatedLayout>
</template>