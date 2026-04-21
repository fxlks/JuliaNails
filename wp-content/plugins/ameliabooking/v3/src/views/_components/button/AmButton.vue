<template>
  <button
    v-bind="bindProps"
    class="am-button"
    :disabled="disabled || loading"
    :autofocus="autofocus"
    :type="nativeType"
    :class="[
      type ? 'am-button--' + type : '',
      size ? 'am-button--' + size : '',
      category ? 'am-button--' + category : '',
      {
        'is-icon-only': iconOnly,
        'is-disabled': disabled,
        'is-loading': loading,
        'is-round': round,
        'is-circle': circle
      },
      customClass
    ]"
    :style="{...cssVars, ...style}"
    @click="handleClick"
  >
    <!-- Loading slot -->
    <slot v-if="(loadingIcon || Object.keys(loadingIcon).length) && loading" name="loading">
      <component :is="loadingIcon" v-if="typeof loadingIcon === 'object'"></component>
      <span v-if="typeof loadingIcon === 'string'" :class="`am-icon-${loadingIcon}`"></span>
    </slot>

    <!-- Icon slot -->
    <slot v-if="(icon || Object.keys(icon).length) && iconOnly && !loading" name="icon">
      <component :is="icon" v-if="typeof icon === 'object'"></component>
      <span v-if="typeof icon === 'string'" :class="`am-icon-${icon}`"></span>
    </slot>

    <!-- Prefix slot -->
    <slot v-if="(prefix || Object.keys(prefix).length) && !iconOnly && !loading" name="prefix">
      <component :is="prefix" v-if="typeof prefix === 'object'"></component>
      <span v-if="typeof prefix === 'string'" :class="`am-icon-${prefix}`"></span>
    </slot>

    <!-- Default slot -->
    <span v-if="$slots.default && !iconOnly" class="am-button__inner" :class="innerClass">
      <slot />
    </span>

    <!-- Suffix slot -->
    <slot v-if="(suffix || Object.keys(suffix).length) && !iconOnly && !loading" name="suffix">
      <component :is="suffix" v-if="typeof suffix === 'object'"></component>
      <span v-if="typeof suffix === 'string'" :class="`am-icon-${suffix}`"></span>
    </slot>
  </button>
</template>

<script setup>
import { useColorTransparency } from '../../../assets/js/common/colorManipulation';

import { computed, ref, inject } from "vue";

/**
 * Component props
 */
const props = defineProps({
  id: {
    type: String
  },
  customClass: {
    type: String,
    default: ''
  },
  innerClass: {
    type: String,
    default: ''
  },
  iconOnly: {
    type: Boolean,
    default: false
  },
  size: {
    // default / medium / small / mini / micro
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small', 'mini', 'micro'].includes(value)
    }
  },
  category: {
    // primary / secondary / success / warning / danger / error
    type: String,
    default: 'primary',
    validator(value) {
      return ['primary', 'secondary', 'success', 'warning', 'danger', 'error', 'waiting'].includes(value)
    }
  },
  type: {
    // filled / plain / text
    type: String,
    default: 'filled',
    validator(value) {
      return ['filled', 'plain', 'text'].includes(value)
    }
  },
  nativeType: {
    // button / submit / reset
    type: String,
    default: 'button',
    validator(value) {
      return ['button', 'submit', 'reset'].includes(value)
    }
  },
  round: {
    type: Boolean,
    default: false
  },
  circle: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  autofocus: {
    type: Boolean,
    default: false
  },
  prefix: {
    type: [String, Object, Function],
    default: ''
  },
  suffix: {
    type: [String, Object, Function],
    default: ''
  },
  icon: {
    type: [String, Object, Function],
    default: ''
  },
  loadingIcon: {
    type: [String, Object, Function],
    default: ''
  },
  style: {
    type: Object,
    default: () => {
      return {}
    }
  }
})

// Create filtered properties
const bindProps = computed(() => {
  const filterObj = {...props};
  const arrayProp = ['id'];

  return Object.fromEntries(Object.entries(filterObj).filter(([key]) => arrayProp.includes(key)));
});

/**
 * Component emits
 * @type {EmitFn<string[]>}
 */
const emits = defineEmits(['click'])

/**
 * Handle click function
 * @param evt
 */
function handleClick(evt) {
  emits('click', evt)
}

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
  colorBtnWaiting: '#CCA20C',
  colorBtnWaitingText: '#FFFFFF',
  colorBtnDanger: '#B4190F',
  colorBtnDangerText: '#FFFFFF',
}))

const cssVars = computed(() => {
  let cssVariables = {}
  if (props.category === 'primary') {
    cssVariables = {
      '--am-c-btn-first': amColors.value.colorBtnPrim,
      '--am-c-btn-second': amColors.value.colorBtnPrimText,
      '--am-c-btn-first-op80': useColorTransparency(amColors.value.colorBtnPrim, 0.8),
      '--am-c-btn-first-op30': useColorTransparency(amColors.value.colorBtnPrim, 0.3),
      '--am-c-btn-first-op20': useColorTransparency(amColors.value.colorBtnPrim, 0.1),
    }
  }

  if (props.category === 'secondary') {
    cssVariables = {
      '--am-c-btn-first': amColors.value.colorBtnSec,
      '--am-c-btn-second': amColors.value.colorBtnSecText,
      '--am-c-btn-first-op80': useColorTransparency(amColors.value.colorBtnSec, 0.8),
      '--am-c-btn-first-op30': useColorTransparency(amColors.value.colorBtnSec, 0.3),
      '--am-c-btn-first-op20': useColorTransparency(amColors.value.colorBtnSec, 0.1),
    }
  }

  if (props.category === 'waiting') {
    let bgr = amColors.value?.colorBtnWaiting ?? '#CCA20C'
    let text = amColors.value?.colorBtnWaitingText ?? '#FFFFFF'

    cssVariables = {
      '--am-c-btn-first': bgr,
      '--am-c-btn-second': text,
      '--am-c-btn-first-op80': useColorTransparency(bgr, 0.8),
      '--am-c-btn-first-op30': useColorTransparency(bgr, 0.3),
      '--am-c-btn-first-op20': useColorTransparency(bgr, 0.1),
    }
  }

  if (props.category === 'warning') {
    cssVariables = {
      '--am-c-btn-first': amColors.value.colorWarning,
      '--am-c-btn-second': amColors.value.colorMainBgr,
      '--am-c-btn-first-op80': useColorTransparency(amColors.value.colorWarning, 0.8),
      '--am-c-btn-first-op30': useColorTransparency(amColors.value.colorWarning, 0.3),
      '--am-c-btn-first-op20': useColorTransparency(amColors.value.colorWarning, 0.1),
    }
  }

  if (props.category === 'danger') {
    cssVariables = {
      '--am-c-btn-first': amColors.value.colorBtnDanger,
      '--am-c-btn-second': amColors.value.colorBtnDangerText,
      '--am-c-btn-first-op80': useColorTransparency(amColors.value.colorBtnDanger, 0.8),
      '--am-c-btn-first-op30': useColorTransparency(amColors.value.colorBtnDanger, 0.3),
      '--am-c-btn-first-op20': useColorTransparency(amColors.value.colorBtnDanger, 0.1),
    }
  }

  return cssVariables
})
</script>

<style lang="scss">
@mixin am-button-block {
  .am-button {
    --am-h-btn: 40px;
    --am-fs-btn: 15px;
    --am-fw-btn: 500;
    --am-lh-btn: 1.6;
    --am-rad-btn: 6px;
    --am-padd-btn: 8px 24px;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: var(--am-h-btn);
    font-size: var(--am-fs-btn);
    font-weight: var(--am-fw-btn);
    line-height: var(--am-lh-btn);
    white-space: nowrap;
    text-decoration: none;
    text-transform: unset;
    border-radius: var(--am-rad-btn);
    outline: 0;
    margin: 0;
    padding: var(--am-padd-btn);
    cursor: pointer;
    transition: 0.3s all ease-in-out;

    // Inner
    span {
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    &.am-button {
      // Type - filled / plain / text
      &--filled {
        --am-c-btn-bgr: var(--am-c-btn-first);
        --am-c-btn-text: var(--am-c-btn-second);
        --am-c-btn-border: var(--am-c-btn-first);
        background-color: var(--am-c-btn-bgr);
        color: var(--am-c-btn-text);
        border: 1px solid var(--am-c-btn-border);

        &:not(.is-disabled) {
          &:hover {
            --am-c-btn-bgr: var(--am-c-btn-first-op80);
          }

          &:focus:not(:active) {
            --am-c-btn-border: var(--am-c-btn-first);
            box-shadow: 0 0 0 3px var(--am-c-btn-first-op30);
          }

          &:active {
            --am-c-btn-bgr: var(--am-c-btn-first);
          }
        }
      }

      &--plain {
        --am-c-btn-bgr: transparent;
        --am-c-btn-text: var(--am-c-btn-first);
        --am-c-btn-border: var(--am-c-btn-first-op30);
        --am-c-btn-shadow: var(--am-c-btn-first-op20);
        background-color: var(--am-c-btn-bgr);
        color: var(--am-c-btn-text);
        border: 1px solid var(--am-c-btn-border);
        box-shadow: 0 1px 3px var(--am-c-btn-shadow);

        &:not(.is-disabled) {
          &:hover {
            --am-c-btn-bgr: var(--am-c-btn-first-op20);
            box-shadow: none;
          }

          &:focus:not(:active) {
            --am-c-btn-border: var(--am-c-btn-second);
            box-shadow: 0 0 0 2px var(--am-c-btn-first-op20);

            &.is-icon-only {
              --am-c-btn-border: var(--am-c-btn-first);
            }

            &:hover {
              --am-c-btn-border: var(--am-c-btn-first-op20);
            }
          }

          &:active {
            --am-c-btn-bgr: var(--am-c-btn-first-op30);
            box-shadow: none;
          }
        }
      }

      &--text {
        --am-c-btn-bgr: transparent;
        --am-c-btn-text: var(--am-c-btn-first);
        --am-c-btn-border: transparent;
        --am-c-btn-shadow: transparent;
        background-color: var(--am-c-btn-bgr);
        color: var(--am-c-btn-text);
        border: 1px solid var(--am-c-btn-border);

        &:not(.is-disabled) {
          &:hover {
            --am-c-btn-bgr: var(--am-c-btn-first-op20);
          }

          &:focus:not(:active) {
            box-shadow: 0 0 0 2px var(--am-c-btn-first);
          }

          &:active {
            --am-c-btn-bgr: var(--am-c-btn-first-op30);
          }
        }
      }

      // Size - default / medium / small / mini / micro
      &--default {
        --am-h-btn: 40px;
        --am-fs-btn: 15px;
        --am-lh-btn: 1.6;
        --am-padd-btn: 8px 24px;

        &.is-icon-only {
          --am-padd-btn: 8px;
          width: 40px;
        }
      }
      &--medium {
        --am-h-btn: 36px;
        --am-fs-btn: 14px;
        --am-lh-btn: 1.42857;
        --am-padd-btn: 8px 20px;

        &.is-icon-only {
          --am-padd-btn: 8px;
          width: 36px;
        }
      }
      &--small {
        --am-h-btn: 32px;
        --am-fs-btn: 14px;
        --am-lh-btn: 1.42857;
        --am-padd-btn: 6px 16px;

        &.is-icon-only {
          --am-padd-btn: 6px;
          width: 32px;
        }
      }
      &--mini {
        --am-h-btn: 28px;
        --am-fs-btn: 13px;
        --am-lh-btn: 1.53846;
        --am-padd-btn: 4px 12px;
        &.is-icon-only {
          --am-padd-btn: 4px;
          width: 28px;
        }
      }
      &--micro {
        --am-h-btn: 24px;
        --am-fs-btn: 12px;
        --am-lh-btn: 1.66666;
        --am-padd-btn: 2px 12px;

        &.is-icon-only {
          --am-padd-btn: 2px;
          --am-fs-btn: 16px;
          width: 24px;
        }
      }

      // Category - primary / secondary / success / warning / danger / error
      // classes usage to change css variables
      &--primary {}
      &--secondary {}
      &--success {}
      &--warning {}
      &--danger {}
      &--error {}

      // Circle
      &.is-circle {
        border-radius: 50%;
      }

      // Round
      &.is-round {
        border-radius: 20px;
      }

      // Disabled
      &.is-disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }

      &--waiting-list { // TODO
      }
    }

    &.am- {
      &w20 {
        width: 20%;
      }

      &w25 {
        width: 25%;
      }

      &w30 {
        width: 30%;
      }

      &w35 {
        width: 35%;
      }

      &w40 {
        width: 40%;
      }

      &w45 {
        width: 45%;
      }

      &w50 {
        width: 50%;
      }

      &w55 {
        width: 55%;
      }

      &w60 {
        width: 60%;
      }

      &w65 {
        width: 65%;
      }

      &w70 {
        width: 70%;
      }

      &w75 {
        width: 75%;
      }

      &w80 {
        width: 80%;
      }

      &w85 {
        width: 85%;
      }

      &w90 {
        width: 90%;
      }

      &w95 {
        width: 95%;
      }

      &w100 {
        width: 100%;
      }

      &mw20 {
        max-width: 20%;
      }

      &mw25 {
        max-width: 25%;
      }

      &mw30 {
        max-width: 30%;
      }

      &mw35 {
        max-width: 35%;
      }

      &mw40 {
        max-width: 40%;
      }

      &mw45 {
        max-width: 45%;
      }

      &mw50 {
        max-width: 50%;
      }

      &mw55 {
        max-width: 55%;
      }

      &mw60 {
        max-width: 60%;
      }

      &mw65 {
        max-width: 65%;
      }

      &mw70 {
        max-width: 70%;
      }

      &mw75 {
        max-width: 75%;
      }

      &mw80 {
        max-width: 80%;
      }

      &mw85 {
        max-width: 85%;
      }

      &mw90 {
        max-width: 90%;
      }

      &mw95 {
        max-width: 95%;
      }

      &mw100 {
        max-width: 100%;
      }
    }
  }
}

// Public
.amelia-v2-booking #amelia-container {
  @include am-button-block;
}

// Dropdown
.am-dialog {
  @include am-button-block;
}

// Modal - Dialog
.am-dialog-popup {
  @include am-button-block;
}

// Admin
#amelia-app-backend-new {
  @include am-button-block;
}
</style>
