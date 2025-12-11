<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";
const props = defineProps({ permintaan: Object, barangs: Array });

const form = useForm({
  items: props.permintaan.detail_permintaans.length
    ? props.permintaan.detail_permintaans.map((d) => {
        const barang = props.barangs.find((b) => b.id === d.barang_id);

        return {
          barang_id: d.barang_id,
          jumlah: d.jumlah_diminta,
          satuan: barang?.satuan?.nama_satuan ?? "",
          catatan: d.catatan ?? "",
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
  const barang = props.barangs.find((b) => b.id == form.items[index].barang_id);

  if (!barang) {
    form.items[index].satuan = "";
    form.items[index].stok = 0;
    return;
  }

  form.items[index].satuan = barang.satuan?.nama_satuan ?? "";
  form.items[index].stok = barang.stok_saat_ini ?? 0;
}

function resetForm() {
  form.reset();
  form.items = [
    {
      barang_id: "",
      jumlah: 1,
      satuan: "",
      catatan: "",
      stok: 0,
    },
  ];
  form.tanggal_mutasi = new Date().toISOString().split("T")[0];
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
      <h2 class="text-xl font-semibold">Proses Permintaan</h2>

      <div class="bg-white rounded p-4 mt-4">
        <h3 class="font-semibold mb-3">Informasi Permintaan & Preview Surat</h3>

        <!-- Informasi Permintaan -->
        <div class="mb-6">
          <p class="text-sm text-gray-500 mt-1">
            <strong>No:</strong> {{ props.permintaan.no_permintaan }}
          </p>
          <p class="text-sm text-gray-500">
            <strong>Tanggal:</strong> {{ props.permintaan.tanggal_pengajuan }}
          </p>
          <p class="text-sm text-gray-500">
            <strong>Pemohon:</strong> {{ props.permintaan.pemohon.name }}
          </p>
        </div>

        <!-- Preview Surat -->
        <div>
          <h4 class="font-semibold">Preview Surat</h4>
          <div class="mt-2 h-[75vh] bg-gray-50 flex items-center justify-center rounded">
            <iframe
              v-if="props.permintaan.file_url"
              :src="props.permintaan.file_url"
              class="w-full h-full rounded border"
            ></iframe>

            <div v-else class="text-gray-500">Tidak ada file</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded p-4 mt-6">
        <h3 class="font-semibold">Detail Barang (isi untuk membuat mutasi keluar)</h3>

        <form @submit.prevent="prosesPermintaan" class="mt-6 bg-white rounded p-4">
          <div class="mt-4">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12"
                    >
                      No
                    </th>
                    <th
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Nama Barang
                    </th>
                    <th
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Jumlah
                    </th>
                    <th
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Stok Saat Ini
                    </th>
                    <th
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Stok Setelah
                    </th>
                    <th
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Satuan
                    </th>
                    <th
                      class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      Catatan
                    </th>
                    <th
                      class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16"
                    >
                      Aksi
                    </th>
                  </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="(item, idx) in form.items" :key="idx">
                    <td class="px-4 py-3 text-center text-sm text-gray-500">
                      {{ idx + 1 }}
                    </td>

                    <td class="px-4 py-3" style="min-width: 150px">
                      <select
                        v-model.number="item.barang_id"
                        @change="onBarangSelect(idx)"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500"
                        required
                      >
                        <option value="">Pilih Barang</option>
                        <option v-for="b in props.barangs" :key="b.id" :value="b.id">
                          {{ b.nama_barang }}
                        </option>
                      </select>
                    </td>

                    <td class="px-4 py-3" style="width: 100px">
                      <input
                        type="number"
                        v-model.number="item.jumlah"
                        min="1"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm"
                        required
                      />
                    </td>

                    <td class="px-4 py-3 text-center" style="width: 100px">
                      {{ item.stok }}
                    </td>

                    <td class="px-4 py-3 text-center" style="width: 100px">
                      {{
                        form.jenis_mutasi === "masuk"
                          ? item.stok + item.jumlah
                          : item.stok - item.jumlah
                      }}
                    </td>

                    <td class="px-4 py-3" style="width: 120px">
                      <input
                        type="text"
                        v-model="item.satuan"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-50"
                        disabled
                      />
                    </td>

                    <td class="px-4 py-3" style="min-width: 150px">
                      <input
                        type="text"
                        v-model="item.catatan"
                        placeholder="Catatan (opsional)"
                        class="w-full p-2 border border-gray-300 rounded-md text-sm"
                      />
                    </td>

                    <td class="px-4 py-3 text-center">
                      <button
                        @click.prevent="removeItem(idx)"
                        class="text-red-500 hover:text-red-700"
                      >
                        Hapus
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <button
              @click.prevent="addItem"
              class="mt-3 px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
            >
              Tambah Barang
            </button>
          </div>

          <!-- BUTTON SIMPAN -->
          <div class="flex justify-end gap-4 mt-6 pt-6 border-t">
            <button
              type="button"
              @click="resetForm"
              class="py-2 px-4 bg-gray-600 text-white rounded-md hover:bg-gray-700"
            >
              Reset
            </button>
            <button
              type="submit"
              class="py-2 px-4 bg-teal-600 text-white rounded-md hover:bg-teal-700"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
