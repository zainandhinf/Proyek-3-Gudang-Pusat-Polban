import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from 'daisyui';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'polban-dark': '#1D3557',
                'polban-medium': '#457B9D',
                'polban-light': '#00B4D8',
                // Dark mode variants
                'dark-bg': '#0f172a',
                'dark-card': '#1e293b',
                'dark-border': '#334155',
                'dark-text': '#e2e8f0',
                'dark-muted': '#94a3b8'
            }
        },
    },

    plugins: [
        forms, 
        daisyui,
    ],
    daisyui: {
        darkTheme: false,
    },
};
