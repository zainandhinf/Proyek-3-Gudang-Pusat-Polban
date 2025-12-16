<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

// Setup Form menggunakan Inertia useForm
const form = useForm({
    no_permintaan: '',
    jenis_keperluan: '',
    file_surat: null,
});

const tanggalHariIni = new Date().toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
});

// Data Dummy untuk Dropdown (Nantinya bisa dari props backend)
const jenisKeperluanList = ['Jurusan', 'Program_Studi', 'Laboratorium', 'Bengkel', 'Bagian', 'Sub_Bagian', 'Unit', 'Urusan'];

const submit = () => {
    form.post(route('permintaan.store'), {
        onSuccess: () => form.reset(),
    });
};

const handleFileUpload = (e) => {
    form.file_surat = e.target.files[0];
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="w-full px-6 py-6">
            <div class="mb-8 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-blue-900">Form Pengajuan Kebutuhan Barang</h2>
                    <p class="text-gray-500 mt-1">Kepada Yth: ..., Politeknik Negeri Bandung</p>
                </div>
                <div class="text-right w-full md:w-72">
                    <p class="text-sm text-gray-500">Nomor Form:</p>
                    <input
                        type="text"
                        v-model="form.no_permintaan"
                        placeholder="Contoh: 384/R11.1/RT.10.00/2025"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                    />
                    <p class="text-sm text-gray-500 mt-1">Tanggal: {{ tanggalHariIni }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <div class="bg-teal-50 p-2 rounded text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-900">Informasi Pengajuan</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Keperluan</label>
                        <select v-model="form.jenis_keperluan" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-teal-500 focus:ring-teal-500">
                            <option value="" disabled>Pilih Jenis Keperluan</option>
                            <option v-for="item in jenisKeperluanList" :key="item">{{ item }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                    <div class="bg-teal-50 p-2 rounded text-teal-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-blue-900">Upload Surat Pengajuan</h3>
                </div>

                <div class="border-2 border-dashed border-teal-200 rounded-xl p-8 text-center hover:bg-teal-50 transition duration-150 ease-in-out relative">
                    <!-- <input type="file" @change="handleFileUpload"> -->
                    <input type="file" @input="form.file_surat = $event.target.files[0]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".pdf,.doc,.docx">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-10 h-10 text-teal-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="text-teal-600 font-medium text-lg">Upload File</p>
                        <p class="text-sm text-gray-400 mt-1">Format: DOC, PDF, maksimal 2MB</p>
                        <p v-if="form.file_surat" class="mt-2 text-sm text-gray-900 font-semibold">
                            File terpilih: {{ form.file_surat.name }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-start gap-4">
                <button 
                    @click="submit"
                    :disabled="form.processing"
                    class="bg-teal-600 hover:bg-teal-700 text-white font-medium py-2.5 px-6 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition duration-150 disabled:opacity-50"
                >
                    Kirim Permintaan
                </button>
                <button 
                    type="button"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-2.5 px-6 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-150"
                >
                    Batal
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
