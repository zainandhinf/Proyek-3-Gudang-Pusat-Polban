<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({ opname: Object });
</script>

<template>
    <AuthenticatedLayout>
        <div class="px-6 py-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900">Detail Opname: {{ opname.no_opname }}</h1>
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-800 uppercase">Selesai</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        Tanggal: {{ new Date(opname.tanggal_opname).toLocaleString('id-ID') }} | 
                        Oleh: {{ opname.dicatat_oleh?.name }}
                    </p>
                </div>
                <Link :href="route('stock-opname.index')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                    Kembali
                </Link>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Barang</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase bg-gray-50">Stok Sistem</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase">Stok Fisik</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase">Selisih</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="item in opname.detail_stock_opnames" :key="item.id" class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ item.barang.nama_barang }}
                                <span class="text-xs text-gray-400 block">{{ item.barang.satuan?.nama_satuan }}</span>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-500 bg-gray-50">{{ item.stok_sistem }}</td>
                            <td class="px-6 py-3 text-center font-semibold">{{ item.stok_fisik }}</td>
                            <td class="px-6 py-3 text-center font-bold" 
                                :class="{'text-red-600': item.selisih < 0, 'text-green-600': item.selisih > 0, 'text-gray-400': item.selisih == 0}">
                                {{ item.selisih > 0 ? '+' : '' }}{{ item.selisih }}
                            </td>
                            <td class="px-6 py-3 text-gray-500 italic">{{ item.keterangan || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>