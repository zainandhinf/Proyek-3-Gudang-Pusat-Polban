<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
// import Chart from 'chart.js/auto';

const props = defineProps({
    // Data Operator
    stats_operator: Object,
    permintaan_terbaru: Array,
    aktivitas: Array,
    
    // Data Pemohon
    barangs: Array,
    riwayats: Array,
    stats_pemohon: Object
});

const user = usePage().props.auth.user;

// Helper Status Warna
const getStatusClass = (status) => {
    const map = {
        'Approved': 'bg-green-100 text-green-800',
        'Pending': 'bg-yellow-100 text-yellow-800',
        'Rejected': 'bg-red-100 text-red-800',
        'Processed': 'bg-blue-100 text-blue-800',
        'Selesai': 'bg-gray-100 text-gray-800'
    };
    return map[status] || 'bg-gray-100 text-gray-600';
};

// Inisialisasi Chart hanya jika user adalah Operator
onMounted(() => {
    if (user.role === 'operator' || user.role === 'admin') {
        const ctx = document.getElementById('chartPermintaan');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    datasets: [
                        {
                            label: 'Permintaan Masuk',
                            data: [12, 19, 3, 5, 2, 3],
                            backgroundColor: '#0d9488', // teal-600
                            borderRadius: 4
                        },
                        {
                            label: 'Barang Keluar',
                            data: [8, 15, 2, 4, 1, 2],
                            backgroundColor: '#94a3b8', // slate-400
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard {{ user.role === 'operator' ? 'Operator' : 'Pemohon' }}
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                
                <div v-if="user.role === 'operator'" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Barang</p>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ stats_operator.total_barang }}</h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Permintaan Pending</p>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ stats_operator.permintaan_pending }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-teal-50 text-teal-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Barang Keluar (Bulan Ini)</p>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ stats_operator.barang_keluar }}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Stok Menipis</p>
                                    <h3 class="text-2xl font-bold text-slate-800">{{ stats_operator.stok_menipis }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-slate-800">Statistik Permintaan & Pengeluaran</h3>
                                <select class="text-sm border-gray-300 rounded-lg text-gray-500">
                                    <option>6 Bulan Terakhir</option>
                                    <option>Tahun Ini</option>
                                </select>
                            </div>
                            <div class="h-64 w-full">
                                <canvas id="chartPermintaan"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-4">Aktivitas Terkini</h3>
                            <div class="space-y-4">
                                <div v-for="(log, idx) in aktivitas" :key="idx" class="flex gap-4 p-3 bg-gray-50 rounded-lg items-start">
                                    <div 
                                        class="w-8 h-8 rounded-full flex items-center justify-center shrink-0"
                                        :class="{
                                            'bg-green-100 text-green-600': log.tipe === 'masuk',
                                            'bg-blue-100 text-blue-600': log.tipe === 'approve',
                                            'bg-orange-100 text-orange-600': log.tipe === 'warning'
                                        }"
                                    >
                                        <span class="text-xs font-bold">•</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800 text-sm">{{ log.judul }}</div>
                                        <div class="text-xs text-gray-500">{{ log.desc }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1">{{ log.waktu }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-slate-800">Permintaan Masuk (Perlu Proses)</h3>
                            <Link :href="route('permintaan.index')" class="text-sm text-teal-600 hover:underline">Lihat Semua</Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3">Pemohon</th>
                                        <th class="px-6 py-3">Unit Kerja</th>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!permintaan_terbaru || permintaan_terbaru.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center">Tidak ada permintaan baru.</td>
                                    </tr>
                                    <tr v-for="item in permintaan_terbaru" :key="item.id" class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ item.pemohon.name }}
                                        </td>
                                        <td class="px-6 py-4">{{ item.jenis_keperluan }}</td>
                                        <td class="px-6 py-4">{{ new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pending</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <Link :href="route('permintaan.proses', item.id)" class="text-white bg-teal-600 hover:bg-teal-700 font-medium rounded-lg text-xs px-3 py-1.5">
                                                Proses
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <div v-else class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-1 bg-gradient-to-br from-teal-600 to-teal-800 rounded-2xl p-6 text-white shadow-lg flex flex-col justify-between">
                            <div>
                                <p class="text-teal-100 text-sm">Selamat Datang,</p>
                                <h3 class="text-2xl font-bold mt-1">{{ user.name }}</h3>
                            </div>
                            <div class="mt-6">
                                <Link :href="route('permintaan.create')" class="inline-block w-full text-center px-4 py-2 bg-white text-teal-700 font-semibold rounded-lg hover:bg-teal-50 transition">
                                    + Buat Permintaan
                                </Link>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center items-center">
                            <span class="text-3xl font-bold text-gray-800">{{ stats_pemohon.total }}</span>
                            <span class="text-sm text-gray-500 mt-1">Total Pengajuan</span>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center items-center">
                            <span class="text-3xl font-bold text-yellow-600">{{ stats_pemohon.proses }}</span>
                            <span class="text-sm text-gray-500 mt-1">Sedang Diproses</span>
                        </div>
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-center items-center">
                            <span class="text-3xl font-bold text-green-600">{{ stats_pemohon.disetujui }}</span>
                            <span class="text-sm text-gray-500 mt-1">Disetujui</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Katalog Barang Tersedia</h3>
                            <span class="text-sm text-gray-500">Barang terbaru</span>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div v-for="barang in barangs" :key="barang.id" class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-4 flex flex-col items-center text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 text-sm line-clamp-1">{{ barang.nama_barang }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ barang.kategori?.nama_kategori }}</p>
                                <div class="mt-3 text-xs font-medium px-2 py-1 rounded-full bg-gray-100 text-green-600">
                                    {{ barang.satuan.nama_satuan }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800">Riwayat Pengajuan Saya</h3>
                        </div>
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3">No. Surat</th>
                                    <th class="px-6 py-3">Tanggal</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in riwayats" :key="item.id" class="bg-white border-b">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ item.no_permintaan }}</td>
                                    <td class="px-6 py-4">{{ new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="getStatusClass(item.status)">
                                            {{ item.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <Link v-if="item.file_path" :href="`/storage/${item.file_path}`" target="_blank" class="text-blue-600 hover:underline">
                                            Lihat
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>