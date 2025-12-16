<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
  permintaan: Object,
});
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Detail Permintaan ${props.permintaan.no_permintaan}`" />

    <div class="w-full px-6 py-8">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Detail Permintaan</h1>
        <p class="text-gray-500">
          Informasi lengkap mengenai permintaan barang yang Anda ajukan
        </p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
        <div>
          <p class="text-sm text-gray-500">Nomor Permintaan</p>
          <p class="font-semibold text-gray-900">{{ props.permintaan.no_permintaan }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500">Jenis Keperluan</p>
          <p class="font-semibold text-gray-900">{{ props.permintaan.jenis_keperluan }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
          <p class="font-semibold text-gray-900">{{ props.permintaan.tanggal_pengajuan }}</p>
        </div>

        <div>
          <p class="text-sm text-gray-500">Status</p>
          <span
            class="px-3 py-1 inline-flex text-xs font-semibold rounded-full"
            :class="props.permintaan.status === 'Disetujui' ? 'bg-green-100 text-green-800' :
                     props.permintaan.status === 'Ditolak' ? 'bg-red-100 text-red-800' :
                     props.permintaan.status === 'Sedang Diproses' ? 'bg-yellow-100 text-yellow-800' :
                     props.permintaan.status === 'Selesai' ? 'bg-blue-100 text-blue-800' :
                     'bg-gray-100 text-gray-800'"
          >
            {{ props.permintaan.status }}
          </span>
        </div>

        <div>
          <p class="text-sm text-gray-500">Surat Pengajuan</p>
          <a
            :href="`/storage/${props.permintaan.file_path}`"
            class="text-blue-600 hover:text-blue-800 font-medium"
            target="_blank"
          >Lihat Surat</a>
        </div>

        <div v-if="props.permintaan.catatan_reject">
          <p class="text-sm text-gray-500">Catatan Penolakan</p>
          <p class="text-red-600 font-medium">{{ props.permintaan.catatan_reject }}</p>
        </div>

      </div>

      <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold mb-4">Detail Barang yang Diminta</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                    No
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nama Barang
                </th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Jumlah Diminta
                </th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Satuan
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Catatan
                </th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                v-for="(item, index) in props.permintaan.detail_permintaans"
                :key="item.id"
                >
                <td class="px-4 py-3 text-center text-sm text-gray-800">
                    {{ index + 1 }}
                </td>

                <td class="px-4 py-3 text-sm text-gray-800">
                    {{ item.barang.nama_barang }}
                </td>

                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">
                    {{ item.jumlah_diminta }}
                </td>

                <td class="px-4 py-3 text-center text-sm text-gray-700">
                    {{ item.barang.satuan.nama_satuan }}
                </td>

                <td class="px-4 py-3 text-sm text-gray-700 italic">
                    {{ item.keterangan ?? '-' }}
                </td>
                </tr>

                <tr v-if="props.permintaan.detail_permintaans.length === 0">
                <td colspan="5" class="px-4 py-3 text-center text-gray-500 italic">
                    Tidak ada detail barang.
                </td>
                </tr>
            </tbody>
            </table>
        </div>
        </div>


      <div class="mt-6">
        <Link
          :href="route('permintaan.riwayat')"
          class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium"
        >Kembali</Link>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
