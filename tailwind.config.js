import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Paleta 60-30-10:
 *  60% neutros  -> slate (fondo de página: slate-50 / slate-950)
 *  30% superficies -> blanco / slate-900 (cards, sidebar, topbar)
 *  10% acento   -> blue (acciones primarias, foco, activo)
 *  Semánticos   -> emerald (éxito/facturado), rose (error/anulado), slate (neutro/borrador)
 */
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.ts',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
