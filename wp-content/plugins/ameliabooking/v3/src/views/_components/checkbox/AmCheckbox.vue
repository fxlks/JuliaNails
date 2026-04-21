<template>
  <div class="am-checkbox-wrapper" :style="cssVars">
    <el-checkbox
      :id="props.id"
      ref="amCheckbox"
      v-model="model"
      :value="props.value"
      :label="props.label"
      :true-value="props.trueValue"
      :false-value="props.falseValue"
      :disabled="props.disabled"
      :border="props.border"
      :name="props.name"
      :checked="props.checked"
      :indeterminate="props.indeterminate"
      :validate-event="props.validateEvent"
      :tabindex="props.tabindex"
      :aria-controls="props.ariaControls"
      :class="[`am-checkbox__${size}`]"
      class="am-checkbox"
      :aria-label="props.label"
      @change="(e) => $emit('change', e)"
    >
      <template v-if="label && !$slots.default">{{ label }}</template>
      <slot></slot>
    </el-checkbox>
  </div>

</template>

<script setup>
import { computed, inject, ref, toRefs } from 'vue';
import { useColorTransparency } from "../../../assets/js/common/colorManipulation";

/**
 * Component Props
 */
const props = defineProps({
  modelValue: {
    type: [String, Number, Boolean]
  },
  value: {
    // ** value of the Checkbox when used inside a checkbox-group
    type: [String, Number, Boolean, Object]
  },
  label: {
    // ** label of the Checkbox when used inside a checkbox-group. If there's no value, label will act as value
    type: [String, Number, Boolean, Object]
  },
  trueValue: {
    // ** value of the Checkbox if it's checked
    type: [String, Number]
  },
  falseValue: {
    // ** value of the Checkbox if it's unchecked
    type: [String, Number]
  },
  disabled: {
    type: Boolean,
    default: false
  },
  border: {
    // ** whether to add a border around Checkbox
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
  checked: {
    type: Boolean,
    default: false
  },
  indeterminate: {
    // ** Set indeterminate state, only responsible for style control
    type: Boolean,
    default: false
  },
  validateEvent: {
    // ** whether to trigger form validation
    type: Boolean,
    default: true
  },
  tabindex: {
    // ** input tabindex
    type: [String, Number],
  },
  id: {
    // ** input id
    type: String,
  },
  ariaControls: {
    // ** same as aria-controls, takes effect when indeterminate is true
    type: String,
  },
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

/**
 * Component reference
 */
const amCheckbox = ref()

// * Color Vars
let amColors = inject('amColors',{})

// * Css Variables
let cssVars = computed(() => {
  return {
    '--am-c-checkbox-text-op60': useColorTransparency(amColors.value.colorInpText, 0.6),
    '--am-c-checkbox-btn-op80': useColorTransparency(amColors.value.colorPrimary, 0.8),
    '--am-c-checkbox-btn-op60': useColorTransparency(amColors.value.colorPrimary, 0.6),
    '--am-c-checkbox-btn-dsb-op60': useColorTransparency(amColors.value.colorInpBgr, 0.6),
  }
})

</script>

<style lang="scss">
@mixin am-checkbox-block {
  .am-checkbox {
    // -c-    color
    // -rad-  border radius
    // -bgr   background
    --am-c-checkbox-bgr: var(--am-c-inp-bgr);
    --am-c-checkbox-bgr-checked: var(--am-c-primary);
    --am-c-checkbox-border: var(--am-c-inp-border);
    --am-c-checkbox-text: var(--am-c-main-text);
    --am-c-checkbox-inp-text: var(--am-c-main-bgr);
    --am-rad-checkbox: 4px;
    --am-w-checkbox-inp: 16px;
    --am-h-checkbox-inp: 16px;

    display: flex;
    align-items: center;
    white-space: pre-line;
    min-height: 32px;
    height: auto;
    gap: 8px;

    &__default {
      --am-w-checkbox-inp: 16px;
      --am-h-checkbox-inp: 16px;
    }

    &__medium {
      --am-w-checkbox-inp: 14px;
      --am-h-checkbox-inp: 14px;
    }

    &__small {
      --am-w-checkbox-inp: 12px;
      --am-h-checkbox-inp: 12px;
    }

    &-wrapper {
      width: 100%;

      .el-checkbox {

        &__input {
          width: var(--am-w-checkbox-inp);
          height: var(--am-h-checkbox-inp);
          border-radius: var(--am-rad-checkbox);
          align-self: flex-start;
          padding: 2px 0;

          &.is-indeterminate.is-checked {
            .el-checkbox__inner {
              --am-c-checkbox-border: var(--am-c-primary);
              --am-c-checkbox-bgr: var(--am-c-inp-bgr);

              &:before {
                background-color: var(--am-c-primary);
                transform: scale(1) translate(-50%, -50%);
                top: 50%;
                left: 50%;
                width: 70%;
                height: 2px;
              }
            }
          }
        }

        &__label {
          //margin-left: 8px;
          font-weight: 500;
          color: var(--am-c-checkbox-text);
          align-self: flex-start;
          word-break: break-word;
          white-space: pre-line;
        }

        &__inner {
          flex: 0 0 auto;
          width: 16px;
          height: 16px;
          border: 1px solid var(--am-c-checkbox-border);
          background: var(--am-c-checkbox-bgr);
          border-radius: var(--am-rad-checkbox);

          &:after {
            left: 5px;
            border-color: var(--am-c-checkbox-inp-text);
            border-width: 2px;
          }

          &:hover {
            --am-c-checkbox-bgr: var(--am-c-checkbox-btn-op80);
          }

          &:focus {
            --am-c-checkbox-border: var(--am-c-checkbox-btn-op80);
          }
        }

        &.is-checked {
          --am-c-checkbox-bgr: var(--am-c-checkbox-bgr-checked);
          --am-c-checkbox-text-op60: var(--am-c-inp-text);

          &.is-disabled {
            --am-c-checkbox-bgr: var(--am-c-checkbox-btn-op60);

            .el-checkbox__inner {
              &:hover {
                --am-c-checkbox-bgr: var(--am-c-checkbox-btn-op60);
              }
            }
          }
        }

        &.is-disabled {
          --am-c-checkbox-bgr: var(--am-c-checkbox-btn-dsb-op60);

          .el-checkbox__inner {
            &:hover {
              --am-c-checkbox-bgr: var(--am-c-checkbox-btn-dsb-op60);
            }
          }
        }
      }
    }
  }
}

// public
.amelia-v2-booking #amelia-container {
  @include am-checkbox-block;
}

// admin
#amelia-app-backend-new {
  @include am-checkbox-block;
}
</style>
