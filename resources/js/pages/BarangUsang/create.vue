<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useForm, Head, Link } from "@inertiajs/vue3";

const props = defineProps({
  barangs: Array,
});

const form = useForm({
  // Field disesuaikan dengan kebutuhan Barang Usang (bukan Mutasi)
  tanggal_catat: new Date().toISOString().split("T")[0],
  no_bukti: "",
  keterangan: "",
  items: [
    {
      barang_id: "",
      jumlah: 1,
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
    catatan: "",
    stok: 0,
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
    item.stok = 0;
    item.satuan = "";
    return;
  }
  // Ambil stok untuk validasi/info saja
  item.stok = barang.stok_saat_ini ?? 0;
  item.satuan = barang.satuan?.nama_satuan || '-';
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
      catatan: "",
      stok: 0,
    },
  ];
  form.tanggal_catat = new Date().toISOString().split("T")[0];
}

// -------------------------------
// SIMPAN DATA
// -------------------------------
function simpan() {
  form.post(route("barang-usang.store"), {
    onSuccess: () => resetForm(),
  });
}
</script>

<template>
  <Head title="Buat Catatan Barang Rusak" />
  <AuthenticatedLayout>
    <div class="p-6">
      <h2 class="text-xl font-bold text-gray-900">Catat Barang Rusak/Usang</h2>

      <form @submit.prevent="simpan" class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Tanggal Catat <span class="text-red-500">*</span>
            </label>
            <input
              type="date"
              v-model="form.tanggal_catat"
              class="w-full p-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-teal-500 focus:border-teal-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                No. Bukti / Dokumen <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                v-model="form.no_bukti" 
                placeholder="Contoh: BA-RUSAK-001" 
                class="w-full p-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-teal-500 focus:border-teal-500" 
                required
            />
            <p class="text-xs text-gray-500 mt-1">Masukkan nomor dokumen manual (wajib).</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Keterangan
            </label>
            <input
              type="text"
              v-model="form.keterangan"
              placeholder="Contoh: Penghapusan Aset Tahunan"
              class="w-full p-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:ring-teal-500 focus:border-teal-500"
            />
          </div>
        </div>

        <hr class="border-gray-200 my-6">

        <div>
          <h3 class="text-lg font-semibold text-gray-800 flex items-center mb-4">
            Detail Barang
          </h3>

          <div class="overflow-x-auto border border-gray-200 rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">No</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase min-w-[200px]">
                    Nama Barang <span class="text-red-500">*</span>
                  </th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-24">
                    Jumlah <span class="text-red-500">*</span>
                  </th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Stok Saat Ini</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase min-w-[200px]">Detail Kerusakan</th>
                  <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-16">Aksi</th>
                </tr>
              </thead>

              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(item, idx) in form.items" :key="idx">
                  <td class="px-4 py-3 text-center text-sm text-gray-500">
                    {{ idx + 1 }}
                  </td>

                  <td class="px-4 py-3">
                    <select
                      v-model.number="item.barang_id"
                      @change="onBarangSelect(idx)"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500"
                      required
                    >
                      <option value="">-- Pilih Barang --</option>
                      <option v-for="b in props.barangs" :key="b.id" :value="b.id">
                        {{ b.nama_barang }}
                      </option>
                    </select>
                  </td>

                  <td class="px-3 py-2">
                    <input
                        type="number"
                        min="1"
                        v-model="item.jumlah"
                        class="w-full p-2 border rounded-md text-sm transition focus:ring-2 focus:ring-teal-500 outline-none"
                        :class="{ 'border-red-500 bg-red-50': item.jumlah > item.stok }"
                        placeholder="0"
                    />
                    <div v-if="item.jumlah > item.stok" class="text-[10px] text-red-600 mt-1 font-bold">
                        Stok Maks: {{ item.stok }}
                    </div>
                    <div v-if="form.errors[`items.${index}.jumlah`]" class="text-xs text-red-500 mt-1">
                        {{ form.errors[`items.${index}.jumlah`] }}
                    </div>
                </td>

                  <td class="px-4 py-3 text-center text-sm text-gray-600 bg-gray-50">
                    {{ item.stok }}
                  </td>

                  <td class="px-4 py-3">
                    <input
                      type="text"
                      v-model="item.catatan"
                      placeholder="Contoh: Pecah / Hilang"
                      class="w-full p-2 border border-gray-300 rounded-md text-sm focus:ring-teal-500 focus:border-teal-500"
                    />
                  </td>

                  <td class="px-4 py-3 text-center">
                    <button
                      @click.prevent="removeItem(idx)"
                      class="text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded transition"
                      title="Hapus Baris"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <button
            @click.prevent="addItem"
            class="mt-4 flex items-center px-4 py-2 bg-teal-50 text-teal-700 border border-teal-200 rounded-lg hover:bg-teal-100 transition text-sm font-medium"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Barang
          </button>
        </div>

        <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
          <Link
            :href="route('barang-usang.index')"
            class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition"
          >
            Batal
          </Link>
          <button
            type="submit"
            class="px-6 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-medium shadow-sm transition"
          >
            Simpan Catatan
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>