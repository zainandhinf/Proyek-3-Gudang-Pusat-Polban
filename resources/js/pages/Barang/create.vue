<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    kategoris: Array, // Data untuk dropdown kategori
    satuans: Array,   // Data untuk dropdown satuan
});

const form = useForm({
    kode_barang: '',
    nama_barang: '',
    kategori_id: '',
    satuan_id: '',
    stok: 0,
    deskripsi: '',
});

const submit = () => {
    form.post(route('barangs.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Barang Baru" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Barang</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Barang</h3>
                            <p class="mt-1 text-sm text-gray-600">Lengkapi detail barang baru di bawah ini.</p>
                        </div>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                
                                <!-- Kode Barang -->
                                <div>
                                    <label for="kode_barang" class="block text-sm font-medium text-gray-700">Kode Barang <span class="text-red-500">*</span></label>
                                    <input type="text" id="kode_barang" v-model="form.kode_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Contoh: BRG-001">
                                    <div v-if="form.errors.kode_barang" class="text-red-500 text-xs mt-1">{{ form.errors.kode_barang }}</div>
                                </div>

                                <!-- Nama Barang -->
                                <div>
                                    <label for="nama_barang" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                                    <input type="text" id="nama_barang" v-model="form.nama_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Contoh: Laptop Asus ROG">
                                    <div v-if="form.errors.nama_barang" class="text-red-500 text-xs mt-1">{{ form.errors.nama_barang }}</div>
                                </div>

                                <!-- Kategori (Dropdown) -->
                                <div>
                                    <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                    <select id="kategori_id" v-model="form.kategori_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        <option value="" disabled>-- Pilih Kategori --</option>
                                        <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">{{ kat.nama_kategori }}</option>
                                    </select>
                                    <div v-if="form.errors.kategori_id" class="text-red-500 text-xs mt-1">{{ form.errors.kategori_id }}</div>
                                </div>

                                <!-- Satuan (Dropdown) -->
                                <div>
                                    <label for="satuan_id" class="block text-sm font-medium text-gray-700">Satuan <span class="text-red-500">*</span></label>
                                    <select id="satuan_id" v-model="form.satuan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        <option value="" disabled>-- Pilih Satuan --</option>
                                        <option v-for="sat in satuans" :key="sat.id" :value="sat.id">{{ sat.nama_satuan }}</option>
                                    </select>
                                    <div v-if="form.errors.satuan_id" class="text-red-500 text-xs mt-1">{{ form.errors.satuan_id }}</div>
                                </div>

                                <!-- Stok Awal -->
                                <!-- <div>
                                    <label for="stok" class="block text-sm font-medium text-gray-700">Stok Awal</label>
                                    <input type="number" id="stok" v-model="form.stok" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <div v-if="form.errors.stok" class="text-red-500 text-xs mt-1">{{ form.errors.stok }}</div>
                                </div> -->

                            </div>

                            <!-- Deskripsi -->
                            <div class="mt-6">
                                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea id="deskripsi" v-model="form.deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Tambahkan catatan tentang barang ini..."></textarea>
                            </div>

                            <!-- Actions -->
                            <div class="mt-6 flex items-center justify-end gap-x-3 border-t border-gray-100 pt-4">
                                <Link :href="route('barangs.index')" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium">
                                    Batal
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 text-sm font-medium shadow-sm disabled:opacity-50">
                                    Simpan Barang
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>