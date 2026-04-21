<template>
  <div class="am-asi" :style="cssVars">
    <div class="am-asi__top">
      <div class="am-asi__header">
        {{ labelsDisplay('access_link_send_check') }}
      </div>
      <div class="am-asi__text">
        {{ labelsDisplay('access_link_send_click') }}
      </div>
      <div class="am-asi__email">support@wpamelia.com</div>
    </div>

    <div class="am-asi__footer">
      <span class="am-asi__footer-text">
        {{ labelsDisplay('access_link_send_inbox') }}
      </span>
      <span class="am-asi__footer-link">
        {{ labelsDisplay('access_link_send_retry') }}
      </span>
    </div>
  </div>
</template>

<script setup>
// * import from Vue
import { computed, inject } from 'vue'

// * Composables
import { useColorTransparency } from '../../../../../../../assets/js/common/colorManipulation.js'
import { useReactiveCustomize } from '../../../../../../../assets/js/admin/useReactiveCustomize.js'

/*************
 * Customize *
 *************/
// * Customize
const { amCustomize } = useReactiveCustomize()

// * Labels
let langKey = inject('langKey')
let amLabels = inject('labels')

let pageRenderKey = inject('pageRenderKey')
let stepName = inject('stepName')

// * Label computed function
function labelsDisplay(label) {
  let computedLabel = computed(() => {
    return amCustomize.value[pageRenderKey.value][stepName.value]
      .translations &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ] &&
      amCustomize.value[pageRenderKey.value][stepName.value].translations[
        label
      ][langKey.value]
      ? amCustomize.value[pageRenderKey.value][stepName.value].translations[
          label
        ][langKey.value]
      : amLabels[label]
  })

  return computedLabel.value
}

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
    '--am-c-main-text-op70': useColorTransparency(
      amColors.value.colorMainText,
      0.7
    ),
    '--am-c-main-text-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-c-main-text-op40': useColorTransparency(
      amColors.value.colorMainText,
      0.4
    ),
    '--am-c-main-text-op25': useColorTransparency(
      amColors.value.colorMainText,
      0.25
    ),
    '--am-c-inp-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-border': amColors.value.colorInpBorder,
    '--am-c-inp-text': amColors.value.colorInpText,
    '--am-c-inp-placeholder': amColors.value.colorInpPlaceHolder,
    '--am-c-btn-prim': amColors.value.colorBtnPrim,
    '--am-c-btn-prim-text': amColors.value.colorBtnPrimText,
    '--am-c-skeleton-op20': useColorTransparency(
      amColors.value.colorMainText,
      0.2
    ),
    '--am-c-skeleton-op60': useColorTransparency(
      amColors.value.colorMainText,
      0.6
    ),
    '--am-font-family': amFonts.value.fontFamily,

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

<script>
export default {
  name: 'AuthResetProcess',
  key: 'accessLinkSuccess',
}
</script>

<style lang="scss">
// am - amelia
// asi - authentication sign in
@mixin auth {
  .am-asi {
    max-width: 400px;
    width: 100%;
    background-color: var(--am-c-main-bgr);
    box-shadow: 0 0 9px -4px var(--am-c-main-text-op40),
      0px 17px 35px -12px var(--am-c-main-text-op25);
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
      margin: 0 0 32px;
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

    &__email {
      font-size: 15px;
      font-weight: 500;
      line-height: 1.6;
      text-align: center;
      color: var(--am-c-main-text);
    }

    &__footer {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-wrap: wrap;

      &-text {
        display: inline-flex;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        margin: 0 4px 0 0;
        color: var(--am-c-main-text-op60);
      }

      &-link {
        display: inline-flex;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.6;
        color: var(--am-c-primary);
        cursor: pointer;
      }
    }
  }
}

// Admin
#amelia-app-backend-new {
  @include auth;
}
</style>
