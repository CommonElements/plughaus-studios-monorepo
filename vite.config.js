import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';
import sass from 'sass';

export default defineConfig({
  plugins: [vue()],
  
  // Build configuration for WordPress plugins
  build: {
    lib: {
      entry: {
        'property-management-admin': resolve(__dirname, 'app/public/wp-content/plugins/vireo-property-management/assets/js/admin.js'),
        'property-management-public': resolve(__dirname, 'app/public/wp-content/plugins/vireo-property-management/assets/js/public.js'),
        'studio-theme': resolve(__dirname, 'app/public/wp-content/themes/vireo-designs/assets/js/main.js'),
      },
      formats: ['iife'],
      name: 'PlugHausStudios'
    },
    rollupOptions: {
      external: ['jquery', 'wp', 'lodash'],
      output: {
        globals: {
          jquery: 'jQuery',
          wp: 'wp',
          lodash: '_'
        },
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.css')) {
            return 'css/[name].[ext]';
          }
          return 'assets/[name].[ext]';
        },
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
      }
    },
    outDir: 'dist',
    emptyOutDir: false,
  },

  // Development server configuration
  server: {
    proxy: {
      // Proxy WordPress requests to Local by Flywheel
      '/wp-admin': {
        target: 'http://vireo-designs-the-beginning-is-finished.local',
        changeOrigin: true,
      },
      '/wp-json': {
        target: 'http://vireo-designs-the-beginning-is-finished.local',
        changeOrigin: true,
      },
    },
  },

  // CSS configuration
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "./app/public/wp-content/themes/vireo-designs/assets/scss/variables.scss";`
      }
    }
  },

  // Path resolution
  resolve: {
    alias: {
      '@': resolve(__dirname, 'app/public/wp-content'),
      '@plugins': resolve(__dirname, 'app/public/wp-content/plugins'),
      '@themes': resolve(__dirname, 'app/public/wp-content/themes'),
      '@shared': resolve(__dirname, 'packages/shared'),
    }
  },

  // Define global constants
  define: {
    __VUE_OPTIONS_API__: true,
    __VUE_PROD_DEVTOOLS__: false,
  },
});