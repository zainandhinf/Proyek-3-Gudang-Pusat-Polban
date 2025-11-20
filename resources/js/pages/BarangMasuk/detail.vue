<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  barangMasuk: Object,
  detailBarang: Array,
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-8 px-6 space-y-8">

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Informasi Transaksi Barang Masuk
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <div>
            <p class="text-sm text-gray-500">Tanggal Masuk</p>
            <p class="font-medium text-gray-800">
              {{ props.barangMasuk.tanggal_masuk }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Dicatat Oleh</p>
            <p class="font-medium text-gray-800">
              {{ props.barangMasuk.operator }}
            </p>
          </div>

          <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Keterangan</p>
            <p class="font-medium text-gray-800">
              {{ props.barangMasuk.keterangan || '-' }}
            </p>
          </div>

        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Daftar Barang dalam Transaksi Ini
        </h2>

        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama Barang
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Jumlah
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Satuan
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="props.detailBarang.length === 0">
              <td colspan="3" class="text-center py-4 text-gray-500">
                Tidak ada data barang.
              </td>
            </tr>

            <tr v-for="item in props.detailBarang" :key="item.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm text-gray-800">
                {{ item.nama_barang }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-800">
                {{ item.jumlah }}
              </td>
              <td class="px-6 py-4 text-sm text-gray-800">
                {{ item.satuan }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <Link
          :href="route('barang-masuk.index')"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
        >
          Kembali
        </Link>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
