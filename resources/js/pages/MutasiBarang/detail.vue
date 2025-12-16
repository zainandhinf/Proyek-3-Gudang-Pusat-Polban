<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  mutasi: Object,      // header mutasi
  detail: Array,       // detail mutasi + barang
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-8 px-6 space-y-8">

      <!-- ======================= -->
      <!--        HEADER MUTASI     -->
      <!-- ======================= -->
      <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Informasi Mutasi Barang
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <div>
            <p class="text-sm text-gray-500">Nomor Mutasi</p>
            <p class="font-medium text-gray-800">
              {{ props.mutasi.nomor_mutasi }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Jenis Mutasi</p>
            <p
              class="font-medium"
              :class="props.mutasi.jenis_mutasi === 'masuk' ? 'text-green-700' : 'text-red-700'"
            >
              {{ props.mutasi.jenis_mutasi.toUpperCase() }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Tanggal Mutasi</p>
            <p class="font-medium text-gray-800">
              {{ props.mutasi.tanggal_mutasi }}
            </p>
          </div>

          <div>
            <p class="text-sm text-gray-500">Dicatat Oleh</p>
            <p class="font-medium text-gray-800">
              {{ props.mutasi.dicatat_oleh }}
            </p>
          </div>

          <div class="md:col-span-2">
            <p class="text-sm text-gray-500">Keterangan</p>
            <p class="font-medium text-gray-800">
              {{ props.mutasi.keterangan ?? '-' }}
            </p>
          </div>

        </div>
      </div>

      <!-- ======================= -->
      <!--      DETAIL BARANG      -->
      <!-- ======================= -->
      <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
          Detail Barang
        </h3>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr class="text-left text-xs font-semibold text-gray-600">
                <th class="px-3 py-2">No</th>
                <th class="px-3 py-2">Nama Barang</th>
                <th class="px-3 py-2">Jumlah</th>
                <th class="px-3 py-2">Satuan</th>
                <th class="px-3 py-2">Stok Sebelum</th>
                <th class="px-3 py-2">Stok Sesudah</th>
                <th class="px-3 py-2">Catatan</th>
              </tr>
            </thead>

            <tbody class="divide-y">
              <tr
                v-for="(d, i) in props.detail"
                :key="i"
                class="hover:bg-gray-50"
              >
                <td class="px-3 py-2">{{ i + 1 }}</td>
                <td class="px-3 py-2 font-medium">{{ d.nama_barang }}</td>
                <td class="px-3 py-2">{{ d.jumlah }}</td>
                <td class="px-3 py-2">{{ d.satuan }}</td>

                <td class="px-3 py-2">{{ d.stok_sebelum }}</td>

                <td
                  class="px-3 py-2 font-semibold"
                  :class="props.mutasi.jenis_mutasi === 'masuk'
                    ? 'text-green-700'
                    : 'text-red-700'"
                >
                  {{ d.stok_sesudah }}
                </td>

                <td class="px-3 py-2">
                  {{ d.catatan ?? '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Button Back -->
        <div class="mt-6">
          <Link
            href="/mutasi-barang"
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
          >
            Kembali
          </Link>
        </div>

      </div>

    </div>
  </AuthenticatedLayout>
</template>
