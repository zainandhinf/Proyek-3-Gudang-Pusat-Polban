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

<style>
    /* Custom styles to support gradient & opacity */
    .bg-gradient-to-br-from-polban-dark-via-polban-dark-to-polban-medium {
      background: linear-gradient(to bottom right, #1D3557, #1D3557, #457B9D);
    }
  </style>

<template>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
        {{ status }}
    </div>
    
    <div class="min-h-screen flex flex-col lg:flex-row">
        
        <div class="lg:w-[35%] bg-gradient-to-br-from-polban-dark-via-polban-dark-to-polban-medium relative overflow-hidden">
    <div class="absolute inset-0 bg-black/10"></div>
    <div class="relative z-10 h-full flex flex-col items-center justify-center px-8 py-12 lg:py-0">
      <!-- Warehouse Icon -->
      <div class="mb-8 opacity-80">
        <svg width="120" height="96" viewBox="0 0 120 96" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 91.5008V32.1195C0 27.207 2.98125 22.8008 7.5375 20.982L57.7688 0.900781C59.1938 0.319531 60.7875 0.319531 62.2312 0.900781L112.462 20.982C117.019 22.8008 120 27.2258 120 32.1195V91.5008C120 93.9945 117.994 96.0008 115.5 96.0008H106.5C104.006 96.0008 102 93.9945 102 91.5008V42.0008C102 38.682 99.3188 36.0008 96 36.0008H24C20.6812 36.0008 18 38.682 18 42.0008V91.5008C18 93.9945 15.9938 96.0008 13.5 96.0008H4.5C2.00625 96.0008 0 93.9945 0 91.5008ZM91.5 96.0008H28.5C26.0062 96.0008 24 93.9945 24 91.5008V81.0008H96V91.5008C96 93.9945 93.9938 96.0008 91.5 96.0008ZM24 75.0008V63.0008H96V75.0008H24ZM24 57.0008V42.0008H96V57.0008H24Z" fill="white"/>
        </svg>
      </div>

      <h1 class="text-white text-3xl font-bold text-center mb-4">Sistem Informasi</h1>
      <h2 class="text-white text-2xl font-semibold text-center mb-12">Manajemen Gudang</h2>

      <!-- Feature Icons -->
      <div class="flex gap-8 lg:gap-12">
        <!-- Inventory -->
        <div class="flex flex-col items-center gap-2">
          <div class="opacity-70">
            <svg width="41" height="36" viewBox="0 0 41 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17.4375 0H14.625C12.7617 0 11.25 1.51172 11.25 3.375V11.25C11.25 13.732 13.268 15.75 15.75 15.75H24.75C27.232 15.75 29.25 13.732 29.25 11.25V3.375C29.25 1.51172 27.7383 0 25.875 0H23.0625V5.625C23.0625 6.24375 22.5563 6.75 21.9375 6.75H18.5625C17.9437 6.75 17.4375 6.24375 17.4375 5.625V0ZM4.5 18C2.01797 18 0 20.018 4.5 22.5V31.5C4.5 33.982 2.01797 36 4.5 36H15.75C18.232 36 20.25 33.982 20.25 31.5V22.5C20.25 20.018 18.232 18 15.75 18H12.9375V23.625C12.9375 24.2438 12.4313 24.75 11.8125 24.75H8.4375C7.81875 24.75 7.3125 24.2438 7.3125 23.625V18H4.5ZM24.75 36H36C38.482 36 40.5 33.982 40.5 31.5V22.5C40.5 20.018 38.482 18 36 18H33.1875V23.625C33.1875 24.2438 32.6813 24.75 32.0625 24.75H28.6875C28.0687 24.75 27.5625 24.2438 27.5625 23.625V18H24.75C23.6953 18 22.725 18.3586 21.9586 18.9703C22.3031 19.7016 22.5 20.5172 22.5 21.375V32.625C22.5 33.4828 22.3031 34.2984 21.9586 35.0297C22.725 35.6414 23.6953 36 24.75 36Z" fill="white"/>
            </svg>
          </div>
          <span class="text-white text-sm">Inventory</span>
        </div>

        <!-- Logistics -->
        <div class="flex flex-col items-center gap-2">
          <div class="opacity-70">
            <svg width="45" height="36" viewBox="0 0 45 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3.375 0C1.51172 0 0 1.51172 3.375 3.375V25.875C3.375 27.7383 1.51172 29.25 3.375 29.25H4.5C4.5 32.9766 7.52344 36 11.25 36C14.9766 36 18 32.9766 18 29.25H27C27 32.9766 30.0234 36 33.75 36C37.4766 36 40.5 32.9766 40.5 29.25H42.75C43.9945 29.25 45 28.2445 45 27C45 25.7555 43.9945 24.75 42.75 24.75V20.25V18V16.6852C42.75 15.4898 42.2789 14.3438 41.4352 13.5L36 8.06484C35.1562 7.22109 34.0102 6.75 32.8148 6.75H29.25V3.375C29.25 1.51172 27.7383 0 25.875 0H3.375ZM29.25 11.25H32.8148L38.25 16.6852V18H29.25V11.25ZM7.875 29.25C7.875 28.3549 8.23058 27.4964 8.86351 26.8635C9.49645 26.2306 10.3549 25.875 11.25 25.875C12.1451 25.875 13.0036 26.2306 13.6365 26.8635C14.2694 27.4964 14.625 28.3549 14.625 29.25C14.625 30.1451 14.2694 31.0036 13.6365 31.6365C13.0036 32.2694 12.1451 32.625 11.25 32.625C10.3549 32.625 9.49645 32.2694 8.86351 31.6365C8.23058 31.0036 7.875 30.1451 7.875 29.25ZM33.75 25.875C34.6451 25.875 35.5035 26.2306 36.1365 26.8635C36.7694 27.4964 37.125 28.3549 37.125 29.25C37.125 30.1451 36.7694 31.0036 36.1365 31.6365C35.5035 32.2694 34.6451 32.625 33.75 32.625C32.8549 32.625 31.9964 32.2694 31.3635 31.6365C30.7306 31.0036 30.375 30.1451 30.375 29.25C30.375 28.3549 30.7306 27.4964 31.3635 26.8635C31.9964 26.2306 32.8549 25.875 33.75 25.875Z" fill="white"/>
            </svg>
          </div>
          <span class="text-white text-sm">Logistics</span>
        </div>

        <!-- Reports -->
        <div class="flex flex-col items-center gap-2">
          <div class="opacity-70">
            <svg width="27" height="36" viewBox="0 0 27 36" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M13.5 0C10.5609 0 8.05781 1.87734 7.13672 4.5H4.5C2.01797 4.5 0 6.51797 0 9V31.5C0 33.982 2.01797 36 4.5 36H22.5C24.982 36 27 33.982 27 31.5V9C27 6.51797 24.982 4.5 22.5 4.5H19.8633C18.9422 1.87734 16.4391 0 13.5 0ZM13.5 4.5C14.0967 4.5 14.669 4.73705 15.091 5.15901C15.5129 5.58097 15.75 6.15326 15.75 6.75C15.75 7.34674 15.5129 7.91903 15.091 8.34099C14.669 8.76295 14.0967 9 13.5 9C12.9033 9 12.331 8.76295 11.909 8.34099C11.4871 7.91903 11.25 7.34674 11.25 6.75C11.25 6.15326 11.4871 5.58097 11.909 5.15901C12.331 4.73705 12.9033 4.5 13.5 4.5ZM5.0625 19.125C5.0625 18.6774 5.24029 18.2482 5.55676 17.9318C5.87322 17.6153 6.30245 17.4375 6.75 17.4375C7.19755 17.4375 7.62678 17.6153 7.94324 17.9318C8.25971 18.2482 8.4375 18.6774 8.4375 19.125C8.4375 19.5726 8.25971 20.0018 7.94324 20.3182C7.62678 20.6347 7.19755 20.8125 6.75 20.8125C6.30245 20.8125 5.87322 20.6347 5.55676 20.3182C5.24029 20.0018 5.0625 19.5726 5.0625 19.125ZM12.375 18H21.375C21.9938 18 22.5 18.5062 22.5 19.125C22.5 19.7438 21.9938 20.25 21.375 20.25H12.375C11.7562 20.25 11.25 19.7438 11.25 19.125C11.25 18.5062 11.7562 18 12.375 18ZM5.0625 25.875C5.0625 25.4274 5.24029 24.9982 5.55676 24.6818C5.87322 24.3653 6.30245 24.1875 6.75 24.1875C7.19755 24.1875 7.62678 24.3653 7.94324 24.6818C8.25971 24.9982 8.4375 25.4274 8.4375 25.875C8.4375 26.3226 8.25971 26.7518 7.94324 27.0682C7.62678 27.3847 7.19755 27.5625 6.75 27.5625C6.30245 27.5625 5.87322 27.3847 5.55676 27.0682C5.24029 26.7518 5.0625 26.3226 5.0625 25.875ZM11.25 25.875C11.25 25.2562 11.7562 24.75 12.375 24.75H21.375C21.9938 24.75 22.5 25.2562 22.5 25.875C22.5 26.4938 21.9938 27 21.375 27H12.375C11.7562 27 11.25 26.4938 11.25 25.875Z" fill="white"/>
            </svg>
          </div>
          <span class="text-white text-sm">Reports</span>
        </div>
      </div>
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
                        
                        <!-- <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-polban-light hover:underline"
                        >
                            Lupa password?
                        </Link> -->
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