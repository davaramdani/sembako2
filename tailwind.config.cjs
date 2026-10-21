/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/{layouts,shop,components,auth,cart,checkout,orders}/**/*.blade.php',
        './resources/js/**/*.js',
        './public/js/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: { sans: ['Plus Jakarta Sans', 'Segoe UI', 'sans-serif'] },
            colors: { brand: { 50: '#f0f9f2', 100: '#ddf2e2', 600: '#16803d', 700: '#116530', 900: '#123c29' } },
            boxShadow: { card: '0 2px 8px rgba(20, 50, 30, 0.04)' },
        },
    },
    plugins: [],
};
