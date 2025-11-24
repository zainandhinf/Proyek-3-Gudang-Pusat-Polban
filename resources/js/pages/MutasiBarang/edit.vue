<script setup>
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  mutasi: Object,
  barangs: Array,
});

const form = useForm({
  jenis_mutasi: props.mutasi.jenis_mutasi,
  tanggal_mutasi: props.mutasi.tanggal_mutasi,
  keterangan: props.mutasi.keterangan ?? "",
  items: props.mutasi.detail.map(d => ({
    barang_id: d.barang_id,
    jumlah: d.jumlah,
    satuan: d.barang?.satuan?.nama_satuan ?? "",
    catatan: d.catatan ?? "",
    stok: d.barang.stok_saat_ini ?? 0
  })),
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

function removeItem(idx) {
  if (form.items.length === 1) return;
  form.items.splice(idx, 1);
}

function onBarangSelect(idx) {
  const item = form.items[idx];
  const barang = props.barangs.find(b => b.id == item.barang_id);

  if (!barang) {
    item.stok = 0;
    item.satuan = "";
    return;
  }

  item.stok = barang.stok_saat_ini ?? 0;
  item.satuan = barang.satuan?.nama_satuan ?? "";
}

function update() {
  form.put(route("mutasi-barang.update", props.mutasi.id));
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="p-6">

      <h2 class="text-xl font-semibold">Edit Mutasi Barang</h2>

      <form @submit.prevent="update" class="mt-6 bg-white p-4 rounded shadow">

        <!-- Data umum -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

          <div>
            <label>Jenis Mutasi</label>
            <select v-model="form.jenis_mutasi" class="w-full p-2 border rounded" required>
              <option value="masuk">Masuk</option>
              <option value="keluar">Keluar</option>
            </select>
          </div>

          <div>
            <label>Tanggal Mutasi</label>
            <input type="date" v-model="form.tanggal_mutasi" class="w-full p-2 border rounded" required>
          </div>

          <div>
            <label>Keterangan</label>
            <input type="text" v-model="form.keterangan" class="w-full p-2 border rounded">
          </div>

        </div>


        <!-- Items -->
        <div class="mt-6">
          <h3 class="font-semibold text-lg">Detail Barang</h3>

          <table class="min-w-full mt-2 border">
            <thead>
              <tr class="bg-gray-100">
                <th>No</th>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Stok Saat Ini</th>
                <th>Stok Setelah</th>
                <th>Satuan</th>
                <th>Catatan</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(item, idx) in form.items" :key="idx">

                <td class="text-center">{{ idx + 1 }}</td>

                <td>
                  <select v-model="item.barang_id" @change="onBarangSelect(idx)" class="border w-full p-1">
                    <option value="">Pilih</option>
                    <option v-for="b in props.barangs" :value="b.id">{{ b.nama_barang }}</option>
                  </select>
                </td>

                <td><input type="number" v-model="item.jumlah" min="1" class="border w-full p-1"></td>

                <td class="text-center">{{ item.stok }}</td>

                <td class="text-center">
                  {{ form.jenis_mutasi == 'masuk'
                      ? item.stok + item.jumlah
                      : item.stok - item.jumlah }}
                </td>

                <td>{{ item.satuan }}</td>

                <td>
                  <input type="text" v-model="item.catatan" class="border w-full p-1">
                </td>

                <td class="text-center">
                  <button @click.prevent="removeItem(idx)" class="text-red-600">Hapus</button>
                </td>

              </tr>
            </tbody>

          </table>

          <button @click.prevent="addItem"
            class="mt-3 px-4 py-2 bg-teal-600 text-white rounded">
            Tambah Barang
          </button>
        </div>


        <!-- Submit -->
        <div class="flex justify-end mt-6">
          <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded">
            Update
          </button>
        </div>

      </form>
    </div>
  </AuthenticatedLayout>
</template>
