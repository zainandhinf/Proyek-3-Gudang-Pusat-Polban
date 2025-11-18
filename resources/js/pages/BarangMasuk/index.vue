<script setup>
import { ref, reactive } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
// import { Head } from '@inertiajs/vue3'; // Jika menggunakan Inertia

// // Data reaktif untuk form tambah barang
// const form = reactive({
//   barang_id: "",
//   jumlah: null,
//   tanggal_masuk: new Date().toISOString().split("T")[0], // Default hari ini
//   keterangan: "",
// });

// Data reaktif untuk form
const form = ref({
  jurusan: "",
  prodi: "",
  lab: "",
  unit: "",
  tanggal_masuk: new Date().toISOString().split("T")[0], // Default hari ini
  keterangan: "",
  items: [],
  menyetujui: "Kepala Subbagian Umum",
  koordinator: "Ahmad Fauzi, S.T., M.T.",
});

// Data dummy untuk riwayat (berdasarkan HTML Anda)
const riwayatBarang = ref([
  {
    id: 1,
    tanggal: "2024-01-15",
    nama: "Laptop Dell Inspiron",
    jumlah: "5 unit",
    operator: "Ahmad Rizki",
    catatan: "Pembelian baru",
  },
  {
    id: 2,
    tanggal: "2024-01-14",
    nama: "Mouse Wireless",
    jumlah: "10 unit",
    operator: "Sari Dewi",
    catatan: "Stok tambahan",
  },
  {
    id: 3,
    tanggal: "2024-01-13",
    nama: "Keyboard Mechanical",
    jumlah: "3 unit",
    operator: "Budi Santoso",
    catatan: "Penggantian",
  },
]);

// Data dummy untuk dropdown pilihan barang
const pilihanBarang = ref([
  { id: 1, nama: "Laptop Dell Inspiron" },
  { id: 2, nama: "Mouse Wireless" },
  { id: 3, nama: "Keyboard Mechanical" },
  { id: 4, nama: "Proyektor" },
]);
const barangSatuanMap = {
  kertas_a4: "Rim",
  tinta_printer: "Botol",
  mouse_logitech: "Unit",
};


// Fungsi untuk menambah baris barang baru
const addItem = () => {
  form.value.items.push({
    id: Date.now(), // ID unik sederhana
    nama: '',
    satuan: '',
    jumlah: null
  });
};

// Fungsi untuk menghapus baris barang
const removeItem = (index) => {
  form.value.items.splice(index, 1);
};

// Fungsi untuk mereset form
function resetForm() {
  form.barang_id = "";
  form.jumlah = null;
  form.tanggal_masuk = new Date().toISOString().split("T")[0];
  form.keterangan = "";
}

// Fungsi untuk menyimpan data
function simpanData() {
  console.log("Data Form:", form);

  // Di aplikasi nyata, Anda akan mengirim ini ke backend Laravel
  // CONTOH:
  // import { useForm } from '@inertiajs/vue3';
  // const inertiaForm = useForm(form);
  // inertiaForm.post('/barang-masuk', {
  //   onSuccess: () => resetForm(),
  // });

  // Untuk demo, kita tambahkan ke list riwayat
  const barangTerpilih = pilihanBarang.value.find((b) => b.id == form.barang_id);
  riwayatBarang.value.unshift({
    id: Date.now(),
    tanggal: form.tanggal_masuk,
    nama: barangTerpilih ? barangTerpilih.nama : "Barang Tidak Dikenali",
    jumlah: `${form.jumlah} unit`,
    operator: "Ahmad Operator", // Ambil dari data user yang login
    catatan: form.keterangan,
  });

  resetForm();
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="py-8 px-4 md:px-6 lg:px-8">
      <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Barang Masuk</h1>
        <p class="mt-1 text-sm text-gray-600">
          Kelola data barang yang masuk ke inventory
        </p>
      </div>

      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
          <h2 class="text-xl font-semibold text-gray-800 border-b pb-4">
            Tambah Barang Masuk
          </h2>
          <p class="text-sm text-gray-500 mt-1">
            Input data barang yang masuk ke inventory
          </p>

          <form @submit.prevent="simpanData" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="tanggal_masuk" class="block text-sm font-medium text-gray-700"
                  >Tanggal Masuk</label
                >
                <input
                  type="date"
                  id="tanggal_masuk"
                  v-model="form.tanggal_masuk"
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                />
              </div>

              <div>
                <label for="keterangan" class="block text-sm font-medium text-gray-700"
                  >Keterangan</label
                >
                <input
                  type="text"
                  id="keterangan"
                  v-model="form.keterangan"
                  placeholder="Tambahkan keterangan (opsional)"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                />
              </div>
            </div>

            <div class="mb-6 mt-6">
              <h3 class="text-lg font-semibold text-blue-900 flex items-center mb-4">
                <svg
                  class="w-5 h-5 text-teal-600 mr-2"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
                  ></path>
                </svg>
                Daftar Barang yang Diajukan
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
                        Satuan
                      </th>
                      <th
                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        Jumlah
                      </th>
                      <th
                        class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16"
                      >
                        Aksi
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="form.items == 0">
                      <td colspan="6" class="px-4 py-4 text-center text-sm text-gray-500">
                        Belum ada barang yang ditambahkan.
                      </td>
                    </tr>
                    <tr v-for="(item, index) in form.items" :key="item.id">
                      <td
                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center"
                      >
                        {{ index + 1 }}
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap" style="min-width: 150px">
                        <select
                          v-model="item.nama"
                          class="w-full p-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-teal-500"
                        >
                          <option value="" disabled>Pilih Barang</option>
                          <option value="kertas_a4">Kertas A4</option>
                          <option value="tinta_printer">Tinta Printer</option>
                          <option value="mouse_logitech">Mouse Logitech</option>
                        </select>
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap" style="width: 120px">
                        <input
                          type="text"
                          v-model="item.satuan"
                          class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-50"
                          placeholder="Satuan"
                          :disabled="!!barangSatuanMap[item.nama]"
                        />
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap" style="width: 100px">
                        <input
                          type="number"
                          v-model="item.jumlah"
                          class="w-full p-2 border border-gray-300 rounded-md text-sm"
                          placeholder="0"
                        />
                      </td>
                      <td class="px-4 py-3 whitespace-nowrap text-center">
                        <button
                          @click.prevent="removeItem(index)"
                          class="text-red-500 hover:text-red-700"
                          title="Hapus barang"
                        >
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path
                              fill-rule="evenodd"
                              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                              clip-rule="evenodd"
                            ></path>
                          </svg>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="flex justify-between items-center mt-4">
                <button
                  @click.prevent="addItem"
                  class="flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
                >
                  <svg
                    class="w-4 h-4 mr-2"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                    ></path>
                  </svg>
                  Tambah Barang
                </button>
                <p class="text-xs text-gray-500">
                  * Jika kebutuhan lebih dari 15 item, sistem akan otomatis membuat
                  halaman tambahan.
                </p>
              </div>
            </div>

            <div class="flex justify-end gap-4 mt-6 pt-6 border-t">
              <button
                type="button"
                @click="resetForm"
                class="py-2 px-4 rounded-md bg-gray-600 text-white font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
              >
                Reset
              </button>
              <button
                type="submit"
                class="py-2 px-4 rounded-md bg-teal-600 text-white font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500"
              >
                Simpan
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
          <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center"
          >
            <div>
              <h2 class="text-xl font-semibold text-gray-800">Riwayat Barang Masuk</h2>
              <p class="text-sm text-gray-500 mt-1">
                Daftar barang yang telah ditambahkan
              </p>
            </div>
            <div class="flex gap-2 mt-4 md:mt-0">
              <button
                class="py-2 px-4 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Filter
              </button>
              <button
                class="py-2 px-4 rounded-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Export
              </button>
            </div>
          </div>

          <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Tanggal
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Barang
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Jumlah
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Operator
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Catatan
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  >
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-if="riwayatBarang.length === 0">
                  <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    Tidak ada data riwayat.
                  </td>
                </tr>
                <tr v-for="item in riwayatBarang" :key="item.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ item.tanggal }}
                  </td>
                  <td
                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"
                  >
                    {{ item.nama }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ item.jumlah }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ item.operator }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ item.catatan }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <button class="text-cyan-600 hover:text-cyan-900">Edit</button>
                    <button class="ml-4 text-red-600 hover:text-red-900">Delete</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div
            class="mt-5 flex items-center justify-between border-t border-gray-200 pt-4"
          >
            <div class="text-sm text-gray-700">
              Menampilkan <span class="font-medium">1</span>-
              <span class="font-medium">{{ riwayatBarang.length }}</span> dari
              <span class="font-medium">{{ riwayatBarang.length }}</span> data
            </div>
            <div class="flex gap-1">
              <button
                class="py-2 px-3 rounded-md border bg-white text-sm hover:bg-gray-50 disabled:opacity-50"
                disabled
              >
                Previous
              </button>
              <button
                class="py-2 px-3 rounded-md border bg-white text-sm hover:bg-gray-50"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
