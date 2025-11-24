<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

// Props dari Controller (nantinya data ini dikirim dari Laravel)
const props = defineProps({
    permintaans: Array,
});

// State untuk filter pencarian
const search = ref('');

// State untuk Modal Preview
const showPreviewModal = ref(false);
const selectedFileUrl = ref('');
const selectedNoPermintaan = ref('');

// Fungsi helper untuk warna status badge
const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Disetujui': return 'bg-green-100 text-green-800';
        case 'Sedang Diproses': return 'bg-yellow-100 text-yellow-800';
        case 'Selesai': return 'bg-blue-100 text-blue-800';
        case 'Ditolak': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

// Fungsi untuk membuka modal
const openPreview = (permintaan) => {
    // Asumsi controller mengirim URL lengkap, atau kita rakit di sini
    // Pastikan Anda sudah menjalankan: php artisan storage:link
    selectedFileUrl.value = `/storage/${permintaan.file_path}`; 
    selectedNoPermintaan.value = permintaan.no_permintaan;
    showPreviewModal.value = true;
};

// Fungsi untuk menutup modal
const closePreview = () => {
    showPreviewModal.value = false;
    selectedFileUrl.value = '';
};

// Fungsi untuk reject
const handleReject = (id) => {
    const alasan = prompt('Masukkan alasan penolakan:');
    
    if (alasan) {
        console.log('Menolak permintaan ID:', id, 'Alasan:', alasan);
        router.post(route('permintaan.reject', id), { alasan: alasan });
    }
};

// Simulasi fungsi aksi
const handleProcess = (id) => {
    console.log('Memproses permintaan ID:', id);
    router.post(route('permintaan.proses', id));
};
</script>

<template>
    <Head title="Proses Permintaan" />

    <AuthenticatedLayout>
        <div class="w-full px-6 py-8">
            
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900">Proses Permintaan</h1>
                <p class="text-gray-500 mt-1">Kelola dan proses permintaan barang yang telah disetujui</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Permintaan</h2>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <!-- <select class="form-select border-gray-300 rounded-lg text-sm focus:border-teal-500 focus:ring-teal-500">
                            <option>Semua Unit</option>
                            <option>Teknik Informatika</option>
                            <option>Teknik Sipil</option>
                        </select> -->

                        <select class="form-select border-gray-300 rounded-lg text-sm focus:border-teal-500 focus:ring-teal-500">
                            <option>Semua Status</option>
                            <option>Pending</option>
                            <option>Processed</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                            <option>Selesai</option>
                        </select>

                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Cari permintaan..." 
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:border-teal-500 focus:ring-teal-500 w-full sm:w-64"
                            >
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Permintaan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pemohon</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Keperluan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(item, index) in props.permintaans" :key="item.id" class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">{{ item.no_permintaan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.pemohon.name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.jenis_keperluan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.tanggal_pengajuan }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span :class="['px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full', getStatusBadgeClass(item.status)]">
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <div class="flex justify-center items-center gap-3">
                                        <!-- <button class="text-cyan-500 hover:text-cyan-700 flex items-center gap-1" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            View
                                        </button> -->

                                        <button 
                                            @click="openPreview(item)"
                                            class="text-cyan-500 hover:text-cyan-700 flex items-center gap-1" 
                                            title="Lihat Surat"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Preview Surat
                                        </button>
                                        
                                        <a 
                                            v-if="item.status !== 'Selesai'" 
                                            :href="route('permintaan.proses', item.id)"
                                            class="text-blue-900 hover:text-blue-700 flex items-center gap-1 font-medium"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                            Proses
                                        </a>

                                        <span v-else class="text-gray-400 text-xs flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Selesai
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50 rounded-b-xl">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span class="font-medium">1</span>-<span class="font-medium">3</span> dari <span class="font-medium">9</span> data
                    </div>
                    <div class="flex gap-1">
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                            &lt;
                        </button>
                        <button class="px-3 py-1 border border-teal-500 bg-teal-50 text-teal-600 rounded-md text-sm font-medium">
                            1
                        </button>
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white text-gray-700 hover:bg-gray-50">
                            2
                        </button>
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white text-gray-700 hover:bg-gray-50">
                            3
                        </button>
                        <button class="px-3 py-1 border border-gray-300 rounded-md text-sm bg-white text-gray-500 hover:bg-gray-50">
                            &gt;
                        </button>
                    </div>
                </div>

            </div>
        </div>


        <div v-if="showPreviewModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm" @click="closePreview">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl h-[80vh] flex flex-col overflow-hidden" @click.stop>
                
                <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Preview Surat Pengajuan</h3>
                        <p class="text-sm text-gray-500">No: {{ selectedNoPermintaan }}</p>
                    </div>
                    <button @click="closePreview" class="text-gray-400 hover:text-gray-600 hover:bg-gray-200 p-2 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="flex-1 bg-gray-100 p-2 relative">
                    <iframe 
                        v-if="selectedFileUrl"
                        :src="selectedFileUrl" 
                        class="w-full h-full rounded border border-gray-300 bg-white"
                        frameborder="0"
                    >
                    </iframe>
                    <div v-else class="flex items-center justify-center h-full text-gray-500">
                        File tidak dapat dimuat atau tidak ditemukan.
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button @click="closePreview" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">
                        Tutup
                    </button>
                    <a :href="selectedFileUrl" target="_blank" download class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Download File
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>