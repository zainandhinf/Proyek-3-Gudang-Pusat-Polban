<script setup>
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Search, Plus, Edit2, Trash2, ChevronRight, ChevronLeft } from "lucide-vue-next";
import { debounce } from "lodash";

const props = defineProps({
  kelompok_barangs: Object,
  filters: Object,
});

const search = ref(props.filters.search);

// Gunakan debounce agar tidak spam request saat ngetik
watch(search, debounce((value) => {
  router.get(route("kelompok-barang.index"), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 300));

const deleteItem = (id) => {
  if (confirm("Yakin ingin menghapus kelompok ini?")) {
    router.delete(route("kelompok-barang.destroy", id));
  }
};
</script>

<template>
  <Head title="Kelompok Barang" />
  <AuthenticatedLayout>
    <div class="p-8">
      <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-end mb-6">
          <div>
            <h1 class="text-2xl font-bold text-slate-800">Master Kelompok Barang</h1>
            <p class="text-slate-500">Kelola pengelompokan dan kode awal barang (misal: ATK, ELK).</p>
          </div>
          <Link :href="route('kelompok-barang.create')" class="btn bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded flex items-center gap-2">
            <Plus class="w-4 h-4" /> Tambah Kelompok
          </Link>
        </div>

        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
          <div class="p-4 border-b border-slate-200 flex justify-between">
            <div class="relative w-72">
              <Search class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
              <input v-model="search" type="text" placeholder="Cari nama atau kode..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-teal-500 focus:border-teal-500">
            </div>
          </div>

          <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
              <tr>
                <th class="px-6 py-3">Kode</th>
                <th class="px-6 py-3">Nama Kelompok</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="kelompok_barangs.data.length === 0">
                <td colspan="3" class="px-6 py-8 text-center text-slate-500">Belum ada data kelompok barang.</td>
              </tr>
              <tr v-for="item in kelompok_barangs.data" :key="item.id" class="border-b hover:bg-slate-50">
                <td class="px-6 py-4 font-mono font-bold text-blue-700">{{ item.kode_kelompok }}</td>
                <td class="px-6 py-4 font-medium text-slate-900">{{ item.nama_kelompok }}</td>
                <td class="px-6 py-4 text-center flex justify-center gap-2">
                  <Link :href="route('kelompok-barang.edit', item.id)" class="p-2 bg-slate-100 rounded hover:bg-slate-200 text-teal-600">
                    <Edit2 class="w-4 h-4" />
                  </Link>
                  <button @click="deleteItem(item.id)" class="p-2 bg-slate-100 rounded hover:bg-red-50 text-red-600">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="p-4 border-t flex justify-end gap-1" v-if="kelompok_barangs.links.length > 3">
             <Link v-for="(link, k) in kelompok_barangs.links" :key="k" 
                :href="link.url ?? '#'" 
                v-html="link.label"
                class="px-3 py-1 border rounded text-sm"
                :class="link.active ? 'bg-teal-600 text-white' : 'bg-white hover:bg-slate-50'"
             />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>