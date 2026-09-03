/**
 * Pipeline PostCSS frontend. Tailwind menghasilkan utility class dan
 * Autoprefixer menambahkan prefix browser pada CSS hasil build.
 */
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
