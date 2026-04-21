<template>
  <div class="am-radio-wrapper">
    <el-radio
      ref="amRadio"
      v-model="model"
      class="am-radio"
      :class="[`am-radio__${props.size}`]"
      :value="props.value"
      :label="props.label"
      :disabled="props.disabled"
      :border="props.border"
      :name="props.name"
      :style="{...cssVars}"
      @change="(e) => $emit('change', e)"
    >
      <slot name="default"></slot>
    </el-radio>
  </div>
</template>

<script setup>
// * Composables
import { useColorTransparency } from '../../../assets/js/common/colorManipulation';

// * Import from Vue
import {
  computed,
  ref,
  inject, toRefs
} from "vue";

/**
 * Component Props
 * */
const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean]
  },
  value: {
    type: [String, Number, Boolean]
  },
  label: {
    // * the label of Radio. If there's no value, label will act as value
    type: [String, Number, Boolean]
  },
  disabled: {
    type: Boolean,
    default: false
  },
  border: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small'].includes(value)
    }
  },
  name: {
    type: String,
    default: ''
  },
  style: {
    type: Object,
    default: () => {
      return {}
    }
  }
})

/**
 * Component Emits
 * */
const emits = defineEmits(['change', 'update:modelValue'])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  }
})

const amRadio = ref(null)

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

const cssVars = computed(() => {
  return {
    '--am-c-radio-bgr': amColors.value.colorInpBgr,
    '--am-c-radio-border': amColors.value.colorInpBorder,
    '--am-c-radio-label': amColors.value.colorInpBorder,
    '--am-c-radio-border-op30': useColorTransparency(amColors.value.colorInpText, 0.3),
    '--am-c-radio-hover-bgr': useColorTransparency(amColors.value.colorInpText, 0.1),
    '--am-c-radio-bgr-op80': useColorTransparency(amColors.value.colorInpText, 0.8),
    '--am-c-radio-border-op60': useColorTransparency(amColors.value.colorPrimary, 0.6),
  }
})

</script>

<style lang="scss">
@mixin am-radio-block {
  // Radio Wrapper
  .am-radio-wrapper {
    // -c-    color
    // -bgr-   background
    --am-c-radio-bgr: transparent;
    --am-c-radio-text: transparent;
    --am-c-radio-border: var(--am-c-inp-border);
    --am-c-radio-label: var(--am-c-main-text);
    --am-c-radio-inp-text: var(--am-c-main-bgr);
    align-self: flex-start;

    // Element Radio
    .el-radio {
      display: flex;
      align-items: center;
      white-space: pre-line;
      min-height: 32px;
      height: auto;

      // Inner
      &__inner {
        height: 16px;
        width: 16px;
        border: 1px solid var(--am-c-radio-border);
        background-color: var(--am-c-radio-bgr);

        &:after {
          background-color: var(--am-c-radio-inp-text);
        }
      }

      // Input
      &__input {
        padding: 4px 0;
        display: flex;
        align-items: center;
        align-self: flex-start;

        // After
        &:after {
          background-color: var(--am-c-radio-text);
        }

        // Checked
        &.is-checked {
          --am-c-radio-bgr: var(--am-c-primary);
          --am-c-radio-border: var(--am-c-primary);
          --am-c-radio-text: var(--am-c-main-bgr);

          &:hover {
            --am-c-radio-bgr: var(--am-c-primary);
          }

          // Disabled
          &.is-disabled {
            --am-c-radio-bgr: var(--am-c-radio-bgr-op80);
            --am-c-radio-border: var(--am-c-radio-border-op60);
          }
        }
      }

      // Label
      &__label {
        display: inline-block;
        font-size: 15px;
        font-weight: 500;
        line-height: 24px;
        word-break: break-word;
        white-space: pre-line;
        align-self: flex-start;
        color: var(--am-c-main-text);
        padding-left: 0.75rem;
        margin-right: 8px;
      }

      // Selected Label
      .el-radio__input.is-checked:not(.is-disabled) + .el-radio__label {
        margin-right: 8px;
      }

      // Hover
      &:hover:not(.is-disabled):not(.is-checked) {

        // Inner
        .el-radio__inner {
          border: 1px solid var(--am-c-radio-border);
          box-shadow: inset 0 1px 1px rgba(20, 35, 61, 0.11);
          background: var(--am-c-primary);
        }
      }

      // Focus
      &:focus:is(.is-focus) {

        // Inner
        .el-radio__inner {
          border: 2px solid var(--am-c-radio-border-lighten80);
        }
      }

      // Disabled
      &.is-disabled {

        // Inner
        .el-radio__inner {
          box-shadow: 0 1px 1px rgba(115, 134, 169, 0.06);
        }

        // Label
        .el-radio__label {
          color: var(--am-c-main-text);
        }

        &.is-checked {
          .el-radio__inner {
            border: 1px solid var(--am-c-radio-border);
            background-color: var(--am-c-radio-border-lighten80);
          }
        }
      }

      // Label Right
      &.is-label-right {

        // Input
        .el-radio__input {
          margin-left: auto;
          order: 2;
        }

        // Label
        .el-radio__label {
          padding-left: 0;
          padding-right: 8px;
        }
      }

      // With Description
      &.is-with-description {
        align-items: flex-start;

        // Description
        .am-radio__description {
          font-weight: 400;
          font-size: 13px;
          line-height: 20px;
        }
      }
    }
  }
}
// public
.amelia-v2-booking #amelia-container {
  @include am-radio-block;
}

// admin
#amelia-app-backend-new {
  @include am-radio-block;
}
</style>