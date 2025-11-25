<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { throttle } from 'lodash';

const props = defineProps({
    barangUsangs: Object,
    filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const isOperator = computed(() => user.value.role === 'operator');

const search = ref(props.filters?.search ?? '');

// SVG Icons
const plusIcon = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>`;
const searchIcon = `<svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>`;
const editIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>`;
const deleteIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>`;

watch(search, throttle((value) => {
    router.get(route('barang-usang.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const deleteItem = (id) => {
    if (confirm('Hapus laporan barang usang ini?')) {
        router.delete(route('barang-usang.destroy', id));
    }
};
</script>

<template>
    <Head title="Barang Usang/Rusak" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Barang Usang/Rusak</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="px-4 sm:px-0">
                    <h1 class="text-3xl font-bold text-gray-800">Barang Usang/Rusak</h1>
                    <p class="mt-2 text-gray-600">Kelola pelaporan barang yang rusak atau sudah usang.</p>
                </div>

                <div v-if="$page.props.flash?.success" class="mt-4 mx-4 sm:mx-0 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ $page.props.flash?.success }}
                </div>

                <div class="flex justify-end mt-6 px-4 sm:px-0">
                    <!-- Tombol Tambah: HANYA OPERATOR -->
                    <Link 
                        v-if="isOperator" 
                        :href="route('barang-usang.create')" 
                        class="flex items-center justify-center px-4 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 shadow-sm"
                    >
                        <span v-html="plusIcon"></span> Lapor Barang Rusak
                    </Link>
                </div>

                <div class="mt-6 bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden mx-4 sm:mx-0">
                    
                    <div class="flex items-center justify-between p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Laporan</h3>
                        <div class="relative w-full md:w-72">
                            <span v-html="searchIcon" class="pointer-events-none"></span>
                            <input type="text" v-model="search" placeholder="Cari barang..." class="w-full py-2 pl-10 text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" />
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Status</th>
                                    <!-- Aksi: Hanya Operator -->
                                    <th v-if="isOperator" class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="barangUsangs.data.length === 0">
                                    <td :colspan="isOperator ? 6 : 5" class="px-6 py-8 text-center text-gray-500">Tidak ada data laporan.</td>
                                </tr>
                                <tr v-for="(item, index) in barangUsangs.data" :key="item.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.tanggal_laporan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ item.barang?.nama_barang || 'Barang Terhapus' }}
                                        <div class="text-xs text-gray-400">{{ item.barang?.kode_barang }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-red-600">{{ item.jumlah }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ item.keterangan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': item.status === 'Pending',
                                                'bg-green-100 text-green-800': item.status === 'Disetujui',
                                                'bg-red-100 text-red-800': item.status === 'Ditolak'
                                            }">
                                            {{ item.status }}
                                        </span>
                                    </td>
                                    
                                    <!-- Aksi: Hanya Operator -->
                                    <td v-if="isOperator" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center gap-3">
                                            <Link :href="route('barang-usang.edit', item.id)" class="text-teal-600 hover:text-teal-900 p-1 rounded hover:bg-teal-50"><span v-html="editIcon"></span></Link>
                                            <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50"><span v-html="deleteIcon"></span></button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="barangUsangs.data.length > 0" class="flex items-center justify-between p-4 border-t border-gray-200">
                        <div class="text-sm text-gray-700">
                            Menampilkan <span class="font-medium">{{ barangUsangs.from ?? 0 }}</span> sampai <span class="font-medium">{{ barangUsangs.to ?? 0 }}</span> dari <span class="font-medium">{{ barangUsangs.total }}</span> hasil
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, k) in barangUsangs.links" :key="k">
                                <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-3 py-1 text-sm border rounded-md bg-white text-gray-700 border-gray-300 hover:bg-gray-50" :class="{ 'bg-teal-600 text-white border-teal-600': link.active }" preserve-scroll />
                                <span v-else v-html="link.label" class="px-3 py-1 text-sm border rounded-md bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed"></span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>