<template>
  <div
    class="am-asi"
    :style="cssVars"
  >
    <div class="am-asi__top">
      <div class="am-asi__img">
        <span class="am-icon-check"></span>
      </div>
      <div class="am-asi__header">
        {{ amLabels.new_password_changed }}
      </div>
      <div class="am-asi__text">
        {{ amLabels.new_password_changed_description }}
      </div>
    </div>

    <AmButton
      class="am-asi__btn"
      :type="amCustomize.setPassSuccess.options.signInBtn.buttonType"
      @click="pageKey = 'signIn'"
    >
      {{ amLabels.sign_in }}
    </AmButton>
  </div>
</template>

<script setup>
// * import from Vue
import {
  reactive,
  computed,
  inject,
} from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../../assets/js/common/colorManipulation.js'

// * Components
import AmButton from '../../../../../_components/button/AmButton.vue'

// * Root Settings
const amSettings = inject('settings')

// * Customized form data
let amCustomize = inject('amCustomize')

// * labels
const labels = inject('labels')

// * local language short code
const localLanguage = inject('localLanguage')

// * if local lang is in settings lang
let langDetection = computed(() => amSettings.general.usedLanguages.includes(localLanguage.value))

// * Computed labels
let amLabels = computed(() => {
  let computedLabels = reactive({...labels})

  let customizedLabels = amCustomize.value.setPassSuccess.translations
  if (customizedLabels) {
    Object.keys(customizedLabels).forEach(labelKey => {
      if (customizedLabels[labelKey][localLanguage.value] && langDetection.value) {
        computedLabels[labelKey] = customizedLabels[labelKey][localLanguage.value]
      } else if (customizedLabels[labelKey].default) {
        computedLabels[labelKey] = customizedLabels[labelKey].default
      }
    })
  }
  return computedLabels
})

/********
 * Form *
 ********/
// * Page key
let pageKey = inject('pageKey')

/*************
 * Customize *
 *************/
// * Fonts
let amFonts = inject('amFonts')

// * Colors block
let amColors = inject('amColors')

let cssVars = computed(() => {
  return {
    '--am-c-primary': amColors.value.colorPrimary,
    '--am-c-success': amColors.value.colorSuccess,
    '--am-c-error': amColors.value.colorError,
    '--am-c-warning': amColors.value.colorWarning,
    '--am-c-main-bgr': amColors.value.colorMainBgr,
    '--am-c-main-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-main-text': amColors.value.colorMainText,
    '--am-c-main-text-op70': useColorTransparency(amColors.value.colorMainText, 0.7),
    '--am-c-main-text-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-c-main-text-op40': useColorTransparency(amColors.value.colorMainText, 0.4),
    '--am-c-main-text-op25': useColorTransparency(amColors.value.colorMainText, 0.25),
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-skeleton-op20': useColorTransparency(amColors.value.colorMainText, 0.2),
    '--am-c-skeleton-op60': useColorTransparency(amColors.value.colorMainText, 0.6),
    '--am-font-family': amFonts.value.fontFamily,

    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
  }
})
</script>

<script>
export default {
  name: 'ChangePassSuccess'
}
</script>

<style lang="scss">
@import '../../../../../../assets/scss/common/icon-fonts/style';
// am - amelia
// asi - authentication sign in
@mixin auth {
  .am-asi {
    max-width: 400px;
    width: 100%;
    background-color: var(--am-c-main-bgr);
    box-shadow: 0 0 9px -4px var(--am-c-main-text-op40), 0px 17px 35px -12px var(--am-c-main-text-op25);
    border-radius: 12px;
    padding: 32px 24px 24px;
    margin: 0 auto;
    font-family: var(--am-font-family);

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    &__top {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 0 0 24px;
    }

    &__img {
      span {
        position: relative;
        font-size: 42px;
        border-radius: 50%;
        color: var(--am-c-success);

        &:after {
          content: '';
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          display: block;
          border-radius: 50%;
          width: 33px;
          height: 33px;
          border: 3px solid var(--am-c-success);
        }
      }
    }

    &__header {
      font-size: 24px;
      font-weight: 500;
      line-height: 1.33333;
      color: var(--am-c-main-heading-text);
      margin: 0 0 4px;
    }

    &__text {
      font-size: 15px;
      font-weight: 400;
      line-height: 1.6;
      text-align: center;
      color: var(--am-c-main-text-op70);
    }

    &__btn {
      width: 100%;
      margin: 16px 0;
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include auth;
}

// Admin
#amelia-app-backend-new {
  @include auth;
}
</style>
