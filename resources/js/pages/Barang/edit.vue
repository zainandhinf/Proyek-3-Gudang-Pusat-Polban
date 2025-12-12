<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    barang: Object,
    satuans: Array,
});

const form = useForm({
    _method: 'PUT', // Penting untuk update file di Inertia/Laravel
    nama_barang: props.barang.nama_barang,
    satuan_id: props.barang.satuan_id,
    stok: props.barang.stok_saat_ini,
    harga: props.barang.harga,
    deskripsi: props.barang.deskripsi,
    foto: null, 
});

const submit = () => {
    // Gunakan POST dengan _method: PUT untuk file upload
    form.post(route('barangs.update', props.barang.id), {
        forceFormData: true, // Pastikan dikirim sebagai FormData
    });
};
</script>

<template>
    <Head title="Edit Barang" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Barang</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kode Barang</label>
                                    <input type="text" :value="barang.kode_barang" disabled class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 cursor-not-allowed">
                                    <p class="text-xs text-gray-500 mt-1">Kode barang tidak dapat diubah.</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                    <input type="text" v-model="form.nama_barang" class="mt-1 block w-full rounded-md border-gray-300">
                                </div>

                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select v-model="form.kategori_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">{{ kat.nama_kategori }}</option>
                                    </select>
                                </div> -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                    <select v-model="form.satuan_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option v-for="sat in satuans" :key="sat.id" :value="sat.id">{{ sat.nama_satuan }}</option>
                                    </select>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Barang</label>
                                    
                                    <div v-if="barang.foto" class="mb-3">
                                        <img :src="`/storage/${barang.foto}`" alt="Foto Lama" class="h-24 w-24 object-cover rounded border">
                                        <p class="text-xs text-gray-500">Foto saat ini</p>
                                    </div>

                                    <input type="file" @input="form.foto = $event.target.files[0]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <textarea v-model="form.deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <Link :href="route('barangs.index')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">Update Barang</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>