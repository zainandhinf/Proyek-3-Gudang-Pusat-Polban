<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { throttle } from 'lodash';

const props = defineProps({
    kartuStok: Object,
    filters: Object,
    years: Array,
});

const search = ref(props.filters.search || '');
const selectedYear = ref(props.filters.year);

// Nama Bulan Singkat
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

// Watcher untuk Search & Filter Tahun
watch([search, selectedYear], throttle(([newSearch, newYear]) => {
    router.get(route('laporan.kartu-stok'), {
        search: newSearch,
        year: newYear
    }, { preserveState: true, replace: true });
}, 500));

// --- HELPER FUNCTIONS ---

const parseKode = (kode) => {
    if (!kode || kode.length < 10) return { kel: '-', urut: '-' };
    return {
        kel: kode.slice(0, 10),
        urut: kode.slice(10)
    };
};

const getTotalPemakaian = (mutasi) => {
    if (!mutasi) return 0;
    return Math.abs(Object.values(mutasi).reduce((acc, val) => val < 0 ? acc + val : acc, 0));
};

const getMutationClass = (val) => {
    // Mode Terang & Gelap untuk Angka Mutasi
    if (val > 0) return 'text-green-600 dark:text-green-400 font-bold'; 
    if (val < 0) return 'text-red-600 dark:text-red-400 font-bold';   
    return 'text-gray-300 dark:text-gray-600';
};

/* --- STYLE DEFINITIONS (DIPERBAIKI) --- */

// 1. Base Class untuk Header (Tanpa Background Color agar tidak tabrakan)
// Menggunakan border-gray-300 (lebih halus) untuk Light Mode
const thBase = "px-2 py-2 text-[10px] font-bold uppercase tracking-wider text-center border whitespace-nowrap " +
               "text-gray-700 border-gray-300 " +  // Light Mode: Teks Abu Gelap, Border Halus
               "dark:text-gray-200 dark:bg-gray-700 dark:border-gray-600"; // Dark Mode

// 2. Class Khusus Header Standar (Pakai Background Abu)
const thStandard = thBase + " bg-gray-100 dark:bg-gray-700";

// 3. Class untuk Body Tabel
const tdBase = "px-2 py-1 text-[11px] text-center border whitespace-nowrap " +
               "text-gray-800 border-gray-300 " + // Light Mode: Text Hitam Soft, Border Halus
               "dark:text-gray-300 dark:border-gray-600"; // Dark Mode

</script>

<style>
/* CSS Khusus Print: Paksa Landscape, Font Kecil, & Warna Putih/Hitam */
@media print {
    @page {
        size: landscape;
        margin: 5mm;
    }
    body {
        -webkit-print-color-adjust: exact;
        font-size: 10px;
        background-color: white !important;
        color: black !important;
    }
    .print-hide {
        display: none !important;
    }
    .overflow-x-auto {
        overflow: visible !important;
    }
    /* Reset warna background saat print agar hemat tinta & bersih */
    .print-bg-white {
        background-color: white !important;
        color: black !important;
    }
    .print-border-black {
        border-color: black !important;
    }
    /* Warna header saat print (tetap berwarna tipis agar terbaca bedanya) */
    .print-bg-gray { background-color: #f3f4f6 !important; } /* Gray-100 */
    .print-bg-blue { background-color: #eff6ff !important; } /* Blue-50 */
    .print-bg-yellow { background-color: #fefce8 !important; } /* Yellow-50 */
    .print-bg-red { background-color: #fef2f2 !important; } /* Red-50 */
}
</style>

<template>
    <Head title="Kartu Pengawasan Stok" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Laporan Kartu Stok
            </h2>
        </template>

        <div class="py-6">
            <div class="mx-auto w-full px-2 sm:px-4">
                
                <div class="flex flex-col md:flex-row justify-end items-center mb-4 gap-3 print-hide">
                    <select v-model="selectedYear" class="rounded-md text-sm shadow-sm focus:ring-teal-500 focus:border-teal-500 
                                                          border-gray-300 bg-white text-gray-900 
                                                          dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>

                    <input type="text" v-model="search" placeholder="Cari Kode/Nama..." 
                           class="rounded-md text-sm w-full md:w-64 shadow-sm focus:ring-teal-500 focus:border-teal-500 
                                  border-gray-300 bg-white text-gray-900 
                                  dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100" />
                    
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-700 text-white rounded-md text-sm hover:bg-gray-800 
                                                            dark:bg-gray-600 dark:hover:bg-gray-500 flex items-center gap-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Print Landscape
                    </button>
                </div>

                <div class="p-4 shadow-lg ring-1 min-h-[29.7cm] 
                            bg-white text-black ring-gray-900/5 
                            dark:bg-gray-800 dark:text-gray-100 dark:ring-white/10 
                            print-bg-white">
                    
                    <div class="text-left mb-6">
                        <h1 class="text-xl font-bold underline decoration-2 underline-offset-4 uppercase 
                                   text-gray-900 dark:text-white print:text-black">
                            KARTU PENGAWASAN (STOCK) BARANG
                        </h1>
                        <p class="mt-1 text-xs font-medium tracking-widest uppercase 
                                  text-gray-600 dark:text-gray-400 print:text-gray-600">
                            TAHUN ANGGARAN {{ selectedYear }}
                        </p>
                    </div>

                    <div class="overflow-x-auto pb-4">
                        <table class="min-w-max border-collapse border 
                                      border-gray-300 dark:border-gray-600 print-border-black">
                            <thead class="print:bg-gray-200">
                                <tr>
                                    <th :class="thStandard" class="min-w-[40px] print-bg-gray">No.</th>
                                    <th :class="thStandard" class="min-w-[100px] print-bg-gray">Kel.<br>Barang</th>
                                    <th :class="thStandard" class="min-w-[80px] print-bg-gray">No.<br>Urut</th>
                                    <th :class="thStandard" class="min-w-[140px] print-bg-gray">Kode Barang</th>
                                    <th :class="thStandard" class="min-w-[250px] text-left px-2 print-bg-gray">Nama Barang / Spesifikasi</th>
                                    <th :class="thStandard" class="min-w-[60px] print-bg-gray">Satuan</th>
                                    
                                    <th :class="thBase" class="min-w-[70px] bg-blue-100 dark:bg-blue-900/40 print-bg-blue">Saldo<br>Awal</th>
                                    
                                    <th v-for="(m, i) in months" :key="i" :class="thStandard" class="min-w-[45px] print-bg-gray">
                                        {{ m }}
                                    </th>
                                    
                                    <th :class="thBase" class="min-w-[70px] bg-yellow-100 dark:bg-yellow-900/40 print-bg-yellow">Saldo<br>Akhir</th>
                                    
                                    <th :class="thBase" class="min-w-[80px] bg-red-50 dark:bg-red-900/30 print-bg-red">Total<br>Pemakaian</th>
                                    
                                    <th :class="thStandard" class="min-w-[150px] print-bg-gray">Ket</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in kartuStok.data" :key="item.id" 
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50 print:break-inside-avoid">
                                    
                                    <td :class="tdBase">{{ index + 1 + (kartuStok.from ? kartuStok.from - 1 : 0) }}</td>
                                    <td :class="tdBase">{{ parseKode(item.kode_barang).kel }}</td>
                                    <td :class="tdBase">{{ parseKode(item.kode_barang).urut }}</td>
                                    <td :class="tdBase" class="font-mono text-[10px]">{{ item.kode_barang }}</td>
                                    
                                    <td :class="tdBase" class="text-left px-2 font-medium whitespace-normal">
                                        {{ item.nama_barang }}
                                    </td>
                                    
                                    <td :class="tdBase">{{ item.satuan }}</td>
                                    
                                    <td :class="tdBase" class="font-semibold bg-blue-50 dark:bg-blue-900/20 print-bg-blue">
                                        {{ item.saldo_awal }}
                                    </td>
                                    
                                    <td v-for="monthIdx in 12" :key="monthIdx" :class="tdBase">
                                        <span v-if="item.mutasi[monthIdx] && item.mutasi[monthIdx] !== 0" 
                                              :class="getMutationClass(item.mutasi[monthIdx])">
                                            {{ Math.abs(item.mutasi[monthIdx]) }}
                                        </span>
                                        <span v-else class="text-gray-300 dark:text-gray-600 text-[9px]">-</span>
                                    </td>

                                    <td :class="tdBase" class="font-bold bg-yellow-50 dark:bg-yellow-900/20 print-bg-yellow">
                                        {{ item.saldo_akhir }}
                                    </td>

                                    <td :class="tdBase" class="font-bold text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 print-bg-red">
                                        {{ getTotalPemakaian(item.mutasi) }}
                                    </td>

                                    <td :class="tdBase" class="italic text-[10px] whitespace-normal text-left px-2 text-gray-500 dark:text-gray-400">
                                        {{ item.keterangan || '' }}
                                    </td>
                                </tr>

                                <tr v-if="kartuStok.data.length === 0">
                                    <td colspan="22" class="px-6 py-8 text-center text-sm border 
                                                            border-gray-300 dark:border-gray-600 
                                                            text-gray-500 dark:text-gray-400">
                                        Tidak ada data stok untuk ditampilkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 border-t pt-4 flex items-center justify-between print-hide 
                                border-gray-200 dark:border-gray-700">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Halaman {{ kartuStok.current_page }} dari {{ kartuStok.last_page }} (Total {{ kartuStok.total }} Barang)
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, k) in kartuStok.links" :key="k">
                                <Link v-if="link.url" :href="link.url" v-html="link.label" 
                                      class="px-2 py-1 text-xs border rounded 
                                             bg-white text-gray-700 hover:bg-gray-100 
                                             dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700" 
                                      :class="{'bg-teal-600 text-white border-teal-600 hover:bg-teal-700 dark:bg-teal-600 dark:text-white': link.active}" />
                                <span v-else v-html="link.label" 
                                      class="px-2 py-1 text-xs border rounded cursor-not-allowed
                                             bg-gray-100 text-gray-400 
                                             dark:bg-gray-900 dark:text-gray-600 dark:border-gray-700"></span>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>