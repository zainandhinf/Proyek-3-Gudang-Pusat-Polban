<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    barang: Object,
    kategoris: Array,
    satuans: Array,
});

const form = useForm({
    kode_barang: props.barang.kode_barang,
    nama_barang: props.barang.nama_barang,
    kategori_id: props.barang.kategori_id,
    satuan_id: props.barang.satuan_id,
    stok: props.barang.stok,
    deskripsi: props.barang.deskripsi,
});

const submit = () => {
    form.put(route('barangs.update', props.barang.id));
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
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Update Data Barang: {{ barang.nama_barang }}</h3>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Kode Barang -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kode Barang</label>
                                    <input type="text" v-model="form.kode_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <div v-if="form.errors.kode_barang" class="text-red-500 text-xs mt-1">{{ form.errors.kode_barang }}</div>
                                </div>

                                <!-- Nama Barang -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                    <input type="text" v-model="form.nama_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <div v-if="form.errors.nama_barang" class="text-red-500 text-xs mt-1">{{ form.errors.nama_barang }}</div>
                                </div>

                                <!-- Kategori -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select v-model="form.kategori_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">{{ kat.nama_kategori }}</option>
                                    </select>
                                </div>

                                <!-- Satuan -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                    <select v-model="form.satuan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        <option v-for="sat in satuans" :key="sat.id" :value="sat.id">{{ sat.nama_satuan }}</option>
                                    </select>
                                </div>

                                <!-- Stok -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stok</label>
                                    <input type="number" v-model="form.stok" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea v-model="form.deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm"></textarea>
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <Link :href="route('barangs.index')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</Link>
                                <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>