<template>
  <div
    class="am-collapse-item"
    :class="[
      {'am-collapse-item-no-button': !partsVisibility || props.side},
      {'am-collapse-item-no-border': props.borderless},
      {'am-collapse-item__arrow' : props.side && partsVisibility},
      {'am-collapse-item__open': contentVisibility}
    ]"
    :style="cssVars"
  >
    <div
      class="am-collapse-item__heading"
      :class="[
        {'am-collapse-item__heading-active' : contentVisibility},
        {'am-collapse-item__heading-side' : contentVisibility && props.side},
        props.headingClass
      ]"
      :tabindex="props.side ? 0 : undefined"
      @click="props.collapsable ? toggleContentHeading() : () => {}"
      @keydown.enter="toggleContentHeading"
    >
      <slot name="heading"></slot>
      <div v-if="props.side && partsVisibility" class="am-collapse-item__trigger am-collapse-item__trigger-side">
        <slot name="icon-start" class="am-collapse-item__trigger-start"></slot>
        <span class="am-icon-arrow-down" :class="{'am-rotate-180': contentVisibility}"></span>
        <slot name="icon-end"></slot>
      </div>
      <slot name="icon-below"></slot>
    </div>

    <template v-if="partsVisibility">
      <div
        ref="collapseContent"
        class="am-collapse-item__content"
        :class="[
          {'am-collapse-item__content-no-border' : !props.side},
          contentVisibility ? 'am-collapse-item__content-open' : 'am-collapse-item__content-close',
          contentClosing ? 'am-collapse-item__content-closing' : ''
        ]"
      >
        <slot></slot>
      </div>
    </template>
    <div
      v-if="partsVisibility && !props.side"
      class="am-collapse-item__trigger"
      :tabindex="partsVisibility && !props.side ? 0 : -1"
      @click="toggleContentButton"
      @keydown.enter="toggleContentButton"
    >
      <span class="am-collapse-item__trigger-label">
        <slot v-if="!contentVisibility && !props.buttonClosed.length" name="button-closed"></slot>
        <template v-if="!contentVisibility && props.buttonClosed.length">
          {{ props.buttonClosed }}
        </template>
        <slot v-if="contentVisibility && !props.buttonOpened.length" name="button-opened"></slot>
        <template v-if="contentVisibility && props.buttonOpened.length">
          {{ props.buttonOpened }}
        </template>
      </span>
      <span class="am-icon-arrow-down" :class="{'am-rotate-180': contentVisibility}"></span>
    </div>
  </div>
</template>

<script setup>
// * Import from Vue
import {
  inject,
  ref,
  reactive,
  computed,
  useSlots
} from 'vue';

// * Composables
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

/**
 * Component Props
 */
let props = defineProps({
  buttonOpened: {
    type: String,
    default: ''
  },
  buttonClosed: {
    type: String,
    default: ''
  },
  side: {
    type: Boolean,
    default: false
  },
  borderless: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: ''
  },
  delay: {
    type: Number,
    default: 500
  },
  collapsable: {
    type: Boolean,
    default: true
  },
  headingClass: {
    type: String,
    default: ''
  }
})

/**
 * Component Emits
 * @type {EmitFn<(string)[]>}
 */
const emits = defineEmits(['collapseOpen', 'collapseClose', 'collapseClicked'])

// * Template slots
let slots = useSlots()

let partsVisibility = computed(() => {
  return slots.default().length && slots.default()[0].props !== null
})

// * Card Expand variable
let contentVisibility = ref(false)
let contentClosing = ref(false)
let collapseContent = ref(null)
let collapseHeight = reactive({
  h: '0px'
})

function closingCollapse () {
  contentClosing.value = true
  setTimeout(() => {
    contentClosing.value = false
  }, 480)
}

function toggleContentHeading () {
  if (props.side) {
    toggleContent()
  }
}

// * Card Expand Function
function toggleContentButton () {
  if (collapseContent.value) {
    toggleContent()
  }
}

// * Card Expand Function
function toggleContent () {
  emits('collapseClicked')
  if (partsVisibility.value) {
    setTimeout(() => {
      if (collapseContent.value) {
        collapseHeight.h = `${collapseContent.value.offsetHeight}px`
      }
      if (contentVisibility.value) closingCollapse()
      contentVisibility.value = !contentVisibility.value
      if (contentVisibility.value) emits('collapseOpen')
      if (!contentVisibility.value) emits('collapseClose')
    }, props.delay)
  }
}

function closingFromParent () {
  if (contentVisibility.value) closingCollapse()
  contentVisibility.value = false
}

function openingFromParent () {
  contentVisibility.value = true
}


defineExpose({
  closingFromParent,
  openingFromParent,
  contentVisibility
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
let cssVars = computed(() => {
  return {
    '--am-c-collapse-text-op80': useColorTransparency(amColors.value.colorMainText, 0.8),
    '--am-c-collapse-text-op10': useColorTransparency(amColors.value.colorMainText, 0.1),
    '--am-delay-collapse': `${props.delay}ms`,
  }
})
</script>

<style lang="scss">
$collapseContentHeight: v-bind('collapseHeight.h');

@keyframes am-animation-collapse-open {
  0%{height: 0;}
  100%{height: $collapseContentHeight;}
}

@keyframes am-animation-collapse-close {
  0%{height: $collapseContentHeight;}
  100%{height: 0;}
}

@mixin am-collapse-item-block {
  // Collapse card
  // am  -- amelia
  // c - color
  // v - visible
  .am-collapse-item {
    --am-c-collapse-border: var(--am-c-inp-border);
    --am-c-collapse-text: var(--am-c-main-text);
    --am-combo-collapse-border: 1px solid var(--am-c-inp-border);
    --am-pointer-collapse: unset;
    position: relative;
    margin: 8px 0;
    overflow: hidden;

    &-no-button {
      .am-collapse-item__heading {
        border-radius: 8px;
        &-side {
          border-radius: 8px 8px 0 0;
          border-bottom: 0;
        }
      }
    }

    &-no-border {
      --am-combo-collapse-border: none;
    }

    &__arrow {
      --am-pointer-collapse: pointer;
    }

    * {
      font-family: var(--am-font-family);
      box-sizing: border-box;
    }

    // Collapse header
    &__heading {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-radius: 8px 8px 0 0;
      padding: 16px;
      border: var(--am-combo-collapse-border);
      cursor: var(--am-pointer-collapse);
      transition-delay: var(--am-delay-collapse);

      &:focus {
        border-color: var(--am-c-collapse-text-op80);

        & + .am-collapse-item__content {
          border-color: var(--am-c-collapse-text-op80);
        }
      }

      &-side {
        transition-delay: 0ms;
      }
    }

    // Collapse card content
    &__content {
      display: flex;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.4285;
      color: var(--am-c-collapse-text-op80);
      border: var(--am-combo-collapse-border);
      border-radius: 0 0 8px 8px;
      overflow: hidden;
      position: absolute;
      opacity: 0;
      z-index: -1;

      & > * {
        padding: 16px;
      }

      &-no-border {
        border-radius: 0;
        border-top: 0;
        border-bottom: 0;
      }

      &-open {
        position: relative;
        opacity: 1;
        z-index: 100;
        animation: 500ms ease-in-out am-animation-collapse-open;
      }

      &-closing {
        position: relative;
        opacity: 1;
        z-index: 100;
      }

      &-close {
        animation: 500ms ease-in-out am-animation-collapse-close;
      }
    }

    // Collapse trigger button
    &__trigger {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      line-height: 1.5385;
      color: var(--am-c-collapse-text);
      padding: 8px 16px;
      border: var(--am-combo-collapse-border);
      border-top: none;
      border-radius: 0 0 8px 8px;
      cursor: pointer;

      &:focus {
        background-color: var(--am-c-collapse-text-op10);

        .am-collapse-item__trigger-label {
          color: var(--am-c-collapse-text-op80);
        }
      }

      &-side {
        border: none
      }

      &-label {
        font-size: 13px;
        line-height: 1.5385;
        color: var(--am-c-collapse-text);
      }

      .am-icon-arrow-down {
        font-size: 16px;
        line-height: 1.4;
        color: var(--am-c-collapse-text);
        transition: all 0.3s ease-in-out;

        &.am-rotate-180 {
          transform: rotate(180deg);
        }
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-collapse-item-block;
}

// admin
#amelia-app-backend-new {
  @include am-collapse-item-block;
}

// modal popup
.am-dialog-popup {
  @include am-collapse-item-block;
}
</style>
