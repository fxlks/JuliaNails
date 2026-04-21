<template>
  <div
    v-if="isLoaded"
    id="amelia-container"
    ref="ameliaContainer"
    class="am-elf"
    :class="{ 'am-elf-dialog': stepsArray[stepIndex].key !== 'list' }"
    :style="cssVars"
  >
    <component
      :is="stepsArray[stepIndex]"
      v-if="stepsArray[stepIndex].key === 'list'"
    ></component>

    <template v-if="stepsArray[stepIndex].key !== 'list'">
      <div v-if="stepIndex > 1" class="am-dialog-el-header">
        <EventListHeader
          :ready="true"
          :loading="false"
          :customized-labels="globalStepLabels()"
        />
      </div>
      <component
        :is="stepsArray[stepIndex]"
        :in-dialog="true"
        global-class="am-dialog-el__main-container"
      ></component>
      <EventListFooter
        v-if="stepsArray[stepIndex].key !== 'list'"
        :customized-labels="globalStepLabels()"
        :primary-footer-button-type="
          isWaiting && stepsArray[stepIndex].name === 'EventInfo'
            ? waitingBtnType
            : primBtn
        "
        :secondary-footer-button-type="secBtn"
        :second-button-show="!licence.isLite ? secBtnVisible : false"
        :is-waiting-list="isWaiting"
      />
    </template>
  </div>
</template>

<script setup>
// * import from Vue
import {
  computed,
  ref,
  inject,
  provide,
  markRaw,
  onBeforeMount,
  watchEffect,
  onMounted,
  onUnmounted,
  nextTick,
} from 'vue'

// * Components Width and Height
import { useElementSize } from '@vueuse/core'

// * Import composables
import { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize.js'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'
import { useReactiveCustomize } from '../../../../assets/js/admin/useReactiveCustomize.js'

// * Form Construction
import EventListHeader from '../../../common/EventListFormConstruction/Header/Header.vue'
import EventListFooter from '../../../common/EventListFormConstruction/Footer/Footer.vue'

// * Step Components
import EventsList from '../steps/Events/EventList/EventsList.vue'
import EventInfo from '../steps/Events/common/EventInfo/EventInfo.vue'
import EventTickets from '../steps/Events/common/EventTickets/EventTickets.vue'
import EventCustomerInfo from '../steps/Events/common/EventCustomerInfo/EventCustomerInfo.vue'
import EventPayment from '../steps/Events/common/EventPayment/EventPayment.vue'
import EventCongratulations from '../steps/Events/common/Congratulations/EventCongratulations.vue'

// * Form Component Collection
const eventsList = markRaw(EventsList)
const eventInfo = markRaw(EventInfo)
const eventTickets = markRaw(EventTickets)
const eventCustomerInfo = markRaw(EventCustomerInfo)
const eventPayment = markRaw(EventPayment)
const eventCongratulations = markRaw(EventCongratulations)

// * Root Settings
let amSettings = inject('settings')

// * Plugin Licence
let licence = inject('licence')

// * Features
let features = useReactiveCustomize().features
provide('features', features)

// * Base Urls
const baseUrls = inject('baseUrls')

// * Step index
let stepIndex = useReactiveCustomize().stepIndex
provide('stepIndex', stepIndex)

// * Bookable Type
const bookableType = ref('event')
provide('bookableType', bookableType)

// * Language Key
let langKey = useReactiveCustomize().langKey
provide('langKey', langKey)

// * Page Render Key
let pageRenderKey = ref('elf')
provide('pageRenderKey', pageRenderKey)

// * Step Name
let stepName = useReactiveCustomize().stepName || 'list'
provide('stepName', stepName)

// * Customize Data
const { amCustomize } = useReactiveCustomize()
provide('customize', amCustomize)

// * is waiting
let isWaiting = computed(() => {
  return features.value.waitingList
})

let amFonts = computed(() => {
  return amCustomize.value.fonts
})
provide('amFonts', amFonts)

let primBtn = computed(() => {
  if (
    amCustomize.value[pageRenderKey.value][stepName.value] &&
    'primBtn' in amCustomize.value[pageRenderKey.value][stepName.value].options
  ) {
    return amCustomize.value[pageRenderKey.value][stepName.value].options
      .primBtn.buttonType
  }
  return 'filled'
})

let secBtn = computed(() => {
  if (
    amCustomize.value[pageRenderKey.value][stepName.value] &&
    'secBtn' in amCustomize.value[pageRenderKey.value][stepName.value].options
  ) {
    return amCustomize.value[pageRenderKey.value][stepName.value].options.secBtn
      .buttonType
  }
  return 'plain'
})

let secBtnVisible = computed(() => {
  if (
    amCustomize.value[pageRenderKey.value][stepName.value] &&
    'secBtn' in amCustomize.value[pageRenderKey.value][stepName.value].options
  ) {
    return amCustomize.value[pageRenderKey.value][stepName.value].options.secBtn
      .visibility
  }
  return true
})

let waitingBtnType = computed(() => {
  if (
    amCustomize.value[pageRenderKey.value][stepName.value] &&
    'waitingBtn' in
      amCustomize.value[pageRenderKey.value][stepName.value].options
  ) {
    return amCustomize.value[pageRenderKey.value][stepName.value].options
      .waitingBtn.buttonType
  }
  return 'filled'
})

let stepsArray = ref([
  eventsList,
  eventInfo,
  eventTickets,
  eventCustomerInfo,
  eventPayment,
  eventCongratulations,
])
provide('stepsArray', stepsArray)

onMounted(() => {
  if (!features.value.tickets) {
    stepsArray.value = [
      eventsList,
      eventInfo,
      eventCustomerInfo,
      eventPayment,
      eventCongratulations,
    ]
  }
})

// * Component reference
let ameliaContainer = ref(null)

let isLoaded = ref(false)

onMounted(() => {
  window.customizeComponentLoaded = true

  if (typeof window.stylesInjectedV3 === 'function') {
    if (window.v3.componentToMount === 'CustomizeEventList') {
      window.stylesInjectedV3 = () => {
        nextTick().then(() => {
          isLoaded.value = true
        })
      }
    }
  }
})

onUnmounted(() => {
  window.customizeComponentLoaded = false
  // Reset to no-op function
  window.stylesInjectedV3 = async function () {}
})

// * Plugin wrapper width
const { width: containerWidth } = useElementSize(ameliaContainer)
provide('containerWidth', containerWidth)

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

function globalStepLabels() {
  let stepLabels = {}
  let customizedLabels =
    amCustomize.value[pageRenderKey.value][stepName.value].translations
  if (customizedLabels && Object.keys(customizedLabels)) {
    Object.keys(
      amCustomize.value[pageRenderKey.value][stepName.value].translations
    ).forEach((label) => {
      stepLabels[label] =
        amCustomize.value[pageRenderKey.value][stepName.value].translations[
          label
        ][langKey.value]
    })
  } else {
    stepLabels = {}
  }

  return stepLabels
}

// * Colors
// * Customize colors
let amColors = computed(() => {
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

    // -mw- max width
    '--am-mw-main': '792px',
    '--am-hd-main': stepIndex.value > 0 ? '592px' : '652px',
    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),

    '--am-rad-inp': '6px',

    '--am-mb-dialog': '300px',
  }
})
</script>

<script>
export default {
  name: 'CustomizeEventList',
}
</script>

<style lang="scss">
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

#amelia-app-backend-new #amelia-container {
  * {
    font-family: var(--am-font-family);
    font-style: initial;
    box-sizing: border-box;
    word-break: break-word;
    letter-spacing: normal;
  }

  // am - amelia
  // elf - events list form
  &.am-elf {
    margin: 24px auto 0;
    max-width: var(--am-mw-main);
    background-color: var(--am-c-main-bgr);
    padding: 16px;
    border-radius: 12px;

    &.am-elf-dialog {
      max-width: 650px;
      padding: 0;
      border-radius: 8px;
      position: relative;
    }
  }

  .am-dialog-el {
    &__main {
      &-container {
        height: var(--am-hd-main);
        display: block;
        overflow-x: hidden;
        padding: 16px 24px 0;

        &::-webkit-scrollbar {
          width: 6px;
        }

        &::-webkit-scrollbar-thumb {
          border-radius: 6px;
          background: var(--am-c-scroll-op30);
        }

        &::-webkit-scrollbar-track {
          border-radius: 6px;
          background: var(--am-c-scroll-op10);
        }
      }
    }
  }
}
</style>
