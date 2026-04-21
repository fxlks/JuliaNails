<template>
  <template v-if="!amFonts.customFontSelected">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      rel="stylesheet"
      type="text/css"
      :href="`${baseUrls.wpAmeliaPluginURL}v3/src/assets/scss/common/fonts/font.css`"
      media="all"
    />
  </template>
  <div
    id="amelia-container"
    ref="ameliaContainer"
    class="am-fc__wrapper"
    :style="cssVars"
  >
    <template v-if="ready && pagesArray.length">
      <component :is="pagesArray[pageIndex]"></component>
    </template>
    <CatalogSkeleton v-else />
  </div>
  <BackLink />
</template>

<script setup>
// * Pages
import CategoriesList from './CategoriesList/CategoriesList.vue'
import CategoryItemsList from './CategoryItemsList/CategoryItemsList.vue'
import CategoryService from './CategoryItem/CategoryService.vue'
import CategoryPackage from './CategoryItem/CategoryPackage.vue'

// * Parts
import CatalogSkeleton from './Parts/Skeleton/CatalogSkeleton.vue'
import BackLink from '../Parts/BackLink'

// * import from Vue
import {
  inject,
  provide,
  markRaw,
  ref,
  watch,
  nextTick,
  computed,
  onMounted,
} from 'vue'

// * import from Vuex
import { useStore } from 'vuex'

// * import composable
import { defaultCustomizeSettings } from '../../../assets/js/common/defaultCustomize'
import { useColorTransparency } from '../../../assets/js/common/colorManipulation'
import useRestore from '../../../assets/js/public/restore'
import useAction from '../../../assets/js/public/actions'
import { useAvailableCategories } from '../../../assets/js/public/catalog'
import { useRenderAction } from '../../../assets/js/public/renderActions'

// * Plugin Licence
let licence = inject('licence')

// * Component reference
let ameliaContainer = ref(null)

// * Component offset scroll
let offsetFromTop = ref(20)

// * Root Urls
const baseUrls = inject('baseUrls')

// * Plugin wrapper width
let containerWidth = ref(0)
provide('containerWidth', containerWidth)

// * window resize listener
window.addEventListener('resize', resize)

// * resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
}

onMounted(() => {
  store.commit('shortcodeParams/setForm', 'catalogForm')

  document
    .getElementById('amelia-v2-booking-' + shortcodeData.value.counter)
    .classList.add(
      'amelia-v2-booking-' + shortcodeData.value.counter + '-loaded'
    )

  useAction(store, {}, 'ViewContent', 'appointment', null, null)

  resize()

  useRenderAction('scrollForm', {
    offsetFromTop,
  })
})

// * Empty State
let empty = ref(false)

// * Root Settings
const amSettings = inject('settings')

// * Shortcode
const shortcodeData = inject('shortcodeData')

// * Define store
const store = useStore()

store.commit('entities/setPreselected', shortcodeData.value)

// * Get Entities from server
store.dispatch('entities/getEntities', {
  types: [
    'employees',
    'categories',
    'locations',
    'packages',
    'entitiesRelations',
    'customFields',
    'taxes',
  ],
  licence: licence,
  loadEntities: shortcodeData.value.hasApiCall || shortcodeData.value.trigger,
  showHidden: false,
  isPanel: false,
})

// * Components
const categoriesList = markRaw(CategoriesList)
const categoryItemsList = markRaw(CategoryItemsList)
const categoryService = markRaw(CategoryService)
const categoryPackage = markRaw(CategoryPackage)

let pagesArray = ref([categoriesList, categoryItemsList])
let pageIndex = ref(0)

let categorySelected = ref(null)
provide('categorySelected', categorySelected)

let availableCategories = ref([])
provide('availableCategories', availableCategories)

let itemType = ref('')
provide('itemType', itemType)

watch(itemType, () => {
  if (itemType.value === 'appointment') {
    pagesArray.value.push(categoryService)
  }

  if (itemType.value === 'package') {
    pagesArray.value.push(categoryPackage)
  }

  if (itemType.value === '') {
    pagesArray.value.forEach((a, index) => {
      if (a.name === 'CategoryService')
        removeItemFromStepArray(pagesArray.value, index)
      if (a.name === 'CategoryPackage')
        removeItemFromStepArray(pagesArray.value, index)
    })
  }
})

function removeItemFromStepArray(arr, identifier) {
  arr.splice(identifier, 1)
}

function iOS() {
  return (
    [
      'iPad Simulator',
      'iPhone Simulator',
      'iPod Simulator',
      'iPad',
      'iPhone',
      'iPod',
    ].includes(navigator.platform) ||
    // iPad on iOS 13 detection
    (navigator.userAgent.includes('Mac') && 'ontouchend' in document)
  )
}

function nextPage() {
  pageIndex.value = pageIndex.value + 1
  if (iOS()) {
    setTimeout(() => {
      let scrollHeightElement =
        ameliaContainer.value.getBoundingClientRect().top +
        window.pageYOffset -
        offsetFromTop.value
      window.scrollTo({
        top: scrollHeightElement,
        behavior: 'smooth',
      })
    }, 500)
  } else {
    let scrollHeightElement =
      ameliaContainer.value.getBoundingClientRect().top +
      window.pageYOffset -
      offsetFromTop.value
    window.scrollTo({
      top: scrollHeightElement,
      behavior: 'smooth',
    })
  }
}

function previousPage() {
  pageIndex.value = pageIndex.value - 1
}

provide('changingPageFunctions', {
  nextPage,
  previousPage,
})

// * When data is ready
let ready = computed(() => store.getters['entities/getReady'])

// * Entities
let amEntities = computed(() => {
  return store.state.entities
})

provide('amEntities', amEntities)

// * Collect shortcode data
function setShortcodeParams() {
  let preselected = store.getters['entities/getPreselected']

  if (preselected.category.length !== 1) {
    if (shortcodeData.value.categories_hidden) {
      availableCategories.value = JSON.parse(
        JSON.stringify(
          useAvailableCategories(amEntities.value, shortcodeData.value)
        )
      )

      nextTick(() => {
        let componentIndex = pagesArray.value.findIndex(
          (a) => a.name === 'CategoriesList'
        )
        pagesArray.value.splice(componentIndex, 1)
      })
    }

    if (!shortcodeData.value.categories_hidden) {
      availableCategories.value = JSON.parse(
        JSON.stringify(
          useAvailableCategories(amEntities.value, shortcodeData.value)
        )
      )

      nextTick(() => {
        if (availableCategories.value.length === 1) {
          store.commit(
            'booking/setCategoryId',
            parseInt(availableCategories.value[0].id)
          )
          categorySelected.value = parseInt(availableCategories.value[0].id)
          let componentIndex = pagesArray.value.findIndex(
            (a) => a.name === 'CategoriesList'
          )
          pagesArray.value.splice(componentIndex, 1)
        }
      })
    }
  }

  if (preselected.category.length === 1) {
    store.commit('booking/setCategoryId', parseInt(preselected.category))

    categorySelected.value = parseInt(preselected.category[0])

    availableCategories.value = JSON.parse(
      JSON.stringify(
        useAvailableCategories(amEntities.value, shortcodeData.value).filter(
          (a) => a.id === parseInt(preselected.category[0])
        )
      )
    )

    nextTick(() => {
      let componentIndex = pagesArray.value.findIndex(
        (a) => a.name === 'CategoriesList'
      )
      pagesArray.value.splice(componentIndex, 1)
    })
  }

  if (preselected.service.length === 1) {
    store.commit('booking/setServiceId', parseInt(preselected.service[0]))
    let service = store.getters['entities/getService'](
      parseInt(preselected.service[0])
    )
    categorySelected.value = service ? parseInt(service.categoryId) : null
    store.commit(
      'booking/setCategoryId',
      service ? parseInt(service.categoryId) : null
    )

    nextTick(() => {
      pagesArray.value = []
      pagesArray.value.push(categoryService)
    })
  }

  if (preselected.employee.length === 1) {
    store.commit('booking/setEmployeeId', parseInt(preselected.employee[0]))
  }

  if (preselected.location.length === 1) {
    store.commit('booking/setLocationId', parseInt(preselected.location[0]))
  }

  if (
    preselected.package.length === 1 &&
    store.getters['entities/getPackages'].length
  ) {
    pagesArray.value = []
    store.commit('booking/setPackageId', parseInt(preselected.package[0]))

    nextTick(() => {
      pagesArray.value.push(categoryPackage)
    })
  }

  if (preselected.show === 'packages') {
    store.commit('booking/setBookableType', 'package')
  } else {
    store.commit('booking/setBookableType', 'appointment')
  }
}

let restore = computed(() => {
  return ready.value ? useRestore(store, shortcodeData.value) : null
})
provide('restoreFormData', restore)

// * Watch when data is ready
watch(ready, (current) => {
  if (current) {
    setShortcodeParams()
    empty.value =
      store.getters['entities/getServices'].length === 0 ||
      store.getters['entities/getEmployees'].length === 0

    if (restore.value) {
      itemType.value = store.state.booking.appointment.type
      nextTick(() => {
        pageIndex.value = pagesArray.value.length - 1
        categorySelected.value = store.state.booking.appointment.categoryId
      })
    }
  }
})

// * Customized data form
let customizedDataForm = computed(() => {
  return amSettings.customizedData && 'cbf' in amSettings.customizedData
    ? amSettings.customizedData.cbf
    : defaultCustomizeSettings.cbf
})

provide('customizedDataForm', customizedDataForm)

// * Fonts
const amFonts = ref(
  amSettings.customizedData
    ? amSettings.customizedData.fonts
    : defaultCustomizeSettings.fonts
)
provide('amFonts', amFonts)

// * Colors block
let amColors = computed(() => {
  return amSettings.customizedData && 'cbf' in amSettings.customizedData
    ? amSettings.customizedData.cbf.colors
    : defaultCustomizeSettings.cbf.colors
})

provide('amColors', amColors)

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-success': amColors.value.colorSuccess,
    '--am-c-error': amColors.value.colorError,
    '--am-c-warning': amColors.value.colorWarning,
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-sb-bgr': amColors.value.colorSbBgr,
    '--am-c-sb-text': amColors.value.colorSbText,
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-drop-bgr': amColors.value.colorDropBgr,
    '--am-c-drop-text': amColors.value.colorDropText,
    '--am-c-card-bgr': amColors.value.colorCardBgr,
    '--am-c-card-text': amColors.value.colorCardText,
    '--am-c-card-border': amColors.value.colorCardBorder,
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-btn-sec': amColors.value.colorBtnSec,
    '--am-c-btn-sec-text': amColors.value.colorBtnSecText,
    '--am-c-skeleton-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-skeleton-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-font-family': amFonts.value.fontFamily,
  }
})

function activateCustomFontStyles() {
  let head = document.head || document.getElementsByTagName('head')[0]
  if (head.querySelector('#amCustomFont')) {
    head.querySelector('#amCustomFont').remove()
  }

  let css = `@font-face {font-family: '${amFonts.value.fontFamily}'; src: url(${amFonts.value.fontUrl});}`
  let style = document.createElement('style')
  head.appendChild(style)
  style.setAttribute('type', 'text/css')
  style.setAttribute('id', 'amCustomFont')
  style.appendChild(document.createTextNode(css))
}

if (amFonts.value.customFontSelected) activateCustomFontStyles()

// * Design Properties
let amDesignProperties = computed(() => {
  return {
    colorInputBorderRadius: '6px',
  }
})
provide('amDesignProperties', amDesignProperties)
</script>

<script>
export default {
  name: 'CatalogFormWrapper',
}
</script>

<style lang="scss">
@import '../../../../src/assets/scss/public/overides/overides';
@import '../../../../src/assets/scss/common/reset/reset';
@import '../../../../src/assets/scss/common/icon-fonts/style';
@import '../../../../src/assets/scss/common/skeleton/skeleton.scss';
@import '../../../../src/assets/scss/common/empty-state/_empty-state-mixin.scss';

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
  --am-c-main-bgr: #{$am-white};
  --am-c-main-heading-text: #{$shade-800};
  --am-c-main-text: #{$shade-900};
  // sidebar container colors - left part of the form
  --am-c-sb-bgr: #17295a;
  --am-c-sb-text: #{$am-white};
  // input global colors - usage input, textarea, checkbox, radio button, select input, adv select input
  --am-c-inp-bgr: #{$am-white};
  --am-c-inp-border: #{$shade-250};
  --am-c-inp-text: #{$shade-900};
  --am-c-inp-placeholder: #{$shade-500};
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

// am -- amelia
// fc -- form catalog
// sb -- sidebar
.amelia-v2-booking {
  background-color: transparent;

  #amelia-container {
    background-color: transparent;

    * {
      font-family: var(--am-font-family), sans-serif;
      font-style: initial;
      box-sizing: border-box;
      word-break: break-word;
    }

    &.am-fc {
      // Container Wrapper
      &__wrapper {
        display: flex;
        justify-content: center;
        max-width: 100%;
        width: 100%;
        height: auto;
        margin: 0;
        padding: 0;
        border-radius: 8px;
        box-shadow: none;

        // Form
        .el-form {
          &-item {
            display: block;
            font-family: var(--am-font-family), sans-serif;
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
          font-family: var(--am-font-family), sans-serif;
          box-sizing: border-box;
        }
      }
    }

    @include empty-state;
  }
}

// General popup styles
.am-dialog-popup.amelia-v2-booking.amelia-v2-booking-dialog {
  .el-dialog {
    .el-dialog {
      &__header {
        padding: 0;
      }

      &__headerbtn {
        width: 19px;
        height: 19px;
        top: 16px;
        right: 16px;
      }

      &__body {
        padding: 0;

        #amelia-container {
          margin: 0 auto;
        }
      }
    }
  }
}
</style>
