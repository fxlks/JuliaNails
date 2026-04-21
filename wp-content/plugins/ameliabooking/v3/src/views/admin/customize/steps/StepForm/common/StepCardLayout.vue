<template>
  <div
    ref="stepCardLayoutRef"
    class="am-fs-scl"
    :class="[
      props.customClass,
      { 'am-oxvisible': bringingAnyoneVisibility || packagesVisibility },
    ]"
    :style="cssVars"
  >
    <AmAlert
      v-if="alertMessage"
      type="error"
      class="am-fs-scl__alert"
      :closable="false"
      :show-border="true"
      :title="alertMessage"
    />
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
      :visibility="bringingAnyoneVisibility"
      class="am-fs__init__bringing"
    >
      <p class="am-fs__popup-x">
        <AmeliaIconClose/>
      </p>
      <BringingAnyone />
      <template #footer>
        <AmButton
          category="secondary"
          :type="amCustomize[pageRenderKey].bringingAnyone.options.secondaryButton.buttonType"
        >
          {{ labelsDisplay('bringing_no', 'bringingAnyone') }}
        </AmButton>
        <AmButton
          :type="amCustomize[pageRenderKey].bringingAnyone.options.primaryButton.buttonType"
        >
          {{ labelsDisplay('bringing_yes', 'bringingAnyone') }}
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Bringing Anyone with you -->

    <!-- Packages Popup -->
    <AmSlidePopup :visibility="packagesVisibility" :style="cssPackage">
      <p class="am-fs__popup-x">
        <span class="am-icon-close"></span>
      </p>
      <div class="am-fs__ps-popup">
        <div
          v-if="amCustomize[pageRenderKey].packageStep.options.heading.visibility"
          class="am-fs__ps-popup__heading"
        >
          {{ labelsDisplay('package_heading', 'packageStep') }}
        </div>
        <PackageStep></PackageStep>
        <div class="am-fs__ps-popup__or">
          {{ labelsDisplay('separator_or', 'packageStep') }}
        </div>
      </div>

      <template #footer>
        <AmButton
          class="am-fs__ps-popup__btn"
          :class="`am-fs__ps-popup__btn${checkScreen ? '-mobile':''}`"
          category="primary"
          size="medium"
          :type="amCustomize[pageRenderKey].packageStep.options.primaryButton.buttonType"
          :suffix="pill"
        >
          {{ labelsDisplay('continue_without_package', 'packageStep') }}
        </AmButton>
      </template>
    </AmSlidePopup>
    <!--/ Packages Popup -->
  </div>
</template>

<script setup>
// * Import from Vue
import { computed, inject, ref, watchEffect } from 'vue'

// * Import components
import AmButton from '../../../../../_components/button/AmButton.vue'
import AmSlidePopup from '../../../../../_components/slide-popup/AmSlidePopup.vue'
import AmAlert from '../../../../../_components/alert/AmAlert.vue'
import AmeliaIconClose from '../../../../../_components/icons/IconClose.vue'
import BringingAnyone from '../BringingAnyone.vue'
import PackageStep from '../PackageStep.vue'

// * Composables
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation'
import { useElementSize } from '@vueuse/core'
import { useResponsiveClass } from '../../../../../../assets/js/common/responsive'

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

// * Amelia Settings
const amSettings = inject('settings')

let subStepName = inject('subStepName')
let pageRenderKey = inject('pageRenderKey')
let amCustomize = inject('customize')

let langKey = inject('langKey')

let amLabels = inject('labels')

// * Label computed function
function labelsDisplay (label, stepKey) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepKey].translations
    && amCustomize.value[pageRenderKey.value][stepKey].translations[label]
    && amCustomize.value[pageRenderKey.value][stepKey].translations[label][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepKey].translations[label][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

// * Bringing anyone with you pop up visibility
let bringingAnyoneVisibility = computed(() => {
  return subStepName.value === 'bringingAnyone'
})

let packagesVisibility = computed(() => {
  return subStepName.value === 'packageStep'
})

let pill = {
  template: `<div class="am-fs__ps-pill">$60 USD</div>`
}

let alertMessage = ref('')

watchEffect(() => {
  if (props.cardSelected) {
    alertMessage.value = ''
  }
})

defineExpose({
  stepCardLayoutRef,
})

// * Global colors
let amColors = inject('amColors');
let cssPackage = computed(() => {
  return {
    '--am-c-ps-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-ps-text-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
  }
})

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
    '--am-c-main-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
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

// Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 560 || (cWidth.value - 240 < 520))
</script>

<style lang="scss">
// scl - Step Card Layout
#amelia-app-backend-new #amelia-container {
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
