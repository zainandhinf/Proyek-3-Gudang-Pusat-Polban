<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { throttle } from 'lodash';
import { Search, Plus, Eye, Trash2 } from 'lucide-vue-next';

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
</script>

<template>
  <Head title="Barang Usang/Rusak" />
  <AuthenticatedLayout>
    <div class="py-8 px-4 md:px-6 lg:px-8">
      
      <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Catatan Barang Usang</h1>
          <p class="mt-1 text-sm text-gray-600">Dokumen pencatatan kerusakan atau penghapusan aset.</p>
        </div>
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
  </AuthenticatedLayout>
</template>