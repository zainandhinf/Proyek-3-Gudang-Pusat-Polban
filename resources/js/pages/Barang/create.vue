<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    satuans: Array,
    kelompok_barangs: Array,
});

const form = useForm({
    kelompok_barang_id: '',
    no_urut: '',
    nama_barang: '',
    satuan_id: '',
    stok: 0,
    harga: 0,
    deskripsi: '',
    foto: null,
});

// Computed preview kode barang
const previewKode = computed(() => {
    if (!form.kelompok_barang_id || !form.no_urut) return '---';
    const kelompok = props.kelompok_barangs.find(k => k.id === form.kelompok_barang_id);
    if (!kelompok) return '---';
    return `${kelompok.kode_kelompok}.${form.no_urut}`;
});

const submit = () => {
    form.post(route('barangs.store'), {
        forceFormData: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Tambah Barang" />

    <AuthenticatedLayout>
        <div class="py-8 px-4 md:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                
                <div class="mb-6 flex items-center gap-2 text-sm text-gray-500">
                    <Link :href="route('barangs.index')" class="hover:text-teal-600 transition">Master Barang</Link>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">Tambah Baru</span>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="text-lg font-bold text-gray-900">Tambah Data Barang</h2>
                        <p class="text-sm text-gray-500 mt-1">Masukkan informasi detail barang baru ke dalam sistem.</p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                
                                <div class="col-span-1 md:col-span-2 bg-teal-50/50 p-5 rounded-lg border border-teal-100">
                                    <h4 class="text-teal-800 font-semibold mb-4 text-sm uppercase tracking-wide">Generate Kode Barang</h4>
                                    <div class="flex flex-col md:flex-row gap-4">
                                        <div class="w-full md:w-1/2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelompok Barang <span class="text-red-500">*</span></label>
                                            <select v-model="form.kelompok_barang_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                                <option value="" disabled>-- Pilih Kelompok --</option>
                                                <option v-for="k in kelompok_barangs" :key="k.id" :value="k.id">
                                                    {{ k.nama_kelompok }} ({{ k.kode_kelompok }})
                                                </option>
                                            </select>
                                            <div v-if="form.errors.kelompok_barang_id" class="text-red-500 text-xs mt-1">{{ form.errors.kelompok_barang_id }}</div>
                                        </div>
                                        <div class="w-full md:w-1/2">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Urut (Manual) <span class="text-red-500">*</span></label>
                                            <input type="text" v-model="form.no_urut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Contoh: 001">
                                            <div v-if="form.errors.no_urut" class="text-red-500 text-xs mt-1">{{ form.errors.no_urut }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center gap-2 text-sm text-teal-800 bg-white px-3 py-2 rounded border border-teal-100 w-fit">
                                        <span>Preview Kode:</span>
                                        <span class="font-bold font-mono text-base">{{ previewKode }}</span>
                                    </div>
                                </div>

                                <div class="md:col-span-2 border-b border-gray-100 pb-2 mb-2">
                                    <h4 class="text-gray-800 font-semibold text-sm uppercase tracking-wide">Informasi Barang</h4>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                                    <input type="text" v-model="form.nama_barang" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Masukkan nama barang">
                                    <div v-if="form.errors.nama_barang" class="text-red-500 text-xs mt-1">{{ form.errors.nama_barang }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan <span class="text-red-500">*</span></label>
                                    <select v-model="form.satuan_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        <option value="" disabled>-- Pilih Satuan --</option>
                                        <option v-for="sat in satuans" :key="sat.id" :value="sat.id">{{ sat.nama_satuan }}</option>
                                    </select>
                                    <div v-if="form.errors.satuan_id" class="text-red-500 text-xs mt-1">{{ form.errors.satuan_id }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                                    <input type="number" v-model="form.stok" min="0" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <div v-if="form.errors.stok" class="text-red-500 text-xs mt-1">{{ form.errors.stok }}</div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan (Rp)</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input 
                                            type="number" 
                                            v-model="form.harga" 
                                            min="0" 
                                            class="w-full rounded-lg border-gray-300 pl-10 focus:border-teal-500 focus:ring-teal-500 sm:text-sm"
                                            placeholder="0"
                                        >
                                    </div>
                                    <div v-if="form.errors.harga" class="text-red-500 text-xs mt-1">{{ form.errors.harga }}</div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Barang</label>
                                    <input 
                                        type="file" 
                                        @input="form.foto = $event.target.files[0]" 
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 transition"
                                        accept="image/*"
                                    >
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, JPEG. Maks: 2MB.</p>
                                    <div v-if="form.progress" class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                        <div class="bg-teal-600 h-2.5 rounded-full" :style="{ width: form.progress.percentage + '%' }"></div>
                                    </div>
                                    <div v-if="form.errors.foto" class="text-red-500 text-xs mt-1">{{ form.errors.foto }}</div>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea 
                                        v-model="form.deskripsi" 
                                        rows="3" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm resize-none"
                                        placeholder="Tambahkan deskripsi detail barang..."
                                    ></textarea>
                                </div>

                            </div>

                            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-100">
                                <Link :href="route('barangs.index')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium text-sm transition">
                                    Batal
                                </Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm transition shadow-sm disabled:opacity-70 disabled:cursor-not-allowed">
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