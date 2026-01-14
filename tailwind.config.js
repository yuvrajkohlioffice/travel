import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Added 'Inter' for the landing page, kept 'Figtree' for dashboard consistency
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Added the custom brand colors from the landing page design
            colors: {
                brand: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    900: '#1e3a8a',
                }
            }
        },
    },

    plugins: [forms, typography],

    // Your existing safelist
    safelist: [
        'bg-lime-500', 'bg-green-500', 'bg-indigo-500', 'bg-purple-500',
        'bg-teal-500', 'bg-gray-500', 'bg-amber-500', 'bg-red-500',
        'bg-gray-300', 'text-white', 'bg-fuchsia-500', 'bg-purple-600', 'bg-purple-700'
    ],
};