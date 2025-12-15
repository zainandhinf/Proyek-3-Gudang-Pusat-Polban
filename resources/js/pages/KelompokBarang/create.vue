<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    kode_kelompok: '',
    nama_kelompok: '',
});

const submit = () => {
    form.post(route('kelompok-barang.store'));
};
</script>

<template>
    <Head title="Tambah Kelompok" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Kelompok Barang</h2>
        </template>

        <div class="py-8 px-4 md:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Kelompok Barang</h1>
                    <p class="mt-1 text-sm text-gray-600">Buat kategori baru untuk pengelompokan barang.</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="space-y-6">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Kelompok <span class="text-red-500">*</span></label>
                                    <input 
                                        v-model="form.kode_kelompok" 
                                        type="text" 
                                        placeholder="Contoh: ATK" 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500 uppercase"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Maksimal 10 karakter. Akan menjadi awalan kode barang.</p>
                                    <div v-if="form.errors.kode_kelompok" class="text-red-500 text-xs mt-1">{{ form.errors.kode_kelompok }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelompok <span class="text-red-500">*</span></label>
                                    <input 
                                        v-model="form.nama_kelompok" 
                                        type="text" 
                                        placeholder="Contoh: Alat Tulis Kantor" 
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                    >
                                    <div v-if="form.errors.nama_kelompok" class="text-red-500 text-xs mt-1">{{ form.errors.nama_kelompok }}</div>
                                </div>

                            </div>

                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                                <Link 
                                    :href="route('kelompok-barang.index')" 
                                    class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm transition"
                                >
                                    Batal
                                </Link>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing" 
                                    class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm transition disabled:opacity-50"
                                >
                                    Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>