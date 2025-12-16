<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Save, ArrowLeft } from 'lucide-vue-next';

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
        <div class="py-8 px-4 md:px-6 lg:px-8 max-w-4xl mx-auto">
            
            <div class="mb-6">
                <Link :href="route('manajemen-akun.approval.index')" class="flex items-center text-gray-500 hover:text-gray-700 transition mb-2">
                    <ArrowLeft class="w-4 h-4 mr-1" /> Kembali
                </Link>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Akun Approval</h1>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                <div class="p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="col-span-2 md:col-span-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input 
                                    v-model="form.name" 
                                    type="text" 
                                    placeholder="Contoh: Dr. Asep, M.T."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                >
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700">NIP / ID Pegawai</label>
                                <input 
                                    v-model="form.nip" 
                                    type="text" 
                                    placeholder="Masukkan NIP"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                >
                                <div v-if="form.errors.nip" class="text-red-500 text-xs mt-1">{{ form.errors.nip }}</div>
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Unit / Jurusan</label>
                                <select 
                                    v-model="form.unit_kerja_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm bg-white"
                                >
                                    <option value="" disabled>-- Pilih Unit Kerja --</option>
                                    <option v-for="unit in unitKerjas" :key="unit.id" :value="unit.id">
                                        {{ unit.nama_unit }}
                                    </option>
                                </select>
                                <div v-if="form.errors.unit_kerja_id" class="text-red-500 text-xs mt-1">{{ form.errors.unit_kerja_id }}</div>
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Email</label>
                                <input 
                                    v-model="form.email" 
                                    type="email" 
                                    placeholder="email@polban.ac.id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                >
                                <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Password</label>
                                <input 
                                    v-model="form.password" 
                                    type="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                >
                                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
                            </div>

                            <div class="col-span-2 md:col-span-1">
                                <label class="block mb-1 text-sm font-medium text-gray-700">Konfirmasi Password</label>
                                <input 
                                    v-model="form.password_confirmation" 
                                    type="password" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-teal-500 focus:border-teal-500 text-sm"
                                >
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                            <Link 
                                :href="route('manajemen-akun.approval.index')" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium hover:bg-gray-50 transition"
                            >
                                Batal
                            </Link>
                            
                            <button 
                                type="submit" 
                                class="flex items-center px-6 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition disabled:opacity-50"
                                :disabled="form.processing"
                            >
                                <Save class="w-4 h-4 mr-2" />
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Akun' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>