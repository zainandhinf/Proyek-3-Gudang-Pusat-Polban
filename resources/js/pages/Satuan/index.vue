<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { throttle } from 'lodash';

// Komponen ini menerima data 'satuans' (paginasi) & 'filters' dari controller
const props = defineProps({
    satuans: Object,
    filters: Object,
});

// State reaktif untuk form pencarian
const search = ref(props.filters.search);

// --- SVG Icons dari file HTML Anda ---
const importIcon = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M9 3.41582V11.0002C9 11.5533 8.55313 12.0002 8 12.0002C7.44688 12.0002 7 11.5533 7 11.0002V3.41582L4.70625 5.70957C4.31563 6.1002 3.68125 6.1002 3.29063 5.70957C2.9 5.31895 2.9 4.68457 3.29063 4.29395L7.29063 0.293945C7.68125 -0.0966797 8.31563 -0.0966797 8.70625 0.293945L12.7063 4.29395C13.0969 4.68457 13.0969 5.31895 12.7063 5.70957C12.3156 6.1002 11.6812 6.1002 11.2906 5.70957L9 3.41582ZM2 11.0002H6C6 12.1033 6.89687 13.0002 8 13.0002C9.10312 13.0002 10 12.1033 10 11.0002H14C15.1031 11.0002 16 11.8971 16 13.0002V14.0002C16 15.1033 15.1031 16.0002 14 16.0002H2C0.896875 16.0002 0 15.1033 0 14.0002V13.0002C0 11.8971 0.896875 11.0002 2 11.0002Z" fill="#0D9488"/></svg>`;
const plusIcon = `<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 14 16" xmlns="http://www.w3.org/2000/svg"><path d="M8 2.5C8 1.94687 7.55312 1.5 7 1.5C6.44688 1.5 6 1.94687 6 2.5V7H1.5C0.946875 7 0.5 7.44688 0.5 8C0.5 8.55312 0.946875 9 1.5 9H6V13.5C6 14.0531 6.44688 14.5 7 14.5C7.55312 14.5 8 14.0531 8 13.5V9H12.5C13.0531 9 13.5 8.55312 13.5 8C13.5 7.44688 13.0531 7 12.5 7H8V2.5Z" fill="white"/></svg>`;
const searchIcon = `<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M13 6.5C13 7.93437 12.5344 9.25938 11.75 10.3344L15.7063 14.2937C16.0969 14.6844 16.0969 15.3188 15.7063 15.7094C15.3156 16.1 14.6812 16.1 14.2906 15.7094L10.3344 11.75C9.25938 12.5375 7.93437 13 6.5 13C2.90937 13 0 10.0906 0 6.5C0 2.90937 2.90937 0 6.5 0C10.0906 0 13 2.90937 13 6.5ZM6.5 11C8.52285 11 10.1349 9.38797 10.1349 7.36512C10.1349 5.34227 8.52285 3.73022 6.5 3.73022C4.47715 3.73022 2.86512 5.34227 2.86512 7.36512C2.86512 9.38797 4.47715 11 6.5 11Z" fill="#94A3B8"/></svg>`;
const editIcon = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 14 14" xmlns="http://www.w3.org/2000/svg"><path d="M12.8953 0.593164C12.2965 -0.00566408 11.3285 -0.00566408 10.7297 0.593164L9.90664 1.41348L12.5836 4.09043L13.4066 3.26738C14.0055 2.66855 14.0055 1.70059 13.4066 1.10176L12.8953 0.593164ZM4.71406 6.60879C4.54727 6.77559 4.41875 6.98066 4.34492 7.20762L3.53555 9.63574C3.45625 9.8709 3.51914 10.1307 3.69414 10.3084C3.86914 10.4861 4.12891 10.5463 4.3668 10.467L6.79492 9.65762C7.01914 9.58379 7.22422 9.45527 7.39375 9.28848L11.9684 4.71113L9.28867 2.03145L4.71406 6.60879ZM2.625 1.7498C1.17578 1.7498 0 2.92559 0 4.3748V11.3748C0 12.824 1.17578 13.9998 2.625 13.9998H9.625C11.0742 13.9998 12.25 12.824 12.25 11.3748V8.7498C12.25 8.26582 11.859 7.8748 11.375 7.8748C10.891 7.8748 10.5 8.26582 10.5 8.7498V11.3748C10.5 11.8588 10.109 12.2498 9.625 12.2498H2.625C2.14102 12.2498 1.75 11.8588 1.75 11.3748V4.3748C1.75 3.89082 2.14102 3.4998 2.625 3.4998H5.25C5.73398 3.4998 6.125 3.10879 6.125 2.6248C6.125 2.14082 5.73398 1.7498 5.25 1.7498H2.625Z" fill="#14B8A6"/></svg>`;
const deleteIcon = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 13 14" xmlns="http://www.w3.org/2000/svg"><path d="M3.69687 0.483984L3.5 0.875H0.875C0.391016 0.875 0 1.26602 0 1.75C0 2.23398 0.391016 2.625 0.875 2.625H11.375C11.859 2.625 12.25 2.23398 12.25 1.75C12.25 1.26602 11.859 0.875 11.375 0.875H8.75L8.55312 0.483984C8.40547 0.185938 8.10195 0 7.77109 0H4.47891C4.14805 0 3.84453 0.185938 3.69687 0.483984ZM11.375 3.5H0.875L1.45469 12.7695C1.49844 13.4613 2.07266 14 2.76445 14H9.48555C10.1773 14 10.7516 13.4613 10.7953 12.7695L11.375 3.5Z" fill="#DC2626"/></svg>`;
// --- Akhir SVG Icons ---

// Kirim request pencarian saat user mengetik, dengan delay (throttle)
watch(search, throttle(value => {
    router.get(route('satuans.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Fungsi untuk menghapus
const deleteSatuan = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        router.delete(route('satuans.destroy', id), {
            preserveScroll: true,
        });
    }
};

</script>

<template>
    <Head title="Master Data Satuan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Master Data Satuan</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="px-4 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-800">Master Data Satuan</h1>
                    <p class="mt-2 text-gray-600">Kelola data satuan untuk produk inventory</p>
                </div>

                <div class="flex justify-end mt-6 gap-x-3">
                    <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700">
                        <span v-html="importIcon"></span>
                        Import Excel
                    </button>
                    <Link :href="route('satuans.create')" class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700">
                        <span v-html="plusIcon"></span>
                        Tambah Satuan
                    </Link>
                </div>

                <div class="mt-4 overflow-hidden bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5">
                    
                    <div class="flex flex-col items-center justify-between p-4 border-b border-gray-200 md:flex-row">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Satuan</h3>
                        <div class="relative w-full mt-4 md:w-1/3 md:mt-0">
                            <span v-html="searchIcon" class="pointer-events-none"></span>
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Cari satuan..."
                                class="w-full py-2 pl-10 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
                            />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="w-16 px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">No</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Satuan</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Keterangan</th>
                                    <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="satuans.data.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        Tidak ada data untuk ditampilkan.
                                    </td>
                                </tr>
                                <tr v-for="(satuan, index) in satuans.data" :key="satuan.id">
                                    <td class="px-6 py-4 text-sm text-center text-gray-900">{{ satuans.from + index }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ satuan.nama_satuan }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ satuan.keterangan }}</td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <div class="flex justify-center gap-x-4">
                                            <Link :href="route('satuans.edit', satuan.id)" class="text-teal-600 hover:text-teal-900" v-html="editIcon"></Link>
                                            <button @click="deleteSatuan(satuan.id)" class="text-red-600 hover:text-red-900" v-html="deleteIcon"></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="satuans.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200">
                        <p class="text-sm text-gray-700">
                            Menampilkan <span>{{ satuans.from }}</span>-<span>{{ satuans.to }}</span> dari <span>{{ satuans.total }}</span> data
                        </p>
                        <div class="flex rounded-md shadow-sm">
                            <Link 
                                v-for="(link, index) in satuans.links" 
                                :key="index"
                                :href="link.url"
                                preserve-scroll
                                v-html="link.label"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border"
                                :class="{
                                    'bg-teal-600 border-teal-600 text-white': link.active,
                                    'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active && link.url,
                                    'bg-gray-100 border-gray-300 text-gray-400': !link.url,
                                    'rounded-l-md': index === 0,
                                    'rounded-r-md': index === satuans.links.length - 1
                                }"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>