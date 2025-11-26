<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const user = usePage().props.auth.user;

const props = defineProps({
  permintaans: Array,
});

const search = ref("");

// warna badge status
const getStatusBadgeClass = (status) => {
  switch (status) {
    case "Disetujui":
      return "bg-green-100 text-green-800";
    case "Sedang Diproses":
      return "bg-yellow-100 text-yellow-800";
    case "Selesai":
      return "bg-blue-100 text-blue-800";
    case "Ditolak":
      return "bg-red-100 text-red-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Riwayat Permintaan" />

    <div class="w-full px-6 py-8">
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Riwayat Permintaan Anda</h1>
        <p class="text-gray-500 mt-1">
          Lihat daftar pengajuan permintaan barang yang pernah Anda lakukan
        </p>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div
          class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4"
        >
          <h2 class="text-lg font-semibold text-slate-900">Daftar Riwayat</h2>

          <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </span>
            <input
              v-model="search"
              type="text"
              placeholder="Cari permintaan..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-teal-500 focus:ring-teal-500 w-full sm:w-64"
            />
          </div>
        </div>

        <!-- TABEL -->
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">No</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Permintaan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Keperluan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pengajuan</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
              </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="(item, index) in props.permintaans"
                :key="item.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-center text-sm">{{ index + 1 }}</td>
                <td class="px-6 py-4 text-sm font-medium text-blue-900">{{ item.no_permintaan }}</td>
                <td class="px-6 py-4 text-sm">{{ item.jenis_keperluan }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ item.tanggal_pengajuan }}</td>
                <td class="px-6 py-4 text-center">
                  <span
                    :class="[
                      'px-3 py-1 inline-flex text-xs font-semibold rounded-full',
                      getStatusBadgeClass(item.status)
                    ]"
                    >{{ item.status }}</span
                  >
                </td>
                <td class="px-6 py-4 text-center">
                  <Link
                    :href="route('permintaan.detail', item.id)"
                    class="text-cyan-600 font-medium hover:text-cyan-800"
                    >Detail</Link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
