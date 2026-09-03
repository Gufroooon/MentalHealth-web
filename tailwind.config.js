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
                    mind: '#F59E0B',
                    body: '#06B6D4',
                    social: '#8B5CF6',
                    life: '#F43F5E',
                }
            },
            borderRadius: {
                '2xl': '1rem',
                '3xl': '1.5rem',
            }
        },
    },

    plugins: [forms],
};
