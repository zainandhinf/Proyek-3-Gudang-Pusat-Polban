<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Menerima data 'satuan' yang akan diedit dari controller
const props = defineProps({
    satuan: Object
});

// Isi form dengan data yang ada
const form = useForm({
    nama_satuan: props.satuan.nama_satuan,
    keterangan: props.satuan.keterangan,
});

// Fungsi untuk update
const updateForm = () => {
    form.put(route('satuans.update', props.satuan.id), {
        onSuccess: () => {
            alert('Satuan berhasil diperbarui!');
        },
        onError: (errors) => {
            console.error('Error validasi:', errors);
        }
    });
};
</script>

<template>
    <Head title="Edit Data Satuan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Data Satuan</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <h1 class="text-3xl font-bold text-gray-800">Edit Data Satuan</h1>
                <p class="mt-2 text-gray-600">Ubah informasi satuan: {{ satuan.nama_satuan }}</p>

                <form @submit.prevent="updateForm" class="p-8 mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        
                        <div>
                            <label for="nama_satuan" class="block text-sm font-medium text-gray-700">
                                Nama Satuan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nama_satuan"
                                v-model="form.nama_satuan"
                                class="w-full p-3 mt-1 border border-gray-300 rounded-md shadow-sm"
                                placeholder="Contoh: Pcs, Unit, Box"
                            />
                            <div v-if="form.errors.nama_satuan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama_satuan }}
                            </div>
                        </div>

                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">
                                Keterangan
                            </label>
                            <input 
                                type="text" 
                                id="keterangan"
                                v-model="form.keterangan"
                                class="w-full p-3 mt-1 border border-gray-300 rounded-md shadow-sm"
                                placeholder="Keterangan tambahan (opsional)"
                            />
                            <div v-if="form.errors.keterangan" class="mt-1 text-sm text-red-600">
                                {{ form.errors.keterangan }}
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 mt-8 border-t border-gray-200 gap-x-3">
                        <Link :href="route('satuans.index')" class="px-5 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Kembali
                        </Link>
                        <button type="submit" :disabled="form.processing" class="flex items-center px-5 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700 disabled:opacity-50">
                            Update Data
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>