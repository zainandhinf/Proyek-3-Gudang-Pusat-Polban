<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  mutasi: Object,   // data mutasi + detail
  barangs: Array,   // data barang lengkap
});

const form = useForm({
  jenis_mutasi: props.mutasi.jenis_mutasi,
  tanggal_mutasi: props.mutasi.tanggal_mutasi,
  keterangan: props.mutasi.keterangan ?? "",
  items: props.mutasi.detail.map(d => {
    const barang = props.barangs.find(b => b.id === d.barang_id);

    return {
      id: d.id ?? null,
      barang_id: d.barang_id,
      jumlah: d.jumlah,
      satuan: barang?.satuan?.nama_satuan ?? "",
      stok: barang?.stok_saat_ini ?? 0,
      catatan: d.catatan ?? ""
    };
  }),
});

// ----------------------------------------------------
// TAMBAH ITEM BARU
// ----------------------------------------------------
function addItem() {
  form.items.push({
    id: null,
    barang_id: "",
    jumlah: 1,
    satuan: "",
    stok: 0,
    catatan: ""
  });
}

// ----------------------------------------------------
// HAPUS ITEM
// ----------------------------------------------------
function removeItem(index) {
  form.items.splice(index, 1);
}

// ----------------------------------------------------
// SAAT PILIH BARANG
// ----------------------------------------------------
function onBarangSelect(index) {
  const item = form.items[index];
  const barang = props.barangs.find(b => b.id == item.barang_id);

  if (!barang) {
    item.satuan = "";
    item.stok = 0;
    return;
  }

  item.stok = barang.stok_saat_ini ?? 0;
  item.satuan = barang.satuan?.nama_satuan ?? "";
}

// ----------------------------------------------------
// UPDATE (PUT)
// ----------------------------------------------------
function update() {
  form.put(route("mutasi-barang.update", props.mutasi.id));
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-xl font-semibold">Edit Mutasi Barang</h2>

      <form @submit.prevent="update" class="mt-6 bg-white p-4 rounded shadow">

        <!-- Data Umum -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="text-sm">Jenis Mutasi</label>
            <select v-model="form.jenis_mutasi"
              class="w-full p-2 border border-gray-300 rounded-md text-sm"
              required>
              <option value="masuk">Masuk</option>
              <option value="keluar">Keluar</option>
            </select>
          </div>

          <div>
            <label class="text-sm">Tanggal Mutasi</label>
            <input type="date" v-model="form.tanggal_mutasi"
              class="w-full p-2 border border-gray-300 rounded-md text-sm"
              required>
          </div>

          <div>
            <label class="text-sm">Keterangan</label>
            <input type="text" v-model="form.keterangan"
              class="w-full p-2 border border-gray-300 rounded-md text-sm"
              placeholder="Opsional">
          </div>
        </div>

        <!-- Detail Barang -->
        <div class="mt-4">
          <h3 class="text-lg font-semibold text-blue-900 mb-4">
            Detail Barang
          </h3>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-3 py-2 text-center text-xs font-medium">No</th>
                  <th class="px-3 py-2 text-left text-xs font-medium">Nama Barang</th>
                  <th class="px-3 py-2 text-left text-xs font-medium">Jumlah</th>
                  <th class="px-3 py-2 text-center text-xs font-medium">Stok Saat Ini</th>
                  <th class="px-3 py-2 text-center text-xs font-medium">Stok Setelah</th>
                  <th class="px-3 py-2 text-left text-xs font-medium">Satuan</th>
                  <th class="px-3 py-2 text-left text-xs font-medium">Catatan</th>
                  <th class="px-3 py-2 text-center text-xs font-medium">Aksi</th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">

                <tr v-for="(item, idx) in form.items" :key="idx">

                  <!-- NO -->
                  <td class="px-3 py-2 text-center">{{ idx + 1 }}</td>

                  <!-- SELECT BARANG -->
                  <td class="px-3 py-2" style="min-width:150px;">
                    <select v-model="item.barang_id"
                      @change="onBarangSelect(idx)"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm" required>
                      <option value="">Pilih Barang</option>
                      <option v-for="b in props.barangs" :key="b.id" :value="b.id">
                        {{ b.nama_barang }}
                      </option>
                    </select>
                  </td>

                  <!-- JUMLAH -->
                  <td class="px-3 py-2" style="width:100px">
                    <input type="number" min="1"
                      v-model.number="item.jumlah"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm" required>
                  </td>

                  <!-- STOK SAAT INI -->
                  <td class="px-3 py-2 text-center">{{ item.stok }}</td>

                  <!-- STOK SETELAH -->
                  <td class="px-3 py-2 text-center font-semibold">
                    {{
                      form.jenis_mutasi === "masuk"
                        ? item.stok + item.jumlah
                        : item.stok - item.jumlah
                    }}
                  </td>

                  <!-- SATUAN -->
                  <td class="px-3 py-2">
                    <input type="text" v-model="item.satuan"
                      class="w-full p-2 border border-gray-300 rounded-md bg-gray-50 text-sm" disabled />
                  </td>

                  <!-- CATATAN -->
                  <td class="px-3 py-2">
                    <input type="text" v-model="item.catatan"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm"
                      placeholder="Opsional" />
                  </td>

                  <!-- AKSI -->
                  <td class="px-3 py-2 text-center">
                    <button @click.prevent="removeItem(idx)"
                      class="text-red-500 hover:text-red-700">
                      Hapus
                    </button>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>

          <!-- Button tambah -->
          <button type="button"
            @click="addItem"
            class="mt-3 px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700">
            Tambah Baris
          </button>
        </div>

        <!-- Submit -->
        <div class="mt-6 flex justify-end">
          <button :disabled="form.processing"
            class="px-5 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Update Mutasi
          </button>
        </div>

      </form>
    </div>
  </AuthenticatedLayout>
</template>
