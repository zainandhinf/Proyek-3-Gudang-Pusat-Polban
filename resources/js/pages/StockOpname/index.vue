<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({ opnames: Object });

const getStatusClass = (status) => {
  return status === 'Selesai' 
    ? 'bg-green-100 text-green-800 border-green-200' 
    : 'bg-yellow-100 text-yellow-800 border-yellow-200';
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-8 px-4 md:px-6 lg:px-8">
      
      <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Stock Opname</h1>
            <p class="mt-1 text-sm text-gray-600">Riwayat pengecekan stok fisik.</p>
        </div>
        <Link
            :href="route('stock-opname.store')" 
            method="post"
            as="button"
            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium shadow-sm transition"
        >
            + Mulai Opname Baru
        </Link>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Opname</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operator</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="opnames.data.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data stock opname.</td>
            </tr>
            <tr v-for="item in opnames.data" :key="item.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-sm font-medium text-blue-900">{{ item.no_opname }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ new Date(item.tanggal_opname).toLocaleString('id-ID') }}</td>
              <td class="px-6 py-4 text-sm text-gray-900">{{ item.dicatat_oleh?.name }}</td>
              <td class="px-6 py-4 text-center">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium border" :class="getStatusClass(item.status)">
                  {{ item.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-center text-sm">
                <Link v-if="item.status === 'Proses'" :href="route('stock-opname.edit', item.id)" class="text-orange-600 hover:text-orange-800 font-medium">
                    Lanjutkan Input
                </Link>
                <Link v-else :href="route('stock-opname.show', item.id)" class="text-cyan-600 hover:text-cyan-800 font-medium">
                    Lihat Detail
                </Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="mt-4 flex justify-end" v-if="opnames.links">
          </div>
    </div>
  </AuthenticatedLayout>
</template>