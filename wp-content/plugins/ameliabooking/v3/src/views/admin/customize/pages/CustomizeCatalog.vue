<template>
  <div
    v-if="isLoaded"
    id="amelia-container"
    ref="ameliaContainer"
    class="am-fc__wrapper"
    :style="cssVars"
  >
    <template v-if="stepsArray.length">
      <component :is="stepsArray[stepIndex]"></component>
    </template>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  provide,
  inject,
  markRaw,
  computed,
  onBeforeMount,
  onMounted,
  watchEffect,
  onUnmounted,
  nextTick,
} from 'vue'

import { useElementSize } from '@vueuse/core'

// * Import composables
import { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize.js'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

// * Step components
import CategoriesList from '../steps/CategoriesList.vue'
import CategoryItemsList from '../steps/CategoryItemsList.vue'
import CategoryService from '../steps/CategoryService.vue'
import CategoryPackage from '../steps/CategoryPackage.vue'

const categoriesList = markRaw(CategoriesList)
const categoryItemsList = markRaw(CategoryItemsList)
const categoryService = markRaw(CategoryService)
const categoryPackage = markRaw(CategoryPackage)

// * Base Urls
const baseUrls = inject('baseUrls')

// * Features integrations
let features = useReactiveCustomize().features
provide('features', features)

let langKey = useReactiveCustomize().langKey
provide('langKey', langKey)

// * Step index
let stepIndex = useReactiveCustomize().stepIndex
provide('stepIndex', stepIndex)

// * Page render key
let pageRenderKey = ref('cbf')
provide('pageRenderKey', pageRenderKey)

// * Step name
let stepName = useReactiveCustomize().stepName
provide('stepName', stepName)

// * Customize data
const { amCustomize } = useReactiveCustomize()
provide('customize', amCustomize)

// * Fonts
let amFonts = computed(() => {
  return amCustomize.value.fonts
})
provide('amFonts', amFonts)

let stepsArray = ref([
  categoriesList,
  categoryItemsList,
  categoryService,
  categoryPackage,
])

// * Component reference
let ameliaContainer = ref(null)

// * Plugin wrapper width
let { width: containerWidth } = useElementSize(ameliaContainer)
provide('containerWidth', containerWidth)

let isLoaded = ref(false)

onMounted(() => {
  window.customizeComponentLoaded = true

  if (typeof window.stylesInjectedV3 === 'function') {
    if (window.v3.componentToMount === 'CustomizeCatalog') {
      window.stylesInjectedV3 = () => {
        nextTick().then(() => {
          isLoaded.value = true
        })
      }
    }
  }
})

// Clean up on unmount to prevent stale callbacks
onUnmounted(() => {
  window.customizeComponentLoaded = false
  // Reset to no-op function
  window.stylesInjectedV3 = async function () {}
})

watchEffect(() => {
  if (amCustomize.value.fonts.customFontSelected) {
    activateCustomFontStyles()
  } else {
    importDefaultFonts()
  }
})

function activateCustomFontStyles() {
  let head = document.head || document.getElementsByTagName('head')[0]
  if (head.querySelector('#amCustomFont')) {
    head.querySelector('#amCustomFont').remove()
  }

  if (head.querySelector('#amDefaultFontImport')) {
    head.querySelector('#amDefaultFontImport').remove()
  }

  let css = `@font-face {font-family: '${amCustomize.value.fonts.fontFamily}'; src: url(${amCustomize.value.fonts.fontUrl});}`
  let style = document.createElement('style')
  head.appendChild(style)
  style.setAttribute('type', 'text/css')
  style.setAttribute('id', 'amCustomFont')
  style.appendChild(document.createTextNode(css))
}

function importDefaultFonts() {
  const head = document.head || document.getElementsByTagName('head')[0]
  if (head.querySelector('#amDefaultFontImport')) {
    return
  }

  const base = baseUrls.value.wpAmeliaPluginURL
  const style = document.createElement('style')
  style.setAttribute('type', 'text/css')
  style.setAttribute('id', 'amDefaultFontImport')
  style.textContent = `@import url("${base}v3/src/assets/scss/common/fonts/font.css");`
  head.appendChild(style)
}

/**
 * Lifecycle Hooks
 */
onBeforeMount(() => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: 'smooth',
  })
})

// * Colors
// * Customize colors
let amColors = computed(() => {
  // debugger
  return amCustomize.value[pageRenderKey.value]
    ? amCustomize.value[pageRenderKey.value].colors
    : defaultCustomizeSettings[pageRenderKey.value].colors
})

provide('amColors', amColors)

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-success': amColors.value.colorSuccess,
    '--am-c-error': amColors.value.colorError,
    '--am-c-warning': amColors.value.colorWarning,
    // input global colors - usage input, textarea, checkbox, radio button, select input, adv select input
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    // dropdown global colors - usage select dropdown, adv select dropdown
    '--am-c-drop-bgr': amColors.value.colorDropBgr,
    '--am-c-drop-text': amColors.value.colorDropText,
    // sidebar container colors - left part of the form
    '--am-c-sb-bgr': amColors.value.colorSbBgr,
    '--am-c-sb-text': amColors.value.colorSbText,
    // main container colors - right part of the form
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    // input global colors - usage input, textarea, checkbox, radio button, select input, adv select input
    '--am-c-card-bgr': amColors.value.colorCardBgr,
    '--am-c-card-border': amColors.value.colorCardBorder,
    '--am-c-card-text': amColors.value.colorCardText,
    // button global colors
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-btn-sec': amColors.value.colorBtnSec,
    '--am-c-btn-sec-text': amColors.value.colorBtnSecText,
    '--am-font-family': amCustomize.value.fonts.fontFamily,
    // css properties
    // -mw- max width
    // -brad- border-radius
    '--am-mw-main': amCustomize.value.sbsNew.sidebar.options.self.visibility
      ? '760px'
      : '520px',
    '--am-brad-main': amCustomize.value.sbsNew.sidebar.options.self.visibility
      ? '0 0.5rem 0.5rem 0'
      : '0.5rem',
  }
})
</script>

<script>
export default {
  name: 'CustomizeCatalog',
}
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/common/reset/reset';

:root {
  // Colors
  // shortcuts
  // -c-    color
  // -bgr-  background
  // -prim- primary
  // -sec-  secondary
  // primitive colors
  --am-c-primary: #{$blue-1000};
  --am-c-success: #{$green-1000};
  --am-c-error: #{$red-900};
  --am-c-warning: #{$yellow-1000};
  // main container colors - right part of the form
  --am-c-main-bgr: #{$blue-900};
  --am-c-main-heading-text: #{$shade-800};
  --am-c-main-text: #{$shade-900};
  // sidebar container colors - left part of the form
  --am-c-sb-bgr: #17295a;
  --am-c-sb-text: #{$am-white};
  // input global colors - usage input, textarea, checkbox, radio button, select input, adv select input
  --am-c-inp-bgr: #{$blue-900};
  --am-c-inp-border: #{$shade-250};
  --am-c-inp-text: #{$shade-900};
  --am-c-inp-placeholder: #{$shade-500};
  --am-c-checkbox-border: #{$shade-300};
  --am-c-checkbox-border-disabled: #{$blue-600};
  --am-c-checkbox-border-focused: #{$blue-700};
  --am-c-checkbox-label-disabled: #{$shade-600};
  // dropdown global colors - usage select dropdown, adv select dropdown
  --am-c-drop-bgr: #{$am-white};
  --am-c-drop-text: #{$shade-1000};
  // button global colors
  --am-c-btn-prim: #{$blue-900};
  --am-c-btn-prim-text: #{$am-white};
  --am-c-btn-sec: #{$am-white};
  --am-c-btn-sec-text: #{$shade-900};

  // Properties
  // shortcuts
  // -h- height
  // -fs- font size
  // -rad- border radius
  --am-h-inp: 40px;
  --am-fs-inp: 15px;
  --am-rad-inp: 6px;
  --am-fs-label: 15px;
  --am-fs-btn: 15px;

  // Font
  --am-font-family: 'Amelia Roboto', sans-serif;
}

//@import url('https://fonts.googleapis.com/css2?family=Rampart+One&display=swap');
// am -- amelia
// fc -- form catalog
#amelia-app-backend-new {
  #amelia-container {
    background-color: transparent;

    * {
      font-family: var(--am-font-family);
      font-style: initial;
      box-sizing: border-box;
      word-break: break-word;
      letter-spacing: normal;
    }

    &.am-fc {
      // Container Wrapper
      &__wrapper {
        display: flex;
        justify-content: center;
        max-width: calc(100% - 48px);
        width: 100%;
        height: auto;
        margin: 48px 24px;
        padding: 0;
        border-radius: 8px;
        box-shadow: none;

        .el-form {
          &-item {
            display: block;
            font-family: var(--am-font-family);
            font-size: var(--am-fs-label);
            margin-bottom: 24px;

            &__label {
              flex: 0 0 auto;
              text-align: left;
              font-size: var(--am-fs-label);
              line-height: 1.3;
              color: var(--am-c-main-text);
              box-sizing: border-box;
              margin: 0;

              &:before {
                color: var(--am-c-error);
              }
            }

            &__content {
              display: flex;
              flex-wrap: wrap;
              align-items: center;
              flex: 1;
              position: relative;
              font-size: var(--am-fs-inp);
              min-width: 0;
              color: var(--am-c-main-text);
            }

            &__error {
              font-size: 12px;
              color: var(--am-c-error);
              padding-top: 4px;
            }
          }
        }

        * {
          font-family: var(--am-font-family);
          box-sizing: border-box;
        }

        .am-empty {
          --am-c-e-bgr: var(--am-c-main-bgr);
          --am-c-e-text: var(--am-c-main-text);
          max-width: 760px;
          width: 100%;
          height: 460px;
          text-align: center;
          background-color: var(--am-c-e-bgr);
          padding: 56px;
          margin: 100px auto;
          box-shadow: 0 30px 40px rgba(0, 0, 0, 0.12);

          * {
            font-family: var(--am-font-family);
            box-sizing: border-box;
            color: var(--am-c-e-text);
          }

          &__heading {
            display: block;
            text-align: center;
            font-size: 24px;
            line-height: 1.5;
            font-weight: bold;
          }

          &__subheading {
            display: block;
            text-align: center;
            font-size: 16px;
            line-height: 1.5;
            font-weight: 400;
          }

          p,
          span {
            padding: 0;
          }

          p {
            font-size: 14px;
            margin: 8px 0;
          }

          a {
            font-size: 14px;
            color: var(--am-c-primary);
          }
        }
      }
    }
  }

  .am-dialog-cs {
    z-index: 10000 !important;
  }
}
</style>
