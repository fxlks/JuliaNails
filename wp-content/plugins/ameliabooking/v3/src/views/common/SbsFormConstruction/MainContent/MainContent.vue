<template>
  <div
    class="am-fs__main"
    :style="cssVars"
    :class="responsiveClass"
    role="main"
  >
    <div class="am-fs__main-inner">
      <slot name="header"></slot>
      <slot name="step"></slot>
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
// * import from Vue
import {
  ref,
  computed,
  inject
} from 'vue';

// * Composables
import { useColorTransparency } from "../../../../assets/js/common/colorManipulation";
import { useResponsiveClass } from "../../../../assets/js/common/responsive";

let props = defineProps({
  maxWidth: {
    type: Number,
    default: 520,
  },
  oldResponsive: {
    type: Boolean,
    default: true
  }
})

// * Container Width
let cWidth = inject('containerWidth', 0)
// am-fs__main-mobile
let checkScreen = computed(() => {
  return cWidth.value < 560 || (cWidth.value > 560 && cWidth.value < 640) ? 'am-fs__main-mobile' : ''
})

let responsiveClass = computed(() => {
  return props.oldResponsive ? checkScreen.value : useResponsiveClass(cWidth.value)
})

// * Color Vars
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

// * Css Variables
let cssVars = computed(() => {
  return {
    // am - amelia
    // mw - max width
    // fsm - form step main
    '--am-mw-fsm': `${props.maxWidth}px`,
    '--am-c-scroll-op30': useColorTransparency(amColors.value.colorPrimary, 0.3),
    '--am-c-scroll-op10': useColorTransparency(amColors.value.colorPrimary, 0.1),
  }
})
</script>

<style lang="scss">
@mixin step-main-container {
  // am -- amelia
  // fs -- form steps
  // cap -- cabinet panel
  // Amelia Form Steps
  .am-fs {

    // Main Container
    &__main {
      --am-brad-main-default: 0 0.5rem 0.5rem 0;
      max-width: var(--am-mw-fsm);
      width: 100%;
      background-color: var(--am-c-main-bgr);
      border-radius: var(--am-brad-main, var(--am-brad-main-default));
      overflow: hidden;

      &-inner {
        position: relative;
        height: 100%;
      }

      &-content {
        display: block;
        overflow-x: hidden;
        height: 444px;
        padding: 16px 32px;

        &.am-cap {
          height: calc(100% - 72px);
        }

        &.am-capi, &.am-caep {
          padding: 0;
          height: calc(100% - 136px);
        }

        // Main Scroll styles
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

      // Mobile
      &-mobile {
        --am-brad-main-default: 0.5rem;
        .am-fs__main-content {
          padding: 16px;
        }
      }

      &.am-rw-480 {
        --am-brad-main: 0.5rem;
        --am-brad-main-default: 0.5rem;
        .am-fs__main-content {
          padding: 16px;

          &.am-capi, &.am-caep {
            padding: 0;
          }
        }
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include step-main-container;
}

// Admin
#amelia-app-backend-new {
  @include step-main-container;
}
</style>
