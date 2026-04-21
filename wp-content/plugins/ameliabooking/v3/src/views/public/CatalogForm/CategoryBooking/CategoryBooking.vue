<template>
  <template v-if="!amFonts.customFontSelected">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css" :href="`${baseUrls.wpAmeliaPluginURL}v3/src/assets/scss/common/fonts/font.css`" media="all">
  </template>
  <div
    v-if="!empty"
    id="amelia-container"
    ref="ameliaContainer"
    class="am-fs__wrapper"
    :class="{'am-collapsed': sidebarCollapsed}"
    :style="cssVars"
  >
    <SideBar
      v-if="containerWidth > 560 && sidebarVisibility"
      class="am-fs-sb"
      :class="{'am-collapsed': sidebarCollapsed}"
      :style="{width: !sidebarCollapsed ? '240px' : '72px', paddingBottom: `${sidebarFooterHeight + 16}px` }"
    >
      <template #step-list>
        <div class="am-fs-sb__step-wrapper" tabindex="0">
          <template v-if="stepsArray[stepIndex] !== congratulationsStep || !amSettings.general.addToCalendar || (booked && booked.data.length === 0)">
            <div
              v-for="step in sidebarSteps"
              :key="step.key"
              class="am-fs-sb__step"
            >
              <div class="am-fs-sb__step-inner">
                <div class="am-fs-sb__step-icon">
                  <span :class="`am-icon-${step.icon}`"></span>
                  <span
                    v-if="step.key === 'cartStep' && useCartHasItems(store) !== 0"
                    class="am-fs-sb__step-icon__number"
                    :class="sidebarCollapseItemsClass"
                  >
                    {{useCartHasItems(store)}}
                  </span>
                </div>
                <transition name="fade">
                  <p
                    v-if="!sidebarCollapsed"
                    class="am-fs-sb__step-heading"
                    :class="sidebarCollapseItemsClass"
                  >
                    {{ step.label }}
                  </p>
                </transition>
                <div
                  class="am-fs-sb__step-checker"
                  :class="[{'am-fs-sb__step-checker-selected': step.selected}, sidebarCollapseItemsClass]"
                >
                  <transition name="fade">
                    <span v-if="step.finished" class="am-icon-check"></span>
                  </transition>
                </div>
              </div>
              <TransitionGroup
                tag="span"
                name="fade"
                appear
                class="am-fs-sb__step-selection__wrapper"
                :class="{'am-fs-sb__step-selection-packages': step.key === packageAppointmentsStep.key && step.stepSelectedData.length > 3 }"
              >
                <template v-if="!sidebarCollapsed">
                  <p
                    v-for="(itemSelected, index) in step.stepSelectedData"
                    :key="itemSelected.position"
                    class="am-fs-sb__step-selection"
                  >
                    <span v-if="step.key !== packageAppointmentsStep.key || index < 3">
                      {{ itemSelected.value }}
                    </span>
                    <span v-else>
                      {{ amLabels.plus_more }}
                    </span>
                  </p>
                </template>
              </TransitionGroup>
            </div>
          </template>
          <template v-else>
            <div>
              <transition name="fade">
                <AddToCalendar v-if="!sidebarCollapsed"></AddToCalendar>
              </transition>
              <div v-if="sidebarCollapsed" class="am-fs-sb__step">
                <div class="am-fs-sb__step-inner">
                  <div class="am-fs-sb__step-icon">
                    <span :class="`am-icon-${stepsArray[stepIndex].sidebarData.icon}`"></span>
                  </div>
                  <div
                    class="am-fs-sb__step-checker"
                    :class="[{'am-fs-sb__step-checker-selected': stepsArray[stepIndex].sidebarData.selected}, sidebarCollapseItemsClass]"
                  >
                    <transition name="fade">
                      <span v-if="stepsArray[stepIndex].sidebarData.finished" class="am-icon-check"></span>
                    </transition>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </template>
      <template #support-info>
        <div
          ref="sidebarFooterRef"
          class="am-fs-sb__footer"
        >
          <div
            v-if="(amSettings.company.email || amSettings.company.phone)
            && (footerCustomizeOptions.heading || footerCustomizeOptions.phone || footerCustomizeOptions.email)"
            class="am-fs-sb__support"
          >
            <transition name="fade">
              <div
                v-if="!sidebarCollapsed && footerCustomizeOptions.heading"
                class="am-fs-sb__support-heading"
                :class="sidebarCollapseItemsClass"
              >
                {{ amLabels.get_in_touch }}
              </div>
            </transition>
            <a
              v-if="amSettings.company.phone && footerCustomizeOptions.phone"
              class="am-fs-sb__support-email"
              :aria-label="`Company phone: ${amSettings.company.phone}`"
              :href="`tel:${amSettings.company.phone}`"
            >
              <template v-if="!sidebarCollapsed">
                {{ amSettings.company.phone }}
              </template>
              <template v-else>
                <span class="am-icon-phone"></span>
              </template>
            </a>
            <a
              v-if="amSettings.company.email && footerCustomizeOptions.email"
              class="am-fs-sb__support-email"
              :aria-label="`Company email: ${amSettings.company.email}`"
              :href="`mailto:${amSettings.company.email}`"
            >
              <template v-if="!sidebarCollapsed">
                {{ amSettings.company.email }}
              </template>
              <template v-else>
                <span class="am-icon-email"></span>
              </template>
            </a>
          </div>
          <div
            class="am-fs-sb__menu"
            :class="sidebarCollapseItemsClass"
            @click="sidebarCollapsed = !sidebarCollapsed"
          >
            <Transition name="fade">
              <span
                v-if="!sidebarCollapsed"
                class="am-fs-sb__menu-text"
              >
                {{ amLabels.collapse_menu }}
              </span>
            </Transition>
            <span :class="`am-icon-arrow-circle-${sidebarCollapsed ? 'left' : 'right'}`"></span>
          </div>
        </div>
      </template>
    </SideBar>
    <MainContent>
      <template v-if="stepsArray[stepIndex] !== congratulationsStep" #header>
        <MainContentHeader :sidebar-visible="sidebarVisibility" :ready="ready"></MainContentHeader>
      </template>
      <template #step>
        <component :is="stepsArray[stepIndex]" global-class="am-fs__main-content" :show-cart="props.showCart && stepsArray[stepIndex] === dateTimeStep"></component>
      </template>
      <template #footer>
        <MainContentFooter
          :second-button-show="stepsArray[stepIndex] === congratulationsStep && !!amSettings.roles.customerCabinet.pageUrl"
          :add-to-cart-button-show="useCartStep(store) && stepsArray[stepIndex] === cartStep"
          :back-to-cart-button-show="useCartStep(store) && cart.length > 1 && stepsArray[stepIndex] !== cartStep && stepsArray[stepIndex] !== infoStep && stepsArray[stepIndex] !== paymentStep && stepsArray[stepIndex] !== congratulationsStep"
          :booked="booked"
          :loading="loading"
          :ready="ready"
          :payment-gateway="paymentGateway"
          :customized-labels="footerLabels"
          :primary-footer-button-type="primFooterBtnType"
          :secondary-footer-button-type="secFooterBtnType"
          :add-to-cart-button-type="addToCartBtnType"
          :back-to-cart-button-type="backToCartBtnType"
          :back-to-cart-label="dedicatedStepLabel('cancel', 'cartStep')"
          @add-to-cart="addToCart"
          @back-to-cart="backToCart"
        ></MainContentFooter>
      </template>
    </MainContent>
  </div>
  <template v-else>
    <div class="am-no-services">
      <img
        :src="baseUrls.wpAmeliaPluginURL+'/v3/src/assets/img/am-empty-booking.svg'"
        style="margin-top: 10px;"
        :alt="amLabels.no_services_employees"
      >
      <h1>{{amLabels.oops}}</h1>
      <h3>{{amLabels.no_services_employees}}</h3>
      <p>{{amLabels.add_services_employees}}</p>
      <a href="https://wpamelia.com/services-and-categories/" rel="nofollow">
        {{amLabels.add_services_url}}&nbsp;
      </a>
      <span style="font-size:14px">{{amLabels.and}}&nbsp;</span>
      <a href="https://wpamelia.com/employees/" rel="nofollow">
        {{amLabels.add_employees_url}}
      </a>
    </div>
  </template>
</template>

<script setup>
// * Form construction
import MainContent from '../../../common/SbsFormConstruction/MainContent/MainContent.vue'
import MainContentHeader from '../../../common/SbsFormConstruction/MainContent/parts/MainContentHeader.vue'
import MainContentFooter from '../../../common/SbsFormConstruction/MainContent/parts/MainContentFooter'
import SideBar from '../../../common/SbsFormConstruction/SideBar/SideBar.vue'

// * Step components
import InitStep from '../../StepForm/InitStep/InitStep.vue'
import BringingAnyone from '../../StepForm/BringingAnyone/BringingAnyone'
import Extras from '../../StepForm/Extras/Extras.vue'
import PackageInfo from '../../StepForm/PakagesStep/PackageInfoStep'
import DateTimeStep from '../../StepForm/DateTimeStep/DateTimeStep.vue'
import PackageAppointmentsStep from '../../StepForm/PakagesStep/PackageAppointmentsStep.vue'
import PackageAppointmentsListStep from '../../StepForm/PakagesStep/PackageAppointmentsListStep.vue'
import CartStep from '../../StepForm/CartStep/CartStep.vue'
import InfoStep from '../../StepForm/InfoStep/InfoStep.vue'
import Congratulations from '../../StepForm/Congratulations/Congratulations.vue'
import AddToCalendar from '../../StepForm/Congratulations/AddToCalendar.vue'
import PaymentStep from '../../StepForm/PaymentStep/PaymentStep.vue'
import PackagesStep from '../../StepForm/PakagesStep/PackageStep.vue'
import RecurringStep from '../../StepForm/RecurringStep/RecurringStep.vue'
import RecurringSummary from '../../StepForm/RecurringStep/RecurringSummary.vue'

// * import from Vue
import {
  ref,
  reactive,
  watch,
  provide,
  inject,
  markRaw,
  computed,
  onBeforeMount,
  onMounted,
  watchEffect,
  nextTick
} from 'vue'
// * import from Vuex
import { useStore } from 'vuex'
// * import composable
import { useBuildPackage } from '../../../../assets/js/public/package.js'
import { defaultCustomizeSettings } from '../../../../assets/js/common/defaultCustomize.js'
import useAction from "../../../../assets/js/public/actions";
import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";
import {
  useCapacity,
  usePrepaidPrice,
  useCheckingIfAllNotFree,
} from "../../../../assets/js/common/appointments";
import { useRenderAction } from "../../../../assets/js/public/renderActions";
import {
  useAddToCart,
  useCart,
  useCartHasItems,
  useCartStep,
  useGoToCartStep,
  useInitCartItem,
  useInitSelection
} from '../../../../assets/js/public/cart'

let props = defineProps({
  newBooking: {
    type: Boolean,
    default: false
  },
  showCart: {
    type: Boolean,
    default: false
  }
})

const emits = defineEmits(['addToCart', 'emptiedCart'])

// * Component reference
let ameliaContainer = ref(null)
// * Plugin wrapper width
let containerWidth = ref()
provide('containerWidth', containerWidth)

let empty = ref(false)

// * window resize listener
window.addEventListener('resize', resize);
// * resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
}

onMounted(() => {
  nextTick(() => {
    if(ameliaContainer.value) {
      containerWidth.value = ameliaContainer.value.offsetWidth
    }
  })

  useAction(store, {containerWidth}, 'ContainerWidth', 'appointment', null, null)
})

// * Root Settings
const amSettings = inject('settings')

// * Customize
const amCustomize = (amSettings.customizedData && amSettings.customizedData.sbsNew) ? amSettings.customizedData.sbsNew : defaultCustomizeSettings.sbsNew
if (amCustomize) {
  provide('amCustomize', amCustomize)
}

let footerCustomizeOptions = computed(() => {
  let obj = {
    heading: true,
    phone: true,
    email: true
  }

  if ('supportHeading' in amCustomize.sidebar.options) {
    obj.heading = amCustomize.sidebar.options.supportHeading.visibility
  }

  if ('companyPhone' in amCustomize.sidebar.options) {
    obj.phone = amCustomize.sidebar.options.companyPhone.visibility
  }

  if ('companyEmail' in amCustomize.sidebar.options) {
    obj.email = amCustomize.sidebar.options.companyEmail.visibility
  }

  return obj
})

// * Fonts
const amFonts = ref(amSettings.customizedData ? amSettings.customizedData.fonts : defaultCustomizeSettings.fonts)
provide('amFonts', amFonts)

// * Form Sidebar Collapse
let sidebarCollapsed = ref(false)
provide('sidebarCollapsed', sidebarCollapsed)

let sidebarCollapseItemsClass = ref('')

let bookingDialogWidth = inject('bookingDialogWidth')

watch(sidebarCollapsed ,(current) => {
  if (current) {
    setTimeout(() => {
      sidebarCollapseItemsClass.value = 'am-collapsed'
      setTimeout(() => {
        bookingDialogWidth.value = '592px'
      }, 200)
    }, 1000)
  } else {
    sidebarCollapseItemsClass.value = ''
    bookingDialogWidth.value = '760px'
  }
})

onMounted(() => {
  if (!amCustomize.sidebar.options.self.visibility) {
    bookingDialogWidth.value = '520px'
  }
})

let sidebarFooterRef = ref(null)
let sidebarFooterHeight = ref(0)

onMounted(() => {
  setTimeout(() => {
    nextTick(() => {
      if (sidebarFooterRef.value) {
        setTimeout(() => {
          if (sidebarFooterRef.value) {
            sidebarFooterHeight.value = sidebarFooterRef.value.offsetHeight
          }
        }, 200)
      }
    })
  }, 800)
})

// * Form Sidebar Visibility
let sidebarVisibility = ref(amCustomize.sidebar ? amCustomize.sidebar.options.self.visibility : true)

// * Root Urls
const baseUrls = inject('baseUrls')

// * Define store
const store = useStore()

let preselected = computed(() => {
  let originalPreselected = store.getters['entities/getOriginalPreselected']

  let bookingPreselected = {
    category: store.state.booking.appointment.categoryId ? [store.state.booking.appointment.categoryId] : [],
    counter: store.state.entities.preselected.counter,
    employee: store.state.booking.appointment.providerId ? [store.state.booking.appointment.providerId] : [],
    hasApiCall: store.state.entities.preselected.hasApiCall,
    location: store.state.booking.appointment.locationId ? [store.state.booking.appointment.locationId] : [],
    service: store.state.booking.appointment.serviceId ? [store.state.booking.appointment.serviceId] : [],
    package: store.state.booking.packageId ? [store.state.booking.packageId] : [],
    show: store.state.booking.appointment.type === 'package' ? 'packages' : 'services',
    trigger: "",
  }

  Object.keys(originalPreselected).forEach((key) => {
    if (!(key in bookingPreselected)) {
      bookingPreselected[key] = originalPreselected[key]
    }
  })

  return bookingPreselected
})

store.commit('entities/setPreselected', preselected.value)

let restore = inject('restoreFormData')

// * Form Component Collection
const initStep = markRaw(InitStep)
const extras = markRaw(Extras)
const packageInfoStep = markRaw(PackageInfo)
const recurringStep = markRaw(RecurringStep)
const recurringSummary = markRaw(RecurringSummary)
const dateTimeStep = markRaw(DateTimeStep)
const packageAppointmentsStep = markRaw(PackageAppointmentsStep)
const packageAppointmentsListStep = markRaw(PackageAppointmentsListStep)
const congratulationsStep = markRaw(Congratulations)
const cartStep = markRaw(CartStep)
const infoStep = markRaw(InfoStep)
const paymentStep = markRaw(PaymentStep)
const packagesStep = markRaw(PackagesStep)
const bringingAnyone = markRaw(BringingAnyone)

// * Array of step components
const stepsArray = ref(
  useCartStep(store) ? [
    initStep,
    dateTimeStep,
    cartStep,
    infoStep,
    congratulationsStep
  ] : [
    initStep,
    dateTimeStep,
    infoStep,
    congratulationsStep
  ]
)

provide('stepsArray', stepsArray)

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

let cart = useCart(store)

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  if (amSettings.customizedData && amSettings.customizedData.sbsNew) {
    Object.keys(amSettings.customizedData.sbsNew).forEach(stepKey => {
      if (stepKey !== 'colors' && amSettings.customizedData.sbsNew[stepKey].translations) {
        let customizedLabels = amSettings.customizedData.sbsNew[stepKey].translations
        Object.keys(customizedLabels).forEach(labelKey => {
          if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
            computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
          } else if (customizedLabels[labelKey].default) {
            computedLabels[labelKey] = customizedLabels[labelKey].default
          }
        })
      }
    })
  }
  return computedLabels
})

provide('amLabels', amLabels)

let footerLabels = computed(() => {
  let customLabels = {}
  if (amSettings.customizedData && amSettings.customizedData.sbsNew) {
    let customizedLabels = amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key] ?
      amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key].translations :
      null
    if (amSettings.customizedData && customizedLabels) {
      Object.keys(customizedLabels).forEach(labelKey => {
        if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
          customLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
        } else if (customizedLabels[labelKey].default) {
          customLabels[labelKey] = customizedLabels[labelKey].default
        }
      })
    }
  }

  return Object.keys(customLabels).length ? customLabels : labels
})

let primFooterBtnType = computed(() => {
  let btnType = 'filled'
  if (amSettings.customizedData &&
      amSettings.customizedData.sbsNew &&
      amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key]) {
    btnType = amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key].options.primaryFooterButton.buttonType
  }

  return btnType
})

provide('primFooterBtnType', primFooterBtnType)

provide('primDescBtnType', primFooterBtnType)

let secFooterBtnType = computed(() => {
  let btnType = 'text'
  if (
    amSettings.customizedData &&
    amSettings.customizedData.sbsNew &&
    amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key] &&
    amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key].options.secondaryFooterButton
  ) {
    btnType = amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key].options.secondaryFooterButton.buttonType
  }

  return btnType
})

let addToCartBtnType = computed(() => {
  if (amSettings.customizedData && amSettings.customizedData.sbsNew && amSettings.customizedData.sbsNew['cartStep']) {
    return amSettings.customizedData.sbsNew['cartStep'].options['addToCart'].buttonType
  }

  return 'text'
})

let backToCartBtnType = computed(() => {
  if (amSettings.customizedData && amSettings.customizedData.sbsNew && amSettings.customizedData.sbsNew['cartStep']) {
    return amSettings.customizedData.sbsNew['cartStep'].options['backToCart'].buttonType
  }

  return 'text'
})

function dedicatedStepLabel (labelKey, stepKey) {
  let customLabel = ''
  if (amSettings.customizedData && amSettings.customizedData.sbsNew) {
    let customizedLabels =  amSettings.customizedData.sbsNew[stepKey] ?
      amSettings.customizedData.sbsNew[stepKey].translations :
      null
    if (amSettings.customizedData && customizedLabels && customizedLabels[labelKey]) {
      if (customLabel === '' && customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        customLabel = customizedLabels[labelKey][localLanguage.value]
      } else if (customLabel === '' && customizedLabels[labelKey].default) {
        customLabel = customizedLabels[labelKey].default
      }
    }
  }

  return customLabel ? customLabel : labels[labelKey]
}

let bringingAnyoneOptions = computed(() => {
  return useCapacity(
    store.getters['entities/getEmployeeServices'](
      store.getters['booking/getSelection']
    )
  )
})

function setShortcodeParams () {
  let preselected = store.getters['entities/getPreselected']

  if (preselected.show === 'packages') {
    if (preselected.package.length > 0) {
      stepsArray.value.splice(0, 1)
      sidebarSteps.value.splice(0, 1)
      selectionFromAvailablePackages()
    }
    sidebarDataUpdate()
  } else {
    changeInitStepDataService()
    let employeeOptions = store.getters["entities/filteredEmployees"](store.getters['booking/getSelection']).length <= 1
    let employeeVisibility = !amCustomize.initStep.options.employee.visibility
    let locationOptions = store.getters['entities/filteredLocations'](store.getters['booking/getSelection']).length <= 1
    let locationVisibility = !amCustomize.initStep.options.location.visibility

    if (
      preselected.service.length > 0 &&
      (preselected.employee.length > 0 || employeeOptions || employeeVisibility) &&
      (preselected.location.length > 0 || locationOptions || locationVisibility)
    ) {
      if (bringingAnyoneOptions.value.availability) {
        stepsArray.value.splice(0, 1, bringingAnyone);
        sidebarSteps.value.splice(0, 1)
      } else {
        stepsArray.value.splice(0, 1)
        sidebarSteps.value.splice(0, 1)
      }
      sidebarDataUpdate()
    }
  }
}

function stepChanger (steps, stepsNamesToRemove, stepsToAdd, startIndex) {
  let removeIndexes = steps.value.map(
    (item, index) => (stepsNamesToRemove.includes(item.name)) ? index : null
  ).filter(item => item !== null).reverse()

  removeIndexes.forEach((item) => {
    steps.value.splice(item, 1)
  })

  let stepIndex = {
    value: startIndex
  }

  stepsToAdd.forEach((item) => {
    if (steps.value.indexOf(item) === -1) {
      if (store.getters['entities/getPreselected'].show === 'packages' && store.getters['entities/getPreselected'].package.length > 0) {
        steps.value.splice(stepIndex.value, 0, item)
      } else {
        steps.value.splice(stepIndex.value + 1, 0, item)
      }
    }
  })
}

provide('goToPackageStep', {goToPackageStep})

function goToPackageStep (pack) {
  store.commit('booking/setPackageId', pack.id)
  store.commit('booking/setBookableType', 'package')
  store.commit('booking/setMultipleAppointments', useBuildPackage(0, pack))
  store.commit('booking/setMultipleAppointmentsIndex', 0)

  let bookingCount = pack.bookable.reduce((partialSum, book) => partialSum + book.maximumScheduled, 0)

  let removeSteps = []
  let addSteps = []

  if (stepsArray.value[0] !== extras) {
    removeSteps.push('ExtrasStep')
  }

  if (stepsArray.value[0] !== dateTimeStep) {
    removeSteps.push('DateTimeStep')
  }

  if (stepsArray.value[0] !== cartStep) {
    removeSteps.push('CartStep')
  }

  if (bookingCount > 0) {
    addSteps = addSteps.concat([packageAppointmentsListStep, packageAppointmentsStep, packageInfoStep])
  } else {
    addSteps.push(packageInfoStep)
  }

  stepChanger(
    stepsArray,
    removeSteps,
    addSteps,
    stepIndex.value
  )

  stepChanger(
    sidebarSteps,
    removeSteps,
    [],
    stepIndex.value
  )

  sidebarDataUpdate()
  nextStep()
}

function selectionFromAvailablePackages () {
  let pack = store.state.entities.packages.find(a => a.id === store.getters['booking/getPackageId'])
  let bookingCount = pack.bookable.reduce((partialSum, book) => partialSum + book.maximumScheduled, 0)

  let removeSteps = []
  let addSteps = []
  if (stepsArray.value.find(a => a.name === 'ExtrasStep')) {
    removeSteps.push('ExtrasStep')
  }
  if (stepsArray.value.find(a => a.name === 'DateTimeStep')) {
    removeSteps.push('DateTimeStep')
  }

  if (bookingCount > 0) {
    addSteps = addSteps.concat([packageAppointmentsListStep, packageAppointmentsStep, packageInfoStep])
  } else {
    addSteps.push(packageInfoStep)
  }

  stepChanger(
    stepsArray,
    removeSteps,
    addSteps,
    stepIndex.value
  )

  stepChanger(
    sidebarSteps,
    removeSteps,
    [],
    stepIndex.value
  )

  sidebarDataUpdate()
  nextStep()
}

function removePackageStep () {
  store.commit('booking/setBookableType', 'appointment')

  store.commit('booking/setMultipleAppointments', [
    {
      packageId: null,
      serviceId: null,
      index: 0,
      services: {},
    }
  ])

  store.commit('booking/setPackageId', null)

  store.commit('booking/setMultipleAppointmentsIndex', 0)

  useInitCartItem(store)

  let addSteps = []

  if (stepsArray.value[0] !== dateTimeStep) {
    if (useCartStep(store)) {
      addSteps.push(cartStep)
    }

    addSteps.push(dateTimeStep)
  }

  if (stepsArray.value[0] !== extras && selectedServiceExtras.value && selectedServiceExtras.value.length) {
    addSteps.push(extras)
  }

  stepChanger(
    stepsArray,
    ['PackageInfoStep', 'PackageAppointmentsStep', 'PackageAppointmentsListStep'],
    addSteps,
    stepIndex.value - 1
  )

  stepChanger(
    sidebarSteps,
    ['PackageInfoStep', 'PackageAppointmentsStep', 'PackageAppointmentsListStep'],
    [],
    stepIndex.value
  )

  sidebarDataUpdate()
}

provide('goToRecurringStep', {goToRecurringStep})

provide('removeRecurringStep', {removeRecurringStep})

function goToRecurringStep () {
  if (useCartStep(store)) {
    removeCartStep()
  }

  let startIndex = 0

  for (let i = 0; i < stepsArray.value.length; i++) {
    if (stepsArray.value[i].name === 'DateTimeStep') {
      startIndex = i

      break
    }
  }

  stepsArray.value.splice( startIndex + 1, 0, recurringStep)
  stepsArray.value.splice( startIndex + 2, 0, recurringSummary)

  sidebarDataUpdate()

  nextStep()
}

function removeRecurringStep () {
  for (let i = stepsArray.value.length - 1; i >= 0; i--) {
    if (stepsArray.value[i].name === 'RecurringStep' || stepsArray.value[i].name === 'RecurringSummary') {
      stepsArray.value.splice( i + 1, 1)
    }
  }

  if (useCartStep(store)) {
    addCartStep(stepIndex.value)
  }

  sidebarDataUpdate()
}

/**
 * Detect if Selected Service has Extras
 * @type {any}
 */
let selectedServiceExtras = computed(() => {
  let service = store.getters['entities/getService'](
    store.getters['booking/getServiceId']
  )

  return service ? service.extras : []
})

/**
 * Add or Remove steps from Steps Array
 */
function changeInitStepDataService () {
  if (selectedServiceExtras.value.length && !stepsArray.value.find(step => step.name === 'ExtrasStep')) {
    stepsArray.value.splice( stepIndex.value + 1, 0, extras)

    sidebarDataUpdate()
  }

  if (sidebarSteps.value.find(step => step.name === 'ExtrasStep') && !Object.keys(selectedServiceExtras.value).length) {
    stepsArray.value.splice(1, 1)
    sidebarSteps.value.splice(1, 1)
  }

  if (!props.showCart) {
    useInitCartItem(store)
  }
}

provide('initDataChanges', {
  changeInitStepDataService
})

// * Step index
const stepIndex = ref(0)
// * StepIndex provide
provide('stepIndex', stepIndex)

let stepName = inject('stepName', ref(''))

// * Monitoring step index for step selection
watch(stepIndex, (currStepIndex, prevStepIndex) => {
  if (currStepIndex < sidebarSteps.value.length) {
    sidebarSteps.value[prevStepIndex].selected = false
    sidebarSteps.value[currStepIndex].selected = true
  }
  if (currStepIndex === sidebarSteps.value.length) {
    sidebarSteps.value[prevStepIndex].selected = false
  }

  stepName.value = stepsArray.value[currStepIndex].name
})

let goBackToPackageBooking = ref(false)
provide('goBackToPackageBooking', goBackToPackageBooking)

// Provide cart step controls for nested components (e.g., Calendar). Bind current step index.
provide('addCartStep', { addCartStep: () => addCartStep(stepIndex.value) })
provide('removeCartStep', { removeCartStep })

/**
 * Move to previous Form Step
 */
function previousStep () {
  footerBtnDisabledUpdater(false)

  if (!navigateInsideStep.value) {
    if (stepsArray.value[stepIndex.value].name === packageInfoStep.name) {
      removePackageStep()
    }

    if (stepsArray.value[stepIndex.value].name === recurringStep.name) {
      stepsArray.value.splice(stepIndex.value, 2)
      sidebarSteps.value.splice(stepIndex.value, 2)

      if (useCartStep(store)) {
        stepsArray.value.splice(stepIndex.value, 0, cartStep)

        stepChanger(
          sidebarSteps,
          [],
          [],
          stepIndex.value
        )

        sidebarDataUpdate()
      }
    }

    if (stepsArray.value[stepIndex.value].name === packageAppointmentsListStep.name) {
      goBackToPackageBooking.value = true
    }

    stepIndex.value = stepIndex.value - 1
  }

  headerButtonPreviousClicked.value = !headerButtonPreviousClicked.value
}

/**
 * Move to next Form Step
 */
function nextStep () {
  if (!navigateInsideStep.value) {
    sidebarSteps.value[stepIndex.value].finished = true
    stepIndex.value = stepIndex.value + 1
  }
}

let ready = computed(() => {
  return store.getters['entities/getReady']
})

// * Footer states
let loading = computed(() => store.getters['booking/getLoading'])
let booked = computed(() => store.getters['booking/getBooked'])
let paymentGateway = computed(() => store.getters['booking/getPaymentGateway'])

// * Footer btn flag
let footerButtonClicked = ref(false)
let footerBtnDisabled = ref(false)

let headerButtonPreviousClicked = ref(false)

/**
 * Footer btn clicked
 */
function footerButtonClick () {
  footerButtonClicked.value = true
}

/**
 * Footer btn reset
 */
function footerButtonReset () {
  footerButtonClicked.value = false
}

/**
 * Header btn previous clicked
 */
function headerButtonPreviousClick () {
  headerButtonPreviousClicked.value = true
}

/**
 * Header btn previous reset
 */
function headerButtonPreviousReset () {
  headerButtonPreviousClicked.value = false
}

function footerBtnDisabledUpdater (data) {
  footerBtnDisabled.value = data
}

function secondButtonClick() {
  if (booked.value) {
    if (booked.value.customerCabinetUrl) {
      window.location.href = booked.value.customerCabinetUrl
    } else {
      window.location.href = amSettings.roles.customerCabinet.pageUrl
    }
  }
}

provide('secondButton', {
  secondButtonClick
})

let navigateInsideStep = ref(false)

provide('changingStepsFunctions', {
  nextStep,
  previousStep,
  footerButtonClick,
  footerButtonReset,
  footerBtnDisabledUpdater,
  headerButtonPreviousClick,
  headerButtonPreviousReset,
  footerBtnDisabled,
  footerButtonClicked,
  headerButtonPreviousClicked,
  navigateInsideStep,
})

// * Array of Sidebar steps
const sidebarSteps = ref([])
provide('sidebarSteps', sidebarSteps)

/**
 * Collecting and rearranging selected data in form sidebar based on item position in form
 * @param data
 */
function sidebarDataCollector (data) {
  // Determines are selected data already exists
  if (sidebarSteps.value[stepIndex.value].stepSelectedData.filter(item => item.reference === data.reference).length) {
    sidebarSteps.value[stepIndex.value].stepSelectedData.forEach((item, index, array) => {
      // Handles changing value of existing data
      if ((item.reference === data.reference) && data.value && data.value !== item.value) {
        item.value = data.value
      }

      // Removes un-selected form data from sidebar array
      if ((item.reference === data.reference) && !data.value) {
        array.splice(index, 1)
      }
    })
  } else {
    if (data.value) {
      sidebarSteps.value[stepIndex.value].stepSelectedData.push(data)
    }
  }

  // Rearranges data in sidebar based on item position in the form
  sidebarSteps.value[stepIndex.value].stepSelectedData.sort((a, b) => {
    return a.position - b.position
  })
}

/**
 * Function that creates Data Array for SideBar
 */
function sidebarDataCreator () {
  stepsArray.value.forEach((item, index) => {
    if (item.name !== "CongratulationsStep") {
      let labelKey = item.sidebarData.label
      let step = {
        name: item.name,
        key: item.key,
        label: amLabels.value[labelKey],
        labelKey: labelKey,
        icon: item.sidebarData.icon,
        stepSelectedData: [],
        finished: false,
        selected: index === stepIndex.value,
      }
      sidebarSteps.value.splice(index, 0, step)
    }
  })
}

/**
 * Lifecycle Hooks
 */
onBeforeMount(() => {
  sidebarDataCreator()
  setShortcodeParams()
})

onMounted(() => {
  switch (store.getters['booking/getBookableType']) {
    case ('appointment'):
      if (!props.showCart) {
        useInitCartItem(store)
      }

      break
  }

  if (props.showCart) {
    useGoToCartStep(stepsArray, stepIndex)
  }

  if (restore.value) {
    stepsArray.value.splice(0, stepsArray.value.length)

    sidebarSteps.value.splice(0, sidebarSteps.value.length)

    stepIndex.value = 0

    restore.value.steps.forEach((key) => {
      switch (key) {
        case ('packageStep'):
          stepsArray.value.push(packagesStep)
          break

        case ('bringingAnyone'):
          stepsArray.value.push(bringingAnyone)
          break

        case ('initStep'):
          stepsArray.value.push(initStep)
          break

        case ('packageInfoStep'):
          stepsArray.value.push(packageInfoStep)
          break

        case ('packageAppointmentsStep'):
          stepsArray.value.push(packageAppointmentsStep)
          break

        case ('packageAppointmentsListStep'):
          stepsArray.value.push(packageAppointmentsListStep)
          break

        case ('extrasStep'):
          stepsArray.value.push(extras)
          break

        case ('dateTimeStep'):
          stepsArray.value.push(dateTimeStep)
          break

        case ('recurringStep'):
          stepsArray.value.push(recurringStep)
          break

        case ('recurringSummary'):
          stepsArray.value.push(recurringSummary)
          break

        case ('cartStep'):
          stepsArray.value.push(cartStep)
          break

        case ('infoStep'):
          stepsArray.value.push(infoStep)
          break

        case ('paymentStep'):
          stepsArray.value.push(paymentStep)
          break

        case ('congratulations'):
          stepsArray.value.push(congratulationsStep)
          break
      }

      let sideBarItem = restore.value.sidebar.find(i => i.key === key)

      sidebarDataUpdate()

      if (typeof sideBarItem !== 'undefined') {
        if (sideBarItem.data) {
          sideBarItem.data.forEach((i) => {
            sidebarDataCollector({
              reference: i.reference,
              position: i.position,
              value: i.value
            })
          })
        }
      }

      stepIndex.value++
    })

    store.commit('booking/setLoading', false)

    let index = -1

    if (restore.value.result === 'success') {
      index = stepsArray.value.length - 1
    } else if (restore.value.result === 'error' || restore.value.result === 'canceled') {
      index = stepsArray.value.length - 2
    }

    for (let i = 0; i <= index; i++) {
      stepsArray.value[i].finished = true
    }

    for (let i = 0; i <= index - 1; i++) {
      sidebarSteps.value[i].finished = true
      sidebarSteps.value[i].selected = false
    }

    if (restore.value.result === 'error' || restore.value.result === 'canceled') {
      sidebarSteps.value[index].finished = false
      sidebarSteps.value[index].selected = true
    }

    stepIndex.value = index
  }
})

/**
 * Function that update SideBar Data Array when some component are dynamically added
 */
function sidebarDataUpdate () {
  stepsArray.value.forEach((item, index) => {
    if (item.name === 'CongratulationsStep') {
      return
    }
    if (!sidebarSteps.value.find(block => block.labelKey === item.sidebarData.label)) {
      let labelKey = item.sidebarData.label
      let step = {
        name: item.name,
        key: item.key,
        label: amLabels.value[labelKey],
        labelKey: labelKey,
        icon: item.sidebarData.icon,
        stepSelectedData: [],
        finished: false,
        selected: index === stepIndex.value,
      }
      sidebarSteps.value.splice(index, 0, step)
    }
  })
}
provide('sidebarStepsFunctions', {
  sidebarDataCollector
})

provide('addPaymentsStep', {addPaymentsStep})

provide('removePaymentsStep', {removePaymentsStep})

function addPaymentsStep () {
  stepsArray.value.splice(stepsArray.value.length - 1, 0, paymentStep)

  sidebarDataUpdate()
}

function removePaymentsStep () {
  let removeSteps = ['PaymentStep']

  stepChanger(
    stepsArray,
    removeSteps,
    [],
    stepIndex.value
  )

  stepChanger(
    sidebarSteps,
    removeSteps,
    [],
    stepIndex.value
  )
}

function addCartStep (index) {
  stepChanger(
    stepsArray,
    [],
    [cartStep],
    index
  )

  stepChanger(
    sidebarSteps,
    [],
    [],
    index
  )

  sidebarDataUpdate()
}

function removeCartStep () {
  stepChanger(
    stepsArray,
    ['CartStep'],
    [],
    stepIndex.value
  )

  stepChanger(
    sidebarSteps,
    ['CartStep'],
    [],
    stepIndex.value
  )

  sidebarDataUpdate()
}

let keepPaymentStep = computed(() => useCart(store).length ? usePrepaidPrice(store) !== 0 : useCheckingIfAllNotFree(store))

// Appointment waiting list flag
const isWaitingListBooking = computed(() => store.getters['appointmentWaitingListOptions/getIsWaitingListSlot'])

watchEffect(() => {
  // If waiting list, remove payment step
  if (isWaitingListBooking.value) {
    removePaymentsStep()
    return
  }

  let cart = useCart(store)

  if (!cart[0].serviceId && Object.keys(cart[0].services).length === 0) {
    emits('emptiedCart')
  }

  if (!restore.value && !keepPaymentStep.value) {
    removePaymentsStep()
  } else if (!restore.value) {
    if (stepsArray.value.indexOf(paymentStep) === -1) {
      addPaymentsStep()
    }
  }
}, {flush: 'post'})

// * Cart
function addToCart () {
  sidebarSteps.value.forEach((item) => {
    item.finished = false
    item.selected = false
  })

  stepsArray.value.forEach((item) => {
    item.finished = false
    item.selected = false
  })

  stepIndex.value = 0

  useInitSelection(store, true)

  useAddToCart(store)

  useInitCartItem(store)

  emits('addToCart')
}

function backToCart () {
  let currentCartItem = store.getters['booking/getCurrentCartItem']

  let items = store.getters['booking/getAllMultipleAppointments']

  if (currentCartItem) {
    store.commit('booking/setCartItem', currentCartItem)

    store.commit('booking/setCartItemIndex', items.length - 1)

    store.commit('booking/setCurrentCartItem', null)
  } else {
    items.pop()

    store.commit('booking/setCartItemIndex', items.length - 1)

    store.commit('booking/setServiceId', items[items.length - 1].serviceId)

    store.commit('booking/setEmployeeId', items[items.length - 1].services[items[items.length - 1].serviceId].providerId)

    store.commit('booking/setLocationId', items[items.length - 1].services[items[items.length - 1].serviceId].locationId)
  }

  useGoToCartStep(stepsArray, stepIndex)
}

// * Colors block
let amColors = computed(() => {
  return (amSettings.customizedData && amSettings.customizedData.sbsNew) ? amSettings.customizedData.sbsNew.colors : defaultCustomizeSettings.sbsNew.colors
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
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-btn-sec': amColors.value.colorBtnSec,
    '--am-c-btn-sec-text': amColors.value.colorBtnSecText,
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-font-family': amFonts.value.fontFamily,

    // css properties
    // -mw- max width
    // -brad- border-radius
    '--am-mw-main': sidebarVisibility.value ? sidebarCollapsed.value ? '592px' : '760px' : '520px',
    '--am-brad-main': sidebarVisibility.value ? '0 0.5rem 0.5rem 0' : '0.5rem'
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

// * Design Properties
let amDesignProperties = computed(() => {
  return {
    colorInputBorderRadius: '6px',
  }
})
provide('amDesignProperties', amDesignProperties);

onMounted(() => {
  useRenderAction(
    'renderForm',
    {
      ameliaContainer,
      containerWidth,
      empty,
      amCustomize,
      amFonts,
      sidebarCollapsed,
      sidebarCollapseItemsClass,
      sidebarFooterRef,
      sidebarFooterHeight,
      sidebarVisibility
    },
  )
})
</script>

<script>
export default {
  name: "CategoryBooking"
}
</script>

<style lang="scss">
@import '../../../../../src/assets/scss/public/overides/overides';
@import '../../../../../src/assets/scss/common/reset/reset';
@import '../../../../../src/assets/scss/common/icon-fonts/style';
@import '../../../../../src/assets/scss/common/skeleton/skeleton.scss';

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

// am -- amelia
// fs -- form steps
// sb -- sidebar
.amelia-v2-booking {
  background-color: transparent;

  #amelia-container {
    background-color: transparent;
    * {
      font-family: var(--am-font-family);
      font-style: initial;
      box-sizing: border-box;
    }

    &.am-fs {
      // Container Wrapper
      &__wrapper {
        display: flex;
        justify-content: center;
        max-width: var(--am-mw-main);
        width: 100%;
        height: 560px;
        margin: 100px auto;
        border-radius: 8px;
        box-shadow: 0 30px 40px rgba(0, 0, 0, 0.12);
        transition: max-width 0.3s ease-in-out;

        &.am-collapsed {
          transition-delay: 1s;
        }

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
      }
    }
  }

  .am-no-services {
    box-shadow: 0 30px 40px rgba(0, 0, 0, 0.12);
    margin: 100px auto;
    text-align: center;
    padding: 56px;
    max-width: 760px;
    height: 460px;
    width: 100%;

    p {
      margin-bottom: 8px;
      padding: 0;
    }

    &-oops {
      font-size: 24px;
      line-height: 1.5;
      font-weight: 400;
      color: #354052;
      margin: 0;
    }

    &-text {
      font-size: 16px;
      line-height: 1.5;
      font-weight: 400;
      color: #354052;
      &-2 {
        color: #354052;
        font-size: 14px;
      }
    }

    a {
      font-size: 14px;
    }

    * {
      font-family: 'Amelia Roboto', sans-serif;
      box-sizing: border-box;
    }
  }
}
</style>
