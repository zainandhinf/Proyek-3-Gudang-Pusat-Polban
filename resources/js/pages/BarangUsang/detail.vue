<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, Head } from "@inertiajs/vue3";

const props = defineProps({
  laporan: Object, // Header
  detail: Array,   // Array Items
});
</script>

<template>
  <Head title="Detail Barang Usang" />
  <AuthenticatedLayout>
    <div class="py-8 px-6 space-y-8">

      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Informasi Catatan Barang Usang
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <div>
            <p class="text-sm text-gray-500">Nomor Catat (System)</p>
            <p class="font-medium text-gray-800">
              {{ props.laporan.no_catat }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Nomor Bukti (Manual)</p>
            <p class="font-medium text-gray-800">
              {{ props.laporan.no_bukti }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Tanggal Catat</p>
            <p class="font-medium text-gray-800">
              {{ props.laporan.tanggal_catat }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Dicatat Oleh</p>
            <p class="font-medium text-gray-800">
              {{ props.laporan.dicatat_oleh?.name || '-' }}
            </p>
          </div>

          <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Keterangan</p>
            <p class="font-medium text-gray-800">
              {{ props.laporan.keterangan || '-' }}
            </p>
          </div>

        </div>
      </div>

      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
          Detail Barang
        </h3>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr class="text-left text-xs font-semibold text-gray-600">
                <th class="px-3 py-2 w-12 text-center">No</th>
                <th class="px-3 py-2">Nama Barang</th>
                <th class="px-3 py-2 w-24 text-center">Jumlah</th>
                <th class="px-3 py-2">Detail Kerusakan</th>
              </tr>
            </thead>

            <tbody class="divide-y">
              <tr
                v-for="(d, i) in props.detail"
                :key="i"
                class="hover:bg-gray-50"
              >
                <td class="px-3 py-2 text-center">{{ i + 1 }}</td>
                <td class="px-3 py-2 font-medium">
                    <div>{{ d.barang?.nama_barang }}</div>
                    <div class="text-xs text-gray-400 font-mono">{{ d.barang?.kode_barang }}</div>
                </td>
                <td class="px-3 py-2 text-center font-semibold text-red-600">
                    {{ d.jumlah }}
                </td>
                <td class="px-3 py-2">
                  {{ d.keterangan_rusak || '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-6">
          <Link
            :href="route('barang-usang.index')"
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition"
          >
            Kembali
          </Link>
        </div>

      </div>

    </div>
  </AuthenticatedLayout>
</template>