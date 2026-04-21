<template>
  <div
    class="am-caph__main"
    :style="cssVars"
  >
    <slot></slot>
  </div>
</template>

<script setup>
import { useColorTransparency } from '../../../../../assets/js/common/colorManipulation'
import {
  ref,
  computed,
  inject
} from 'vue'

defineProps({
  ready: {
    type: Boolean,
    default: true
  }
})

// * Colors
let amColors = inject('amColors', ref({
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
  colorInpPlaceHolder: '#1A2C37',
  colorDropBgr: '#FFFFFF',
  colorDropBorder: '#D1D5D7',
  colorDropText: '#0E1920',
  colorBtnPrim: '#265CF2',
  colorBtnPrimText: '#FFFFFF',
  colorBtnSec: '#1A2C37',
  colorBtnSecText: '#FFFFFF',
}))

// * Calculate Step Width
let cssVars = computed(() => {
  return {
    // am   - amelia
    // caph - cabinet
    '--am-c-caph-text': amColors.value.colorMainText,
    '--am-c-caph-heading-text': amColors.value.colorMainHeadingText,
    '--am-c-caph-text-op15': useColorTransparency(amColors.value.colorMainText, 0.15)
  }
})
</script>

<style lang="scss">
@mixin main-content-header {
  // am  - amelia
  // caph - cabinet panel header
  .am-caph {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;

    &__main {
      padding: 16px 32px;
      box-shadow: 0 2px 3px var(--am-c-caph-text-op15);
    }

    &__text {
      font-family: var(--am-font-family), sans-serif;
      font-size: 18px;
      font-weight: 500;
      font-style: normal;
      line-height: 1.55;
      text-transform: initial;
      letter-spacing: initial;
      color: var(--am-c-caph-heading-text);
      padding: 6px 0;
      margin: 0;
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;

      &:before { // theme Twenty Nineteen
        display: none;
      }
    }

    &__menu {
      font-size: 24px;
    }

    .am-select-wrapper {
      max-width: 250px;
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include main-content-header;
}

// Admin
#amelia-app-backend-new #amelia-container {
  @include main-content-header;
}
</style>
