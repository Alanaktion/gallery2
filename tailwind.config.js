const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors');

module.exports = {
    purge: [
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: 'media',
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.trueGray,
            indigo: colors.indigo,
        },
        extend: {
            //
        },
    },
    plugins: [require('@tailwindcss/forms')],
};
