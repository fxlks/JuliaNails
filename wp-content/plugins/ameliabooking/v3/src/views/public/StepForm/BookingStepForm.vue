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
    v-if="!empty && formDialogVisibility"
    id="amelia-container"
    ref="ameliaContainer"
    class="am-fs__wrapper"
    :class="{ 'am-collapsed': sidebarCollapsed }"
    :style="cssVars"
  >
    <SideBar
      v-if="containerWidth > 560 && sidebarVisibility"
      class="am-fs-sb"
      :class="[{ 'am-collapsed': sidebarCollapsed }, { 'am-rtl': isRtl }]"
      :style="{
        width: !sidebarCollapsed ? '240px' : '72px',
        paddingBottom: `${sidebarFooterHeight + 16}px`,
      }"
    >
      <template #step-list>
        <div class="am-fs-sb__step-wrapper" tabindex="0">
          <template
            v-if="
              ready &&
              (stepsArray[stepIndex] !== congratulationsStep ||
                !amSettings.general.addToCalendar ||
                (booked && booked.data.length === 0))
            "
          >
            <div
              v-for="step in sidebarSteps"
              :key="step.key"
              class="am-fs-sb__step"
            >
              <div class="am-fs-sb__step-inner">
                <div class="am-fs-sb__step-icon">
                  <span :class="`am-icon-${step.icon}`"></span>
                  <span
                    v-if="
                      step.key === 'cartStep' && useCartHasItems(store) !== 0
                    "
                    class="am-fs-sb__step-icon__number"
                    :class="[{ 'am-rtl': isRtl }, sidebarCollapseItemsClass]"
                  >
                    {{ useCartHasItems(store) }}
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
                  :class="[
                    { 'am-fs-sb__step-checker-selected': step.selected },
                    { 'am-rtl': isRtl },
                    sidebarCollapseItemsClass,
                  ]"
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
                :class="{
                  'am-fs-sb__step-selection-packages':
                    step.key === packageAppointmentsStep.key &&
                    step.stepSelectedData.length > 3,
                }"
              >
                <template
                  v-if="
                    !sidebarCollapsed &&
                    (useCart(store).length === 1 || step.key === 'cartStep')
                  "
                >
                  <p
                    v-for="(itemSelected, index) in step.stepSelectedData"
                    :key="itemSelected.position"
                    class="am-fs-sb__step-selection"
                  >
                    <span
                      v-if="
                        step.key !== packageAppointmentsStep.key || index < 3
                      "
                    >
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
          <template v-else-if="ready">
            <div>
              <transition name="fade">
                <AddToCalendar v-if="!sidebarCollapsed"></AddToCalendar>
              </transition>
              <div v-if="sidebarCollapsed" class="am-fs-sb__step">
                <div class="am-fs-sb__step-inner">
                  <div class="am-fs-sb__step-icon">
                    <span
                      :class="`am-icon-${stepsArray[stepIndex].sidebarData.icon}`"
                    ></span>
                  </div>
                  <div
                    class="am-fs-sb__step-checker"
                    :class="[
                      {
                        'am-fs-sb__step-checker-selected':
                          stepsArray[stepIndex].sidebarData.selected,
                      },
                      sidebarCollapseItemsClass,
                    ]"
                  >
                    <transition name="fade">
                      <span
                        v-if="stepsArray[stepIndex].sidebarData.finished"
                        class="am-icon-check"
                      ></span>
                    </transition>
                  </div>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <!-- Skeleton -->
            <el-skeleton animated>
              <template #template>
                <div v-for="item in new Array(3)" :key="item">
                  <div class="el-skeleton-item-wrapper">
                    <el-skeleton-item variant="text" />
                  </div>
                </div>
              </template>
            </el-skeleton>
            <!-- /Skeleton -->
          </template>
        </div>
      </template>
      <template #support-info>
        <div ref="sidebarFooterRef" class="am-fs-sb__footer">
          <div
            v-if="
              (amSettings.company.email || amSettings.company.phone) &&
              (footerCustomizeOptions.heading ||
                footerCustomizeOptions.phone ||
                footerCustomizeOptions.email)
            "
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
              <span v-if="!sidebarCollapsed" class="am-fs-sb__menu-text">
                {{ amLabels.collapse_menu }}
              </span>
            </Transition>
            <span
              :class="`am-icon-arrow-circle-${
                sidebarCollapsed ? 'left' : 'right'
              }`"
            ></span>
          </div>
        </div>
      </template>
    </SideBar>
    <MainContent>
      <template v-if="stepsArray[stepIndex] !== congratulationsStep" #header>
        <MainContentHeader
          :sidebar-visible="sidebarVisibility"
          :ready="ready"
        ></MainContentHeader>
      </template>
      <template #step>
        <component
          :is="stepsArray[stepIndex]"
          global-class="am-fs__main-content"
        ></component>
      </template>
      <template #footer>
        <MainContentFooter
          :second-button-show="
            stepsArray[stepIndex] === congratulationsStep &&
            !!amSettings.roles.customerCabinet.pageUrl
          "
          :add-to-cart-button-show="
            useCartStep(store) && stepsArray[stepIndex] === cartStep
          "
          :back-to-cart-button-show="
            useCartStep(store) &&
            cart.length > 1 &&
            stepsArray[stepIndex] !== cartStep &&
            stepsArray[stepIndex] !== infoStep &&
            stepsArray[stepIndex] !== paymentStep &&
            stepsArray[stepIndex] !== congratulationsStep
          "
          :booked="booked"
          :loading="loading"
          :payment-gateway="paymentGateway"
          :customized-labels="footerLabels"
          :primary-footer-button-type="primFooterBtnType"
          :secondary-footer-button-type="secFooterBtnType"
          :add-to-cart-button-type="addToCartBtnType"
          :back-to-cart-button-type="backToCartBtnType"
          :back-to-cart-label="dedicatedStepLabel('cancel', 'cartStep')"
          :ready="ready"
          @add-to-cart="addToCart"
          @back-to-cart="backToCart"
        ></MainContentFooter>
      </template>
    </MainContent>
  </div>
  <template v-else>
    <div
      v-if="shortcodeData.show !== 'packages' && !shortcodeData.package"
      ref="ameliaContainer"
      class="am-no-services"
    >
      <img
        :src="
          baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/am-empty-booking.svg'
        "
        style="margin-top: 10px"
        :alt="amLabels.no_services_employees"
      />
      <div class="am-no-services-oops">{{ amLabels.oops }}</div>
      <div class="am-no-services-text">
        {{ amLabels.no_services_employees }}
      </div>
      <div class="am-no-services-text-2">
        <p>{{ amLabels.add_services_employees }}</p>
        <a href="https://wpamelia.com/services-and-categories/" rel="nofollow">
          {{ amLabels.add_services_url }}&nbsp;
        </a>
        <span style="font-size: 14px">{{ amLabels.and }}&nbsp;</span>
        <a href="https://wpamelia.com/employees/" rel="nofollow">
          {{ amLabels.add_employees_url }}
        </a>
      </div>
    </div>
    <div
      v-else
      ref="ameliaContainer"
      class="am-no-services"
      style="height: 100%"
    >
      <img
        :src="
          baseUrls.wpAmeliaPluginURL + '/v3/src/assets/img/am-empty-booking.svg'
        "
        style="margin-top: 10px"
        :alt="amLabels.no_package_services"
      />
      <div>{{ amLabels.oops }}</div>
      <div>{{ amLabels.no_package_services }}</div>
      <a href="https://wpamelia.com/services-and-categories/" rel="nofollow">
        {{ amLabels.add_services_url }}&nbsp;
      </a>
    </div>
  </template>
  <BackLink />
</template>

<script setup>
// * Form construction
import MainContent from '../../common/SbsFormConstruction/MainContent/MainContent.vue'
import MainContentHeader from '../../common/SbsFormConstruction/MainContent/parts/MainContentHeader.vue'
import MainContentFooter from '../../common/SbsFormConstruction/MainContent/parts/MainContentFooter'
import SideBar from '../../common/SbsFormConstruction/SideBar/SideBar.vue'
import BackLink from '../Parts/BackLink'

// * Step components
import InitStep from './InitStep/InitStep.vue'
import BringingAnyone from './BringingAnyone/BringingAnyone'
import Extras from './Extras/Extras.vue'
import PackageInfo from './PakagesStep/PackageInfoStep'
import DateTimeStep from './DateTimeStep/DateTimeStep.vue'
import PackageAppointmentsStep from './PakagesStep/PackageAppointmentsStep.vue'
import PackageAppointmentsListStep from './PakagesStep/PackageAppointmentsListStep.vue'
import CartStep from './CartStep/CartStep.vue'
import InfoStep from './InfoStep/InfoStep.vue'
import Congratulations from './Congratulations/Congratulations.vue'
import AddToCalendar from './Congratulations/AddToCalendar.vue'
import PaymentStep from './PaymentStep/PaymentStep.vue'
import PackagesStep from './PakagesStep/PackageStep'
import RecurringStep from './RecurringStep/RecurringStep'
import RecurringSummary from './RecurringStep/RecurringSummary'
import ServiceStep from './ServiceStep/ServiceStep.vue'
import EmployeeStep from './EmployeeStep/EmployeeStep.vue'
import LocationStep from './LocationStep/LocationStep.vue'

// * import from Vue
import {
  ref,
  reactive,
  watch,
  provide,
  inject,
  markRaw,
  computed,
  onMounted,
  watchEffect,
  nextTick,
} from 'vue'
// * import from Vuex
import { useStore } from 'vuex'
// * import composable
import useRestore from '../../../assets/js/public/restore'
import { useBuildPackage } from '../../../assets/js/public/package.js'
import { defaultCustomizeSettings } from '../../../assets/js/common/defaultCustomize.js'
import useAction from '../../../assets/js/public/actions'
import { useRenderAction } from '../../../assets/js/public/renderActions.js'
import { useColorTransparency } from '../../../assets/js/common/colorManipulation'
import {
  usePrepaidPrice,
  useCapacity,
  useCheckingIfAllNotFree,
} from '../../../assets/js/common/appointments'
import {
  useCart,
  useCartStep,
  useInitCartItem,
  useCartHasItems,
  useInitSelection,
  useAddToCart,
  useGoToCartStep,
} from '../../../assets/js/public/cart'

let formDialogVisibility = inject('formDialogVisibility', ref(true))

// * Shortcode data
const shortcodeData = inject('shortcodeData')

// * Plugin Licence
let licence = inject('licence')

// * Component reference
let ameliaContainer = ref(null)
// * Plugin wrapper width
let containerWidth = ref()
provide('containerWidth', containerWidth)

let empty = ref(false)

// * window resize listener
window.addEventListener('resize', resize)
// * resize function
function resize() {
  if (ameliaContainer.value) {
    containerWidth.value = ameliaContainer.value.offsetWidth
  }
}

onMounted(() => {
  store.commit('shortcodeParams/setForm', 'stepForm')

  document
    .getElementById('amelia-v2-booking-' + shortcodeData.value.counter)
    .classList.add(
      'amelia-v2-booking-' + shortcodeData.value.counter + '-loaded'
    )

  useAction(store, {}, 'ViewContent', 'appointment', null, null)

  nextTick(() => {
    containerWidth.value = ameliaContainer.value.offsetWidth
  })

  useAction(
    store,
    { containerWidth },
    'ContainerWidth',
    'appointment',
    null,
    null
  )
})

// * Root Settings
const amSettings = inject('settings')

// * Customize
const amCustomize =
  amSettings.customizedData && amSettings.customizedData.sbsNew
    ? amSettings.customizedData.sbsNew
    : defaultCustomizeSettings.sbsNew
if (amCustomize) {
  provide('amCustomize', amCustomize)
}

let footerCustomizeOptions = computed(() => {
  let obj = {
    heading: true,
    phone: true,
    email: true,
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
const amFonts = ref(
  amSettings.customizedData
    ? amSettings.customizedData.fonts
    : defaultCustomizeSettings.fonts
)
provide('amFonts', amFonts)

// * Dialog width
let dialogWrapperWidth = inject('dialogWrapperWidth', ref(''))

// * Form Sidebar Collapse
let sidebarCollapsed = ref(false)
provide('sidebarCollapsed', sidebarCollapsed)

let sidebarCollapseItemsClass = ref('')

watch(sidebarCollapsed, (current) => {
  if (current) {
    setTimeout(() => {
      sidebarCollapseItemsClass.value = 'am-collapsed'
      dialogWrapperWidth.value = '592px'
    }, 1000)
  } else {
    sidebarCollapseItemsClass.value = ''
    dialogWrapperWidth.value = '760px'
  }
})

let sidebarFooterRef = ref(null)
let sidebarFooterHeight = ref(0)

// * Form Sidebar Visibility
let sidebarVisibility = ref(
  amCustomize.sidebar ? amCustomize.sidebar.options.self.visibility : true
)

// * Root Urls
const baseUrls = inject('baseUrls')

// * Define store
const store = useStore()

let isRtl = computed(() => store.getters['getIsRtl'])

let ready = computed(() => store.getters['entities/getReady'])

let isRestored = ref(false)

watch(ready, (current) => {
  if (current) {
    nextTick(() => {
      if (sidebarFooterRef.value) {
        setTimeout(() => {
          sidebarFooterHeight.value = sidebarFooterRef.value.offsetHeight
        }, 200)
      }
    })

    setShortcodeParams()

    let preselected = store.getters['entities/getPreselected']
    empty.value =
      store.getters['entities/getServices'].length === 0 ||
      store.getters['entities/getEmployees'].length === 0 ||
      (store.getters['entities/getPackages'].length === 0 &&
        (shortcodeData.value.show === 'packages' ||
          preselected.package.length > 0))

    let restore = useRestore(store, shortcodeData.value)

    if (restore) {
      stepsArray.value.splice(0, stepsArray.value.length)

      sidebarSteps.value.splice(0, sidebarSteps.value.length)

      stepIndex.value = 0

      restore.steps.forEach((key) => {
        switch (key) {
          case 'packageStep':
            stepsArray.value.push(packagesStep)
            break

          case 'bringingAnyone':
            stepsArray.value.push(bringingAnyone)
            break

          case 'initStep':
            stepsArray.value.push(initStep)
            break

          case 'serviceStep':
            stepsArray.value.push(serviceStep)
            break

          case 'employeeStep':
            stepsArray.value.push(employeeStep)
            break

          case 'locationStep':
            stepsArray.value.push(locationStep)
            break

          case 'packageInfoStep':
            stepsArray.value.push(packageInfoStep)
            break

          case 'packageAppointmentsStep':
            stepsArray.value.push(packageAppointmentsStep)
            break

          case 'packageAppointmentsListStep':
            stepsArray.value.push(packageAppointmentsListStep)
            break

          case 'extrasStep':
            stepsArray.value.push(extras)
            break

          case 'dateTimeStep':
            stepsArray.value.push(dateTimeStep)
            break

          case 'recurringStep':
            stepsArray.value.push(recurringStep)
            break

          case 'recurringSummary':
            stepsArray.value.push(recurringSummary)
            break

          case 'cartStep':
            stepsArray.value.push(cartStep)
            break

          case 'infoStep':
            stepsArray.value.push(infoStep)
            break

          case 'paymentStep':
            stepsArray.value.push(paymentStep)
            break

          case 'congratulations':
            stepsArray.value.push(congratulationsStep)
            break
        }

        let sideBarItem = restore.sidebar.find((i) => i.key === key)

        sidebarDataUpdate()

        if (typeof sideBarItem !== 'undefined') {
          if (sideBarItem.data) {
            sideBarItem.data.forEach((i) => {
              sidebarDataCollector({
                reference: i.reference,
                position: i.position,
                value: i.value,
              })
            })
          }
        }

        stepIndex.value++
      })

      store.commit('booking/setLoading', false)

      let index = -1

      if (restore.result === 'success') {
        index = stepsArray.value.length - 1
      } else if (restore.result === 'error' || restore.result === 'canceled') {
        index = stepsArray.value.length - 2
      }

      for (let i = 0; i <= index; i++) {
        stepsArray.value[i].finished = true
      }

      for (let i = 0; i <= index - 1; i++) {
        sidebarSteps.value[i].finished = true
        sidebarSteps.value[i].selected = false
      }
      if (restore.result === 'error' || restore.result === 'canceled') {
        sidebarSteps.value[index].finished = false
        sidebarSteps.value[index].selected = true
      }

      isRestored.value = true
      stepIndex.value = index
    }
  }
})

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

// * List Layout Components
const serviceStep = markRaw(ServiceStep)
const employeeStep = markRaw(EmployeeStep)
const locationStep = markRaw(LocationStep)

// * Array of step components
const stepsArray = ref(calculateInitStepsArray())

function calculateInitStepsArray() {
  let steps
  if (useCartStep(store)) {
    steps = [initStep, dateTimeStep, cartStep, infoStep, congratulationsStep]
  } else {
    steps = [initStep, dateTimeStep, infoStep, congratulationsStep]
  }

  if (shortcodeData.value.layout === '2') {
    steps.splice(0, 1)
    let stepsOrder =
      'order' in amCustomize
        ? amCustomize.order
        : [
            { id: 'ServiceStep' },
            { id: 'EmployeeStep' },
            { id: 'LocationStep' },
          ]
    stepsOrder.forEach((stepKey, index) => {
      if (stepKey.id === 'ServiceStep') {
        steps.splice(index, 0, serviceStep)
      }

      if (stepKey.id === 'EmployeeStep') {
          steps.splice(index, 0, employeeStep)
      }

      if (stepKey.id === 'LocationStep') {
          steps.splice(index, 0, locationStep)
      }
    })
  }

  return steps
}

provide('stepsArray', stepsArray)

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() =>
  amSettings.general.usedLanguages.includes(localLanguage.value)
)

let cart = useCart(store)

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({ ...labels })

  if (amSettings.customizedData && amSettings.customizedData.sbsNew) {
    Object.keys(amSettings.customizedData.sbsNew).forEach((stepKey) => {
      if (
        stepKey !== 'colors' &&
        amSettings.customizedData.sbsNew[stepKey].translations
      ) {
        let customizedLabels =
          amSettings.customizedData.sbsNew[stepKey].translations
        Object.keys(customizedLabels).forEach((labelKey) => {
          if (
            customizedLabels[labelKey][localLanguage.value] &&
            langDetection.value
          ) {
            computedLabels[labelKey] =
              customizedLabels[labelKey][localLanguage.value]
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
    let customizedLabels = amSettings.customizedData.sbsNew[
      stepsArray.value[stepIndex.value].key
    ]
      ? amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key]
          .translations
      : null
    if (amSettings.customizedData && customizedLabels) {
      Object.keys(customizedLabels).forEach((labelKey) => {
        if (
          customizedLabels[labelKey][localLanguage.value] &&
          langDetection.value
        ) {
          customLabels[labelKey] =
            customizedLabels[labelKey][localLanguage.value]
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
  if (
    amSettings.customizedData &&
    amSettings.customizedData.sbsNew &&
    amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key]
  ) {
    btnType =
      amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key]
        .options.primaryFooterButton.buttonType
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
    amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key]
      .options.secondaryFooterButton
  ) {
    btnType =
      amSettings.customizedData.sbsNew[stepsArray.value[stepIndex.value].key]
        .options.secondaryFooterButton.buttonType
  }

  return btnType
})

let addToCartBtnType = computed(() => {
  if (
    amSettings.customizedData &&
    amSettings.customizedData.sbsNew &&
    amSettings.customizedData.sbsNew['cartStep']
  ) {
    return amSettings.customizedData.sbsNew['cartStep'].options['addToCart']
      .buttonType
  }

  return 'text'
})

let backToCartBtnType = computed(() => {
  if (
    amSettings.customizedData &&
    amSettings.customizedData.sbsNew &&
    amSettings.customizedData.sbsNew['cartStep']
  ) {
    return amSettings.customizedData.sbsNew['cartStep'].options['backToCart']
      .buttonType
  }

  return 'text'
})

function dedicatedStepLabel(labelKey, stepKey) {
  let customLabel = ''
  if (amSettings.customizedData && amSettings.customizedData.sbsNew) {
    let customizedLabels = amSettings.customizedData.sbsNew[stepKey]
      ? amSettings.customizedData.sbsNew[stepKey].translations
      : null
    if (
      amSettings.customizedData &&
      customizedLabels &&
      customizedLabels[labelKey]
    ) {
      if (
        customLabel === '' &&
        customizedLabels[labelKey][localLanguage.value] &&
        langDetection.value
      ) {
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

function setShortcodeParams() {
  let preselected = store.getters['entities/getPreselected']

  // * When single category selected
  if (preselected.category.length === 1) {
    store.commit('booking/setCategoryId', parseInt(preselected.category[0]))
  }

  // * When single service selected
  if (preselected.service.length === 1) {
    let service = store.getters['entities/getService'](
      parseInt(preselected.service[0])
    )

    if (service) {
      store.commit('booking/setServiceId', parseInt(preselected.service[0]))
    }
    // * Set category id from service if it exists
    store.commit(
      'booking/setCategoryId',
      service ? parseInt(service.categoryId) : null
    )
  }

  // * When single employee selected
  if (preselected.employee.length === 1) {
    store.commit('booking/setEmployeeId', parseInt(preselected.employee[0]))
  }

  // * When single location selected
  if (preselected.location.length === 1) {
    store.commit('booking/setLocationId', parseInt(preselected.location[0]))
  }

  // * When single package selected
  if (preselected.package.length === 1) {
    store.commit('booking/setPackageId', parseInt(preselected.package[0]))
  }

  if (preselected.package.length === 1) {
    if (shortcodeData.value.layout === '2') {
      stepsArray.value.splice(0, 3, packageInfoStep)
      sidebarSteps.value.splice(0, 3)
    } else {
      stepsArray.value.splice(0, 1, packageInfoStep)
      sidebarSteps.value.splice(0, 1)
    }

    sidebarDataUpdate()
    let selectedPackage = store.getters['entities/filteredPackages'](
      store.getters['booking/getSelection']
    )[0]
    if (selectedPackage) {
      goToPackageStep(selectedPackage, false)
    }
  } else if (
    preselected.show === 'packages' ||
    preselected.package.length > 1
  ) {
    if (shortcodeData.value.layout === '2') {
      stepsArray.value.splice(0, 3, packagesStep)
      sidebarSteps.value.splice(0, 3)
    } else {
      stepsArray.value.splice(0, 1, packagesStep)
      sidebarSteps.value.splice(0, 1)
    }

    // * Set bookable type to package
    store.commit('booking/setBookableType', 'package')
    sidebarDataUpdate()
  } else {
    changeInitStepDataService()
    store.commit('booking/setBookableType', 'appointment')
    let employeeOptions =
      store.getters['entities/filteredEmployees'](
        store.getters['booking/getSelection']
      ).length <= 1

    let employeeStepVisibility = amCustomize?.employeeStep ? !amCustomize.employeeStep.options.employee.visibility : !defaultCustomizeSettings.sbsNew.employeeStep.options.employee.visibility
    let employeeVisibility = shortcodeData.value.layout === '2' ? employeeStepVisibility : !amCustomize.initStep.options.employee.visibility
    let locationOptions =
      store.getters['entities/filteredLocations'](
        store.getters['booking/getSelection']
      ).length <= 1
    let locationStepVisibility = amCustomize?.locationStep ? !amCustomize.locationStep.options.location.visibility : !defaultCustomizeSettings.sbsNew.locationStep.options.location.visibility
    let locationVisibility = shortcodeData.value.layout === '2' ? locationStepVisibility : !amCustomize.initStep.options.location.visibility

    if (shortcodeData.value.layout === '2') {
      // * SeviceStep index in stepsArray
      let serviceStepIndex = stepsArray.value.findIndex(
        (item) => item.name === 'ServiceStep'
      )

      // * If sevice is preslected
      if (preselected.service.length === 1 && serviceStepIndex !== -1) {
        stepsArray.value.splice(serviceStepIndex, 1)
        sidebarSteps.value.splice(serviceStepIndex, 1)
      }

      // * EmployeeStep index in stepsArray
      let employeeStepIndex = stepsArray.value.findIndex(
        (item) => item.name === 'EmployeeStep'
      )

      // * If employee is preslected
      if (
        (preselected.employee.length === 1 &&
        employeeStepIndex !== -1 &&
        employeeOptions) ||
        employeeVisibility
      ) {
        stepsArray.value.splice(employeeStepIndex, 1)
        sidebarSteps.value.splice(employeeStepIndex, 1)
      }

      // * LocationStep index in stepsArray
      let locationStepIndex = stepsArray.value.findIndex(
        (item) => item.name === 'LocationStep'
      )

      // * If location is preslected
      if (
        ((preselected.location.length === 1 || store.getters['entities/getLocations'].length === 0) &&
        locationStepIndex !== -1 &&
        locationOptions) ||
        locationVisibility
      ) {
        stepsArray.value.splice(locationStepIndex, 1)
        sidebarSteps.value.splice(locationStepIndex, 1)
      }

      if (
        preselected.service.length === 1 &&
        (preselected.employee.length === 1 || employeeOptions || employeeVisibility) &&
        (preselected.location.length === 1 || locationOptions || locationVisibility)
      ) {
        if (bringingAnyoneOptions.value.availability) {
          stepsArray.value.splice(0, 0, bringingAnyone)
        }
        sidebarDataUpdate()
      }
    } else {
      if (
        preselected.service.length === 1 &&
        (preselected.employee.length === 1 ||
          employeeOptions ||
          employeeVisibility) &&
        (preselected.location.length === 1 ||
          locationOptions ||
          locationVisibility)
      ) {
        if (bringingAnyoneOptions.value.availability) {
          stepsArray.value.splice(0, 1, bringingAnyone)
          sidebarSteps.value.splice(0, 1)
        } else {
          stepsArray.value.splice(0, 1)
          sidebarSteps.value.splice(0, 1)
        }
        sidebarDataUpdate()
      }
    }
  }
}

function stepChanger(steps, stepsNamesToRemove, stepsToAdd, startIndex) {
  let removeIndexes = steps.value
    .map((item, index) =>
      stepsNamesToRemove.includes(item.name) ? index : null
    )
    .filter((item) => item !== null)
    .reverse()

  removeIndexes.forEach((item) => {
    steps.value.splice(item, 1)
  })

  stepsToAdd.forEach((item) => {
    if (steps.value.indexOf(item) === -1) {
      steps.value.splice(startIndex + 1, 0, item)
    }
  })
}

provide('goToPackageStep', { goToPackageStep })

// * Those variables are used to store initial steps array if layout is 2
let initStepsArray = ref([])
let lowestInitStepsIndex = ref(0)

function goToPackageStep(pack, goToNextStep = true) {
  store.commit('booking/setPackageId', pack.id)
  store.commit('booking/setBookableType', 'package')
  store.commit('booking/setMultipleAppointments', useBuildPackage(0, pack))
  store.commit('booking/setMultipleAppointmentsIndex', 0)

  let bookingCount = pack.bookable.reduce(
    (partialSum, book) => partialSum + book.maximumScheduled,
    0
  )

  let removeSteps = []
  let addSteps = []

  if (shortcodeData.value.layout === '2') {
    stepsArray.value.forEach((step, index) => {
      if (
        step.name === 'ServiceStep' ||
        step.name === 'EmployeeStep' ||
        step.name === 'LocationStep'
      ) {
        initStepsArray.value.push({
          index: index,
          component: step,
        })
      }
    })

    lowestInitStepsIndex.value = Math.min(
      ...initStepsArray.value.map((step) => step.index)
    )

    // * If ServiceStep is present in initial steps
    if (
      initStepsArray.value.find((step) => step.component.name === 'ServiceStep')
    ) {
      initStepsArray.value.forEach((step) => {
        if (step.component.name !== 'ServiceStep') {
          removeSteps.push(step.component.name)
        }
      })
    } else {
      // * If ServiceStep is not present in initial steps
      initStepsArray.value.forEach((step) => {
        if (step.index !== lowestInitStepsIndex.value) {
          removeSteps.push(step.component.name)
        }
      })
    }
  }

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
    addSteps = addSteps.concat([
      packageAppointmentsListStep,
      packageAppointmentsStep,
      packageInfoStep,
    ])
  } else {
    addSteps.push(packageInfoStep)
  }

  if (stepIndex.value > 0) {
    stepIndex.value = 0
  }
  stepChanger(stepsArray, removeSteps, addSteps, stepIndex.value)

  stepChanger(sidebarSteps, removeSteps, [], stepIndex.value)

  sidebarDataUpdate()

  if (goToNextStep) {
    nextStep()
  }
}

function removePackageStep() {
  store.commit('booking/setBookableType', 'appointment')

  store.commit('booking/setMultipleAppointments', [
    {
      packageId: null,
      serviceId: null,
      index: 0,
      services: {},
    },
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

  if (
    stepsArray.value[0] !== extras &&
    selectedServiceExtras.value &&
    selectedServiceExtras.value.length
  ) {
    addSteps.push(extras)
  }

  let addingStartIndex = stepIndex.value - 1

  if (shortcodeData.value.layout === '2' && initStepsArray.value.length) {
    initStepsArray.value.forEach((step) => {
      if (!stepsArray.value.some((s) => s.name === step.component.name)) {
        stepsArray.value.splice(step.index, 0, step.component)
      }
    })

    addingStartIndex = initStepsArray.value.length - 1
  }

  stepChanger(
    stepsArray,
    [
      'PackageInfoStep',
      'PackageAppointmentsStep',
      'PackageAppointmentsListStep',
    ],
    addSteps,
    addingStartIndex
  )

  stepChanger(
    sidebarSteps,
    [
      'PackageInfoStep',
      'PackageAppointmentsStep',
      'PackageAppointmentsListStep',
    ],
    [],
    stepIndex.value
  )

  sidebarDataUpdate()
}

provide('goToRecurringStep', { goToRecurringStep })

provide('removeRecurringStep', { removeRecurringStep })

function goToRecurringStep() {
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

  stepsArray.value.splice(startIndex + 1, 0, recurringStep)
  stepsArray.value.splice(startIndex + 2, 0, recurringSummary)

  sidebarDataUpdate()

  nextStep()
}

function removeRecurringStep() {
  for (let i = stepsArray.value.length - 1; i >= 0; i--) {
    if (
      stepsArray.value[i].name === 'RecurringStep' ||
      stepsArray.value[i].name === 'RecurringSummary'
    ) {
      stepsArray.value.splice(i + 1, 1)
    }
  }

  if (useCartStep(store)) {
    addCartStep(stepIndex.value)
  }

  sidebarDataUpdate()
}

provide('addPaymentsStep', { addPaymentsStep })

provide('removePaymentsStep', { removePaymentsStep })

// Provide cart step controls for child components (e.g., Calendar) so they can hide cart during waiting list selection
provide('addCartStep', { addCartStep: () => addCartStep(stepIndex.value) })

provide('removeCartStep', { removeCartStep })

function addPaymentsStep() {
  stepsArray.value.splice(stepsArray.value.length - 1, 0, paymentStep)

  sidebarDataUpdate()
}

function removePaymentsStep() {
  let removeSteps = ['PaymentStep']

  stepChanger(stepsArray, removeSteps, [], stepIndex.value)

  stepChanger(sidebarSteps, removeSteps, [], stepIndex.value)
}

function addCartStep(index) {
  stepChanger(stepsArray, [], [cartStep], index)

  stepChanger(sidebarSteps, [], [], index)

  sidebarDataUpdate()
}

function removeCartStep() {
  stepChanger(stepsArray, ['CartStep'], [], stepIndex.value)

  stepChanger(sidebarSteps, ['CartStep'], [], stepIndex.value)

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

// * Appointment Waiting List: populate module on service selection
watch(() => store.getters['booking/getServiceId'], (newServiceId) => {
  // Reset first
  store.dispatch('appointmentWaitingListOptions/resetWaitingOptions')

  if (!newServiceId) return

  let globalEnabled = !!(amSettings.featuresIntegrations?.waitingListAppointments?.enabled)
  if (!globalEnabled) return

  let service = store.getters['entities/getService'](newServiceId)
  if (!service || !service.settings) return

  let serviceSettings = null
  try {
    serviceSettings = JSON.parse(service.settings)
  } catch (e) {
    serviceSettings = null
  }
  if (!serviceSettings || !serviceSettings.waitingList || !serviceSettings.waitingList.enabled) return

  const wl = serviceSettings.waitingList
  // Backend may not yet supply peopleWaiting for appointments; default 0
  let waitingPayload = {
    enabled: !!wl.enabled,
    maxCapacity: wl.maxCapacity || 0,
    maxExtraPeople: wl.maxExtraPeople || 0,
    maxExtraPeopleEnabled: !!wl.maxExtraPeopleEnabled,
    peopleWaiting: wl.peopleWaiting || 0,
    isWaitingListSlot: false
  }
  store.commit('appointmentWaitingListOptions/setAllData', waitingPayload)
}, { immediate: true })

/**
 * Add or Remove steps from Steps Array
 */
function changeInitStepDataService() {
  // * Adding Extras Step
  if (
    selectedServiceExtras.value.length &&
    !stepsArray.value.find((step) => step.name === 'ExtrasStep')
  ) {
    if (shortcodeData.value.layout === '2') {
      let initStepsIndex = Math.max(
        stepsArray.value.findIndex((step) => step.name === 'ServiceStep'),
        stepsArray.value.findIndex((step) => step.name === 'EmployeeStep'),
        stepsArray.value.findIndex((step) => step.name === 'LocationStep')
      )
      stepsArray.value.splice(initStepsIndex + 1, 0, extras)
    } else {
      stepsArray.value.splice(stepIndex.value + 1, 0, extras)
    }

    sidebarDataUpdate()
  }

  // * Removing Extras Step
  if (
    sidebarSteps.value.find((step) => step.name === 'ExtrasStep') &&
    !Object.keys(selectedServiceExtras.value).length
  ) {
    let extrasIndex = stepsArray.value.findIndex(
      (step) => step.name === 'ExtrasStep'
    )
    stepsArray.value.splice(extrasIndex, 1)
    sidebarSteps.value.splice(extrasIndex, 1)
  }

  useInitCartItem(store)
}

provide('initDataChanges', {
  changeInitStepDataService,
})

// * Step index
const stepIndex = ref(0)
// * StepIndex provide
provide('stepIndex', stepIndex)

// * Monitoring step index for step selection
watch(stepIndex, (currStepIndex, prevStepIndex) => {
  if (currStepIndex < sidebarSteps.value.length) {
    sidebarSteps.value[prevStepIndex].selected = false
    sidebarSteps.value[currStepIndex].selected = true
  }

  if (currStepIndex === sidebarSteps.value.length) {
    sidebarSteps.value[prevStepIndex].selected = false
  }
})

let goBackToPackageBooking = ref(false)
provide('goBackToPackageBooking', goBackToPackageBooking)

/**
 * Move to previous Form Step
 */
function previousStep() {
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

        stepChanger(sidebarSteps, [], [], stepIndex.value)

        sidebarDataUpdate()
      }
    }

    if (
      stepsArray.value[stepIndex.value].name ===
      packageAppointmentsListStep.name
    ) {
      goBackToPackageBooking.value = true
    }

    stepIndex.value = stepIndex.value - 1
  }

  headerButtonPreviousClicked.value = !headerButtonPreviousClicked.value
}

/**
 * Move to next Form Step
 */
function nextStep() {
  if (!navigateInsideStep.value) {
    sidebarSteps.value[stepIndex.value].finished = true
    stepIndex.value = stepIndex.value + 1
  }
}

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
function footerButtonClick() {
  footerButtonClicked.value = true
}

/**
 * Footer btn reset
 */
function footerButtonReset() {
  footerButtonClicked.value = false
}

/**
 * Header btn previous clicked
 */
function headerButtonPreviousClick() {
  headerButtonPreviousClicked.value = true
}

/**
 * Header btn previous reset
 */
function headerButtonPreviousReset() {
  headerButtonPreviousClicked.value = false
}

function footerBtnDisabledUpdater(data) {
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
  secondButtonClick,
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

let keepPaymentStep = computed(() =>
  !empty.value ? (useCart(store).length ? usePrepaidPrice(store) !== 0 : useCheckingIfAllNotFree(store)) : true
)

// Appointment waiting list flag
const isWaitingListBooking = computed(() => store.getters['appointmentWaitingListOptions/getIsWaitingListSlot'])

watchEffect(() => {
  // If waiting list, remove payment step
  if (isWaitingListBooking.value) {
    removePaymentsStep()
    return
  }
  if (!isRestored.value && !keepPaymentStep.value) {
    removePaymentsStep()
  } else if (!isRestored.value) {
    if (stepsArray.value.indexOf(paymentStep) === -1) {
      addPaymentsStep()
    }
  }
})

/**
 * Collecting and rearranging selected data in form sidebar based on item position in form
 * @param data
 */
function sidebarDataCollector(data) {
  // Determines are selected data already exists
  if (
    sidebarSteps.value[stepIndex.value].stepSelectedData.filter(
      (item) => item.reference === data.reference
    ).length
  ) {
    sidebarSteps.value[stepIndex.value].stepSelectedData.forEach(
      (item, index, array) => {
        // Handles changing value of existing data
        if (
          item.reference === data.reference &&
          data.value &&
          data.value !== item.value
        ) {
          item.value = data.value
        }

        // Removes un-selected form data from sidebar array
        if (item.reference === data.reference && !data.value) {
          array.splice(index, 1)
        }
      }
    )
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

provide('sidebarStepsFunctions', {
  sidebarDataCollector,
})

/**
 * Function that update SideBar Data Array when some component are dynamically added
 */
function sidebarDataUpdate() {
  stepsArray.value.forEach((item, index) => {
    if (item.name === 'CongratulationsStep') {
      return
    }
    if (
      !sidebarSteps.value.find(
        (block) => block.labelKey === item.sidebarData.label
      )
    ) {
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

// * Cart
function addToCart() {
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
}

function backToCart() {
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

    store.commit(
      'booking/setEmployeeId',
      items[items.length - 1].services[items[items.length - 1].serviceId]
        .providerId
    )

    store.commit(
      'booking/setLocationId',
      items[items.length - 1].services[items[items.length - 1].serviceId]
        .locationId
    )
  }

  useGoToCartStep(stepsArray, stepIndex)
}

// * Colors block
let amColors = computed(() => {
  return amSettings.customizedData && amSettings.customizedData.sbsNew
    ? amSettings.customizedData.sbsNew.colors
    : defaultCustomizeSettings.sbsNew.colors
})
provide('amColors', amColors)

let borderCalculation = computed(() => containerWidth.value !== 0 && containerWidth.value > 560 ? sidebarVisibility.value || sidebarCollapsed.value : false)

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
    '--am-c-skeleton-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-skeleton-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-skeleton-sb-op20': useColorTransparency(
      amColors.value.colorSbText,
      0.2
    ),
    '--am-c-skeleton-sb-op60': useColorTransparency(
      amColors.value.colorSbText,
      0.6
    ),
    '--am-font-family': amFonts.value.fontFamily,

    // css properties
    // -mw- max width
    // -brad- border-radius
    '--am-mw-main': sidebarVisibility.value
      ? sidebarCollapsed.value
        ? '592px'
        : '760px'
      : '520px',
    '--am-brad-main': borderCalculation.value
      ? isRtl.value
        ? '0.5rem 0 0 0.5rem'
        : '0 0.5rem 0.5rem 0'
      : '0.5rem',
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

onMounted(() => {
  useRenderAction('renderForm', {
    ameliaContainer,
    containerWidth,
    empty,
    amCustomize,
    amFonts,
    sidebarCollapsed,
    sidebarCollapseItemsClass,
    sidebarFooterRef,
    sidebarFooterHeight,
    sidebarVisibility,
  })
})
</script>

<style lang="scss">
@import '../../../assets/scss/public/overides/overides';
@import '../../../assets/scss/common/reset/reset';
@import '../../../assets/scss/common/icon-fonts/style';
@import '../../../assets/scss/common/skeleton/skeleton.scss';

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
      word-break: break-word;
      letter-spacing: normal;
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
