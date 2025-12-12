<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    satuans: Array,
    kelompok_barangs: Array, // Data tabel statis
});

const form = useForm({
    kelompok_barang_id: '',
    no_urut: '',
    nama_barang: '',
    satuan_id: '',
    stok: 0,
    harga: 0,
    deskripsi: '',
    foto: null, // Untuk file upload
});

// Computed property untuk preview kode barang real-time
const previewKode = computed(() => {
    if (!form.kelompok_barang_id || !form.no_urut) return '---';
    
    const kelompok = props.kelompok_barangs.find(k => k.id === form.kelompok_barang_id);
    if (!kelompok) return '---';

    // Format gabungan: KODE_KELOMPOK + . + NO_URUT
    return `${kelompok.kode_kelompok}.${form.no_urut}`;
});

const submit = () => {
    form.post(route('barangs.store'), {
        forceFormData: true, // Wajib true untuk upload file
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Barang" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Barang</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                
                                <div class="col-span-1 md:col-span-2 bg-blue-50 p-4 rounded-lg border border-blue-100">
                                    <h4 class="text-blue-800 font-semibold mb-3">Buat Kode Barang</h4>
                                    <div class="flex gap-4">
                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">Kelompok Barang</label>
                                            <select v-model="form.kelompok_barang_id" class="mt-1 block w-full rounded-md border-gray-300">
                                                <option value="" disabled>-- Pilih Kelompok --</option>
                                                <option v-for="k in kelompok_barangs" :key="k.id" :value="k.id">
                                                    {{ k.nama_kelompok }} ({{ k.kode_kelompok }})
                                                </option>
                                            </select>
                                            <div v-if="form.errors.kelompok_barang_id" class="text-red-500 text-xs mt-1">{{ form.errors.kelompok_barang_id }}</div>
                                        </div>
                                        <div class="w-1/2">
                                            <label class="block text-sm font-medium text-gray-700">No. Urut (Input Manual)</label>
                                            <input type="text" v-model="form.no_urut" class="mt-1 block w-full rounded-md border-gray-300" placeholder="Contoh: 001">
                                            <div v-if="form.errors.no_urut" class="text-red-500 text-xs mt-1">{{ form.errors.no_urut }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-sm text-blue-800">
                                        Preview Kode Barang: <span class="font-bold text-lg bg-white px-2 py-1 rounded ml-2">{{ previewKode }}</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Barang</label>
                                    <input type="text" v-model="form.nama_barang" class="mt-1 block w-full rounded-md border-gray-300">
                                    <div v-if="form.errors.nama_barang" class="text-red-500 text-xs mt-1">{{ form.errors.nama_barang }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Stok Awal</label>
                                    <input type="number" v-model="form.stok" min="0" class="mt-1 block w-full rounded-md border-gray-300">
                                    <div v-if="form.errors.stok" class="text-red-500 text-xs mt-1">{{ form.errors.stok }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga Satuan (Rp)</label>
                                    <div class="relative mt-1 rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input 
                                            type="number" 
                                            v-model="form.harga" 
                                            min="0" 
                                            class="block w-full rounded-md border-gray-300 pl-10 focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                            placeholder="0"
                                        >
                                    </div>
                                    <div v-if="form.errors.harga" class="text-red-500 text-xs mt-1">{{ form.errors.harga }}</div>
                                </div>

                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select v-model="form.kategori_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="" disabled>-- Pilih Kategori --</option>
                                        <option v-for="kat in kategoris" :key="kat.id" :value="kat.id">{{ kat.nama_kategori }}</option>
                                    </select>
                                    <div v-if="form.errors.kategori_id" class="text-red-500 text-xs mt-1">{{ form.errors.kategori_id }}</div>
                                </div> -->

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                                    <select v-model="form.satuan_id" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="" disabled>-- Pilih Satuan --</option>
                                        <option v-for="sat in satuans" :key="sat.id" :value="sat.id">{{ sat.nama_satuan }}</option>
                                    </select>
                                    <div v-if="form.errors.satuan_id" class="text-red-500 text-xs mt-1">{{ form.errors.satuan_id }}</div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Foto Barang</label>
                                    <input 
                                        type="file" 
                                        @input="form.foto = $event.target.files[0]" 
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100"
                                        accept="image/*"
                                    >
                                    <progress v-if="form.progress" :value="form.progress.percentage" max="100">
                                        {{ form.progress.percentage }}%
                                    </progress>
                                    <div v-if="form.errors.foto" class="text-red-500 text-xs mt-1">{{ form.errors.foto }}</div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                    <textarea v-model="form.deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                                </div>

                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <Link :href="route('barangs.index')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">Simpan Barang</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>