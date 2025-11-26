<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({ 
  mutasis: Array, 
  barangs: Array, 
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-8 px-4 md:px-6 lg:px-8">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Mutasi Barang</h1>
        <p class="mt-1 text-sm text-gray-600">
          Kelola data mutasi barang pada inventory
        </p>
      </div>
      <div class="flex justify-end mt-6 gap-x-3 px-4 sm:px-0">
          <Link
            :href="route('mutasi-barang.create')"
            class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700"
            >Buat Mutasi
          </Link>
      </div>

      <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
          <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center"
          >
            <div>
              <h2 class="text-xl font-semibold text-gray-800">Riwayat Barang Masuk</h2>
              <p class="text-sm text-gray-500 mt-1">
                Daftar barang yang telah dimutasi
              </p>
            </div>
            <div class="flex gap-2 mt-4 md:mt-0">
              <button
                class="py-2 px-4 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Filter
              </button>
              <button
                class="py-2 px-4 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Export
              </button>
            </div>
          </div>

          <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nomor
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Jenis
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tanggal
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Keterangan
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Aksi
                  </th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="props.mutasis.data.length === 0">
                  <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data riwayat.
                  </td>
                </tr>

                <tr v-for="(item, idx) in props.mutasis.data" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ idx + 1 }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.nomor_mutasi }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.jenis_mutasi }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.tanggal_mutasi }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.keterangan }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <Link :href="route('mutasi-barang.show', item.id)" class="text-cyan-600 hover:text-cyan-900"
                      >Detail</Link
                    >
                    <Link
                      :href="route('mutasi-barang.edit', item.id)"
                      class="ml-3 text-cyan-600 hover:text-cyan-900"
                      >Edit</Link
                    >
                    <Link
                      :href="route('mutasi-barang.destroy', item.id)"
                      method="delete"
                      ask="Yakin hapus data ini?"
                      as="button"
                      class="ml-4 text-red-600 hover:text-red-900"
                    >
                      Hapus
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            class="mt-5 flex items-center justify-between border-t border-gray-200 pt-4"
          >
            <div class="text-sm text-gray-700">
              Menampilkan <span class="font-medium">1</span>-
              <span class="font-medium">{{ props.mutasis.data.length }}</span> dari
              <span class="font-medium">{{ props.mutasis.data.length }}</span> data
            </div>
            <div class="flex gap-1">
              <button
                class="py-2 px-3 rounded-md border bg-white text-sm hover:bg-gray-50 disabled:opacity-50"
                disabled
              >
                Previous
              </button>
              <button
                class="py-2 px-3 rounded-md border bg-white text-sm hover:bg-gray-50"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
