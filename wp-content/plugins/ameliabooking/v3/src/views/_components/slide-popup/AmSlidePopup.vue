<template>
  <transition duration="550" name="nested">
    <div
      v-show="visibility"
      class="am-slide-popup__block"
      :class="`am-position-${position}`"
      :style="{...cssVars, ...customCss}"
    >
      <div
        v-click-outside="onClickOutside"
        class="am-slide-popup__block-inner"
        :class="[
          {'am-slide-popup__up-inner-mobile': checkScreen},
          customClass,
          `am-position-${position}`
        ]"
      >
        <div v-if="popupHeaderVisibility" class="am-slide-popup__block-header">
          <slot name="header"></slot>
          <span class="am-icon-close" @click="emits('update:visibility', false)" />
        </div>
        <slot></slot>
        <div v-if="props.footerVisibility" class="am-slide-popup__block-footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  computed,
  useSlots
} from 'vue';

// * Import from Libraries
import { ClickOutside as vClickOutside } from "element-plus";

// * Composables
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

// * Components Props
let props = defineProps({
  visibility: {
    type: Boolean,
    default: false,
    required: true
  },
  customClass: {
    type: String,
    default: ''
  },
  position: {
    type: String,
    default: 'bottom',
    validator(value) {
      return ['bottom', 'top', 'left', 'right', 'center'].includes(value)
    }
  },
  closeOutside: {
    type: Boolean,
    default: false
  },
  customCss: {
    type: Object,
    default: () => {}
  },
  footerVisibility: {
    type: Boolean,
    default: true
  }
})

// * Compomnets Emits
let emits = defineEmits(['click-outside', 'update:visibility'])

const slots = useSlots()

// * Click outside of menu
function onClickOutside () {
  emits('click-outside')
  if (props.closeOutside) {
    emits('update:visibility', false)
  }
}

// * Container Width
let cWidth = inject('containerWidth', 0)
let checkScreen = computed(() => cWidth.value < 460 || (cWidth.value > 560 && cWidth.value - 240 < 460))

let popupHeaderVisibility = computed(() => {
  return !!slots.header?.()
})

// * Components Colors
let amColors = inject('amColors');

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-c-spb-bgr': amColors.value.colorMainBgr,
    '--am-c-spb-text': amColors.value.colorMainText,
    '--am-c-spb-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1)
  }
})
</script>

<style lang="scss">
@mixin am-slide-popup {
  .am-slide-popup {
    position: absolute;
    top: 0;
    left: 0;
    display: block;
    width: 100%;
  }

  .am-slide-popup__block {
    $classP: '.am-slide-popup__block';
    @extend .am-slide-popup;
    height: 100%;
    padding: 0;
    //background: rgba(4, 8, 11, 0.3);
    background-color: var(--am-c-spb-text-op10);
    z-index: 1000;

    * {
      box-sizing: border-box;
    }

    &-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0 24px;
      background: var(--am-c-spb-bgr);
      color: var(--am-c-spb-text);
      padding: 0;

      .am-icon-close {
        color: var(--am-c-spb-text);
        font-size: 20px;
        cursor: pointer;
        flex: 0 0 auto;
      }
    }

    &-footer {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 0 8px;

      .am-fs__ps-popup__btn-mobile {
        width: 100%;
        display: flex;
        justify-content: space-between;
      }
    }

    &-inner {
      @extend .am-slide-popup;
      background: var(--am-c-main-bgr);
      padding: 16px 32px;

      &.am-position-top {
        top: 0;
        bottom: auto;
        left: 0;
        right: auto;
        min-height: 100px;
      }

      &.am-position-right {
        top: 0;
        bottom: 0;
        left: auto;
        right: 0;
        width: auto;
      }

      &.am-position-left {
        top: 0;
        bottom: 0;
        left: 0;
        right: auto;
        width: auto;
      }

      &.am-position-bottom {
        top: auto;
        bottom: 0;
        right: auto;
        min-height: 100px;
      }

      &.am-position-center {
        top: 50%;
        bottom: auto;
        left: 50%;
        right: auto;
        transform: translate(-50%, -50%);
        width: auto;
        max-width: calc(100% - 32px);
        height: auto;
        max-height: calc(100% - 32px);
      }

      &-mobile {
        padding: 16px;

        .am-fs__ps-popup__btn-mobile {
          height: auto;
          width: 100%;
          flex-direction: column;
          gap: 8px;
          white-space: break-spaces;
        }
      }
    }

    &.nested-enter-active, &.nested-leave-active {
      transition: all 0.3s ease-in-out;

      #{$classP}-inner {
        transition: all 0.3s ease-in-out;
      }
    }

    &.nested-enter-from, &.nested-leave-to {
      opacity: 0;

      &.am-position-top {
        transform: translateY(-30px);
      }

      &.am-position-right {
        transform: translateX(30px);
      }

      &.am-position-left {
        transform: translateX(-30px);
      }

      &.am-position-bottom, &.am-position-center {
        transform: translateY(30px);
      }

      #{$classP}-inner {
        &.am-position-top {
          transform: translateY(-30px);
        }

        &.am-position-right {
          transform: translateX(30px);
        }

        &.am-position-left {
          transform: translateX(-30px);
        }

        &.am-position-bottom {
          transform: translateY(30px);
        }

        &.am-position-center {
          transform: translate(-50%, 50%);
        }
        /*
          Hack around a Chrome 96 bug in handling nested opacity transitions.
          This is not needed in other browsers or Chrome 99+ where the bug
          has been fixed.
        */
        opacity: 0.001;
      }
    }

    /* delay leave of parent element */
    &.nested-leave-active {
      transition-delay: 0.25s;
    }

    /* delay enter of nested element */
    &.nested-enter-active {
      #{$classP}-inner {
        transition-delay: 0.25s;
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-slide-popup;
}

// admin
#amelia-app-backend-new {
  @include am-slide-popup;
}
</style>