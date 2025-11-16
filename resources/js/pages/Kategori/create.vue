<script setup>
import { ref } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// Data reaktif untuk menampung isi form
const form = ref({
  kode_kategori: '',
  nama_kategori: '',
  deskripsi: '',
});

// Fungsi untuk mengirim data ke backend Laravel
const submitForm = async () => {
  try {
    // Kirim data ke API Laravel
    const response = await axios.post('/api/kategori', form.value);
    
    console.log('Data berhasil disimpan:', response.data);
    alert('Data kategori berhasil disimpan!');
    resetForm();
    
  } catch (error) {
    // Tangani error validasi atau server
    console.error('Error menyimpan data:', error.response.data);
    alert('Terjadi kesalahan: ' + error.response.data.message);
  }
};

// Fungsi untuk reset form
const resetForm = () => {
  form.value.kode_kategori = '';
  form.value.nama_kategori = '';
  form.value.deskripsi = '';
};
</script>


<template>
    <AuthenticatedLayout>
  <div class="p-8">
    <h1 class="text-3xl font-bold text-gray-800">Tambah Data Kategori</h1>
    <p class="mt-2 text-gray-600">Masukkan informasi lengkap kategori baru untuk ditambahkan ke sistem.</p>

    <form @submit.prevent="submitForm" class="p-8 mt-8 bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        
        <div>
          <label for="kode_kategori" class="block text-sm font-medium text-gray-700">
            Kode Kategori <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            id="kode_kategori"
            v-model="form.kode_kategori"
            class="w-full p-3 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
            placeholder="Masukkan kode 6 digit (contoh: KAT001)"
          >
          <p class="mt-1 text-xs text-gray-500">Format: 3 huruf kategori + 3 digit angka</p>
        </div>

        <div>
          <label for="nama_kategori" class="block text-sm font-medium text-gray-700">
            Nama Kategori <span class="text-red-500">*</span>
          </label>
          <input 
            type="text" 
            id="nama_kategori"
            v-model="form.nama_kategori"
            class="w-full p-3 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
            placeholder="Contoh: Alat Tulis Kantor"
          >
        </div>
      </div>

      <div class="mt-8">
        <label for="deskripsi" class="block text-sm font-medium text-gray-700">
          Deskripsi
        </label>
        <textarea 
          id="deskripsi"
          v-model="form.deskripsi"
          rows="5"
          class="w-full p-3 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"
          placeholder="Masukkan deskripsi kategori..."
        ></textarea>
      </div>

      <div class="flex justify-end pt-6 mt-8 border-t border-gray-200 gap-x-3">
        <button type="button" class="px-5 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
          Kembali
        </button>
        <button type="reset" @click="resetForm" class="px-5 py-2 text-gray-800 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
          Reset
        </button>
        <button type="submit" class="flex items-center px-5 py-2 text-white bg-teal-600 rounded-md hover:bg-teal-700">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 14 16" xmlns="http://www.w3.org/2000/svg">
            <path d="M2 1C0.896875 1 0 1.89688 0 3V13C0 14.1031 0.896875 15 2 15H12C13.1031 15 14 14.1031 14 13V5.41563C14 4.88438 13.7906 4.375 13.4156 4L11 1.58438C10.625 1.20938 10.1156 1 9.58438 1H2ZM2 4C2 3.44688 2.44688 3 3 3H9C9.55313 3 10 3.44688 10 4V6C10 6.55312 9.55313 7 9 7H3C2.44688 7 2 6.55312 2 6V4ZM7 9C7.53043 9 8.03914 9.21071 8.41421 9.58579C8.78929 9.96086 9 10.4696 9 11C9 11.5304 8.78929 12.0391 8.41421 12.4142C8.03914 12.7893 7.53043 13 7 13C6.46957 13 5.96086 12.7893 5.58579 12.4142C5.21071 12.0391 5 11.5304 5 11C5 10.4696 5.21071 9.96086 5.58579 9.58579C5.96086 9.21071 6.46957 9 7 9Z" />
          </svg>
          Simpan Data
        </button>
      </div>
    </form>
  </div>
    </AuthenticatedLayout>
</template>


