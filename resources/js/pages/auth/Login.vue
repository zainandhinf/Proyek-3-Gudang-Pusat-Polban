<script setup>
// Bagian <script> ini kita ambil 99% dari file Login.vue Anda yang lama.
import Checkbox from '@/Components/Checkbox.vue';
// Kita TIDAK lagi menggunakan GuestLayout
// import GuestLayout from '@/Layouts/GuestLayout.vue'; 
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
// Kita ganti TextInput dengan komponen kustom kita
// import TextInput from '@/Components/TextInput.vue';
import InputIcon from '@/Components/InputIcon.vue'; // <-- KOMPONEN BARU
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
    </div>
    
    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <div class="lg:w-[35%] bg-gradient-to-br-from-polban-dark-via-polban-dark-to-polban-medium relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10 h-full flex flex-col items-center justify-center px-8 py-12 lg:py-0">
                <div class="mb-8 opacity-80">
                    <svg width="120" height="96" viewBox="0 0 120 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 91.5008V32.1195C0 27.207 2.98125 22.8008 7.5375 20.982L57.7688 0.900781C59.1938 0.319531 60.7875 0.319531 62.2312 0.900781L112.462 20.982C117.019 22.8008 120 27.2258 120 32.1195V91.5008C120 93.9945 117.994 96.0008 115.5 96.0008H106.5C104.006 96.0008 102 93.9945 102 91.5008V42.0008C102 38.682 99.3188 36.0008 96 36.0008H24C20.6812 36.0008 18 38.682 18 42.0008V91.5008C18 93.9945 15.9938 96.0008 13.5 96.0008H4.5C2.00625 96.0008 0 93.9945 0 91.5008ZM91.5 96.0008H28.5C26.0062 96.0008 24 93.9945 24 91.5008V81.0008H96V91.5008C96 93.9945 93.9938 96.0008 91.5 96.0008ZM24 75.0008V63.0008H96V75.0008H24ZM24 57.0008V42.0008H96V57.0008H24Z" fill="white"/>
                    </svg>
                </div>
                <h1 class="text-white text-3xl font-bold text-center mb-4">Sistem Informasi</h1>
                <h2 class="text-white text-2xl font-semibold text-center mb-12">Manajemen Gudang</h2>
                </div>
        </div>

        <div class="flex-1 flex items-center justify-center px-6 py-12 lg:px-16 bg-white">
            <div class="w-full max-w-[512px]">
                
                <div class="text-center mb-12">
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 bg-polban-light rounded-full flex items-center justify-center">
                            <svg width="38" height="30" viewBox="0 0 38 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18.7499 1.875C18.2753 1.875 17.8066 1.95703 17.3613 2.11523L0.925707 8.05078C0.369066 8.25586 -7.46717e-05 8.7832 -7.46717e-05 9.375C-7.46717e-05 9.9668 0.369066 10.4941 0.925707 10.6992L4.31828 11.9238C3.35735 13.4355 2.81243 15.2227 2.81243 17.1035V18.75C2.81243 20.4141 2.17961 22.1309 1.50578 23.4844C1.12493 24.2461 0.691332 24.9961 0.187425 25.6875C-7.46697e-05 25.9395 -0.052809 26.2676 0.0526597 26.5664C0.158128 26.8652 0.404222 27.0879 0.70891 27.1641L4.45891 28.1016C4.705 28.166 4.96868 28.1191 5.18547 27.9844C5.40227 27.8496 5.55461 27.627 5.60149 27.375C6.10539 24.8672 5.85344 22.6172 5.47844 21.0059C5.29094 20.1738 5.03899 19.3242 4.68743 18.5449V17.1035C4.68743 15.334 5.28508 13.6641 6.32219 12.3281C7.07805 11.4199 8.05657 10.6875 9.205 10.2363L18.4042 6.62109C18.8847 6.43359 19.4296 6.66797 19.6171 7.14844C19.8046 7.62891 19.5702 8.17383 19.0898 8.36133L9.89055 11.9766C9.16399 12.2637 8.52532 12.7031 8.00383 13.2422L17.3554 16.6172C17.8007 16.7754 18.2695 16.8574 18.7441 16.8574C19.2187 16.8574 19.6874 16.7754 20.1327 16.6172L36.5741 10.6992C37.1308 10.5 37.4999 9.9668 37.4999 9.375C37.4999 8.7832 37.1308 8.25586 36.5741 8.05078L20.1386 2.11523C19.6933 1.95703 19.2245 1.875 18.7499 1.875ZM7.49993 23.9062C7.49993 25.9746 12.539 28.125 18.7499 28.125C24.9609 28.125 29.9999 25.9746 29.9999 23.9062L29.1034 15.3867L20.7714 18.3984C20.121 18.6328 19.4355 18.75 18.7499 18.75C18.0644 18.75 17.373 18.6328 16.7284 18.3984L8.39641 15.3867L7.49993 23.9062Z" fill="white"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-polban-dark text-2xl font-bold mb-2">POLBAN</h1>
                    <p class="text-polban-medium text-base font-medium mb-6">Politeknik Negeri Bandung</p>
                    <h2 class="text-polban-dark text-3xl font-bold mb-2">Selamat Datang</h2>
                    <p class="text-gray-600 text-base">Masuk ke Sistem Informasi Manajemen Gudang Pusat</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    
                    <div>
                        <InputLabel for="email" value="Email" class="block text-polban-dark text-sm font-semibold mb-2" />
                        
                        <InputIcon
                            id="email"
                            type="email"
                            placeholder="Masukkan email Anda"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        >
                            <template #icon>
                                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7 8C8.06087 8 9.07828 7.57857 9.82843 6.82843C10.5786 6.07828 11 5.06087 11 4C11 2.93913 10.5786 1.92172 9.82843 1.17157C9.07828 0.421427 8.06087 0 7 0C5.93913 0 4.92172 0.421427 4.17157 1.17157C3.42143 1.92172 3 2.93913 3 4C3 5.06087 3.42143 6.07828 4.17157 6.82843C4.92172 7.57857 5.93913 8 7 8ZM5.57188 9.5C2.49375 9.5 0 11.9937 0 15.0719C0 15.5844 0.415625 16 0.928125 16H13.0719C13.5844 16 14 15.5844 14 15.0719C14 11.9937 11.5063 9.5 8.42813 9.5H5.57188Z" fill="#9CA3AF"/>
                                </svg>
                            </template>
                        </InputIcon>
                        
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" class="block text-polban-dark text-sm font-semibold mb-2" />
                        
                        <InputIcon
                            id="password"
                            type="password"
                            placeholder="Masukkan password Anda"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        >
                            <template #icon>
                                <svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.5 4.5V6H9.5V4.5C9.5 3.11875 8.38125 2 7 2C5.61875 2 4.5 3.11875 4.5 4.5ZM2.5 6V4.5C2.5 2.01562 4.51562 0 7 0C9.48438 0 11.5 2.01562 11.5 4.5V6H12C13.1031 6 14 6.89688 14 8V14C14 15.1031 13.1031 16 12 16H2C0.896875 16 0 15.1031 0 14V8C0 6.89688 0.896875 6 2 6H2.5Z" fill="#9CA3AF"/>
                                </svg>
                            </template>
                        </InputIcon>
                        
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="text-gray-600">Ingat saya</span>
                        </label>
                        
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-polban-light hover:underline"
                        >
                            Lupa password?
                        </Link>
                    </div>

                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="w-full h-[60px] text-lg"
                    >
                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M7.66055 3.72305L11.9777 8.04023C12.2309 8.29336 12.375 8.64141 12.375 9C12.375 9.35859 12.2309 9.70664 11.9777 9.95977L7.66055 14.277C7.43555 14.502 7.1332 14.625 6.8168 14.625C6.15937 14.625 5.625 14.0906 5.625 13.4332V11.25H1.125C0.502734 11.25 0 10.7473 0 10.125V7.875C0 7.25273 0.502734 6.75 1.125 6.75H5.625V4.5668C5.625 3.90937 6.15937 3.375 6.8168 3.375C7.1332 3.375 7.43555 3.50156 7.66055 3.72305ZM12.375 14.625H14.625C15.2473 14.625 15.75 14.1223 15.75 13.5V4.5C15.75 3.87773 15.2473 3.375 14.625 3.375H12.375C11.7527 3.375 11.25 2.87227 11.25 2.25C11.25 1.62773 11.7527 1.125 12.375 1.125H14.625C16.4883 1.125 18 2.63672 18 4.5V13.5C18 15.3633 16.4883 16.875 14.625 16.875H12.375C11.7527 16.875 11.25 16.3723 11.25 15.75C11.25 15.1277 11.7527 14.625 12.375 14.625Z" fill="white"/>
                        </svg>
                        Login
                    </PrimaryButton>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-gray-500 text-sm mb-4">Butuh bantuan? Hubungi administrator sistem</p>
                    </div>
            </div>
        </div>
    </div>
    
    </template>