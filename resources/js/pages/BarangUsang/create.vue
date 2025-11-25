<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    barangs: Array, // Data barang untuk dropdown
});

const form = useForm({
    barang_id: '',
    tanggal_laporan: new Date().toISOString().substr(0, 10), // Default hari ini
    jumlah: 1,
    keterangan: '',
});

const submit = () => {
    form.post(route('barang-usang.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Lapor Barang Usang" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Lapor Barang Usang</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Pelaporan Barang Rusak</h3>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                
                                <!-- Pilih Barang -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                                    <select v-model="form.barang_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                        <option value="" disabled>-- Pilih Barang --</option>
                                        <option v-for="b in barangs" :key="b.id" :value="b.id">{{ b.nama_barang }} (Stok: {{ b.stok }})</option>
                                    </select>
                                    <div v-if="form.errors.barang_id" class="text-red-500 text-xs mt-1">{{ form.errors.barang_id }}</div>
                                </div>

                                <!-- Tanggal -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Laporan</label>
                                    <input type="date" v-model="form.tanggal_laporan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <div v-if="form.errors.tanggal_laporan" class="text-red-500 text-xs mt-1">{{ form.errors.tanggal_laporan }}</div>
                                </div>

                                <!-- Jumlah Rusak -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jumlah Rusak <span class="text-red-500">*</span></label>
                                    <input type="number" v-model="form.jumlah" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                                    <div v-if="form.errors.jumlah" class="text-red-500 text-xs mt-1">{{ form.errors.jumlah }}</div>
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700">Keterangan / Penyebab Kerusakan</label>
                                <textarea v-model="form.keterangan" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="Contoh: Barang pecah saat pengiriman..."></textarea>
                                <div v-if="form.errors.keterangan" class="text-red-500 text-xs mt-1">{{ form.errors.keterangan }}</div>
                            </div>

                            <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                                <Link :href="route('barang-usang.index')" class="px-4 py-2 border rounded-md text-gray-700 hover:bg-gray-50">Batal</Link>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 disabled:opacity-50">Simpan Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>