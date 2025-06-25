import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/search.js',
                'resources/js/agenda.js',
                'resources/js/medicAgenda.js',
                'resources/js/roomAgenda.js',
                'resources/js/dailyAgenda.js',
                'resources/js/schedule.js',
                'resources/js/assistantSchedule.js',
                'resources/js/assistantScheduleMonthly.js',
                'resources/js/clinicSchedule.js',
                'resources/js/clinicScheduleMonthly.js',
                'resources/js/patients.js',
                'resources/js/reservations.js',
                'resources/js/registerOffice.js',
                'resources/js/onBoardingMedicsTours.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            'vue': 'vue/dist/vue.esm-bundler.js'
        }
    },
});