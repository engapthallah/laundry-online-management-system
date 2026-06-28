import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/customer-order.css',
                'resources/js/customer-order.js'
            ],
            refresh: true,
        }),
    ],
});
