const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */


mix.js('resources/js/app.js', 'public/js/app.js')
   .ts(['resources/js/pages/application/create.ts'], 'public/js/application/create.js')
   .ts(['resources/js/pages/login.ts'], 'public/js/login.js')
   .ts(['resources/js/pages/home/navigation.ts'], 'public/js/navigation.js')
   .sass('resources/sass/pages/login.scss', 'public/css/login.css')
   .sass('resources/sass/pages/application/create.scss', 'public/css/application/create.css')
   .sass('resources/sass/pages/home/navigation.scss', 'public/css/navigation.css')
   .sourceMaps()
   .version();


