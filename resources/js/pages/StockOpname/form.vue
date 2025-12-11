<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link, router } from "@inertiajs/vue3";

const props = defineProps({ opname: Object });

// Load data detail ke form
const form = useForm({
  items: props.opname.detail_stock_opnames.map((d) => ({
    id: d.id,
    nama_barang: d.barang.nama_barang,
    satuan: d.barang.satuan?.nama_satuan,
    stok_sistem: d.stok_sistem,
    stok_fisik: d.stok_fisik,
    keterangan: d.keterangan, // Catatan per item (misal: "Rusak 1")
  })),
});

const saveDraft = () => {
  form
    .transform((data) => ({ ...data, finish: false }))
    .put(route("stock-opname.update", props.opname.id), {
      preserveScroll: true,
      onSuccess: () => {
        /* Optional toast */
      },
    });
};

const finishOpname = () => {
  if (
    confirm(
      'SELESAIKAN OPNAME?\n\nStok master akan diperbarui mengikuti "Stok Fisik" yang Anda input. Pastikan semua data sudah benar.'
    )
  ) {
    form
      .transform((data) => ({ ...data, finish: true }))
      .put(route("stock-opname.update", props.opname.id));
  }
};

// const cancelOpname = () => {
//     if (confirm('BATALKAN SESI?\n\nSemua data hitungan di sesi ini akan dihapus.')) {
//         router.delete(route('stock-opname.destroy', props.opname.id));
//     }
// };

const cancelOpname = () => {
  if (
    confirm("BATALKAN SESI?\n\nSemua data hitungan di sesi ini akan dihapus permanen.")
  ) {
    router.delete(route("stock-opname.destroy", props.opname.id), {
      onSuccess: () => alert("Sesi berhasil dibatalkan."), // Feedback visual
      onError: (errors) => console.error(errors),
    });
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="px-6 py-8">
      <div
        class="bg-white border border-gray-200 rounded-xl p-4 mb-6 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4"
      >
        <div>
          <h2 class="text-lg font-bold text-slate-800">
            Input Opname: {{ opname.no_opname }}
          </h2>
          <p class="text-xs text-gray-500">
            Snapshot Stok: {{ new Date(opname.tanggal_opname).toLocaleString("id-ID") }}
          </p>
        </div>
        <div class="flex gap-3">
          <button
            @click="cancelOpname"
            class="btn btn-sm bg-red-50 text-red-600 border-red-200 hover:bg-red-100 px-4 py-2"
          >
            Batalkan Sesi
          </button>
          <button
            @click="saveDraft"
            :disabled="form.processing"
            class="btn btn-sm bg-white border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2"
          >
            Simpan Draft
          </button>
          <button
            @click="finishOpname"
            :disabled="form.processing"
            class="btn btn-sm bg-teal-600 text-white hover:bg-teal-700 border-none px-4 py-2"
          >
            Selesai & Update Stok
          </button>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div
          class="p-3 bg-blue-50 border-b border-blue-100 text-blue-800 text-xs flex items-center"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>
          Isi kolom <b>"Stok Fisik"</b>. Kolom "Selisih" akan terhitung otomatis.
          Kosongkan jika belum dihitung.
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">
                  Barang
                </th>
                <th
                  class="px-6 py-3 text-center font-medium text-gray-500 uppercase bg-gray-100 w-24"
                >
                  Sistem
                </th>
                <th
                  class="px-6 py-3 text-center font-medium text-gray-500 uppercase w-32"
                >
                  Fisik
                </th>
                <th
                  class="px-6 py-3 text-center font-medium text-gray-500 uppercase w-24"
                >
                  Selisih
                </th>
                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">
                  Keterangan
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="(item, index) in form.items"
                :key="item.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-3">
                  <div class="font-medium text-gray-900">{{ item.nama_barang }}</div>
                  <div class="text-xs text-gray-500">{{ item.satuan }}</div>
                </td>
                <td class="px-6 py-3 text-center bg-gray-50 font-mono text-gray-600">
                  {{ item.stok_sistem }}
                </td>
                <td class="px-6 py-3 text-center">
                  <input
                    type="number"
                    v-model="item.stok_fisik"
                    class="w-24 text-center border-gray-300 rounded-md shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm p-1"
                    placeholder="..."
                    min="0"
                  />
                </td>
                <td class="px-6 py-3 text-center font-bold">
                  <span
                    v-if="item.stok_fisik !== null"
                    :class="{
                      'text-red-600': item.stok_fisik - item.stok_sistem < 0,
                      'text-green-600': item.stok_fisik - item.stok_sistem > 0,
                      'text-gray-300': item.stok_fisik - item.stok_sistem === 0,
                    }"
                  >
                    {{ item.stok_fisik - item.stok_sistem > 0 ? "+" : ""
                    }}{{ item.stok_fisik - item.stok_sistem }}
                  </span>
                  <span v-else class="text-gray-300">-</span>
                </td>
                <td class="px-6 py-3">
                  <input
                    type="text"
                    v-model="item.keterangan"
                    class="w-full border-gray-300 rounded-md text-xs p-1.5 focus:ring-teal-500 focus:border-teal-500"
                    placeholder="Catatan..."
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
