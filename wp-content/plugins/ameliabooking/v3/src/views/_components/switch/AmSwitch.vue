<template>
<div
  class="am-switch-wrapper"
  :class="parentClass"
  :style="cssVars"
>
  <el-switch
    :id="id"
    ref="amSwitch"
    v-model="model"
    class="am-switch"
    :class="props.class"
    :name="name"
    :width="width"
    :size="size"
    :validate-event="validateEvent"
    :loading="loading"
    :disabled="disabled"
    :active-color="activeColor"
    :inactive-color="inactiveColor"
    :active-text="activeText"
    :inactive-text="inactiveText"
    :active-value="activeValue"
    :inactive-value="inactiveValue"
    :active-icon-class="activeIconClass"
    :inactive-icon-class="inactiveIconClass"
    :active-icon="activeIcon"
    :inactive-icon="inactiveIcon"
    :before-change="beforeChange"
    :border-color="borderColor"
    :inline-prompt="inlinePrompt"
    :aria-label="ariaLabel"
    @change="(e) => emits('change', e)"
  >
  </el-switch>
</div>
</template>

<script setup>
// * Import from Vue
import {
  ref,
  toRefs,
  computed,
  inject,
} from 'vue';

/**
 * Component Props
 */
const props = defineProps({
  modelValue: {
    type: [Boolean, String, Number],
    default: false
  },
  id: {
    type: String
  },
  name: {
    type: String,
  },
  width: {
    type: [Number, String],
  },
  size: {
    type: String,
    default: 'default',
    validator(value) {
      return ['large', 'default', 'small'].includes(value)
    }
  },
  validateEvent: {
    type: Boolean,
    default: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  activeColor: {
    type: String,
  },
  inactiveColor: {
    type: String,
  },
  activeText: {
    type: String,
  },
  inactiveText: {
    type: String,
  },
  activeValue: {
    type: [Boolean, String, Number],
    default: true
  },
  inactiveValue: {
    type: [Boolean, String, Number],
    default: false
  },
  activeIconClass: {
    type: String,
  },
  inactiveIconClass: {
    type: String,
  },
  activeIcon: {
    type: String,
  },
  inactiveIcon: {
    type: String,
  },
  beforeChange: {
    type: [Function, Boolean],
    default: () => {}
  },
  borderColor: {
    type: String,
  },
  inlinePrompt: {
    type: Boolean,
    default: false
  },
  parentClass: {
    type: String,
  },
  class: {
    type: String,
  },
  ariaLabel: {
    type: String,
  },
})

/**
 * Component Emits
 */
const emits = defineEmits(['update:modelValue', 'change'])

/**
 * Component model
 */
const { modelValue } = toRefs(props)
const model = computed({
  get: () => modelValue.value,
  set: (value) => emits('update:modelValue', value)
})

/**
 * Component reference
 */
const amSwitch = ref(null)

// * Color vars
const amColors = inject('amColors', ref({
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

// * CSS Variables
const cssVars = computed(() => {
  return {
    '--am-c-switch-bgr-active': amColors.value.colorPrimary,
    '--am-c-switch-bgr-inactive': amColors.value.colorInpBorder,
    '--am-c-switch-action': amColors.value.colorMainBgr,
    '--am-c-switch-text': amColors.value.colorMainText,
  }
})
</script>

<style lang="scss">
@mixin am-switch-block {
  .am-switch {
    --am-c-switch-bgr: var(--am-c-switch-bgr-inactive);
    position: relative;
    display: flex;
    align-items: center;
    gap: 8px;

    &-wrapper {
      display: inline-flex;
    }
    //
    &.is-checked {
      --am-c-switch-bgr: var(--am-c-switch-bgr-active);

      .el-switch {
        &__core {
          background-color: var(--am-c-switch-bgr-active);
        }
      }
    }
    .el-switch {
      &__core {
        border: 1px solid var(--am-c-switch-bgr);
        background-color: var(--am-c-switch-bgr);
      }

      &__action {
        background-color: var(--am-c-switch-action);
      }

      &__label {
        color: var(--am-c-switch-text);
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-switch-block;
}

// admin
#amelia-app-backend-new {
  @include am-switch-block;
}
</style>