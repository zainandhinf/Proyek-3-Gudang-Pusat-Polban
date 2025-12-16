<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
    unitKerjas: Array,
});

const form = useForm({
    name: '',
    nip: '',
    unit_kerja_id: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('approval.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Tambah Akun Approval" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah Akun Approval
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Formulir Tambah Approval</h2>

                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                            <input 
                                v-model="form.name" 
                                type="text" 
                                placeholder="Contoh: Dr. Asep, M.T."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
                            >
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">NIP / ID Pegawai</label>
                            <input 
                                v-model="form.nip" 
                                type="text" 
                                placeholder="Masukkan NIP (Wajib Unik)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
                            >
                            <div v-if="form.errors.nip" class="text-red-500 text-sm mt-1">{{ form.errors.nip }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Unit / Jurusan</label>
                            <select 
                                v-model="form.unit_kerja_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-black"
                            >
                                <option value="" disabled>-- Pilih Unit Kerja --</option>
                                <option v-for="unit in unitKerjas" :key="unit.id" :value="unit.id">
                                    {{ unit.nama_unit }}
                                </option>
                            </select>
                            <div v-if="form.errors.unit_kerja_id" class="text-red-500 text-sm mt-1">{{ form.errors.unit_kerja_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                            <input 
                                v-model="form.email" 
                                type="email" 
                                placeholder="email@polban.ac.id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
                            >
                            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">Password</label>
                                <input 
                                    v-model="form.password" 
                                    type="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
                                >
                                <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">Konfirmasi Password</label>
                                <input 
                                    v-model="form.password_confirmation" 
                                    type="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-black"
                                >
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t">
                            <Link 
                                :href="route('manajemen-akun.approval.index')" 
                                class="text-gray-600 hover:text-gray-900 font-medium text-sm px-4 py-2"
                            >
                                Batal
                            </Link>
                            
                            <button 
                                type="submit" 
                                class="bg-blue-600 text-white font-bold px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Akun' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>