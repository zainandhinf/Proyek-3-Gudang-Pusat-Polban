<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { throttle } from 'lodash';
import { Search, Plus, Edit2, Trash2, Upload } from 'lucide-vue-next';

// Props
const props = defineProps({
    satuans: Object,
    filters: Object,
});

// User Role Logic
const page = usePage();
const user = computed(() => page.props.auth.user);
const isOperator = computed(() => user.value.role === 'operator');

// State search
const search = ref(props.filters?.search ?? '');

watch(search, throttle(value => {
    router.get(route('satuans.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

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
        <div class="py-8 px-4 md:px-6 lg:px-8">
            
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Master Data Satuan</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola data satuan untuk produk inventory</p>
            </div>

            <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ $page.props.flash.success }}
            </div>
            <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                {{ $page.props.flash.error }}
            </div>

            <div v-if="isOperator" class="flex justify-end mt-6 gap-x-3">
                <!-- <button class="flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 shadow-sm transition">
                    <Upload class="w-4 h-4 mr-2" />
                    Import Excel
                </button> -->
                <Link :href="route('satuans.create')" class="flex items-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm transition">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Satuan
                </Link>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden ring-1 ring-gray-900/5">
                
                <div class="p-6 border-b border-gray-200">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Daftar Satuan</h3>
                            <p class="text-sm text-gray-500 mt-1">List unit satuan barang</p>
                        </div>
                        
                        <div class="relative w-full md:w-72">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <Search class="w-5 h-5 text-gray-400" />
                            </div>
                            <input 
                                type="text" 
                                v-model="search"
                                placeholder="Cari satuan..."
                                class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500 shadow-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-16 px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">No</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Satuan</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Keterangan</th>
                                <th v-if="isOperator" scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="satuans.data.length === 0">
                                <td :colspan="isOperator ? 4 : 3" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada data untuk ditampilkan.
                                </td>
                            </tr>
                            <tr v-for="(satuan, index) in satuans.data" :key="satuan.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-center text-gray-900">{{ satuans.from + index }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ satuan.nama_satuan }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ satuan.keterangan || '-' }}</td>
                                <td v-if="isOperator" class="px-6 py-4 text-sm text-center">
                                    <div class="flex justify-center gap-2">
                                        <Link :href="route('satuans.edit', satuan.id)" class="p-1.5 text-teal-600 hover:text-teal-900 hover:bg-teal-50 rounded transition-colors" title="Edit">
                                            <Edit2 class="w-4 h-4" />
                                        </Link>
                                        <button @click="deleteSatuan(satuan.id)" class="p-1.5 text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="satuans.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200 bg-gray-50/50">
                    <p class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">{{ satuans.from ?? 0 }}</span>-<span class="font-medium">{{ satuans.to ?? 0 }}</span> dari <span class="font-medium">{{ satuans.total }}</span> data
                    </p>
                    <div class="flex gap-1">
                        <template v-for="(link, index) in satuans.links" :key="index">
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