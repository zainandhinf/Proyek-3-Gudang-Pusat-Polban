<script setup>
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import {
  Search,
  Upload,
  Plus,
  Edit2,
  Trash2,
  ChevronRight,
  ChevronLeft,
} from "lucide-vue-next";
// Asumsi Anda memiliki Layout. Ganti 'YourLayout' dengan nama layout Anda
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout.vue";
import Auth from "@/actions/App/Http/Controllers/Auth";

// Mendefinisikan props yang diterima dari KategoriController
const props = defineProps({
  kategoris: Object, // Ini adalah objek paginasi dari Laravel
  filters: Object, // Ini adalah filter pencarian
});

// State untuk input pencarian
const search = ref(props.filters.search);

let searchTimeout = null;
watch(search, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    // Pastikan nama rute ini ('kategoris.index') sesuai dengan di web.php
    router.get(
      route("kategoris.index"),
      { search: value },
      {
        preserveState: true,
        replace: true,
      }
    );
  }, 300); // Debounce 300ms
});

// Fungsi untuk 'Tambah Kategori' (Contoh)
const addKategori = () => {
  // Pastikan nama rute ini ('kategoris.create') sesuai dengan di web.php
  router.get(route("kategori.create"));
};

// Fungsi untuk 'Edit' (Contoh)
const editKategori = (id) => {
  // Pastikan nama rute ini ('kategoris.edit') sesuai dengan di web.php
  router.get(route("kategoris.edit", id));
};

// Fungsi untuk 'Delete' (Contoh)
const deleteKategori = (id) => {
  if (confirm("Apakah Anda yakin ingin menghapus kategori ini?")) {
    // Pastikan nama rute ini ('kategoris.destroy') sesuai dengan di web.php
    router.delete(route("kategoris.destroy", id), {
      preserveScroll: true,
    });
  }
};
</script>

<template>
  <Head title="Master Data Kategori" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Master Data Kategori
      </h2>
    </template>

  <!-- Halaman Utama -->
  <div class="flex-1 ml-30 mt-[7px] p-8">
    <div class="max-w-[1184px]">
      <!-- Breadcrumbs -->
      <div class="flex items-center gap-2 mb-6 text-sm">
        <div class="flex items-center gap-2">
          <!-- SVG Ikon Tag -->
          <svg
            width="16"
            height="16"
            viewBox="0 0 16 16"
            fill="currentColor"
            class="text-[#6B7280]"
          >
            <path
              d="M10.7812 1.22187L14.775 5.2625C16.4125 6.91875 16.4125 9.58125 14.775 11.2375L11.275 14.7781C10.9844 15.0719 10.5094 15.075 10.2156 14.7844C9.92187 14.4938 9.91875 14.0188 10.2094 13.725L13.7063 10.1844C14.7656 9.1125 14.7656 7.39062 13.7063 6.31875L9.71562 2.27813C9.425 1.98438 9.42813 1.50938 9.72188 1.21875C10.0156 0.928125 10.4906 0.93125 10.7812 1.225V1.22187ZM0 7.17188V2.5C0 1.67188 0.671875 1 1.5 1H6.17188C6.70312 1 7.2125 1.20938 7.5875 1.58438L12.8375 6.83437C13.6187 7.61562 13.6187 8.88125 12.8375 9.6625L8.66562 13.8344C7.88437 14.6156 6.61875 14.6156 5.8375 13.8344L0.5875 8.58438C0.209375 8.20938 0 7.70312 0 7.17188ZM4.5 4.5C4.5 4.23478 4.39464 3.98043 4.20711 3.79289C4.01957 3.60536 3.76522 3.5 3.5 3.5C3.23478 3.5 2.98043 3.60536 2.79289 3.79289C2.60536 3.98043 2.5 4.23478 2.5 4.5C2.5 4.76522 2.60536 5.01957 2.79289 5.20711C2.98043 5.39464 3.23478 5.5 3.5 5.5C3.76522 5.5 4.01957 5.39464 4.20711 5.20711C4.39464 5.01957 4.5 4.76522 4.5 4.5Z"
            />
          </svg>
          <span class="text-[#6B7280]">Kategori</span>
        </div>
        <ChevronRight class="w-3 h-3 text-[#6B7280]" />
        <span class="text-[#1D3557] font-medium">Operator Gudang</span>
      </div>

      <!-- Header Halaman -->
      <div class="mb-10">
        <h1 class="text-[30px] font-bold text-[#0F172A] leading-9 mb-2">
          Master Data Kategori
        </h1>
        <p class="text-base text-[#475569] leading-6">Kelola kategori produk Anda</p>
      </div>

      <!-- Tombol Aksi -->
      <div class="flex justify-end gap-3 mb-6">
        <button
          class="flex items-center gap-2 h-10 px-4 bg-[#F1F5F9] rounded-lg text-[#334155] text-base hover:bg-[#E2E8F0] transition-colors"
        >
          <Upload class="w-4 h-4 text-[#0D9488]" />
          Import Excel
        </button>

        <button
          @click="addKategori"
          class="flex items-center gap-2 h-10 px-4 bg-[#0D9488] rounded-lg text-white text-base hover:bg-[#0D9488]/90 transition-colors"
        >
          <Plus class="w-[14px] h-4" />
          Tambah Kategori
        </button>
      </div>

      <!-- Konten Utama (Tabel) -->
      <div class="bg-white rounded-lg border border-[#E2E8F0] shadow-sm">
        <!-- Header Tabel + Pencarian -->
        <div
          class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0]"
        >
          <h2 class="text-lg font-semibold text-[#0F172A]">Daftar Kategori</h2>
          <div class="relative w-[292px]">
            <Search
              class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[#94A3B8]"
            />
            <input
              v-model="search"
              type="text"
              placeholder="Cari kategori..."
              class="w-full h-[42px] pl-10 pr-4 border border-[#CBD5E1] rounded-lg text-base placeholder:text-[#ADAEBC] focus:outline-none focus:ring-2 focus:ring-[#0D9488]"
            />
          </div>
        </div>

        <!-- Tabel -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-[#F8FAFC]">
              <tr>
                <th
                  class="px-4 py-4 text-center text-xs font-medium text-[#64748B] tracking-wider uppercase"
                >
                  ID
                </th>
                <th
                  class="px-4 py-4 text-center text-xs font-medium text-[#64748B] tracking-wider uppercase"
                >
                  Nama Kategori
                </th>
                <th
                  class="px-4 py-4 text-center text-xs font-medium text-[#64748B] tracking-wider uppercase"
                >
                  Deskripsi
                </th>
                <th
                  class="px-4 py-4 text-center text-xs font-medium text-[#64748B] tracking-wider uppercase"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <!-- Looping data menggunakan v-for -->
              <tr
                v-for="(kategori, index) in kategoris.data"
                :key="kategori.id"
                :class="[
                  index % 2 === 0 ? 'bg-white' : 'bg-[#F8FAFC]',
                  'border-t border-[#E2E8F0]',
                ]"
              >
                <td class="px-4 py-5 text-center text-sm text-[#0F172A]">
                  {{ kategori.id }}
                </td>
                <td class="px-4 py-5 text-center text-sm font-medium text-[#0F172A]">
                  {{ kategori.name }}
                </td>
                <td class="px-4 py-5 text-center text-sm text-[#64748B]">
                  {{ kategori.description }}
                </td>
                <td class="px-4 py-5">
                  <div class="flex items-center justify-center gap-3">
                    <button
                      @click="editKategori(kategori.id)"
                      class="p-1 hover:bg-[#F1F5F9] rounded transition-colors"
                    >
                      <Edit2 class="w-[14px] h-[14px] text-[#0D9488]" />
                    </button>
                    <button
                      @click="deleteKategori(kategori.id)"
                      class="p-1 hover:bg-[#FEE2E2] rounded transition-colors"
                    >
                      <Trash2 class="w-3 h-[14px] text-[#DC2626]" />
                    </button>
                  </div>
                </td>
              </tr>
              <!-- Tampilan jika tidak ada data -->
              <tr v-if="kategoris.data.length === 0">
                <td colspan="4" class="px-4 py-5 text-center text-sm text-gray-500">
                  Tidak ada data kategori ditemukan.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          class="flex items-center justify-between px-6 py-4 border-t border-[#E2E8F0]"
        >
          <!-- Info Paginasi -->
          <div class="text-sm text-[#374151]">
            Menampilkan {{ kategoris.from || 0 }}-{{ kategoris.to || 0 }} dari
            {{ kategoris.total || 0 }} data
          </div>

          <!-- Link Paginasi (dinamis dari Laravel) -->
          <div class="flex items-center gap-2">
            <template v-for="(link, index) in kategoris.links" :key="index">
              <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                  'flex items-center justify-center border border-[#CBD5E1] rounded-lg text-sm font-normal transition-colors',
                  {
                    'bg-[#14B8A6] text-white w-[30px] h-[36px] border-[#14B8A6]':
                      link.active,
                  },
                  { 'hover:bg-[#F8FAFC] w-[35px] h-[38px]': !link.active },
                ]"
              >
                <span v-if="index === 0">
                  <ChevronLeft class="w-[9px] h-[14px] text-black" />
                </span>
                <span v-else-if="index === kategoris.links.length - 1">
                  <ChevronRight class="w-[9px] h-[14px] text-black" />
                </span>
                <span v-else v-html="link.label"></span>
              </Link>

              <span
                v-else
                :class="[
                  'flex items-center justify-center border border-[#CBD5E1] rounded-lg text-sm font-normal transition-colors',
                  'opacity-50 cursor-not-allowed w-[35px] h-[38px]',
                ]"
              >
                <span v-if="index === 0">
                  <ChevronLeft class="w-[9px] h-[14px] text-black" />
                </span>
                <span v-else-if="index === kategoris.links.length - 1">
                  <ChevronRight class="w-[9px] h-[14px] text-black" />
                </span>
                <span v-else v-html="link.label"></span>
              </span>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>
