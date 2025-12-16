<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

// Props: 'user' (data lama) & 'units' (dropdown)
const props = defineProps({
    user: Object,
    units: Array,
});

// Isi form dengan data yang sudah ada (user.name, dll)
const form = useForm({
    name: props.user.name,
    nip: props.user.nip,
    unit_kerja_id: props.user.unit_kerja_id,
    email: props.user.email,
    password: '',            // Password kosongkan saja
    password_confirmation: '',
});

const submit = () => {
    // Gunakan PUT untuk update
    form.put(route('pemohon.update', props.user.id), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Edit Akun Pemohon" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Akun Pemohon
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Data: {{ user.name }}</h2>

                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Nama Lengkap</label>
                            <input v-model="form.name" type="text" class="w-full px-3 py-2 border rounded-lg">
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">NIP</label>
                            <input v-model="form.nip" type="text" class="w-full px-3 py-2 border rounded-lg">
                            <div v-if="form.errors.nip" class="text-red-500 text-sm mt-1">{{ form.errors.nip }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Unit Kerja</label>
                            <select v-model="form.unit_kerja_id" class="w-full px-3 py-2 border rounded-lg bg-white">
                                <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                    {{ unit.nama_unit }}
                                </option>
                            </select>
                            <div v-if="form.errors.unit_kerja_id" class="text-red-500 text-sm mt-1">{{ form.errors.unit_kerja_id }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Email</label>
                            <input v-model="form.email" type="email" class="w-full px-3 py-2 border rounded-lg">
                            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg mb-6 border">
                            <p class="text-sm text-gray-600 mb-2 font-bold">Ubah Password (Opsional)</p>
                            <p class="text-xs text-gray-500 mb-3">Biarkan kosong jika tidak ingin mengganti password.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <input v-model="form.password" type="password" placeholder="Password Baru" class="w-full px-3 py-2 border rounded-lg">
                                    <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                                </div>
                                <div>
                                    <input v-model="form.password_confirmation" type="password" placeholder="Konfirmasi Password" class="w-full px-3 py-2 border rounded-lg">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <Link :href="route('manajemen-akun.pemohon.index')" class="px-4 py-2 text-gray-600 font-medium">Batal</Link>
                            <button type="submit" class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-700 font-bold" :disabled="form.processing">
                                Perbarui Akun
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>