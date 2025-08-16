// vite.config.js
import { defineConfig } from "file:///C:/laragon/www/Integrador-III-webApp/node_modules/vite/dist/node/index.js";
import laravel from "file:///C:/laragon/www/Integrador-III-webApp/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///C:/laragon/www/Integrador-III-webApp/node_modules/@vitejs/plugin-vue/dist/index.mjs";
var vite_config_default = defineConfig({
  plugins: [
    laravel({
      input: [
        "resources/sass/app.scss",
        "resources/js/app.js",
        "resources/js/search.js",
        "resources/js/agenda.js",
        "resources/js/medicAgenda.js",
        "resources/js/roomAgenda.js",
        "resources/js/dailyAgenda.js",
        "resources/js/schedule.js",
        "resources/js/assistantSchedule.js",
        "resources/js/assistantScheduleMonthly.js",
        "resources/js/clinicSchedule.js",
        "resources/js/clinicScheduleMonthly.js",
        "resources/js/patients.js",
        "resources/js/reservations.js",
        "resources/js/registerOffice.js",
        "resources/js/onBoardingMedicsTours.js"
      ],
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  resolve: {
    alias: {
      "@": "/resources/js",
      "vue": "vue/dist/vue.esm-bundler.js"
    }
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJDOlxcXFxsYXJhZ29uXFxcXHd3d1xcXFxJbnRlZ3JhZG9yLUlJSS13ZWJBcHBcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIkM6XFxcXGxhcmFnb25cXFxcd3d3XFxcXEludGVncmFkb3ItSUlJLXdlYkFwcFxcXFx2aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vQzovbGFyYWdvbi93d3cvSW50ZWdyYWRvci1JSUktd2ViQXBwL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSAndml0ZSc7XG5pbXBvcnQgbGFyYXZlbCBmcm9tICdsYXJhdmVsLXZpdGUtcGx1Z2luJztcbmltcG9ydCB2dWUgZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlJztcblxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKHtcbiAgICBwbHVnaW5zOiBbXG4gICAgICAgIGxhcmF2ZWwoe1xuICAgICAgICAgICAgaW5wdXQ6IFtcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL3Nhc3MvYXBwLnNjc3MnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvYXBwLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3NlYXJjaC5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9hZ2VuZGEuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvbWVkaWNBZ2VuZGEuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvcm9vbUFnZW5kYS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9kYWlseUFnZW5kYS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9zY2hlZHVsZS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9hc3Npc3RhbnRTY2hlZHVsZS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9hc3Npc3RhbnRTY2hlZHVsZU1vbnRobHkuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvY2xpbmljU2NoZWR1bGUuanMnLFxuICAgICAgICAgICAgICAgICdyZXNvdXJjZXMvanMvY2xpbmljU2NoZWR1bGVNb250aGx5LmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3BhdGllbnRzLmpzJyxcbiAgICAgICAgICAgICAgICAncmVzb3VyY2VzL2pzL3Jlc2VydmF0aW9ucy5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9yZWdpc3Rlck9mZmljZS5qcycsXG4gICAgICAgICAgICAgICAgJ3Jlc291cmNlcy9qcy9vbkJvYXJkaW5nTWVkaWNzVG91cnMuanMnLFxuICAgICAgICAgICAgXSxcbiAgICAgICAgICAgIHJlZnJlc2g6IHRydWUsXG4gICAgICAgIH0pLFxuICAgICAgICB2dWUoe1xuICAgICAgICAgICAgdGVtcGxhdGU6IHtcbiAgICAgICAgICAgICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgICAgICAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgICAgICAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcbiAgICAgICAgICAgICAgICB9LFxuICAgICAgICAgICAgfSxcbiAgICAgICAgfSksXG4gICAgXSxcbiAgICByZXNvbHZlOiB7XG4gICAgICAgIGFsaWFzOiB7XG4gICAgICAgICAgICAnQCc6ICcvcmVzb3VyY2VzL2pzJyxcbiAgICAgICAgICAgICd2dWUnOiAndnVlL2Rpc3QvdnVlLmVzbS1idW5kbGVyLmpzJ1xuICAgICAgICB9XG4gICAgfSxcbn0pOyJdLAogICJtYXBwaW5ncyI6ICI7QUFBc1MsU0FBUyxvQkFBb0I7QUFDblUsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sU0FBUztBQUVoQixJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUN4QixTQUFTO0FBQUEsSUFDTCxRQUFRO0FBQUEsTUFDSixPQUFPO0FBQUEsUUFDSDtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0o7QUFBQSxNQUNBLFNBQVM7QUFBQSxJQUNiLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNBLFVBQVU7QUFBQSxRQUNOLG9CQUFvQjtBQUFBLFVBQ2hCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ3JCO0FBQUEsTUFDSjtBQUFBLElBQ0osQ0FBQztBQUFBLEVBQ0w7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNMLE9BQU87QUFBQSxNQUNILEtBQUs7QUFBQSxNQUNMLE9BQU87QUFBQSxJQUNYO0FBQUEsRUFDSjtBQUNKLENBQUM7IiwKICAibmFtZXMiOiBbXQp9Cg==
