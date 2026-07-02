import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
    // بارگذاری متغیرهای محیطی (در صورت نیاز به استفاده در پیکربندی build)
    loadEnv(mode, process.cwd(), '');

    const isProduction = mode === 'production';

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/js/dashboard.js',
                ],
                refresh: true,
            }),
            // پلاگین Tailwind CSS 4 — برای کامپایل @import 'tailwindcss' لازم است
            tailwindcss(),
        ],
        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources/js'),
                '@css': resolve(__dirname, 'resources/css'),
                '@animations': resolve(__dirname, 'resources/js/animations'),
                '@components': resolve(__dirname, 'resources/js/components'),
            },
        },
        server: {
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
        build: {
            // code splitting — کتابخانه‌های vendor از node_modules جدا chunk می‌شوند
            rollupOptions: {
                output: {
                    manualChunks(id) {
                        if (id.includes('node_modules')) {
                            return 'vendor';
                        }
                    },
                },
            },
            // بهینه‌سازی حجم؛ در production کنسول‌لاگ‌ها حذف می‌شوند (نیازمند terser)
            minify: isProduction ? 'terser' : false,
            terserOptions: {
                compress: {
                    drop_console: isProduction,
                },
            },
            // chunk بزرگ‌تر از 500KB هشدار بده
            chunkSizeWarningLimit: 500,
        },
        // بهینه‌سازی CSS — sourcemap فقط در حالت development
        css: {
            devSourcemap: !isProduction,
        },
    };
});
