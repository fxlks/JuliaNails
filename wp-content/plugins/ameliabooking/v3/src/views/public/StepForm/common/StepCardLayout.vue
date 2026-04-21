<template>
  <div
    v-if="loaded"
    ref="stepCardLayoutRef"
    class="am-fs-scl"
    :class="[
      props.customClass,
      { 'am-oxvisible': bringingAnyoneVisibility || packagesVisibility },
    ]"
    :style="cssVars"
  >
    <div
      class="am-fs-scl__filters"
      :class="responsiveClass"
    >
      <slot name="filters" />
    </div>
    <div class="am-fs-scl__content">
      <slot />
    </div>

    <!-- Bringing Anyone with you -->
    <AmSlidePopup
      v-if="bringingAnyoneOptions.availability"
      :visibility="bringingAnyoneVisibility"
      class="am-fs__init__bringing"
    >
      <p
        class="am-fs__popup-x"
        :class="{ 'am-rtl': isRtl }"
        @click="closeBringingPopup"
      >
        <AmeliaIconClose></AmeliaIconClose>
      </p>
      <BringingAnyone :in-popup="true" />
      <template #footer>
        <AmButton
          v-if="
            bringingAnyoneOptions.min !== bringingAnyoneOptions.max &&
            bringingAnyoneOptions.min <= 0
          "
          category="secondary"
          :type="amCustomize.bringingAnyone.options.secondaryButton.buttonType"
          :disabled="
            bringingAnyoneOptions.min === bringingAnyoneOptions.max ||
            (!amSettings.appointments.allowBookingIfNotMin &&
              bringingAnyoneOptions.min > 0)
          "
          @click="noOneBringWith"
        >
          {{ bringingLabels.bringing_no }}
        </AmButton>
        <AmButton
          :type="
            bringingAnyoneOptions.min !== bringingAnyoneOptions.max &&
            bringingAnyoneOptions.min <= 0
              ? amCustomize.bringingAnyone.options.primaryButton.buttonType
              : amCustomize.bringingAnyone.options.primaryFooterButton
                  .buttonType
          "
          :disabled="
            bookingPersons ===
            (amSettings.appointments.bringingAnyoneLogic === 'additional'
              ? 1
              : 0)
          "
          @click="bringPeopleWithYou"
        >
          <template
            v-if="
              bringingAnyoneOptions.min !== bringingAnyoneOptions.max &&
              bringingAnyoneOptions.min <= 0
            "
          >
            {{ bringingLabels.bringing_yes }}
          </template>
          <template v-else>
            {{ bringingLabels.continue }}
          </template>
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Bringing Anyone with you -->

    <!-- Packages Popup -->
    <PackagesPopup
      class="am-fs__init__package"
      :footer-visibility="packagesPopupFooterVisibility"
      @continue-with-service="continueWithService()"
      @close-package-popup="packagesPopupFooterVisibility = true"
    />
    <!--/ Packages Popup -->
  </div>
</template>

<script setup>
// * Import from Vue
import { computed, inject, provide, reactive, ref, watchEffect } from 'vue'

// * Import from Vuex
import { useStore } from 'vuex'

// * Import components
import AmButton from '../../../_components/button/AmButton.vue'
import AmSlidePopup from '../../../_components/slide-popup/AmSlidePopup.vue'
import PackagesPopup from '../PakagesStep/parts/PackagesPopup.vue'
import BringingAnyone from '../BringingAnyone/BringingAnyone.vue'
import AmeliaIconClose from '../../../_components/icons/IconClose.vue'

// * Composables
import { useCapacity } from '../../../../assets/js/common/appointments'
import { useCart } from '../../../../assets/js/public/cart'
import { useColorTransparency } from '../../../../assets/js/common/colorManipulation'
import { useElementSize } from '@vueuse/core'
import { useResponsiveClass } from '../../../../assets/js/common/responsive'

// * Store
const store = useStore()

// * Props
const props = defineProps({
  customClass: {
    type: [String, Array],
    default: '',
  },
  cardSelected: {
    type: Boolean,
    default: false,
  },
  allowPopup: {
    type: Boolean,
    default: false,
  },
})

// * Step Card Layout reference
const stepCardLayoutRef = ref(null)
// * Component width
const { width: componentWidth } = useElementSize(stepCardLayoutRef)
// * Responsive class
const responsiveClass = computed(() => {
  return useResponsiveClass(componentWidth.value)
})

// * Loaded state
let loaded = computed(() => {
  return store.getters['entities/getReady']
})

// * Amelia Settings
const amSettings = inject('settings')

// * Short Code
const shortcodeData = inject('shortcodeData')

// * Labels
const globalLabels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() =>
  amSettings.general.usedLanguages.includes(localLanguage.value)
)

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({ ...globalLabels })

  if (
    amSettings.customizedData &&
    amSettings.customizedData.sbsNew &&
    amSettings.customizedData.sbsNew.initStep.translations
  ) {
    let customizedLabels =
      amSettings.customizedData.sbsNew.initStep.translations
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
  return computedLabels
})

provide('amLabels', amLabels)

let bringingLabels = computed(() => {
  let computedLabels = reactive({ ...globalLabels })

  if (
    amSettings.customizedData &&
    amSettings.customizedData.sbsNew &&
    amSettings.customizedData.sbsNew.bringingAnyone.translations
  ) {
    let customizedLabels =
      amSettings.customizedData.sbsNew.bringingAnyone.translations
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
  return computedLabels
})

// * Customization
let amCustomize = inject('amCustomize')

// * Document text orientation
let isRtl = computed(() => store.getters['getIsRtl'])

// * Step Functions
const { nextStep, footerButtonReset, footerButtonClicked } = inject(
  'changingStepsFunctions',
  {
    nextStep: () => {},
    footerButtonReset: () => {},
    footerButtonClicked: {
      value: false,
    },
  }
)

// * Bringing Anyone block
let bringingAnyoneOptions = computed(() => {
  return useCapacity(
    store.getters['entities/getEmployeeServices'](
      store.getters['booking/getServiceProviderSelection']
    )
  )
})

provide('bringingOptions', {
  bringingAnyoneOptions,
})

// * Bringing anyone with you pop up visibility
let bringingAnyoneVisibility = ref(false)

// * Booking persons
let bookingPersons = computed(() => {
  return store.getters['booking/getBookingPersons']
})

function noOneBringWith() {
  closeBringingPopup()
  store.commit('booking/setBookingPersons', 0)
  nextStep()
}

function bringPeopleWithYou() {
  nextStep()
}

function continueWithService() {
  packagesVisibility.value = false
  if (bringingAnyoneOptions.value.availability) {
    bringingAnyoneVisibility.value = true
  } else {
    nextStep()
  }
}

function closeBringingPopup() {
  bringingAnyoneVisibility.value = false
}

// * Package popup
let packagesOptions = computed(() =>
  store.getters['entities/filteredPackages'](
    store.getters['booking/getSelection']
  )
)

let packagesPopupFooterVisibility = ref(true)

let packagesVisibility = ref(false)
provide('packagesVisibility', packagesVisibility)
watchEffect(() => {
  if (footerButtonClicked.value) {
    footerButtonReset()
    if (props.cardSelected) {
      if (props.allowPopup) {
        if (
          packagesOptions.value.length &&
          shortcodeData.value.show !== 'services' &&
          useCart(store).length <= 1
        ) {
          packagesVisibility.value = true
        } else {
          continueWithService()
        }
      } else {
        nextStep()
      }
    }
  }
})

defineExpose({
  packagesVisibility,
  packagesPopupFooterVisibility,
  stepCardLayoutRef,
})

const amColors = inject(
  'amColors',
  ref({
    colorPrimary: '#1246D6',
    colorSuccess: '#019719',
    colorError: '#B4190F',
    colorWarning: '#CCA20C',
    colorMainBgr: '#FFFFFF',
    colorMainHeadingText: '#33434C',
    colorMainText: '#1A2C37',
    colorSbBgr: '#17295A',
    colorSbText: '#FFFFFF',
    colorInpBgr: '#FFFFFF',
    colorInpBorder: '#D1D5D7',
    colorInpText: '#1A2C37',
    colorInpPlaceHolder: '#808A90',
    colorDropBgr: '#FFFFFF',
    colorDropBorder: '#D1D5D7',
    colorDropText: '#0E1920',
    colorBtnPrim: '#265CF2',
    colorBtnPrimText: '#FFFFFF',
    colorBtnSec: '#1A2C37',
    colorBtnSecText: '#FFFFFF',
  })
)

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-primary-op05': useColorTransparency(
      amColors.value.colorPrimary,
      0.05
    ),
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-main-text-op80': useColorTransparency(
      amColors.value.colorMainText,
      0.8
    ),
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-scroll-op30': useColorTransparency(
      amColors.value.colorPrimary,
      0.3
    ),
    '--am-c-scroll-op10': useColorTransparency(
      amColors.value.colorPrimary,
      0.1
    ),
  }
})
</script>

<style lang="scss">
// scl - Step Card Layout
.amelia-v2-booking #amelia-container {
  .am-fs-scl {
    display: flex;
    flex-direction: column;
    gap: 16px;

    &__filters {
      display: flex;
      flex-direction: row;
      gap: 16px;

      &.am-rw-360 {
        flex-direction: column;
      }
    }

    &__content {
      display: flex;
      flex-direction: column;
      flex-wrap: wrap;
      gap: 8px;
    }
  }
}
</style>
