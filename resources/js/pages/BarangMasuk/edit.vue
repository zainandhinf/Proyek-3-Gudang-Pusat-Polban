<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Link } from "@inertiajs/vue3";

const props = defineProps({
  barangMasuk: Object,
  detailBarang: Array,
  barangs: Array,
});

const form = useForm({
  tanggal_masuk: props.barangMasuk.tanggal_masuk,
  keterangan: props.barangMasuk.keterangan ?? "",
  items: props.detailBarang.map((d) => ({
    id: d.id,
    barang_id: d.barang_id,
    jumlah: d.jumlah,
    satuan: d.satuan,
  })),
});

function addItem() {
  form.items.push({
    id: null,
    barang_id: "",
    jumlah: 1,
    satuan: "",
  });
}

function onBarangSelect(index) {
  const item = form.items[index];
  const barang = props.barangs.find((b) => b.id == item.barang_id);
  item.satuan = barang?.satuan?.nama_satuan ?? "-";
}

function removeItem(index) {
  form.items.splice(index, 1);
}

function updateData() {
  form.put(`/barang-masuk/${props.barangMasuk.id}`);
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-8 px-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">
            Edit Barang Masuk
          </h1>
          <p class="text-gray-600 text-sm mt-1">
            Perbarui informasi barang masuk dan detail barang
          </p>
        </div>

        <Link
          :href="route('barang-masuk.index')"
          class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700"
        >
          Kembali
        </Link>
      </div>

      <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">
          Informasi Umum
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-sm text-gray-600">Tanggal Masuk</label>
            <input
              type="date"
              v-model="form.tanggal_masuk"
              class="w-full mt-1 px-3 py-2 border rounded-md"
            />
            <div v-if="form.errors.tanggal_masuk" class="text-red-500 text-sm mt-1">
              {{ form.errors.tanggal_masuk }}
            </div>
          </div>

          <div>
            <label class="text-sm text-gray-600">Keterangan</label>
            <input
              type="text"
              v-model="form.keterangan"
              class="w-full mt-1 px-3 py-2 border rounded-md"
              placeholder="Opsional"
            />
            <div v-if="form.errors.keterangan" class="text-red-500 text-sm mt-1">
              {{ form.errors.keterangan }}
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-800">
            Detail Barang Masuk
          </h2>

          <button
            type="button"
            @click="addItem"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Tambah Baris
          </button>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Barang
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Jumlah
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Satuan
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-600 uppercase">
                Aksi
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="(item, index) in form.items"
              :key="index"
              class="hover:bg-gray-50"
            >
              <td class="px-4 py-2">
                <select
                  v-model="item.barang_id"
                  @change="onBarangSelect(index)"
                  class="w-full px-3 py-2 border rounded-md"
                >
                  <option value="">Pilih barang</option>
                  <option
                    v-for="barang in props.barangs"
                    :key="barang.id"
                    :value="barang.id"
                  >
                    {{ barang.nama_barang }}
                  </option>
                </select>

                <div class="text-red-500 text-sm mt-1" v-if="form.errors[`items.${index}.barang_id`]">
                  {{ form.errors[`items.${index}.barang_id`] }}
                </div>
              </td>

              <td class="px-4 py-2">
                <input
                  type="number"
                  v-model="item.jumlah"
                  min="1"
                  class="w-full px-3 py-2 border rounded-md"
                />

                <div class="text-red-500 text-sm mt-1" v-if="form.errors[`items.${index}.jumlah`]">
                  {{ form.errors[`items.${index}.jumlah`] }}
                </div>
              </td>

              <td class="px-4 py-2 text-gray-800">
                {{ item.satuan || "-" }}
              </td>

              <td class="px-4 py-2">
                <button
                  type="button"
                  @click="removeItem(index)"
                  class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                >
                  Hapus
                </button>
              </td>
            </tr>

            <tr v-if="form.items.length === 0">
              <td colspan="4" class="text-center py-4 text-gray-500">
                Belum ada detail barang masuk.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="updateData"
          :disabled="form.processing"
          class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
        >
          Perbarui Data
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
