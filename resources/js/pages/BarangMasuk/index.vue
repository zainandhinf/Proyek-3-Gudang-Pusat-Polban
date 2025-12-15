<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { throttle } from 'lodash';
import { Search, Upload, Plus, Edit2, Trash2 } from "lucide-vue-next";

// Props
const props = defineProps({
    barangs: Object,
    filters: Object,
});

// User Role Logic
const page = usePage();
const user = computed(() => page.props.auth.user);
const isOperator = computed(() => user.value.role === 'operator');

// State Pencarian
const search = ref(props.filters?.search ?? '');

// Helper Format Rupiah
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
};

// Logic Search
watch(search, throttle((value) => {
    router.get(route('barangs.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

// Logic Delete
const deleteBarang = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus data barang ini?')) {
        router.delete(route('barangs.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Master Data Barang" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Master Data Barang</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola semua data barang, stok, dan harga di gudang.</p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                {{ $page.props.flash.error }}
            </div>

            <div v-if="isOperator" class="flex justify-end mt-6 gap-x-3">
                <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition">
                    <Upload class="w-4 h-4 mr-2" />
                    Import Excel
                </button>
                
                <Link :href="route('barangs.create')" class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm transition">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Barang
                </Link>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Daftar Barang</h3>
                            <p class="text-sm text-gray-500 mt-1">List seluruh item inventory</p>
                        </div>
                        
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <Search class="w-5 h-5 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Cari kode atau nama barang..."
                                class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Info Barang</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Satuan</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Harga</th>
                                <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Stok</th>
                                <th v-if="isOperator" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="barangs.data.length === 0">
                                <td :colspan="isOperator ? 5 : 4" class="px-6 py-8 text-center text-gray-500">
                                    Data barang tidak ditemukan.
                                </td>
                            </tr>
                            
                            <tr v-for="barang in barangs.data" :key="barang.id" class="hover:bg-gray-50 transition-colors">
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img v-if="barang.foto" :src="`/storage/${barang.foto}`" class="h-10 w-10 rounded-md object-cover border" alt="Foto Barang">
                                            <div v-else class="h-10 w-10 rounded-md bg-gray-100 flex items-center justify-center text-gray-400 text-xs font-medium border border-gray-200">No Img</div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ barang.nama_barang }}</div>
                                            <div class="text-xs text-teal-600 font-mono bg-teal-50 px-1.5 py-0.5 rounded inline-block mt-1 border border-teal-100">{{ barang.kode_barang }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ barang.satuan?.nama_satuan || '-' }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                    {{ formatRupiah(barang.harga) }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <span class="font-bold px-2 py-1 rounded text-xs" :class="barang.stok_saat_ini > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                        {{ barang.stok_saat_ini }}
                                    </span>
                                </td>

                                <td v-if="isOperator" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <Link :href="route('barangs.edit', barang.id)" class="p-1.5 text-teal-600 hover:text-teal-800 hover:bg-teal-50 rounded transition-colors" title="Edit">
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        <button @click="deleteBarang(barang.id)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="barangs.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ barangs.from ?? 0 }}</span> sampai <span class="font-medium">{{ barangs.to ?? 0 }}</span> dari <span class="font-medium">{{ barangs.total }}</span> data
                    </div>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in barangs.links" :key="index">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                preserve-scroll
                                v-html="link.label"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border rounded-md transition-colors shadow-sm"
                                :class="{
                                    'bg-teal-600 border-teal-600 text-white hover:bg-teal-700': link.active,
                                    'bg-white border-gray-300 text-gray-700 hover:bg-gray-50': !link.active
                                }"
                            />
                            <span 
                                v-else
                                v-html="link.label"
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium border bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed rounded-md shadow-sm"
                            ></span>
                        </template>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>