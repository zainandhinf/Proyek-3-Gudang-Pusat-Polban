<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Props: Data kategori yang dikirim dari controller (method edit)
const props = defineProps({
    kategori: Object,
});

// Inisialisasi form dengan data yang sudah ada
const form = useForm({
    kode_kategori: props.kategori.kode_kategori,
    nama_kategori: props.kategori.nama_kategori,
    deskripsi: props.kategori.deskripsi,
});

// Submit Update
const submit = () => {
    form.put(route('kategori.update', props.kategori.id), {
        onSuccess: () => {
            // Redirect ditangani oleh controller, form tidak perlu di-reset
        },
    });
};
</script>

<template>
    <Head title="Edit Kategori" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Kategori</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Ubah Data Kategori</h3>
                            <p class="mt-1 text-sm text-gray-600">Silakan ubah informasi kategori di bawah ini.</p>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                
                                <div>
                                    <label for="kode_kategori" class="block text-sm font-medium text-gray-700">
                                        Kode Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="kode_kategori" 
                                        v-model="form.kode_kategori" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    >
                                    <div v-if="form.errors.kode_kategori" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.kode_kategori }}
                                    </div>
                                </div>

                                <div>
                                    <label for="nama_kategori" class="block text-sm font-medium text-gray-700">
                                        Nama Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="nama_kategori" 
                                        v-model="form.nama_kategori" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    >
                                    <div v-if="form.errors.nama_kategori" class="text-red-500 text-xs mt-1">
                                        {{ form.errors.nama_kategori }}
                                    </div>
                                </div>

                            </div>

                            <div class="mt-6">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">
                                    Deskripsi
                                </label>
                                <textarea 
                                    id="deskripsi" 
                                    v-model="form.deskripsi" 
                                    rows="4" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                    placeholder="Tambahkan deskripsi kategori..."
                                ></textarea>
                                <div v-if="form.errors.deskripsi" class="text-red-500 text-xs mt-1">
                                    {{ form.errors.deskripsi }}
                                </div>
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-x-3 pt-4 border-t border-gray-100">
                                <Link 
                                    :href="route('kategori.index')" 
                                    class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium"
                                >
                                    Batal
                                </Link>
                                <button 
                                    type="submit" 
                                    :disabled="form.processing" 
                                    class="flex items-center px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 text-sm font-medium shadow-sm disabled:opacity-50"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>