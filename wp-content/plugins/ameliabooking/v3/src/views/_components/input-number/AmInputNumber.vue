<template>
  <el-input-number
    :id="id"
    ref="amInputNumber"
    v-model="model"
    class="am-input-number"
    :class="[`am-input-number__${size}`]"
    :style="cssVars"
    :min="min"
    :max="max"
    :step="step"
    :step-strictly="stepStrictly"
    :precision="precision"
    :readonly="readonly"
    :disabled="disabled"
    :controls="controls"
    :controls-position="controlsPosition"
    :name="name"
    :aria-label="ariaLabel"
    :place-holder="placeHolder"
    @blur="(e) => $emit('blur', e)"
    @focus="(e) => $emit('focus', e)"
    @change="(currentValue, oldValue) => $emit('change', currentValue, oldValue)"
  />
</template>

<script setup>
// * Import from Vue
import {
  ref,
  toRefs,
  inject,
  computed
} from 'vue'

// * Composables
import { useColorTransparency } from '../../../assets/js/common/colorManipulation'

/**
 * Component Props
 */
const props = defineProps({
  id: {
    type: String,
  },
  modelValue: {
    type: Number,
  },
  min: {
    type: Number,
    required: true,
  },
  max: {
    type: Number,
    default: Infinity,
  },
  step: {
    type: Number,
    default: 1,
  },
  stepStrictly: {
    type: Boolean,
    default: false,
  },
  precision: {
    type: Number,
  },
  size: {
    type: String,
    default: 'default',
    validator(value) {
      return ['default', 'medium', 'small'].includes(value)
    },
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  controls: {
    type: Boolean,
    default: true,
  },
  controlsPosition: {
    // right
    type: String,
  },
  name: {
    type: String,
    default: '',
  },
  ariaLabel: {
    type: String,
    default: 'input-number',
  },
  placeHolder: {
    type: String,
    default: '',
  },
})

/**
 * Component Emits
 * */
const emits = defineEmits([
  'change',
  'visible-change',
  'clear',
  'blur',
  'focus',
  'update:modelValue',
])

/**
 * Component model
 */
let { modelValue } = toRefs(props)
let model = computed({
  get: () => modelValue.value,
  set: (val) => {
    emits('update:modelValue', val)
  },
})

/**
 * Component reference
 */
const amInputNumber = ref(null)

// * Colors
let amColors = inject(
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
    colorInpPlaceHolder: '#1A2C37',
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
    '--am-c-inp-number-bgr': amColors.value.colorInpBgr,
    '--am-c-inp-number-border': amColors.value.colorInpBorder,
    '--am-c-inp-number-text': amColors.value.colorInpText,
    '--am-c-inp-number-text-op10': useColorTransparency(
      amColors.value.colorInpText,
      0.1
    ),
    '--am-c-inp-number-text-op03': useColorTransparency(
      amColors.value.colorInpText,
      0.03
    ),
    '--am-c-inp-number-text-op40': useColorTransparency(
      amColors.value.colorInpText,
      0.4
    ),
    '--am-c-inp-number-text-op60': useColorTransparency(
      amColors.value.colorInpText,
      0.6
    ),
    '--am-c-inp-number-placeholder': amColors.value.colorInpPlaceHolder,
  }
})
</script>

<style lang="scss">
@mixin am-inp-number-block {
  .am-input-number {
    --am-c-inp-number-bgr: var(--am-c-inp-bgr);
    --am-c-inp-number-border: var(--am-c-inp-border);
    --am-c-inp-number-text: var(--am-c-inp-text);
    --am-c-inp-number-placeholder: var(--am-c-inp-placeholder);
    --am-rad-inp-number: var(--am-rad-inp);
    --am-fs-inp-number: var(--am-fs-inp);

    &__default {
      --am-input-number-height: 40px;
      --am-input-number-padding: 8px 34px;
    }

    &__medium {
      --am-input-number-height: 36px;
      --am-input-number-padding: 6px 34px;
    }

    &__small {
      --am-input-number-height: 32px;
      --am-input-number-padding: 4px 34px;
    }
  }

  .el-input-number {
    .el-input {
      &__wrapper {
        height: var(--am-input-number-height);
        border: none;
        border-radius: var(--am-rad-inp-number);
        background-color: var(--am-c-inp-number-bgr);
        box-shadow: 0 0 0 1px var(--am-c-inp-number-border);
        padding: var(--am-input-number-padding);
        box-sizing: border-box;
        transition: all 0.3s ease-in-out;

        &.is-focus {
          --am-c-inp-number-border: var(--am-c-primary);
        }
      }

      &__inner {
        font-size: var(--am-fs-inp-number);
        line-height: 1.6;
        color: var(--am-c-inp-number-text);
        border: none;
        background: none;
        padding: 0;
        margin: 0;
        max-width: 100%;
        width: 100%;

        &::-webkit-input-placeholder {
          /* Chrome/Opera/Safari */
          color: var(--am-c-inp-number-placeholder);
        }
        &::-moz-placeholder {
          /* Firefox 19+ */
          color: var(--am-c-inp-number-placeholder);
        }
        &:-ms-input-placeholder {
          /* IE 10+ */
          color: var(--am-c-inp-number-placeholder);
        }
        &:-moz-placeholder {
          /* Firefox 18- */
          color: var(--am-c-inp-number-placeholder);
        }
      }
    }

    &__decrease {
      border-right-color: transparent;
    }

    &__increase {
      border-left-color: transparent;
    }

    &__decrease,
    &__increase {
      background-color: transparent;

      &:hover {
        .el-icon {
          color: var(--am-c-inp-number-text);
          border-radius: 4px;
          background-color: var(--am-c-inp-number-text-op10);
        }

        ~ .el-input:not(.is-disabled) .el-input__wrapper {
          --am-c-inp-number-border: var(--am-c-primary);
        }
      }

      .el-icon {
        width: 22px;
        height: 22px;
        color: var(--am-c-inp-number-text);
      }
    }

    &.is-disabled {
      .el-icon {
        color: var(--am-c-inp-number-text-op40);
      }

      .el-input__wrapper {
        background-color: var(--am-c-inp-number-text-op03);
        color: var(--am-c-inp-number-text-op60);
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-inp-number-block;
}

// admin
#amelia-app-backend-new {
  @include am-inp-number-block;
}
</style>
