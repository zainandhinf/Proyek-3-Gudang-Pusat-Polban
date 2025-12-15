<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nama_satuan: '',
    keterangan: '',
});

const submitForm = () => {
    form.post(route('satuans.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Data Satuan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Data Satuan</h2>
        </template>

        <div class="py-8 px-4 md:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Data Satuan</h1>
                    <p class="mt-1 text-sm text-gray-600">Masukkan informasi unit satuan baru.</p>
                </div>

                <div class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
                    <div class="p-6">
                        <form @submit.prevent="submitForm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <div class="col-span-1">
                                    <label for="nama_satuan" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nama_satuan"
                                        v-model="form.nama_satuan"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                        placeholder="Contoh: Pcs"
                                    />
                                    <div v-if="form.errors.nama_satuan" class="mt-1 text-xs text-red-600">
                                        {{ form.errors.nama_satuan }}
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">
                                        Keterangan
                                    </label>
                                    <input 
                                        type="text" 
                                        id="keterangan"
                                        v-model="form.keterangan"
                                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-teal-500 focus:border-teal-500"
                                        placeholder="Opsional"
                                    />
                                    <div v-if="form.errors.keterangan" class="mt-1 text-xs text-red-600">
                                        {{ form.errors.keterangan }}
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                                <Link :href="route('satuans.index')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm transition">
                                    Kembali
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm transition disabled:opacity-50">
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