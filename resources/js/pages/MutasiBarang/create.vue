<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  barangs: Array,
});

const form = useForm({
  jenis_mutasi: "",
  tanggal_mutasi: new Date().toISOString().split("T")[0],
  keterangan: "",
  items: [
    {
      barang_id: "",
      jumlah: 1,
      satuan: "",
      catatan: "",
      stok: 0,
    },
  ],
});

// -------------------------------
// TAMBAH ITEM BARU
// -------------------------------
function addItem() {
  form.items.push({
    barang_id: "",
    jumlah: 1,
    satuan: "",
    catatan: "",
  });
}

// -------------------------------
// HAPUS ITEM
// -------------------------------
function removeItem(index) {
  if (form.items.length === 1) return;
  form.items.splice(index, 1);
}

// -------------------------------
// SAAT PILIH BARANG
// -------------------------------
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

// -------------------------------
// RESET FORM
// -------------------------------
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

// -------------------------------
// SIMPAN DATA
// -------------------------------
function simpan() {
  form.post(route("mutasi-barang.store"), {
    onSuccess: () => resetForm(),
  });
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-xl font-semibold">Buat Mutasi Barang</h2>

      <form @submit.prevent="simpan" class="mt-6 bg-white rounded p-4">
        <!-- FORM ATAS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="text-sm">Jenis Mutasi</label>
            <select
              v-model="form.jenis_mutasi"
              class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500"
              required
            >
              <option value="">Pilih Jenis Mutasi</option>
              <option value="masuk">Masuk</option>
              <option value="keluar">Keluar</option>
            </select>
          </div>

          <div>
            <label class="text-sm">Tanggal Mutasi</label>
            <input
              type="date"
              v-model="form.tanggal_mutasi"
              class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500"
              required
            />
          </div>

          <div>
            <label class="text-sm">Keterangan</label>
            <input
              type="text"
              v-model="form.keterangan"
              placeholder="Tambahkan keterangan (opsional)"
              class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500"
            />
          </div>
        </div>

        <!-- TABEL ITEM -->
        <div class="mt-4">
          <h3 class="text-lg font-semibold text-blue-900 flex items-center mb-4">
            Detail Barang
          </h3>

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
  </AuthenticatedLayout>
</template>
