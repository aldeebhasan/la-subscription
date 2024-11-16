import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: ['./resources/**/*.{js,ts,jsx,tsx,vue,blade.php}'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"Cerebri Sans"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
