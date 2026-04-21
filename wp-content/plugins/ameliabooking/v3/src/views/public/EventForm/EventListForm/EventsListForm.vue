<template>
  <template v-if="!amFonts.customFontSelected">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" :href="`${baseUrls.wpAmeliaPluginURL}v3/src/assets/scss/common/fonts/font.css`" media="all">
  </template>
  <div
    id="amelia-container"
    ref="ameliaContainer"
    class="am-elf"
    :style="cssVars"
    role="main"
  >
      <!-- Events List -->
      <EventsList />
      <!-- /Events List -->

      <AmDialog
        v-model="popupVisible"
        width="650px"
        :append-to-body="true"
        :destroy-on-close="true"
        :align-center="true"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        :show-close="ready && !loading"
        modal-class="amelia-v2-booking am-dialog-el"
        :custom-styles="cssVars"
        @close="onCloseEventDialog"
        @closed="onClosedEventDialog"
        @opened="onOpenedEventDialog"
      >
        <template #default>
          <div
            id="amelia-container"
            ref="dialogContainer"
          >
            <div v-if="stepIndex > 0" class="am-dialog-el-header">
              <EventListHeader
                :ready="ready"
                :loading="loading"
                :loading-upcoming="false"
                :customized-labels="customizedStepLabels"
              />
            </div>
            <component
              :is="stepsArray[stepIndex]"
              :in-dialog="true"
              global-class="am-dialog-el__main-container"
            ></component>
            <EventListFooter
              :ready="ready"
              :loading="loading"
              :loading-upcoming="false"
              :second-button-show="secBtnShow && customerCabinetButton"
              :payment-gateway="store.getters['payment/getPaymentGateway']"
              :customized-labels="customizedStepLabels"
              :primary-footer-button-type="isWaitingList && stepsArray[stepIndex].name === 'EventInfo' ? waitingButtonTypeEnabled : customizedStepOptions.primBtn.buttonType"
              :secondary-footer-button-type="customizedStepOptions.secBtn.buttonType"
              :is-waiting-list="isWaitingList"
            />
          </div>
        </template>
      </AmDialog>
  </div>
  <BackLink/>
</template>

<script setup>
// * import from Vue
import {
  computed,
  ref,
  inject,
  provide,
  markRaw,
  nextTick,
  watch,
  onMounted
} from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * import composable
import {
  defaultCustomizeSettings
} from '../../../../assets/js/common/defaultCustomize'
import useAction from '../../../../assets/js/public/actions'
import useRestore from "../../../../assets/js/public/restore";
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'
import { useScrollTo } from "../../../../assets/js/common/scrollElements";
import { useWaitingListAvailability } from "../../../../assets/js/public/events";

// * Parts
import BackLink from '../../Parts/BackLink'

// * Structure Components
import AmDialog from '../../../_components/dialog/AmDialog.vue'

// * Step components
import EventsList from './EventsList/EventsList.vue'

// * Dialog Steps
import EventInfo from '../Common/EventInfo/EventInfo.vue'
import EventTickets from '../Common/EventTickets/EventTickets.vue'
import EventCustomerInfo from '../Common/EventCustomerInfo/EventCustomerInfo.vue'
import EventPayment from '../Common/EventPayment/EventPayment.vue'
import EventCongratulations from '../Common/Congratulations/Congratulations.vue'
import EventListFooter from '../../../common/EventListFormConstruction/Footer/Footer.vue'
import EventListHeader from '../../../common/EventListFormConstruction/Header/Header.vue'

// * Form Component Collection
const eventInfo = markRaw(EventInfo)
const eventTickets = markRaw(EventTickets)
const eventCustomerInfo = markRaw(EventCustomerInfo)
const eventPayment = markRaw(EventPayment)
const eventCongratulations = markRaw(EventCongratulations)

// * Dynamic height
let dynamicVh = ref(0)
function updateVH() {
  dynamicVh.value = window.visualViewport.height
}

window.addEventListener('resize', updateVH)

// * Popup Visibility
const popupVisible = ref(false)
provide('popupVisible', popupVisible)

// * Root Settings
const amSettings = inject('settings')

// * Root Urls
const baseUrls = inject('baseUrls')

// * Define store
const store = useStore()

// * Form Key
store.commit('setFormKey', 'elf')

// * Shortcode data
const shortcodeData = inject('shortcodeData')
store.commit('shortcodeParams/setShortcodeParams', shortcodeData.value)

// * Set Bookable Type
store.commit('bookableType/setType', 'event')

// * Component reference
let ameliaContainer = ref(null)
let dialogContainer = ref(null)

// * Events array
let events = computed(() => store.getters['eventEntities/getEvents'])

function useEvents (hookModifiedEvents) {
  store.commit(
      'eventEntities/setEvents',
      hookModifiedEvents
  )
}

// * Plugin wrapper width
let containerWidth = ref(0)
provide('containerWidth', containerWidth)

// * Dialog wrapper width
let dialogWidth = ref(0)
provide('dialogWidth', dialogWidth)

// * window resize listener
window.addEventListener('resize', resize);

// * Resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }

  if (dialogContainer.value) {
    dialogWidth.value = dialogContainer.value.offsetWidth
  }
}

// * Ready state
let ready = computed(() => store.getters['getReady'])

// * Loading state
let loading = computed(() => store.getters['getLoading'])

// * Booked data
let booked = computed(() => store.getters['eventBooking/getBooked'])

// * Step index
const stepIndex = ref(0)

// * Step Index provide
provide('stepIndex', stepIndex)

// * Array of step components
const stepsArray = ref(
  [
    eventInfo,
    eventCustomerInfo,
    eventCongratulations
  ]
)

provide('stepsArray', stepsArray)

// * Array that is used for determination what step need to be added or removed from stepsArray
let eventFluidStepKey = ref([])
provide('eventFluidStepKey', eventFluidStepKey)

watch(eventFluidStepKey ,(current) => {
  // * Add tickets step
  if (current.indexOf('eventTickets') > -1 && stepsArray.value.find(step => step.name === 'EventTickets') === undefined) {
    stepsArray.value.splice((stepIndex.value + 1), 0, eventTickets)
  }

  // * Remove tickets step
  if (current.indexOf('eventTickets') < 0 && stepsArray.value.find(step => step.name === 'EventTickets') !== undefined) {
    let index = stepsArray.value.indexOf(eventTickets)
    stepsArray.value.splice(index, 1)
  }

  // * Add payment step
  if (current.indexOf('eventPayment') > -1 && stepsArray.value.find(step => step.name === 'EventPayment') === undefined) {
    stepsArray.value.splice((stepIndex.value + 1), 0, eventPayment)
  }

  // * Remove payment step
  if (current.indexOf('eventPayment') < 0 && stepsArray.value.find(step => step.name === 'EventPayment') !== undefined) {
    let index = stepsArray.value.indexOf(eventPayment)
    stepsArray.value.splice(index, 1)
  }

}, {deep: true})

// * Waiting list availability and options
let isWaitingList = computed(() => useWaitingListAvailability(store.getters['eventEntities/getEvent'](store.getters['eventBooking/getSelectedEventId'])))
watch(isWaitingList, (current) => {
  if (current) {
    let waitingOptions = JSON.parse(JSON.stringify(store.getters['eventEntities/getEvent'](store.getters['eventBooking/getSelectedEventId']).waitingList))
    store.commit('eventWaitingListOptions/setAllData', {...waitingOptions, isAvailable: isWaitingList.value})
  }
})


// * Bringing anyone with you - popup visibility
let bringingAnyoneVisibility = ref(false)

// * Bringing anyone provide
provide('bringingAnyoneVisibility', bringingAnyoneVisibility)

// * Footer btn flag
let footerButtonClicked = ref(false)
let footerBtnDisabled = ref(false)

let ifBookedCustomerCabinetUrl = computed(() => {
  if (booked.value !== null) {
    return booked.value.customerCabinetUrl.length > 0
  }

  return true
})

let customerCabinetButton = computed(() => {
  if (stepsArray.value[stepIndex.value].name === 'CongratulationsStep') {
    return !!amSettings.roles.customerCabinet.pageUrl && ifBookedCustomerCabinetUrl.value
  }

  return true
})

// * Move to next Form Step
function nextStep () {
  store.commit('setLoading', true)
  stepIndex.value = stepIndex.value + 1
}

// * Move to previous Form Step
function previousStep () {
  store.commit('setLoading', true)
  stepIndex.value = stepIndex.value - 1
  headerButtonPreviousClicked.value = !headerButtonPreviousClicked.value
}

// * Footer btn click functionality
function footerButtonClick () {
  footerButtonClicked.value = true

  let dialogEl = document.getElementsByClassName('am-dialog-el')[0]
  useScrollTo(dialogEl.children[0], dialogEl.children[0].children[0], 50, 300)
}

function footerButtonReset () {
  footerButtonClicked.value = false
}

function footerBtnDisabledUpdater (data) {
  footerBtnDisabled.value = data
}

// * Header btn click functionality
let headerButtonPreviousClicked = ref(false)
function headerButtonPreviousClick () {
  headerButtonPreviousClicked.value = true
}

function headerButtonPreviousReset () {
  headerButtonPreviousClicked.value = false
}

function secondButtonClick() {
  if (stepsArray.value[stepIndex.value].name === 'CongratulationsStep' && booked.value) {
    window.location.href = booked.value.customerCabinetUrl
  } else {
    popupVisible.value = false
  }
}

provide('secondButton', {
  secondButtonClick
})

provide('changingStepsFunctions', {
  nextStep,
  previousStep,
  footerButtonClick,
  footerButtonReset,
  footerBtnDisabledUpdater,
  footerButtonClicked,
  footerBtnDisabled,
  headerButtonPreviousClick,
  headerButtonPreviousReset,
  headerButtonPreviousClicked
})

// * Get Entities from server
store.dispatch(
  'eventEntities/requestEntities',
  {
    types: [
      'tags',
      'employees',
      'locations',
      'customFields',
      'taxes',
    ],
    loadEntities: shortcodeData.value.hasApiCall || shortcodeData.value.trigger,
  }
)

const loadDialogCounter = inject('loadDialogCounter', ref(null))

watch(loadDialogCounter, () => {
  if (shortcodeData.value.in_dialog && store.getters['eventEntities/getEventsDisplay']) {
    store.dispatch('eventEntities/requestEvents', store.getters['eventEntities/getEventsDisplay'])
  } else if (shortcodeData.value.in_dialog) {
    store.dispatch('eventEntities/requestEvents')
  }
})

onMounted(() => {
  document.getElementById(
    'amelia-v2-booking-' + shortcodeData.value.counter
  ).classList.add('amelia-v2-booking-' + shortcodeData.value.counter + '-loaded')

  watch(ready, (current) => {
    if(current) {
      useAction(store, {events, useEvents}, 'ViewContent', 'event', null, null)
    }
  })

  nextTick(() => {
    if(ameliaContainer.value) {
      containerWidth.value = ameliaContainer.value.offsetWidth
    }

    if (dialogContainer.value) {
      dialogWidth.value = dialogContainer.value.offsetWidth
    }
  })

  useAction(store, {containerWidth}, 'ContainerWidth', 'event', null, null)

  updateVH()
})

function onCloseEventDialog () {
  if (stepsArray.value[stepIndex.value].name === 'CongratulationsStep') {
    window.location.reload()
  }

  eventFluidStepKey.value = []
  stepIndex.value = 0
}

function onClosedEventDialog () {
  nextTick(() => {
    store.commit('eventBooking/setEventId', null)
    store.dispatch('persons/resetPersons')
    store.dispatch('coupon/resetCoupon')
    store.dispatch('tickets/resetCustomTickets')
    store.dispatch('eventWaitingListOptions/resetWaitingOptions')
    store.dispatch('payment/resetPaymentData')
  })
}

function onOpenedEventDialog () {
  nextTick(() => {
    if (dialogContainer.value) {
      dialogWidth.value = dialogContainer.value.offsetWidth
    }
  })
}

onMounted(() => {
    let restore = useRestore(store, shortcodeData.value)

    if (restore) {
      stepsArray.value.splice(0, stepsArray.value.length)

      stepIndex.value = 0

      restore.steps.forEach((key) => {
        switch (key) {
          case ('EventInfo'):
            stepsArray.value.push(eventInfo)

            break

          case ('EventTickets'):
            stepsArray.value.push(eventTickets)

            break

          case ('EventCustomerInfo'):
            stepsArray.value.push(eventCustomerInfo)

            break

          case ('EventPayment'):
            stepsArray.value.push(eventPayment)

            break

          case ('CongratulationsStep'):
            stepsArray.value.push(eventCongratulations)

            break
        }

        stepIndex.value++
      })

      let index = -1

      if (restore.result === 'success') {
        index = stepsArray.value.length - 1
      } else if (restore.result === 'error' || restore.result === 'canceled') {
        index = stepsArray.value.length - 2
      }

      stepIndex.value = index
      popupVisible.value = true
    }
})

// * Customized data form
let customizedDataForm = computed(() => {
  return amSettings.customizedData && 'elf' in amSettings.customizedData ? amSettings.customizedData.elf : defaultCustomizeSettings.elf
})
provide('customizedDataForm', customizedDataForm)

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

let customizedStepLabels = computed(() => {
  let customLabels = {}

  let customizedLabels = customizedDataForm.value[stepsArray.value[stepIndex.value].key].translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        customLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        customLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }

  return Object.keys(customLabels).length ? customLabels : labels
})

let customizedStepOptions = computed(() => {
  return customizedDataForm.value[stepsArray.value[stepIndex.value].key].options
})

// * Second button visibility
let secBtnShow = computed(() => {
  if ('secBtn' in customizedStepOptions.value && 'visibility' in customizedStepOptions.value.secBtn) {
    return customizedStepOptions.value.secBtn.visibility
  }

  return true
})

let waitingButtonTypeEnabled = computed(() => {
  return 'waitingBtn' in customizedStepOptions.value ? customizedStepOptions.value.waitingBtn.buttonType : 'filled'
})

// * Fonts
const amFonts = ref(amSettings.customizedData ? amSettings.customizedData.fonts : defaultCustomizeSettings.fonts)
provide('amFonts', amFonts)

// * Colors block
let amColors = computed(() => {
  return amSettings.customizedData && 'elf' in amSettings.customizedData ? amSettings.customizedData.elf.colors : defaultCustomizeSettings.elf.colors
})

provide('amColors', amColors);

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
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-font-family': amFonts.value.fontFamily,

    // -dvh- dynamic height
    '--am-dvh': dynamicVh.value ? `${dynamicVh.value}px` : '100dvh',

    // -mw- max width
    '--am-mw-main': '792px',
    // -hd- height dialog
    '--am-hd-main': stepIndex.value > 0 ? '592px' :'652px',
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),

    '--am-rad-inp': '6px',

    // -mb- margin bottom
    '--am-mb-dialog': '300px'
  }
})

function activateCustomFontStyles () {
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
</script>

<script>
export default {
  name: "EventsListFormWrapper"
}
</script>

<style lang="scss">
@import '../../../../assets/scss/public/overides/overides';
@import '../../../../assets/scss/common/reset/reset';
@import '../../../../assets/scss/common/icon-fonts/style';
@import '../../../../assets/scss/common/skeleton/skeleton.scss';
@import '../../../../assets/scss/common/empty-state/_empty-state-mixin.scss';
@import '../../../../assets/scss/common/transitions/_transitions-mixin.scss';

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
  --am-c-sb-bgr: #17295A;
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

.amelia-v2-booking {
  /* Event list dialog */
  // el - event list
  &.am-dialog-el.am-dialog-popup {
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 99999999 !important;

    .el-dialog {
      @media only screen and (max-width: 768px) {
        margin-top: 0;
      }

      #amelia-container {
        background-color: var(--am-c-main-bgr);
      }

      .el-dialog {
        // am - amelia
        // mb - margin bottom
        margin-bottom: var(--am-mb-dialog);
        border-radius: 8px;
        overflow: hidden;
        background-color: var(--am-c-main-bgr);

        * {
          box-sizing: border-box;
          font-family: var(--am-font-family), sans-serif;
          word-break: break-word;
        }

        &__header {
          padding: 0;
        }

        &__headerbtn {
          top: 8px;
          right: 12px;
          background: transparent !important;
          padding: 0;

          &:active {
            position: absolute;
            border: none;
            outline: 0;
          }
        }

        &__close {
          width: 12px;
          height: 12px;

          .am-icon-close:before {
            color: var(--am-c-main-text);
          }
        }

        &__body {
          padding: 0;
        }

        &__footer {
          display: none;
          padding: 0;
        }
      }
    }
  }

  #amelia-container {
    .el-skeleton {
      width: 100%;
      padding: 16px 32px;
    }

    // am - amelia
    // elf - events list form
    &.am-elf {
      margin: 0 auto;
      max-width: var(--am-mw-main);
      background-color: var(--am-c-main-bgr);
      padding: 16px;
      border-radius: 12px
    }

    * {
      font-family: var(--am-font-family), sans-serif;
      box-sizing: border-box;
      word-break: break-word;
    }

    // el - event list
    .am-dialog-el {
      &__main-container {
        max-height: var(--am-hd-main);
        display: block;
        overflow-x: hidden;
        padding: 16px 24px 24px;

        @media only screen and (max-width: 768px) {
          max-height: unset;
          height: calc(var(--am-dvh, 100vh) - 128px);

          &.am-eli {
            height: calc(var(--am-dvh, 100vh) - 68px);
          }
        }

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
