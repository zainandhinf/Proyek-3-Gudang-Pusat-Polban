<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { throttle } from 'lodash';

const props = defineProps({
    barangs: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');

// --- SVG Icons sesuai desain Figma ---
const importIcon = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>`;
const plusIcon = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>`;
const searchIcon = `<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>`;
const editIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>`;
const deleteIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`;

// Search Debounce
watch(
    search,
    throttle((value) => {
        router.get(
            route('barangs.index'),
            { search: value && value.length > 0 ? value : undefined },
            { preserveState: true, replace: true }
        );
    }, 300)
);
  

const deleteBarang = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data barang ini?')) {
        router.delete(route('barangs.destroy', id));
    }
};
</script>

<template>
    <Head title="Master Data Barang" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Master Data Barang</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <!-- Header Title -->
                <div class="px-4 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-800">Master Data Barang</h1>
                    <p class="mt-2 text-gray-600">Kelola semua data barang yang tersedia di gudang, termasuk stok dan kategorisasi.</p>
                </div>

                <!-- Toolbar -->
                <div class="flex flex-col mt-6 md:flex-row md:justify-end gap-x-3 gap-y-3">
                    <button class="flex items-center justify-center px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 shadow-sm">
                        <span v-html="importIcon"></span>
                        Import Excel
                    </button>
                    <Link :href="route('barangs.create')" class="flex items-center justify-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm">
                        <span v-html="plusIcon"></span>
                        Tambah Barang
                    </Link>
                </div>

                <!-- Content Table -->
                <div class="mt-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
                    
                    <!-- Search Bar -->
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                        <div class="relative w-full md:w-72">
                            <span v-html="searchIcon"></span>
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Cari kode atau nama barang..."
                                class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500"
                            />
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase w-16">No</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Kode Barang</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Barang</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Kategori</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Satuan</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Stok</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="barangs.data.length === 0">
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        Data barang tidak ditemukan.
                                    </td>
                                </tr>
                                <tr v-for="(barang, index) in barangs.data" :key="barang.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-center text-gray-500">
                                        {{ (barangs.current_page - 1) * barangs.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">
                                            {{ barang.kode_barang }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ barang.nama_barang }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <span v-if="barang.kategori" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ barang.kategori.nama_kategori }}
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ barang.satuan ? barang.satuan.nama_satuan : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center font-medium" :class="barang.stok > 0 ? 'text-green-600' : 'text-red-600'">
                                        {{ barang.stok }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <div class="flex justify-center gap-x-3">
                                            <Link :href="route('barangs.edit', barang.id)" class="text-teal-600 hover:text-teal-900 p-1 rounded hover:bg-teal-50" title="Edit">
                                                <span v-html="editIcon"></span>
                                            </Link>
                                            <button @click="deleteBarang(barang.id)" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50" title="Hapus">
                                                <span v-html="deleteIcon"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex items-center justify-between p-4 border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">{{ barangs.from ?? 0 }}</span> sampai <span class="font-medium">{{ barangs.to ?? 0 }}</span> dari <span class="font-medium">{{ barangs.total }}</span> hasil
                        </div>
                        <div class="flex gap-1">
                                <template v-for="(link, k) in barangs.links" :key="k">
                                    <Link 
                                        v-if="link.url"
                                        :href="link.url"
                                        v-html="link.label"
                                        class="px-3 py-1 text-sm border rounded-md"
                                        :class="{
                                            'bg-teal-600 text-white border-teal-600': link.active,
                                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-50': !link.active
                                        }"
                                    />
                                    <span 
                                        v-else
                                        v-html="link.label"
                                        class="px-3 py-1 text-sm border rounded-md bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed"
                                    ></span>
                                </template>
                            </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>