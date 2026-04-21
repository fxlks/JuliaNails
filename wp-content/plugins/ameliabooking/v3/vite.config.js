import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import Inspect from 'vite-plugin-inspect'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'
import { useDynamicPublicPath } from 'vite-plugin-dynamic-publicpath'
import fs from 'fs'

const path = require('path')

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  // Different font paths for dev vs production
  // Dev: Full URL to Vite dev server (CSS is loaded in WordPress context, so relative paths won't work)
  // Prod: Fonts are copied to public/assets/fonts/, CSS is in public/assets/, so use ./fonts
  const fontPath =
    mode === 'development'
      ? 'http://localhost:3000/src/assets/scss/common/icon-fonts/fonts'
      : './fonts'

  // Plugin to copy icon fonts to public/assets/fonts/ during build
  function copyIconFonts() {
    return {
      name: 'copy-icon-fonts',
      writeBundle() {
        const srcDir = path.resolve(
          __dirname,
          'src/assets/scss/common/icon-fonts/fonts'
        )
        const destDir = path.resolve(__dirname, 'public/assets/fonts')

        // Create destination directory if it doesn't exist
        if (!fs.existsSync(destDir)) {
          fs.mkdirSync(destDir, { recursive: true })
        }

        // Copy all font files
        const files = fs.readdirSync(srcDir)
        files.forEach((file) => {
          const srcFile = path.join(srcDir, file)
          const destFile = path.join(destDir, file)
          fs.copyFileSync(srcFile, destFile)
        })

        console.log('✓ Icon fonts copied to public/assets/fonts/')
      },
    }
  }

  function collectAllCss() {
    return {
      name: 'collect-all-css',
      transform(code, id) {
        // Only process .vue files in development
        if (mode === 'development' && id.endsWith('.vue')) {
          // Inject a script that runs in the browser, not during build
          return {
            code:
              code +
              `
              if (typeof document !== 'undefined') {
                requestIdleCallback(() => {
                  const styleElements = Array.from(document.getElementsByTagName('style')).filter(
                    (el) => el.hasAttribute('type') && el.getAttribute('type') === 'text/css' && el.attributes.length === 1
                  );
                  
                  let existingStyleTag = document.getElementById('vite-dev-vue3');
                  if (!existingStyleTag) {
                    existingStyleTag = document.createElement('style');
                    existingStyleTag.id = 'vite-dev-vue3';
                    document.head.appendChild(existingStyleTag);
                  }
                  
                  // Accumulate styles into the combined tag
                  let combinedStyles = existingStyleTag.innerHTML || '';
                  styleElements.forEach((el,index) => {
                    combinedStyles += el.innerHTML;
                    el.remove(); // Remove individual style tags
                  });
                  
                  existingStyleTag.innerHTML = combinedStyles;
                });
              }
            `,
            map: null,
          }
        }
      },
    }
  }

  return {
    plugins: [
      vue(),
      collectAllCss(),
      Inspect(),
      useDynamicPublicPath({
        dynamicImportHandler: 'window.__dynamic_handler__',
        dynamicImportPreload: 'window.__dynamic_preload__',
      }),
      AutoImport({
        imports: ['vue', '@vueuse/core'],
        resolvers: [ElementPlusResolver()],
      }),
      Components({
        resolvers: [ElementPlusResolver()],
      }),
      copyIconFonts(),
    ],

    resolve: {
      extensions: ['.js', '.vue', '.json', '.mjs'],
      alias: {
        '@css': path.resolve(__dirname, '/src/assets/scss'),
      },
    },

    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `
            $icomoon-font-path: "${fontPath}" !default;
            @import "@css/admin/_variables";
            @import "@css/common/variables/_breakpoints";
            @import "@css/common/icon-fonts/variables";
            @import "@css/common/icon-fonts/style";
            @import "@css/common/animations/animations";
          `,
        },
      },
    },

    base: '',

    build: {
      target: 'esnext',
      chunkSizeWarningLimit: 1500,
      rollupOptions: {
        input: [
          'src/assets/js/admin/admin.js',
          'src/assets/js/public/public.js',
        ],
        output: {
          manualChunks: {
            stepForm: ['src/views/public/StepForm/BookingStepForm.vue'],
            catalogForm: ['src/views/public/CatalogForm/CatalogForm.vue'],
            eventListForm: [
              'src/views/public/EventForm/EventListForm/EventsListForm.vue',
            ],
            eventCalendarForm: [
              'src/views/public/EventForm/EventCalendarForm/EvensCalendarForm.vue',
            ],
            customerPanel: [
              'src/views/public/Cabinet/CustomerPanel/CustomerPanel.vue',
            ],
          },
        },
      },
      outDir: './public',
    },
  }
})
