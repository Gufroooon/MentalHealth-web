/**
 * Konfigurasi Tailwind untuk class pada Blade dan token visual aplikasi.
 * Content menentukan file yang dipindai, sedangkan theme menambahkan font,
 * warna vector sinyal, dan radius komponen yang dipakai UI NARA.
 */
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                nara: {
                    50: '#F4F7FB',
                    100: '#E8EFF6',
                    200: '#D2E1EF',
                    300: '#A9C4E0',
                    400: '#7B9FC7',
                    500: '#557FA8',
                    600: '#436B92',
                    700: '#345577',
                    800: '#263F59',
                    900: '#1B2D40',
                    950: '#111D2A',
                    sand: '#F8F6F0',
                    cream: '#FFFDF9',
                    coral: '#D97B66',
                    'coral-soft': '#F6D2C8',
                    'coral-deep': '#B85844',
                    amber: '#E09A3E',
                    'amber-soft': '#FDE8C5',
                    teal: '#0D9488',
                    'teal-soft': '#CCFBF1',
                    ink: '#1E293B',
                },
                sage: {
                    50: '#F4F7F5',
                    100: '#E6ECE8',
                    200: '#CFDDD3',
                    300: '#AAC4B2',
                    400: '#7EA68B',
                    500: '#5A8968',
                    600: '#436E50',
                    700: '#2D6A4F',
                    800: '#1B4332',
                    900: '#0B2317',
                },
                signal: {
                    mind: '#E09A3E',
                    body: '#0D9488',
                    social: '#8B5CF6',
                    life: '#F43F5E',
                }
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
            boxShadow: {
                'card': '0 4px 20px -2px rgba(30, 41, 59, 0.05), 0 2px 6px -1px rgba(30, 41, 59, 0.03)',
                'card-hover': '0 12px 30px -4px rgba(30, 41, 59, 0.08), 0 4px 12px -2px rgba(30, 41, 59, 0.04)',
                'hero': '0 20px 50px -10px rgba(33, 54, 74, 0.25)',
                'pill': '0 4px 14px rgba(0, 0, 0, 0.08)',
            }
        },
    },

    plugins: [forms],
};
