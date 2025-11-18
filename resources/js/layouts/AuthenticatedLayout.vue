<script setup>
import { onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

// Ambil data user dari Laravel (yang sudah kita setup sebelumnya)
const user = usePage().props.auth.user;
const appName = usePage().props.appName || 'SIMGP Polban';

// --- LOGIKA UNTUK THEME TOGGLE ---
const isDark = ref(false);

const toggleTheme = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initialTheme = savedTheme || (prefersDark ? 'dark' : 'light');
    
    if (initialTheme === 'dark') {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
// --- AKHIR LOGIKA THEME TOGGLE ---
</script>

<template>
    <div class="flex min-h-screen bg-gray-50 text-gray-900 dark:bg-dark-bg dark:text-dark-text transition-colors duration-300">
        
        <aside class="w-64 bg-white border-r border-gray-200 shadow-lg fixed left-0 top-16 bottom-0 overflow-y-auto dark:bg-dark-card dark:border-dark-border hidden lg:block">
            <nav class="p-4 space-y-2">
                
                <Link :href="route('dashboard')" 
                      :class="{
                        'bg-polban-light text-white': route().current('dashboard'),
                        'text-gray-700 hover:bg-gray-100 dark:text-dark-muted dark:hover:bg-gray-700/30': !route().current('dashboard')
                      }"
                      class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" :class="{ 'fill-white': route().current('dashboard'), 'fill-current': !route().current('dashboard') }">
                        <path d="M2 2C2 1.44687 1.55313 1 1 1C0.446875 1 0 1.44687 0 2V12.5C0 13.8813 1.11875 15 2.5 15H15C15.5531 15 16 14.5531 16 14C16 13.4469 15.5531 13 15 13H2.5C2.225 13 2 12.775 2 12.5V2ZM14.7063 4.70625C15.0969 4.31563 15.0969 3.68125 14.7063 3.29063C14.3156 2.9 13.6812 2.9 13.2906 3.29063L10 6.58437L8.20625 4.79063C7.81563 4.4 7.18125 4.4 6.79063 4.79063L3.29063 8.29062C2.9 8.68125 2.9 9.31563 3.29063 9.70625C3.68125 10.0969 4.31563 10.0969 4.70625 9.70625L7.5 6.91563L9.29375 8.70938C9.68437 9.1 10.3188 9.1 10.7094 8.70938L14.7094 4.70937L14.7063 4.70625Z" />
                    </svg>
                    <span class="text-base">Dashboard</span>
                </Link>

                <div v-if="user.role === 'operator' || user.role === 'approval'">
                    <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Master Data</div>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M10.7812 1.22187L14.775 5.2625C16.4125 6.91875 16.4125 9.58125 14.775 11.2375L11.275 14.7781C10.9844 15.0719 10.5094 15.075 10.2156 14.7844C9.92187 14.4938 9.91875 14.0188 10.2094 13.725L13.7063 10.1844C14.7656 9.1125 14.7656 7.39062 13.7063 6.31875L9.71562 2.27813C9.425 1.98438 9.42813 1.50938 9.72188 1.21875C10.0156 0.928125 10.4906 0.93125 10.7812 1.225V1.22187ZM0 7.17188V2.5C0 1.67188 0.671875 1 1.5 1H6.17188C6.70312 1 7.2125 1.20938 7.5875 1.58438L12.8375 6.83437C13.6187 7.61562 13.6187 8.88125 12.8375 9.6625L8.66562 13.8344C7.88437 14.6156 6.61875 14.6156 5.8375 13.8344L0.5875 8.58438C0.209375 8.20938 0 7.70312 0 7.17188ZM4.5 4.5C4.5 4.23478 4.39464 3.98043 4.20711 3.79289C4.01957 3.60536 3.76522 3.5 3.5 3.5C3.23478 3.5 2.98043 3.60536 2.79289 3.79289C2.60536 3.98043 2.5 4.23478 2.5 4.5C2.5 4.76522 2.60536 5.01957 2.79289 5.20711C2.98043 5.39464 3.23478 5.5 3.5 5.5C3.76522 5.5 4.01957 5.39464 4.20711 5.20711C4.39464 5.01957 4.5 4.76522 4.5 4.5Z"/></svg>
                            <span class="text-base">Kategori</span>
                        </Link>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M7.32812 0.179688C7.7625 0.0234375 8.2375 0.0234375 8.675 0.179688L14.675 2.32344C15.4688 2.60781 16 3.36094 16 4.20781V11.7953C16 12.6391 15.4688 13.3953 14.6719 13.6797L8.67188 15.8234C8.2375 15.9797 7.7625 15.9797 7.325 15.8234L1.325 13.6797C0.53125 13.3953 0 12.6422 0 11.7953V4.20781C0 3.36406 0.53125 2.60781 1.32812 2.32344L7.32812 0.179688ZM8 2.06406L2.57188 4.00156L8 5.93906L13.4281 4.00156L8 2.06406ZM9 13.5828L14 11.7984V5.92344L9 7.70781V13.5828Z"/></svg>
                            <span class="text-base">Satuan</span>
                        </Link>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M1.25 1.5C0.834375 1.5 0.5 1.83437 0.5 2.25V3.75C0.5 4.16563 0.834375 4.5 1.25 4.5H2.75C3.16563 4.5 3.5 4.16563 3.5 3.75V2.25C3.5 1.83437 3.16563 1.5 2.75 1.5H1.25ZM6 2C5.44687 2 5 2.44687 5 3C5 3.55313 5.44687 4 6 4H15C15.5531 4 16 3.55313 16 3C16 2.44687 15.5531 2 15 2H6ZM6 7C5.44687 7 5 7.44687 5 8C5 8.55313 5.44687 9 6 9H15C15.5531 9 16 8.55313 16 8C16 7.44687 15.5531 7 15 7H6ZM6 12C5.44687 12 5 12.4469 5 13C5 13.5531 5.44687 14 6 14H15C15.5531 14 16 13.5531 16 13C16 12.4469 15.5531 12 15 12H6ZM0.5 7.25V8.75C0.5 9.16563 0.834375 9.5 1.25 9.5H2.75C3.16563 9.5 3.5 9.16563 3.5 8.75V7.25C3.5 6.83437 3.16563 6.5 2.75 6.5H1.25C0.834375 6.5 0.5 6.83437 0.5 7.25ZM1.25 11.5C0.834375 11.5 0.5 11.8344 0.5 12.25V13.75C0.5 14.1656 0.834375 14.5 1.25 14.5H2.75C3.16563 14.5 3.5 14.1656 3.5 13.75V12.25C3.5 11.8344 3.16563 11.5 2.75 11.5H1.25Z"/></svg>
                            <span class="text-base">Barang</span>
                        </Link>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Manajemen Akun</div>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M3.6 0C4.13043 0 4.63914 0.230468 5.01421 0.640704C5.38929 1.05094 5.6 1.60734 5.6 2.1875C5.6 2.76766 5.38929 3.32406 5.01421 3.7343C4.63914 4.14453 4.13043 4.375 3.6 4.375C3.06957 4.375 2.56086 4.14453 2.18579 3.7343C1.81071 3.32406 1.6 2.76766 1.6 2.1875C1.6 1.60734 1.81071 1.05094 2.18579 0.640704C2.56086 0.230468 3.06957 0 3.6 0ZM12.8 0C13.3304 0 13.8391 0.230468 14.2142 0.640704C14.5893 1.05094 14.8 1.60734 14.8 2.1875C14.8 2.76766 14.5893 3.32406 14.2142 3.7343C13.8391 4.14453 13.3304 4.375 12.8 4.375C12.2696 4.375 11.7609 4.14453 11.3858 3.7343C11.0107 3.32406 10.8 2.76766 10.8 2.1875C10.8 1.60734 11.0107 1.05094 11.3858 0.640704C11.7609 0.230468 12.2696 0 12.8 0ZM0 8.16758C0 6.55703 1.195 5.25 2.6675 5.25H3.735C4.1325 5.25 4.51 5.3457 4.85 5.51523C4.8175 5.71211 4.8025 5.91719 4.8025 6.125C4.8025 7.16953 5.2225 8.10742 5.885 8.75C5.88 8.75 5.875 8.75 5.8675 8.75H0.5325C0.24 8.75 0 8.4875 0 8.16758ZM10.1325 8.75C10.1275 8.75 10.1225 8.75 10.115 8.75C10.78 8.10742 11.1975 7.16953 11.1975 6.125C11.1975 5.91719 11.18 5.71484 11.15 5.51523C11.49 5.34297 11.8675 5.25 12.265 5.25H13.3325C14.805 5.25 16 6.55703 16 8.16758C16 8.49023 15.76 8.75 15.4675 8.75H10.1325ZM5.6 6.125C5.6 5.42881 5.85286 4.76113 6.30294 4.26884C6.75303 3.77656 7.36348 3.5 8 3.5C8.63652 3.5 9.24697 3.77656 9.69706 4.26884C10.1471 4.76113 10.4 5.42881 10.4 6.125C10.4 6.82119 10.1471 7.48887 9.69706 7.98116C9.24697 8.47344 8.63652 8.75 8 8.75C7.36348 8.75 6.75303 8.47344 6.30294 7.98116C5.85286 7.48887 5.6 6.82119 5.6 6.125ZM3.2 13.2699C3.2 11.2574 4.6925 9.625 6.5325 9.625H9.4675C11.3075 9.625 12.8 11.2574 12.8 13.2699C12.8 13.6719 12.5025 14 12.1325 14H3.8675C3.5 14 3.2 13.6746 3.2 13.2699Z"/></svg>
                            <span class="text-base">Pemohon</span>
                        </Link>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M3.6 0C4.13043 0 4.63914 0.230468 5.01421 0.640704C5.38929 1.05094 5.6 1.60734 5.6 2.1875C5.6 2.76766 5.38929 3.32406 5.01421 3.7343C4.63914 4.14453 4.13043 4.375 3.6 4.375C3.06957 4.375 2.56086 4.14453 2.18579 3.7343C1.81071 3.32406 1.6 2.76766 1.6 2.1875C1.6 1.60734 1.81071 1.05094 2.18579 0.640704C2.56086 0.230468 3.06957 0 3.6 0ZM12.8 0C13.3304 0 13.8391 0.230468 14.2142 0.640704C14.5893 1.05094 14.8 1.60734 14.8 2.1875C14.8 2.76766 14.5893 3.32406 14.2142 3.7343C13.8391 4.14453 13.3304 4.375 12.8 4.375C12.2696 4.375 11.7609 4.14453 11.3858 3.7343C11.0107 3.32406 10.8 2.76766 10.8 2.1875C10.8 1.60734 11.0107 1.05094 11.3858 0.640704C11.7609 0.230468 12.2696 0 12.8 0ZM0 8.16758C0 6.55703 1.195 5.25 2.6675 5.25H3.735C4.1325 5.25 4.51 5.3457 4.85 5.51523C4.8175 5.71211 4.8025 5.91719 4.8025 6.125C4.8025 7.16953 5.2225 8.10742 5.885 8.75C5.88 8.75 5.875 8.75 5.8675 8.75H0.5325C0.24 8.75 0 8.4875 0 8.16758ZM10.1325 8.75C10.1275 8.75 10.1225 8.75 10.115 8.75C10.78 8.10742 11.1975 7.16953 11.1975 6.125C11.1975 5.91719 11.18 5.71484 11.15 5.51523C11.49 5.34297 11.8675 5.25 12.265 5.25H13.3325C14.805 5.25 16 6.55703 16 8.16758C16 8.49023 15.76 8.75 15.4675 8.75H10.1325ZM5.6 6.125C5.6 5.42881 5.85286 4.76113 6.30294 4.26884C6.75303 3.77656 7.36348 3.5 8 3.5C8.63652 3.5 9.24697 3.77656 9.69706 4.26884C10.1471 4.76113 10.4 5.42881 10.4 6.125C10.4 6.82119 10.1471 7.48887 9.69706 7.98116C9.24697 8.47344 8.63652 8.75 8 8.75C7.36348 8.75 6.75303 8.47344 6.30294 7.98116C5.85286 7.48887 5.6 6.82119 5.6 6.125ZM3.2 13.2699C3.2 11.2574 4.6925 9.625 6.5325 9.625H9.4675C11.3075 9.625 12.8 11.2574 12.8 13.2699C12.8 13.6719 12.5025 14 12.1325 14H3.8675C3.5 14 3.2 13.6746 3.2 13.2699Z"/></svg>
                            <span class="text-base">Approval</span>
                        </Link>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Transaksi</div>
                        <Link :href="route('barang-masuk.index')" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor"><path d="M1.5 0C0.671875 0 0 0.671875 0 1.5V11.5C0 12.3281 0.671875 13 1.5 13H2C2 14.6562 3.34375 16 5 16C6.65625 16 8 14.6562 8 13H12C12 14.6562 13.3438 16 15 16C16.6562 16 18 14.6562 18 13H19C19.5531 13 20 12.5531 20 12C20 11.4469 19.5531 11 19 11V9V8V7.41563C19 6.88438 18.7906 6.375 18.4156 6L16 3.58437C15.625 3.20937 15.1156 3 14.5844 3H13V1.5C13 0.671875 12.3281 0 11.5 0H1.5ZM13 5H14.5844L17 7.41563V8H13V5ZM3.5 13C3.5 12.6022 3.65804 12.2206 3.93934 11.9393C4.22064 11.658 4.60218 11.5 5 11.5C5.39782 11.5 5.77936 11.658 6.06066 11.9393C6.34196 12.2206 6.5 12.6022 6.5 13C6.5 13.3978 6.34196 13.7794 6.06066 14.0607C5.77936 14.342 5.39782 14.5 5 14.5C4.60218 14.5 4.22064 14.342 3.93934 14.0607C3.65804 13.7794 3.5 13.3978 3.5 13ZM15 11.5C15.3978 11.5 15.7794 11.658 16.0607 11.9393C16.342 12.2206 16.5 12.6022 16.5 13C16.5 13.3978 16.342 13.7794 16.0607 14.0607C15.7794 14.342 15.3978 14.5 15 14.5C14.6022 14.5 14.2206 14.342 13.9393 14.0607C13.658 13.7794 13.5 13.3978 13.5 13C13.5 12.6022 13.658 12.2206 13.9393 11.9393C14.2206 11.658 14.6022 11.5 15 11.5Z"/></svg>
                            <span class="text-base">Barang Masuk</span>
                        </Link>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><path d="M6 0C4.69375 0 3.58125 0.834375 3.17188 2H2C0.896875 2 0 2.89688 0 4V14C0 15.1031 0.896875 16 2 16H10C11.1031 16 12 15.1031 12 14V4C12 2.89688 11.1031 2 10 2H8.82812C8.41875 0.834375 7.30625 0 6 0ZM6 2C6.26522 2 6.51957 2.10536 6.70711 2.29289C6.89464 2.48043 7 2.73478 7 3C7 3.26522 6.89464 3.51957 6.70711 3.70711C6.51957 3.89464 6.26522 4 6 4C5.73478 4 5.48043 3.89464 5.29289 3.70711C5.10536 3.51957 5 3.26522 5 3C5 2.73478 5.10536 2.48043 5.29289 2.29289C5.48043 2.10536 5.73478 2 6 2ZM2.25 8.5C2.25 8.30109 2.32902 8.11032 2.46967 7.96967C2.61032 7.82902 2.80109 7.75 3 7.75C3.19891 7.75 3.38968 7.82902 3.53033 7.96967C3.67098 8.11032 3.75 8.30109 3.75 8.5C3.75 8.69891 3.67098 8.88968 3.53033 9.03033C3.38968 9.17098 3.19891 9.25 3 9.25C2.80109 9.25 2.61032 9.17098 2.46967 9.03033C2.32902 8.88968 2.25 8.69891 2.25 8.5ZM5.5 8H9.5C9.775 8 10 8.225 10 8.5C10 8.775 9.775 9 9.5 9H5.5C5.225 9 5 8.775 5 8.5C5 8.225 5.225 8 5.5 8ZM2.25 11.5C2.25 11.3011 2.32902 11.1103 2.46967 10.9697C2.61032 10.829 2.80109 10.75 3 10.75C3.19891 10.75 3.38968 10.829 3.53033 10.9697C3.67098 11.1103 3.75 11.3011 3.75 11.5C3.75 11.6989 3.67098 11.8897 3.53033 12.0303C3.38968 12.171 3.19891 12.25 3 12.25C2.80109 12.25 2.61032 12.171 2.46967 12.0303C2.32902 11.8897 2.25 11.6989 2.25 11.5ZM5 11.5C5 11.225 5.225 11 5.5 11H9.5C9.775 11 10 11.225 10 11.5C10 11.775 9.775 12 9.5 12H5.5C5.225 12 5 11.775 5 11.5Z"/></svg>
                            <span class="text-base" v-if="user.role === 'operator'">Proses Permintaan</span>
                            <span class="text-base" v-if="user.role === 'approval'">Permintaan Barang</span>
                        </Link>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor"><path d="M1.5 0C0.671875 0 0 0.671875 0 1.5V11.5C0 12.3281 0.671875 13 1.5 13H2C2 14.6562 3.34375 16 5 16C6.65625 16 8 14.6562 8 13H12C12 14.6562 13.3438 16 15 16C16.6562 16 18 14.6562 18 13H19C19.5531 13 20 12.5531 20 12C20 11.4469 19.5531 11 19 11V9V8V7.41563C19 6.88438 18.7906 6.375 18.4156 6L16 3.58437C15.625 3.20937 15.1156 3 14.5844 3H13V1.5C13 0.671875 12.3281 0 11.5 0H1.5ZM13 5H14.5844L17 7.41563V8H13V5ZM3.5 13C3.5 12.6022 3.65804 12.2206 3.93934 11.9393C4.22064 11.658 4.60218 11.5 5 11.5C5.39782 11.5 5.77936 11.658 6.06066 11.9393C6.34196 12.2206 6.5 12.6022 6.5 13C6.5 13.3978 6.34196 13.7794 6.06066 14.0607C5.77936 14.342 5.39782 14.5 5 14.5C4.60218 14.5 4.22064 14.342 3.93934 14.0607C3.65804 13.7794 3.5 13.3978 3.5 13ZM15 11.5C15.3978 11.5 15.7794 11.658 16.0607 11.9393C16.342 12.2206 16.5 12.6022 16.5 13C16.5 13.3978 16.342 13.7794 16.0607 14.0607C15.7794 14.342 15.3978 14.5 15 14.5C14.6022 14.5 14.2206 14.342 13.9393 14.0607C13.658 13.7794 13.5 13.3978 13.5 13C13.5 12.6022 13.658 12.2206 13.9393 11.9393C14.2206 11.658 14.6022 11.5 15 11.5Z"/></svg>
                            <span class="text-base">Barang Keluar</span>
                        </Link>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Manajemen Stok</div>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><path d="M6 0C4.69375 0 3.58125 0.834375 3.17188 2H2C0.896875 2 0 2.89688 0 4V14C0 15.1031 0.896875 16 2 16H10C11.1031 16 12 15.1031 12 14V4C12 2.89688 11.1031 2 10 2H8.82812C8.41875 0.834375 7.30625 0 6 0ZM6 2C6.26522 2 6.51957 2.10536 6.70711 2.29289C6.89464 2.48043 7 2.73478 7 3C7 3.26522 6.89464 3.51957 6.70711 3.70711C6.51957 3.89464 6.26522 4 6 4C5.73478 4 5.48043 3.89464 5.29289 3.70711C5.10536 3.51957 5 3.26522 5 3C5 2.73478 5.10536 2.48043 5.29289 2.29289C5.48043 2.10536 5.73478 2 6 2ZM9.53125 8.53125L5.53125 12.5312C5.2375 12.825 4.7625 12.825 4.47188 12.5312L2.46875 10.5312C2.175 10.2375 2.175 9.7625 2.46875 9.47188C2.7625 9.18125 3.2375 9.17813 3.52813 9.47188L4.99687 10.9406L8.46875 7.46875C8.7625 7.175 9.2375 7.175 9.52812 7.46875C9.81875 7.7625 9.82187 8.2375 9.52812 8.52812L9.53125 8.53125Z"/></svg>
                            <span class="text-base">Stock Opname</span>
                        </Link>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="18" height="16" viewBox="0 0 18 16" fill="currentColor"><path d="M7.75 0H6.5C5.67188 0 5 0.671875 5 1.5V5C5 6.10312 5.89688 7 7 7H11C12.1031 7 13 6.10312 13 5V1.5C13 0.671875 12.3281 0 11.5 0H10.25V2.5C10.25 2.775 10.025 3 9.75 3H8.25C7.975 3 7.75 2.775 7.75 2.5V0ZM2 8C0.896875 8 0 8.89688 0 10V14C0 15.1031 0.896875 16 2 16H7C8.10312 16 9 15.1031 9 14V10C9 8.89688 8.10312 8 7 8H5.75V10.5C5.75 10.775 5.525 11 5.25 11H3.75C3.475 11 3.25 10.775 3.25 10.5V8H2ZM11 16H16C17.1031 16 18 15.1031 18 14V10C18 8.89688 17.1031 8 16 8H14.75V10.5C14.75 10.775 14.525 11 14.25 11H12.75C12.475 11 12.25 10.775 12.25 10.5V8H11C10.5312 8 10.1 8.15937 9.75937 8.43125C9.9125 8.75625 10 9.11875 10 9.5V14.5C10 14.8812 9.9125 15.2438 9.75937 15.5688C10.1 15.8406 10.5312 16 11 16Z"/></svg>
                            <span class="text-base">Barang Usang/Rusak</span>
                        </Link>
                    </div>

                    <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Laporan</div>
                        <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                            <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><path d="M2 0C0.896875 0 0 0.896875 0 2V14C0 15.1031 0.896875 16 2 16H10C11.1031 16 12 15.1031 12 14V5H8C7.44687 5 7 4.55313 7 4V0H2ZM8 0V4H12L8 0ZM3.5 8H8.5C8.775 8 9 8.225 9 8.5C9 8.775 8.775 9 8.5 9H3.5C3.225 9 3 8.775 3 8.5C3 8.225 3.225 8 3.5 8ZM3.5 10H8.5C8.775 10 9 10.225 9 10.5C9 10.775 8.775 11 8.5 11H3.5C3.225 11 3 10.775 3 10.5C3 10.225 3.225 10 3.5 10ZM3.5 12H8.5C8.775 12 9 12.225 9 12.5C9 12.775 8.775 13 8.5 13H3.5C3.225 13 3 12.775 3 12.5C3 12.225 3.225 12 3.5 12Z"/></svg>
                            <span class="text-base">Laporan</span>
                        </Link>
                    </div>
                </div>
                
                <div v-if="user.role === 'pemohon'">
                    <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 16C10.1217 16 12.1566 15.1571 13.6569 13.6569C15.1571 12.1566 16 10.1217 16 8C16 5.87827 15.1571 3.84344 13.6569 2.34315C12.1566 0.842855 10.1217 0 8 0C5.87827 0 3.84344 0.842855 2.34315 2.34315C0.842855 3.84344 0 5.87827 0 8C0 10.1217 0.842855 12.1566 2.34315 13.6569C3.84344 15.1571 5.87827 16 8 16ZM7.25 10.75V8.75H5.25C4.83437 8.75 4.5 8.41562 4.5 8C4.5 7.58437 4.83437 7.25 5.25 7.25H7.25V5.25C7.25 4.83437 7.58437 4.5 8 4.5C8.41562 4.5 8.75 4.83437 8.75 5.25V7.25H10.75C11.1656 7.25 11.5 7.58437 11.5 8C11.5 8.41562 11.1656 8.75 10.75 8.75H8.75V10.75C8.75 11.1656 8.41562 11.5 8 11.5C7.58437 11.5 7.25 11.1656 7.25 10.75Z" fill="currentColor"/></svg>
                        <span class="text-base">Ajukan Barang</span>
                    </Link>
                    <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.34375 2.34375L1.28125 1.28125C0.809375 0.809375 0 1.14375 0 1.80938V5.25C0 5.66563 0.334375 6 0.75 6H4.19063C4.85938 6 5.19375 5.19063 4.72188 4.71875L3.75938 3.75625C4.84375 2.67188 6.34375 2 8 2C11.3125 2 14 4.6875 14 8C14 11.3125 11.3125 14 8 14C6.725 14 5.54375 13.6031 4.57188 12.925C4.11875 12.6094 3.49687 12.7188 3.17812 13.1719C2.85938 13.625 2.97187 14.2469 3.425 14.5656C4.725 15.4688 6.30312 16 8 16C12.4187 16 16 12.4187 16 8C16 3.58125 12.4187 0 8 0C5.79063 0 3.79063 0.896875 2.34375 2.34375ZM8 4C7.58437 4 7.25 4.33437 7.25 4.75V8C7.25 8.2 7.32812 8.39062 7.46875 8.53125L9.71875 10.7812C10.0125 11.075 10.4875 11.075 10.7781 10.7812C11.0687 10.4875 11.0719 10.0125 10.7781 9.72188L8.74687 7.69063V4.75C8.74687 4.33437 8.4125 4 7.99687 4H8Z" fill="currentColor"/></svg>
                        <span class="text-base">Riwayat Permintaan</span>
                    </Link>
                </div>



            </nav>
        </aside>
        
        <div class="flex-1 lg:ml-64">
            <header class="bg-white border-b border-gray-200 shadow-sm fixed top-0 left-0 right-0 z-10 dark:bg-dark-card dark:border-dark-border">
                <div class="flex items-center justify-between px-6 py-4">
                    
                    <div class="flex items-center gap-3">
                        <label for="my-drawer-2" class="btn btn-ghost btn-circle drawer-button lg:hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </label>
                        <div class="hidden lg:flex items-center gap-3">
                            <div class="w-8 h-8 bg-polban-dark rounded-lg flex items-center justify-center">
                                <svg width="18" height="14" viewBox="0 0 18 14" fill="white"><path d="M0 13.3425V4.68271C0 3.96631 0.434766 3.32373 1.09922 3.0585L8.42461 0.12998C8.63242 0.0452148 8.86484 0.0452148 9.07539 0.12998L16.4008 3.0585C17.0652 3.32373 17.5 3.96904 17.5 4.68271V13.3425C17.5 13.7062 17.2074 13.9987 16.8438 13.9987H15.5312C15.1676 13.9987 14.875 13.7062 14.875 13.3425V6.12373C14.875 5.63975 14.484 5.24873 14 5.24873H3.5C3.01602 5.24873 2.625 5.63975 2.625 6.12373V13.3425C2.625 13.7062 2.33242 13.9987 1.96875 13.9987H0.65625C0.292578 13.9987 0 13.7062 0 13.3425ZM13.3438 13.9987H4.15625C3.79258 13.9987 3.5 13.7062 3.5 13.3425V11.8112H14V13.3425C14 13.7062 13.7074 13.9987 13.3438 13.9987ZM3.5 10.9362V9.18623H14V10.9362H3.5ZM3.5 8.31123V6.12373H14V8.31123H3.5Z"/></svg>
                            </div>
                            <div>
                                <h1 class="text-lg font-semibold text-polban-dark dark:text-white">{{ appName }}</h1>
                                <p class="text-xs text-gray-500 dark:text-dark-muted">Sistem Informasi Manajemen Gudang Pusat</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex-1 max-w-md mx-8 hidden md:block">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-dark-muted" viewBox="0 0 16 16" fill="currentColor"><path d="M13 6.5C13 7.93438 12.5344 9.25938 11.75 10.3344L15.7063 14.2937C16.0969 14.6844 16.0969 15.3188 15.7063 15.7094C15.3156 16.1 14.6812 16.1 14.2906 15.7094L10.3344 11.75C9.25938 12.5375 7.93438 13 6.5 13C2.90937 13 0 10.0906 0 6.5C0 2.90937 2.90937 0 6.5 0C10.0906 0 13 2.90937 13 6.5ZM6.5 11C7.09095 11 7.67611 10.8836 8.22208 10.6575C8.76804 10.4313 9.26412 10.0998 9.68198 9.68198C10.0998 9.26412 10.4313 8.76804 10.6575 8.22208C10.8836 7.67611 11 7.09095 11 6.5C11 5.90905 10.8836 5.32389 10.6575 4.77792C10.4313 4.23196 10.0998 3.73588 9.68198 3.31802C9.26412 2.90016 8.76804 2.56869 8.22208 2.34254C7.67611 2.1164 7.09095 2 6.5 2C5.90905 2 5.32389 2.1164 4.77792 2.34254C4.23196 2.56869 3.73588 2.90016 3.31802 3.31802C2.90016 3.73588 2.56869 4.23196 2.34254 4.77792C2.1164 5.32389 2 5.90905 2 6.5C2 7.09095 2.1164 7.67611 2.34254 8.22208C2.56869 8.76804 2.90016 9.26412 3.31802 9.68198C3.73588 10.0998 4.23196 10.4313 4.77792 10.6575C5.32389 10.8836 5.90905 11 6.5 11Z"/></svg>
                            <TextInput
                                type="text"
                                placeholder="Cari barang, kategori..."
                                class="w-full pl-10 pr-4 py-2.5"
                            />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button @click="toggleTheme" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg v-if="!isDark" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <svg v-else class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        </button>
                        
                        <button class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors dark:hover:bg-gray-700">
                            <svg width="18" height="20" viewBox="0 0 18 20" fill="#6B7280" class="dark:fill-dark-muted"><path d="M8.75023 0C8.05882 0 7.50023 0.558594 7.50023 1.25V2C4.64867 2.57813 2.50023 5.10156 2.50023 8.125V8.85938C2.50023 10.6953 1.82445 12.4687 0.605698 13.8437L0.316635 14.168C-0.0114899 14.5352 -0.0896149 15.0625 0.109604 15.5117C0.308823 15.9609 0.758041 16.25 1.25023 16.25H16.2502C16.7424 16.25 17.1877 15.9609 17.3909 15.5117C17.594 15.0625 17.5119 14.5352 17.1838 14.168L16.8948 13.8437C15.676 12.4687 15.0002 10.6992 15.0002 8.85938V8.125C15.0002 5.10156 12.8518 2.57813 10.0002 2V1.25C10.0002 0.558594 9.44163 0 8.75023 0ZM10.5198 19.2695C10.9885 18.8008 11.2502 18.1641 11.2502 17.5H8.75023H6.25023C6.25023 18.1641 6.51195 18.8008 6.9807 19.2695C7.44945 19.7383 8.08617 20 8.75023 20C9.41429 20 10.051 19.7383 10.5198 19.2695Z"/></svg>
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center text-white text-xs">3</span>
                        </button>
                        
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <div class="flex items-center gap-3 cursor-pointer">
                                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-semibold text-gray-500 dark:bg-gray-600 dark:text-gray-300">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-polban-dark dark:text-white">{{ user.name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-dark-muted capitalize">{{ user.role }}</div>
                                    </div>
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="#9CA3AF" class="dark:fill-dark-muted">
                                        <path d="M6.38213 11.1161C6.72393 11.4579 7.279 11.4579 7.6208 11.1161L12.8708 5.86611C13.2126 5.52432 13.2126 4.96924 12.8708 4.62744C12.529 4.28564 11.9739 4.28564 11.6321 4.62744L7.0001 9.25947L2.36807 4.63018C2.02627 4.28838 1.47119 4.28838 1.12939 4.63018C0.787598 4.97197 0.787598 5.52705 1.12939 5.86885L6.37939 11.1188L6.38213 11.1161Z"/>
                                    </svg>
                                </div>
                            </template>

                            <template #content>
                                <li>
                                    <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                </li>
                                <li>
                                    <DropdownLink :href="route('logout')" method="post" as="button" class="text-error">
                                        Log Out
                                    </DropdownLink>
                                </li>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <main class="pt-20 p-6 min-h-screen">
                <slot />
            </main>
        </div>
        
        <div class="lg:hidden">
            <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
            <div class="drawer-side z-30">
                <label for="my-drawer-2" class="drawer-overlay"></label> 
                <ul class="menu p-4 w-80 min-h-full bg-white dark:bg-dark-card text-base-content flex flex-col justify-between">
                    <div>
                        <div class="mb-6 px-4 text-2xl font-bold text-polban-dark dark:text-white">
                            SIMGP Polban
                        </div>

                        <Link :href="route('dashboard')" :class="{ 'bg-polban-light text-white': route().current('dashboard'), 'text-gray-700 hover:bg-gray-100 dark:text-dark-muted dark:hover:bg-gray-700/30': !route().current('dashboard') }" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" :class="{ 'fill-white': route().current('dashboard'), 'fill-current': !route().current('dashboard') }"><path d="M2 2C2 1.44687 1.55313 1 1 1C0.446875 1 0 1.44687 0 2V12.5C0 13.8813 1.11875 15 2.5 15H15C15.5531 15 16 14.5531 16 14C16 13.4469 15.5531 13 15 13H2.5C2.225 13 2 12.775 2 12.5V2ZM14.7063 4.70625C15.0969 4.31563 15.0969 3.68125 14.7063 3.29063C14.3156 2.9 13.6812 2.9 13.2906 3.29063L10 6.58437L8.20625 4.79063C7.81563 4.4 7.18125 4.4 6.79063 4.79063L3.29063 8.29062C2.9 8.68125 2.9 9.31563 3.29063 9.70625C3.68125 10.0969 4.31563 10.0969 4.70625 9.70625L7.5 6.91563L9.29375 8.70938C9.68437 9.1 10.3188 9.1 10.7094 8.70938L14.7094 4.70937L14.7063 4.70625Z" /></svg>
                            <span class="text-base">Dashboard</span>
                        </Link>

                        <div v-if="user.role === 'operator'">
                            <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Master Data</div>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M10.7812 1.22187L14.775 5.2625C16.4125 6.91875 16.4125 9.58125 14.775 11.2375L11.275 14.7781C10.9844 15.0719 10.5094 15.075 10.2156 14.7844C9.92187 14.4938 9.91875 14.0188 10.2094 13.725L13.7063 10.1844C14.7656 9.1125 14.7656 7.39062 13.7063 6.31875L9.71562 2.27813C9.425 1.98438 9.42813 1.50938 9.72188 1.21875C10.0156 0.928125 10.4906 0.93125 10.7812 1.225V1.22187ZM0 7.17188V2.5C0 1.67188 0.671875 1 1.5 1H6.17188C6.70312 1 7.2125 1.20938 7.5875 1.58438L12.8375 6.83437C13.6187 7.61562 13.6187 8.88125 12.8375 9.6625L8.66562 13.8344C7.88437 14.6156 6.61875 14.6156 5.8375 13.8344L0.5875 8.58438C0.209375 8.20938 0 7.70312 0 7.17188ZM4.5 4.5C4.5 4.23478 4.39464 3.98043 4.20711 3.79289C4.01957 3.60536 3.76522 3.5 3.5 3.5C3.23478 3.5 2.98043 3.60536 2.79289 3.79289C2.60536 3.98043 2.5 4.23478 2.5 4.5C2.5 4.76522 2.60536 5.01957 2.79289 5.20711C2.98043 5.39464 3.23478 5.5 3.5 5.5C3.76522 5.5 4.01957 5.39464 4.20711 5.20711C4.39464 5.01957 4.5 4.76522 4.5 4.5Z"/></svg>
                                    <span class="text-base">Kategori</span>
                                </Link>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M7.32812 0.179688C7.7625 0.0234375 8.2375 0.0234375 8.675 0.179688L14.675 2.32344C15.4688 2.60781 16 3.36094 16 4.20781V11.7953C16 12.6391 15.4688 13.3953 14.6719 13.6797L8.67188 15.8234C8.2375 15.9797 7.7625 15.9797 7.325 15.8234L1.325 13.6797C0.53125 13.3953 0 12.6422 0 11.7953V4.20781C0 3.36406 0.53125 2.60781 1.32812 2.32344L7.32812 0.179688ZM8 2.06406L2.57188 4.00156L8 5.93906L13.4281 4.00156L8 2.06406ZM9 13.5828L14 11.7984V5.92344L9 7.70781V13.5828Z"/></svg>
                                    <span class="text-base">Satuan</span>
                                </Link>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M1.25 1.5C0.834375 1.5 0.5 1.83437 0.5 2.25V3.75C0.5 4.16563 0.834375 4.5 1.25 4.5H2.75C3.16563 4.5 3.5 4.16563 3.5 3.75V2.25C3.5 1.83437 3.16563 1.5 2.75 1.5H1.25ZM6 2C5.44687 2 5 2.44687 5 3C5 3.55313 5.44687 4 6 4H15C15.5531 4 16 3.55313 16 3C16 2.44687 15.5531 2 15 2H6ZM6 7C5.44687 7 5 7.44687 5 8C5 8.55313 5.44687 9 6 9H15C15.5531 9 16 8.55313 16 8C16 7.44687 15.5531 7 15 7H6ZM6 12C5.44687 12 5 12.4469 5 13C5 13.5531 5.44687 14 6 14H15C15.5531 14 16 13.5531 16 13C16 12.4469 15.5531 12 15 12H6ZM0.5 7.25V8.75C0.5 9.16563 0.834375 9.5 1.25 9.5H2.75C3.16563 9.5 3.5 9.16563 3.5 8.75V7.25C3.5 6.83437 3.16563 6.5 2.75 6.5H1.25C0.834375 6.5 0.5 6.83437 0.5 7.25ZM1.25 11.5C0.834375 11.5 0.5 11.8344 0.5 12.25V13.75C0.5 14.1656 0.834375 14.5 1.25 14.5H2.75C3.16563 14.5 3.5 14.1656 3.5 13.75V12.25C3.5 11.8344 3.16563 11.5 2.75 11.5H1.25Z"/></svg>
                                    <span class="text-base">Barang</span>
                                </Link>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Manajemen Akun</div>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M3.6 0C4.13043 0 4.63914 0.230468 5.01421 0.640704C5.38929 1.05094 5.6 1.60734 5.6 2.1875C5.6 2.76766 5.38929 3.32406 5.01421 3.7343C4.63914 4.14453 4.13043 4.375 3.6 4.375C3.06957 4.375 2.56086 4.14453 2.18579 3.7343C1.81071 3.32406 1.6 2.76766 1.6 2.1875C1.6 1.60734 1.81071 1.05094 2.18579 0.640704C2.56086 0.230468 3.06957 0 3.6 0ZM12.8 0C13.3304 0 13.8391 0.230468 14.2142 0.640704C14.5893 1.05094 14.8 1.60734 14.8 2.1875C14.8 2.76766 14.5893 3.32406 14.2142 3.7343C13.8391 4.14453 13.3304 4.375 12.8 4.375C12.2696 4.375 11.7609 4.14453 11.3858 3.7343C11.0107 3.32406 10.8 2.76766 10.8 2.1875C10.8 1.60734 11.0107 1.05094 11.3858 0.640704C11.7609 0.230468 12.2696 0 12.8 0ZM0 8.16758C0 6.55703 1.195 5.25 2.6675 5.25H3.735C4.1325 5.25 4.51 5.3457 4.85 5.51523C4.8175 5.71211 4.8025 5.91719 4.8025 6.125C4.8025 7.16953 5.2225 8.10742 5.885 8.75C5.88 8.75 5.875 8.75 5.8675 8.75H0.5325C0.24 8.75 0 8.4875 0 8.16758ZM10.1325 8.75C10.1275 8.75 10.1225 8.75 10.115 8.75C10.78 8.10742 11.1975 7.16953 11.1975 6.125C11.1975 5.91719 11.18 5.71484 11.15 5.51523C11.49 5.34297 11.8675 5.25 12.265 5.25H13.3325C14.805 5.25 16 6.55703 16 8.16758C16 8.49023 15.76 8.75 15.4675 8.75H10.1325ZM5.6 6.125C5.6 5.42881 5.85286 4.76113 6.30294 4.26884C6.75303 3.77656 7.36348 3.5 8 3.5C8.63652 3.5 9.24697 3.77656 9.69706 4.26884C10.1471 4.76113 10.4 5.42881 10.4 6.125C10.4 6.82119 10.1471 7.48887 9.69706 7.98116C9.24697 8.47344 8.63652 8.75 8 8.75C7.36348 8.75 6.75303 8.47344 6.30294 7.98116C5.85286 7.48887 5.6 6.82119 5.6 6.125ZM3.2 13.2699C3.2 11.2574 4.6925 9.625 6.5325 9.625H9.4675C11.3075 9.625 12.8 11.2574 12.8 13.2699C12.8 13.6719 12.5025 14 12.1325 14H3.8675C3.5 14 3.2 13.6746 3.2 13.2699Z"/></svg>
                                    <span class="text-base">Pemohon</span>
                                </Link>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M3.6 0C4.13043 0 4.63914 0.230468 5.01421 0.640704C5.38929 1.05094 5.6 1.60734 5.6 2.1875C5.6 2.76766 5.38929 3.32406 5.01421 3.7343C4.63914 4.14453 4.13043 4.375 3.6 4.375C3.06957 4.375 2.56086 4.14453 2.18579 3.7343C1.81071 3.32406 1.6 2.76766 1.6 2.1875C1.6 1.60734 1.81071 1.05094 2.18579 0.640704C2.56086 0.230468 3.06957 0 3.6 0ZM12.8 0C13.3304 0 13.8391 0.230468 14.2142 0.640704C14.5893 1.05094 14.8 1.60734 14.8 2.1875C14.8 2.76766 14.5893 3.32406 14.2142 3.7343C13.8391 4.14453 13.3304 4.375 12.8 4.375C12.2696 4.375 11.7609 4.14453 11.3858 3.7343C11.0107 3.32406 10.8 2.76766 10.8 2.1875C10.8 1.60734 11.0107 1.05094 11.3858 0.640704C11.7609 0.230468 12.2696 0 12.8 0ZM0 8.16758C0 6.55703 1.195 5.25 2.6675 5.25H3.735C4.1325 5.25 4.51 5.3457 4.85 5.51523C4.8175 5.71211 4.8025 5.91719 4.8025 6.125C4.8025 7.16953 5.2225 8.10742 5.885 8.75C5.88 8.75 5.875 8.75 5.8675 8.75H0.5325C0.24 8.75 0 8.4875 0 8.16758ZM10.1325 8.75C10.1275 8.75 10.1225 8.75 10.115 8.75C10.78 8.10742 11.1975 7.16953 11.1975 6.125C11.1975 5.91719 11.18 5.71484 11.15 5.51523C11.49 5.34297 11.8675 5.25 12.265 5.25H13.3325C14.805 5.25 16 6.55703 16 8.16758C16 8.49023 15.76 8.75 15.4675 8.75H10.1325ZM5.6 6.125C5.6 5.42881 5.85286 4.76113 6.30294 4.26884C6.75303 3.77656 7.36348 3.5 8 3.5C8.63652 3.5 9.24697 3.77656 9.69706 4.26884C10.1471 4.76113 10.4 5.42881 10.4 6.125C10.4 6.82119 10.1471 7.48887 9.69706 7.98116C9.24697 8.47344 8.63652 8.75 8 8.75C7.36348 8.75 6.75303 8.47344 6.30294 7.98116C5.85286 7.48887 5.6 6.82119 5.6 6.125ZM3.2 13.2699C3.2 11.2574 4.6925 9.625 6.5325 9.625H9.4675C11.3075 9.625 12.8 11.2574 12.8 13.2699C12.8 13.6719 12.5025 14 12.1325 14H3.8675C3.5 14 3.2 13.6746 3.2 13.2699Z"/></svg>
                                    <span class="text-base">Approval</span>
                                </Link>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Transaksi</div>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor"><path d="M1.5 0C0.671875 0 0 0.671875 0 1.5V11.5C0 12.3281 0.671875 13 1.5 13H2C2 14.6562 3.34375 16 5 16C6.65625 16 8 14.6562 8 13H12C12 14.6562 13.3438 16 15 16C16.6562 16 18 14.6562 18 13H19C19.5531 13 20 12.5531 20 12C20 11.4469 19.5531 11 19 11V9V8V7.41563C19 6.88438 18.7906 6.375 18.4156 6L16 3.58437C15.625 3.20937 15.1156 3 14.5844 3H13V1.5C13 0.671875 12.3281 0 11.5 0H1.5ZM13 5H14.5844L17 7.41563V8H13V5ZM3.5 13C3.5 12.6022 3.65804 12.2206 3.93934 11.9393C4.22064 11.658 4.60218 11.5 5 11.5C5.39782 11.5 5.77936 11.658 6.06066 11.9393C6.34196 12.2206 6.5 12.6022 6.5 13C6.5 13.3978 6.34196 13.7794 6.06066 14.0607C5.77936 14.342 5.39782 14.5 5 14.5C4.60218 14.5 4.22064 14.342 3.93934 14.0607C3.65804 13.7794 3.5 13.3978 3.5 13ZM15 11.5C15.3978 11.5 15.7794 11.658 16.0607 11.9393C16.342 12.2206 16.5 12.6022 16.5 13C16.5 13.3978 16.342 13.7794 16.0607 14.0607C15.7794 14.342 15.3978 14.5 15 14.5C14.6022 14.5 14.2206 14.342 13.9393 14.0607C13.658 13.7794 13.5 13.3978 13.5 13C13.5 12.6022 13.658 12.2206 13.9393 11.9393C14.2206 11.658 14.6022 11.5 15 11.5Z"/></svg>
                                    <span class="text-base">Barang Masuk</span>
                                </Link>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><path d="M6 0C4.69375 0 3.58125 0.834375 3.17188 2H2C0.896875 2 0 2.89688 0 4V14C0 15.1031 0.896875 16 2 16H10C11.1031 16 12 15.1031 12 14V4C12 2.89688 11.1031 2 10 2H8.82812C8.41875 0.834375 7.30625 0 6 0ZM6 2C6.26522 2 6.51957 2.10536 6.70711 2.29289C6.89464 2.48043 7 2.73478 7 3C7 3.26522 6.89464 3.51957 6.70711 3.70711C6.51957 3.89464 6.26522 4 6 4C5.73478 4 5.48043 3.89464 5.29289 3.70711C5.10536 3.51957 5 3.26522 5 3C5 2.73478 5.10536 2.48043 5.29289 2.29289C5.48043 2.10536 5.73478 2 6 2ZM2.25 8.5C2.25 8.30109 2.32902 8.11032 2.46967 7.96967C2.61032 7.82902 2.80109 7.75 3 7.75C3.19891 7.75 3.38968 7.82902 3.53033 7.96967C3.67098 8.11032 3.75 8.30109 3.75 8.5C3.75 8.69891 3.67098 8.88968 3.53033 9.03033C3.38968 9.17098 3.19891 9.25 3 9.25C2.80109 9.25 2.61032 9.17098 2.46967 9.03033C2.32902 8.88968 2.25 8.69891 2.25 8.5ZM5.5 8H9.5C9.775 8 10 8.225 10 8.5C10 8.775 9.775 9 9.5 9H5.5C5.225 9 5 8.775 5 8.5C5 8.225 5.225 8 5.5 8ZM2.25 11.5C2.25 11.3011 2.32902 11.1103 2.46967 10.9697C2.61032 10.829 2.80109 10.75 3 10.75C3.19891 10.75 3.38968 10.829 3.53033 10.9697C3.67098 11.1103 3.75 11.3011 3.75 11.5C3.75 11.6989 3.67098 11.8897 3.53033 12.0303C3.38968 12.171 3.19891 12.25 3 12.25C2.80109 12.25 2.61032 12.171 2.46967 12.0303C2.32902 11.8897 2.25 11.6989 2.25 11.5ZM5 11.5C5 11.225 5.225 11 5.5 11H9.5C9.775 11 10 11.225 10 11.5C10 11.775 9.775 12 9.5 12H5.5C5.225 12 5 11.775 5 11.5Z"/></svg>
                                    <span class="text-base">Proses Permintaan</span>
                                </Link>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Manajemen Stok</div>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><path d="M6 0C4.69375 0 3.58125 0.834375 3.17188 2H2C0.896875 2 0 2.89688 0 4V14C0 15.1031 0.896875 16 2 16H10C11.1031 16 12 15.1031 12 14V4C12 2.89688 11.1031 2 10 2H8.82812C8.41875 0.834375 7.30625 0 6 0ZM6 2C6.26522 2 6.51957 2.10536 6.70711 2.29289C6.89464 2.48043 7 2.73478 7 3C7 3.26522 6.89464 3.51957 6.70711 3.70711C6.51957 3.89464 6.26522 4 6 4C5.73478 4 5.48043 3.89464 5.29289 3.70711C5.10536 3.51957 5 3.26522 5 3C5 2.73478 5.10536 2.48043 5.29289 2.29289C5.48043 2.10536 5.73478 2 6 2ZM9.53125 8.53125L5.53125 12.5312C5.2375 12.825 4.7625 12.825 4.47188 12.5312L2.46875 10.5312C2.175 10.2375 2.175 9.7625 2.46875 9.47188C2.7625 9.18125 3.2375 9.17813 3.52813 9.47188L4.99687 10.9406L8.46875 7.46875C8.7625 7.175 9.2375 7.175 9.52812 7.46875C9.81875 7.7625 9.82187 8.2375 9.52812 8.52812L9.53125 8.53125Z"/></svg>
                                    <span class="text-base">Stock Opname</span>
                                </Link>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="18" height="16" viewBox="0 0 18 16" fill="currentColor"><path d="M7.75 0H6.5C5.67188 0 5 0.671875 5 1.5V5C5 6.10312 5.89688 7 7 7H11C12.1031 7 13 6.10312 13 5V1.5C13 0.671875 12.3281 0 11.5 0H10.25V2.5C10.25 2.775 10.025 3 9.75 3H8.25C7.975 3 7.75 2.775 7.75 2.5V0ZM2 8C0.896875 8 0 8.89688 0 10V14C0 15.1031 0.896875 16 2 16H7C8.10312 16 9 15.1031 9 14V10C9 8.89688 8.10312 8 7 8H5.75V10.5C5.75 10.775 5.525 11 5.25 11H3.75C3.475 11 3.25 10.775 3.25 10.5V8H2ZM11 16H16C17.1031 16 18 15.1031 18 14V10C18 8.89688 17.1031 8 16 8H14.75V10.5C14.75 10.775 14.525 11 14.25 11H12.75C12.475 11 12.25 10.775 12.25 10.5V8H11C10.5312 8 10.1 8.15937 9.75937 8.43125C9.9125 8.75625 10 9.11875 10 9.5V14.5C10 14.8812 9.9125 15.2438 9.75937 15.5688C10.1 15.8406 10.5312 16 11 16Z"/></svg>
                                    <span class="text-base">Barang Usang/Rusak</span>
                                </Link>
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-dark-border">
                                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 px-4 dark:text-dark-muted">Laporan</div>
                                <Link href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 w-full transition-colors dark:text-dark-muted dark:hover:bg-gray-700/30">
                                    <svg width="12" height="16" viewBox="0 0 12 16" fill="currentColor"><path d="M2 0C0.896875 0 0 0.896875 0 2V14C0 15.1031 0.896875 16 2 16H10C11.1031 16 12 15.1031 12 14V5H8C7.44687 5 7 4.55313 7 4V0H2ZM8 0V4H12L8 0ZM3.5 8H8.5C8.775 8 9 8.225 9 8.5C9 8.775 8.775 9 8.5 9H3.5C3.225 9 3 8.775 3 8.5C3 8.225 3.225 8 3.5 8ZM3.5 10H8.5C8.775 10 9 10.225 9 10.5C9 10.775 8.775 11 8.5 11H3.5C3.225 11 3 10.775 3 10.5C3 10.225 3.225 10 3.5 10ZM3.5 12H8.5C8.775 12 9 12.225 9 12.5C9 12.775 8.775 13 8.5 13H3.5C3.225 13 3 12.775 3 12.5C3 12.225 3.225 12 3.5 12Z"/></svg>
                                    <span class="text-base">Laporan</span>
                                </Link>
                            </div>
                        </div>

                        <div v-if="user.role === 'approver'">
                            </div>

                        <div v-if="user.role === 'pemohon'">
                            </div>
                    </div>

                    <div class="border-t border-base-300 pt-4">
                        <div class="flex items-center gap-3 px-2 mb-2">
                            <div class="avatar placeholder">
                                <div class="bg-neutral-focus text-neutral-content rounded-full w-10">
                                    <span class="text-xs">{{ user.name.charAt(0) }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="font-bold text-sm">{{ user.name }}</p>
                                <p class="text-xs opacity-70 capitalize">{{ user.role }}</p>
                            </div>
                        </div>
                        <li>
                            <Link :href="route('logout')" method="post" as="button" class="text-error">
                                🚪 Logout
                            </Link>
                        </li>
                    </div>
                </ul>
            </div>
        </div>

    </div>
</template>