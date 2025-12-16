<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({ 
    permintaan: Object, 
    barangs: Array 
});

const form = useForm({
  items: props.permintaan.detail_permintaans.length
    ? props.permintaan.detail_permintaans.map((d) => {
        const barang = props.barangs.find((b) => b.id === d.barang_id);

        return {
          barang_id: d.barang_id,
          jumlah: d.jumlah_diminta,
          satuan: barang?.satuan?.nama_satuan ?? "",
          catatan: d.keterangan ?? "",
          stok: barang?.stok_saat_ini ?? 0,
        };
      })
    : [
        {
          barang_id: "",
          jumlah: 1,
          satuan: "",
          catatan: "",
          stok: 0,
        },
      ],
});

function addItem() {
  form.items.push({
    barang_id: "",
    jumlah: 1,
    satuan: "",
    catatan: "",
    stok: 0,
  });
}

function removeItem(index) {
  if (form.items.length === 1) return;
  form.items.splice(index, 1);
}

function onBarangSelect(index) {
  const item = form.items[index];
  const barang = props.barangs.find((b) => b.id == item.barang_id);

  if (!barang) {
    item.satuan = "";
    item.stok = 0;
    return;
  }

  item.satuan = barang.satuan?.nama_satuan ?? "";
  item.stok = barang.stok_saat_ini ?? 0;
}

function resetForm() {
  form.reset();
  // Set default item kosong
  form.items = [
    {
      barang_id: "",
      jumlah: 1,
      satuan: "",
      catatan: "",
      stok: 0,
    },
  ];
}

function prosesPermintaan() {
    form.post(route("permintaan.proses.store", props.permintaan.id), {
        onSuccess: () => (window.location = route("permintaan.index")),
    });
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-xl font-semibold text-gray-800">Proses Transkripsi Permintaan</h2>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-4 grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div>
            <h3 class="font-bold text-lg mb-4 text-gray-700 border-b pb-2">Informasi Permintaan</h3>
            <div class="space-y-3 text-sm">
                <div class="grid grid-cols-3">
                    <span class="text-gray-500">No. Surat</span>
                    <span class="col-span-2 font-medium text-gray-900">{{ props.permintaan.no_permintaan }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-gray-500">Tanggal Pengajuan</span>
                    <span class="col-span-2 font-medium text-gray-900">{{ new Date(props.permintaan.tanggal_pengajuan).toLocaleDateString('id-ID') }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-gray-500">Pemohon</span>
                    <span class="col-span-2 font-medium text-gray-900">{{ props.permintaan.pemohon.name }}</span>
                </div>
                <div class="grid grid-cols-3">
                    <span class="text-gray-500">Unit/Keperluan</span>
                    <span class="col-span-2 font-medium text-gray-900">{{ props.permintaan.jenis_keperluan }}</span>
                </div>
            </div>
        </div>

        <div>
          <h3 class="font-bold text-lg mb-4 text-gray-700 border-b pb-2">Preview Dokumen</h3>
          <div class="h-[400px] bg-gray-50 border rounded-lg flex items-center justify-center overflow-hidden">
            <iframe
              v-if="props.permintaan.file_url"
              :src="props.permintaan.file_url"
              class="w-full h-full"
            ></iframe>
            <div v-else class="text-gray-400 flex flex-col items-center">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Tidak ada file surat dilampirkan</span>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="font-bold text-lg mb-4 text-gray-700">Detail Barang (Transkripsi)</h3>
        <p class="text-sm text-gray-500 mb-4">
            Salin data barang dari surat permintaan di atas ke dalam tabel di bawah ini. 
            <span class="text-red-500 font-medium">Perhatikan stok saat ini!</span>
        </p>

        <form @submit.prevent="prosesPermintaan">
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50 text-gray-700 text-xs uppercase font-bold">
                <tr>
                  <th class="px-4 py-3 text-center w-12">No</th>
                  <th class="px-4 py-3 text-left min-w-[250px]">Nama Barang</th>
                  <th class="px-4 py-3 text-left w-32">Jumlah Minta</th>
                  <th class="px-4 py-3 text-center w-32">Stok Tersedia</th>
                  <th class="px-4 py-3 text-left w-32">Satuan</th>
                  <th class="px-4 py-3 text-left min-w-[200px]">Catatan</th>
                  <th class="px-4 py-3 text-center w-20">Aksi</th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item, idx) in form.items" :key="idx">
                  <td class="px-4 py-3 text-center text-sm text-gray-500">
                    {{ idx + 1 }}
                  </td>

                  <td class="px-4 py-3">
                    <select
                      v-model="item.barang_id"
                      @change="onBarangSelect(idx)"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500 focus:border-teal-500"
                      required
                    >
                      <option value="" disabled>-- Pilih Barang --</option>
                      <option v-for="b in props.barangs" :key="b.id" :value="b.id">
                        {{ b.kode_barang }} - {{ b.nama_barang }}
                      </option>
                    </select>
                    <div v-if="form.errors[`items.${idx}.barang_id`]" class="text-red-500 text-xs mt-1">
                        {{ form.errors[`items.${idx}.barang_id`] }}
                    </div>
                  </td>

                  <td class="px-4 py-3">
                    <input
                      type="number"
                      v-model.number="item.jumlah"
                      min="1"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm text-center"
                      required
                    />
                    <div v-if="form.errors[`items.${idx}.jumlah`]" class="text-red-500 text-xs mt-1">
                        {{ form.errors[`items.${idx}.jumlah`] }}
                    </div>
                  </td>

                  <td class="px-4 py-3 text-center">
                    <span 
                        class="px-2 py-1 rounded text-xs font-bold"
                        :class="item.stok < item.jumlah ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'"
                    >
                        {{ item.stok }}
                    </span>
                    <div v-if="item.stok < item.jumlah && item.barang_id" class="text-[10px] text-red-600 mt-1 font-semibold">
                        Stok Kurang!
                    </div>
                  </td>

                  <td class="px-4 py-3">
                    <input
                      type="text"
                      v-model="item.satuan"
                      class="w-full p-2 border border-gray-200 bg-gray-50 rounded-md text-sm text-gray-500"
                      disabled
                    />
                  </td>

                  <td class="px-4 py-3">
                    <input
                      type="text"
                      v-model="item.catatan"
                      placeholder="Contoh: Untuk Praktikum"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500"
                    />
                  </td>

                  <td class="px-4 py-3 text-center">
                    <button
                      type="button"
                      @click="removeItem(idx)"
                      class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition"
                      title="Hapus Baris"
                    >
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="mt-4">
            <button
              type="button"
              @click="addItem"
              class="inline-flex items-center px-4 py-2 border border-teal-600 text-teal-600 rounded-lg text-sm font-medium hover:bg-teal-50 transition"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
              Tambah Barang
            </button>
          </div>

          <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-100">
            <button
              type="button"
              @click="resetForm"
              class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition"
            >
              Reset Form
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-bold shadow-sm transition flex items-center disabled:opacity-50"
            >
              <span v-if="form.processing" class="mr-2">Menyimpan...</span>
              Simpan & Proses
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>