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
    <div class="py-12 max-w-2xl mx-auto px-4">
      <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h2 class="text-xl font-bold mb-4">Tambah Kelompok Barang</h2>
        
        <form @submit.prevent="submit">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Kode Kelompok</label>
              <input v-model="form.kode_kelompok" type="text" placeholder="Contoh: ATK" class="mt-1 w-full border-gray-300 rounded-md focus:ring-teal-500 uppercase">
              <p class="text-xs text-gray-500 mt-1">Maksimal 10 karakter. Akan menjadi awalan kode barang.</p>
              <div v-if="form.errors.kode_kelompok" class="text-red-500 text-xs mt-1">{{ form.errors.kode_kelompok }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Nama Kelompok</label>
              <input v-model="form.nama_kelompok" type="text" placeholder="Contoh: Alat Tulis Kantor" class="mt-1 w-full border-gray-300 rounded-md focus:ring-teal-500">
              <div v-if="form.errors.nama_kelompok" class="text-red-500 text-xs mt-1">{{ form.errors.nama_kelompok }}</div>
            </div>
          </div>

          <div class="mt-6 flex justify-end gap-3">
            <Link :href="route('kelompok-barang.index')" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-50">Batal</Link>
            <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-teal-600 text-white rounded hover:bg-teal-700">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>