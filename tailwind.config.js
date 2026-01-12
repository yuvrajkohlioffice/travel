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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],

    // Safelist dynamic classes to prevent purge from removing them
    safelist: [
        'bg-lime-500', 'bg-green-500', 'bg-indigo-500', 'bg-purple-500',
        'bg-teal-500', 'bg-gray-500', 'bg-amber-500', 'bg-red-500',
        'bg-gray-300', 'text-white','bg-fuchsia-500','bg-purple-600','bg-purple-700'
    ],
};
